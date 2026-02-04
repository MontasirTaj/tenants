<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Cashier;

class SubscriptionController extends Controller
{
    /**
     * Start Stripe Checkout for a given package.
     */
    public function subscribe(Request $request, string $package)
    {
        $prices = [
            'starter' => 'price_1StSBfJvJu3OqrwqJqoIhJTc',
            'pro' => 'price_1StSCQJvJu3OqrwqCNB7oDGY',
            'enterprise' => 'price_1StSCyJvJu3OqrwqJ88EITwf',
        ];

        $key = strtolower($package);
        abort_unless(isset($prices[$key]), 404);

        $user = Auth::user() ?: User::first();
        abort_unless($user, 403);

        return $user
            ->newSubscription('default', $prices[$key])
            ->checkout([
                'success_url' => route('subscribe.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('subscribe.cancel'),
            ]);
    }

    public function success(Request $request): RedirectResponse
    {
        try {
            $sessionId = $request->query('session_id');
            $user = Auth::user() ?: User::first();
            if ($sessionId && $user) {
                $stripe = Cashier::stripe();
                $session = $stripe->checkout->sessions->retrieve($sessionId);
                $subId = $session->subscription ?? null;
                if ($subId) {
                    $stripeSub = $stripe->subscriptions->retrieve($subId);
                    $item = $stripeSub->items->data[0] ?? null;
                    $priceId = $item && isset($item->price) ? ($item->price->id ?? null) : null;
                    $quantity = $item ? ($item->quantity ?? null) : null;
                    $trialEnds = isset($stripeSub->trial_end) && $stripeSub->trial_end ? Carbon::createFromTimestamp($stripeSub->trial_end) : null;

                    $user->subscriptions()->updateOrCreate(
                        ['stripe_id' => $subId],
                        [
                            'type' => 'default',
                            'stripe_status' => $stripeSub->status ?? 'active',
                            'stripe_price' => $priceId,
                            'quantity' => $quantity,
                            'trial_ends_at' => $trialEnds,
                        ]
                    );
                }
            }
        } catch (\Throwable $e) {
            Log::error('Subscribe success processing failed', ['error' => $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with('status', __('Subscription activated successfully.'));
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('landing')->with('status', __('Checkout canceled. No charges were made.'));
    }
}
