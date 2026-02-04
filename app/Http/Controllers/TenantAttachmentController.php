<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\TenantAttachment;

class TenantAttachmentController extends Controller
{
    /**
     * عرض صفحة الإرفاق (فقط لمن يملك صلاحية Attachement).
     */
    public function index(string $subdomain)
    {
        $user = Auth::guard('tenant')->user();

        $attachments = TenantAttachment::where('uploaded_by', $user->id)
            ->latest()
            ->limit(20)
            ->get();

        return view('pages.tenant.attachments.index', [
            'user' => $user,
            'subdomain' => $subdomain,
            'attachments' => $attachments,
        ]);
    }

    /**
     * استقبال الملف / الصورة وحفظها.
     */
    public function store(Request $request, string $subdomain)
    {
        $request->validate([
            'attachment' => ['required', 'file', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx', 'max:5120'],
            'page_count' => ['nullable', 'integer', 'min:1'],
        ], [
            'attachment.required' => __('الملف مطلوب'),
            'attachment.file' => __('يجب اختيار ملف صالح'),
            'attachment.mimes' => __('نوع الملف غير مسموح به'),
            'attachment.max' => __('أقصى حجم للملف هو 5 ميغابايت'),
        ]);

        $file = $request->file('attachment');

        // خزن الملف في مجلد خاص بالمستأجر على قرص public
        $path = $file->store("tenants/{$subdomain}/attachments", 'public');

        // تحديد نوع المرفق (صورة أو ملف عام)
        $mime = $file->getClientMimeType();
        $type = Str::startsWith($mime, 'image/') ? 'image' : 'file';

        // محاولة تحديد عدد الصفحات للملفات (خاصة PDF)
        $pageCount = $request->input('page_count');
        if ($type === 'file' && empty($pageCount)) {
            $ext = strtolower($file->getClientOriginalExtension());
            if ($ext === 'pdf') {
                try {
                    $contents = Storage::disk('public')->get($path);
                    if (is_string($contents)) {
                        if (preg_match_all('/\/Type\s*\/Page[^s]/', $contents, $matches)) {
                            $pageCount = count($matches[0]) ?: null;
                        }
                    }
                } catch (\Throwable $e) {
                    $pageCount = null;
                }
            }
        }

        // إنشاء سجل في جدول المرفقات في قاعدة المستأجر
        $attachmentModel = TenantAttachment::create([
            'type' => $type,
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $mime,
            'path' => $path,
            'disk' => 'public',
            'size_bytes' => $file->getSize(),
            // عدد الصفحات (للملفات فقط إن تم احتسابه)
            'page_count' => $type === 'file' ? ($pageCount ?: null) : null,
            // حقول المعالجة تبقى null في البداية
            'uploaded_by' => Auth::guard('tenant')->id(),
        ]);

        tenant_activity('tenant.attachments.store', 'upload_attachment', $attachmentModel, [
            'description' => 'تم رفع مرفق جديد',
            'name' => $attachmentModel->original_name,
            'type' => $attachmentModel->type,
            'extension' => $attachmentModel->extension,
        ]);

        return back()->with('status', __('تم رفع الملف بنجاح'))->with('uploaded_path', $path);
    }
    
    /**
     * عرض نموذج تعديل بيانات المرفق.
     */
    public function edit(string $subdomain, int $attachment)
    {
        $user = Auth::guard('tenant')->user();
        $attachmentModel = TenantAttachment::where('id', $attachment)
            ->where('uploaded_by', $user->id)
            ->firstOrFail();

        return view('pages.tenant.attachments.edit', [
            'subdomain' => $subdomain,
            'attachment' => $attachmentModel,
        ]);
    }

    /**
     * تحديث بيانات المرفق (مثلاً الاسم وعدد الصفحات).
     */
    public function update(Request $request, string $subdomain, int $attachment)
    {
        $user = Auth::guard('tenant')->user();
        $attachmentModel = TenantAttachment::where('id', $attachment)
            ->where('uploaded_by', $user->id)
            ->firstOrFail();

        $data = $request->validate([
            'original_name' => ['required', 'string', 'max:255'],
            'page_count' => ['nullable', 'integer', 'min:1'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx', 'max:5120'],
        ]);

        // نبدأ من القيمة القادمة من العميل (إن وُجدت)، ثم نحاول احتسابها عند استبدال PDF
        $pageCount = $data['page_count'] ?? null;

        // تحديث الملف نفسه إذا تم رفع ملف جديد
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');

            // حذف الملف القديم إن وجد
            if ($attachmentModel->path && $attachmentModel->disk) {
                Storage::disk($attachmentModel->disk)->delete($attachmentModel->path);
            }

            // خزن الملف الجديد في نفس هيكلية المجلدات
            $path = $file->store("tenants/{$subdomain}/attachments", 'public');

            $mime = $file->getClientMimeType();
            $type = Str::startsWith($mime, 'image/') ? 'image' : 'file';

            $attachmentModel->type = $type;
            $attachmentModel->extension = $file->getClientOriginalExtension();
            $attachmentModel->mime_type = $mime;
            $attachmentModel->path = $path;
            $attachmentModel->disk = 'public';
            $attachmentModel->size_bytes = $file->getSize();

            // عند استبدال الملف نعيد ضبط بيانات المعالجة
            $attachmentModel->processing_response = null;
            $attachmentModel->processed_at = null;
            $attachmentModel->processed_by = null;

            // محاولة تحديد عدد الصفحات تلقائياً لملفات PDF إذا لم تُمرَّر من العميل
            if (empty($pageCount) && $attachmentModel->type === 'file' && strtolower($attachmentModel->extension) === 'pdf') {
                try {
                    $contents = Storage::disk($attachmentModel->disk)->get($attachmentModel->path);
                    if (is_string($contents)) {
                        if (preg_match_all('/\/Type\s*\/Page[^s]/', $contents, $matches)) {
                            $pageCount = count($matches[0]) ?: null;
                        }
                    }
                } catch (\Throwable $e) {
                    $pageCount = null;
                }
            }
        }

        $attachmentModel->original_name = $data['original_name'];
        $attachmentModel->page_count = $pageCount;
        $attachmentModel->save();

        tenant_activity('tenant.attachments.update', 'update_attachment', $attachmentModel, [
            'description' => 'تم تحديث بيانات المرفق',
            'name' => $attachmentModel->original_name,
            'type' => $attachmentModel->type,
            'extension' => $attachmentModel->extension,
        ]);

        return redirect()
            ->route('tenant.subdomain.attachments.index', ['subdomain' => $subdomain])
            ->with('status', __('تم تحديث المرفق بنجاح'));
    }

    /**
     * حذف المرفق من التخزين وقاعدة البيانات.
     */
    public function destroy(string $subdomain, int $attachment)
    {
        $user = Auth::guard('tenant')->user();
        $attachmentModel = TenantAttachment::where('id', $attachment)
            ->where('uploaded_by', $user->id)
            ->firstOrFail();

        if ($attachmentModel->path && $attachmentModel->disk) {
            Storage::disk($attachmentModel->disk)->delete($attachmentModel->path);
        }

        tenant_activity('tenant.attachments.destroy', 'delete_attachment', $attachmentModel, [
            'description' => 'تم حذف مرفق',
            'name' => $attachmentModel->original_name,
            'type' => $attachmentModel->type,
            'extension' => $attachmentModel->extension,
        ]);

        $attachmentModel->delete();

        return redirect()
            ->route('tenant.subdomain.attachments.index', ['subdomain' => $subdomain])
            ->with('status', __('تم حذف المرفق بنجاح'));
    }

    public function exportExcel(string $subdomain)
    {
        $userId = Auth::guard('tenant')->id();

        $attachments = TenantAttachment::where('uploaded_by', $userId)
            ->orderByDesc('created_at')
            ->get();

        $fileName = 'tenant_'.$subdomain.'_attachments_'.now()->format('Ymd_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () use ($attachments) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                __('app.name'),
                __('app.type'),
                __('app.extension'),
                __('app.size_kb'),
                __('app.page_count'),
                __('app.created_at'),
            ]);

            foreach ($attachments as $att) {
                $sizeKb = $att->size_bytes ? round($att->size_bytes / 1024, 1) : null;

                fputcsv($handle, [
                    $att->original_name,
                    $att->type,
                    $att->extension,
                    $sizeKb,
                    $att->page_count,
                    optional($att->created_at)->toDateTimeString(),
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    public function exportPdf(string $subdomain)
    {
        $user = Auth::guard('tenant')->user();

        $attachments = TenantAttachment::where('uploaded_by', $user->id)
            ->orderByDesc('created_at')
            ->get();

        return view('exports.tenant.attachments', [
            'attachments' => $attachments,
            'subdomain' => $subdomain,
        ]);
    }
}
