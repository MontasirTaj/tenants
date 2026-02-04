<?php

namespace App\Http\Controllers;

use App\Models\TenantConversation;
use App\Models\TenantConversationParticipant;
use App\Models\TenantMessage;
use App\Models\TenantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantConversationController extends Controller
{
    public function index(string $subdomain, Request $request)
    {
        $user = Auth::guard('tenant')->user();

        $conversations = TenantConversation::with(['participants.user', 'messages' => function ($q) {
                $q->latest();
            }])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderByDesc('updated_at')
            ->get();

        $stats = [
            'total_conversations' => $conversations->count(),
            'total_messages' => TenantMessage::whereIn('conversation_id', $conversations->pluck('id'))->count(),
        ];

        return view('pages.tenant.messages.index', compact('conversations', 'stats', 'subdomain'));
    }

    public function show(string $subdomain, int $conversation)
    {
        $user = Auth::guard('tenant')->user();

        $conversation = TenantConversation::with(['participants.user', 'messages.sender'])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->findOrFail($conversation);

        // حدّث آخر قراءة للمستخدم
        TenantConversationParticipant::where('conversation_id', $conversation->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        $otherUsers = TenantUser::where('id', '!=', $user->id)->get();

        return view('pages.tenant.messages.show', compact('conversation', 'otherUsers', 'subdomain'));
    }

    public function startDirect(string $subdomain, Request $request)
    {
        $user = Auth::guard('tenant')->user();

        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:tenant.users,id'],
        ]);

        $otherId = $data['user_id'];

        // لا تسمح بإنشاء محادثة مباشرة مع نفس المستخدم
        if ((int) $otherId === (int) $user->id) {
            return redirect()->route('tenant.subdomain.messages.index', [
                'subdomain' => $subdomain,
            ])->with('status', __('app.messages_cannot_message_self'));
        }

        // ابحث عن محادثة مباشرة موجودة بين المستخدمين
        $conversation = TenantConversation::where('type', 'direct')
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereHas('participants', function ($q) use ($otherId) {
                $q->where('user_id', $otherId);
            })
            ->first();

        if (! $conversation) {
            $conversation = TenantConversation::create([
                'type' => 'direct',
                'title' => null,
                'created_by' => $user->id,
            ]);

            TenantConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $user->id,
                'joined_at' => now(),
            ]);

            TenantConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $otherId,
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('tenant.subdomain.messages.show', [
            'subdomain' => $subdomain,
            'conversation' => $conversation->id,
        ]);
    }

    public function storeGroup(string $subdomain, Request $request)
    {
        $user = Auth::guard('tenant')->user();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'participants' => ['required', 'array', 'min:1'],
            'participants.*' => ['integer', 'exists:tenant.users,id'],
        ]);

        $conversation = TenantConversation::create([
            'type' => 'group',
            'title' => $data['title'],
            'created_by' => $user->id,
        ]);

        // أضف المنشئ + المشاركين
        $allIds = collect($data['participants'])->push($user->id)->unique();
        foreach ($allIds as $uid) {
            TenantConversationParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $uid,
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('tenant.subdomain.messages.show', [
            'subdomain' => $subdomain,
            'conversation' => $conversation->id,
        ]);
    }

    public function storeMessage(string $subdomain, int $conversation, Request $request)
    {
        $user = Auth::guard('tenant')->user();

        $conversationModel = TenantConversation::whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->findOrFail($conversation);

        $data = $request->validate([
            'body' => ['required', 'string'],
        ]);

        $message = TenantMessage::create([
            'conversation_id' => $conversationModel->id,
            'sender_id' => $user->id,
            'body' => $data['body'],
        ]);

        $conversationModel->touch();

        TenantConversationParticipant::where('conversation_id', $conversationModel->id)
            ->where('user_id', $user->id)
            ->update(['last_read_at' => now()]);

        tenant_activity('tenant.messages.store', 'send_message', $message, [
            'description' => 'تم إرسال رسالة جديدة',
            'conversation_id' => $conversationModel->id,
        ]);

        return redirect()->route('tenant.subdomain.messages.show', [
            'subdomain' => $subdomain,
            'conversation' => $conversationModel->id,
        ]);
    }

    public function unreadSummary(string $subdomain)
    {
        $user = Auth::guard('tenant')->user();

        $conversations = TenantConversation::with(['messages' => function ($q) {
                $q->latest();
            }])
            ->whereHas('participants', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->get();

        $totalUnread = 0;
        $items = [];

        foreach ($conversations as $conv) {
            $participant = $conv->participants()->where('user_id', $user->id)->first();
            $lastRead = $participant?->last_read_at;
            $unreadCount = TenantMessage::where('conversation_id', $conv->id)
                ->when($lastRead, function ($q) use ($lastRead) {
                    $q->where('created_at', '>', $lastRead);
                })
                ->count();

            $totalUnread += $unreadCount;

            if ($unreadCount > 0) {
                $lastMessage = $conv->messages->first();
                $items[] = [
                    'id' => $conv->id,
                    'title' => $conv->type === 'group'
                        ? ($conv->title ?: __('app.messages_group'))
                        : __('app.messages_direct'),
                    'last_message' => $lastMessage?->body,
                    'unread' => $unreadCount,
                    'url' => route('tenant.subdomain.messages.show', ['subdomain' => $subdomain, 'conversation' => $conv->id]),
                ];
            }
        }

        return response()->json([
            'count' => $totalUnread,
            'items' => $items,
        ]);
    }
}
