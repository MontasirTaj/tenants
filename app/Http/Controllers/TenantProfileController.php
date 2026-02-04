<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class TenantProfileController extends Controller
{
    public function editPassword()
    {
        $user = Auth::guard('tenant')->user();

        return view('pages.tenant.profile.password', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::guard('tenant')->user();

        if (! $user) {
            abort(403);
        }

        $changingPassword = $request->filled('current_password') || $request->filled('password') || $request->filled('password_confirmation');
        $changingAvatar = $request->hasFile('avatar');

        $rules = [];

        if ($changingPassword) {
            $rules['current_password'] = ['required'];
            $rules['password'] = ['required', 'confirmed', PasswordRule::min(6)->mixedCase()->numbers()->symbols()];
        }

        if ($changingAvatar) {
            $rules['avatar'] = ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'];
        }

        if (empty($rules)) {
            return back()->withErrors(['general' => __('app.nothing_to_update')]);
        }

        $validated = $request->validate($rules);

        if ($changingPassword) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => __('auth.password')])->withInput();
            }

            $user->password = Hash::make($validated['password']);
            // بعد تغيير كلمة المرور بنجاح، لم يعد مجبَرًا على تغييرها
            if (isset($user->must_change_password) && $user->must_change_password) {
                $user->must_change_password = false;
            }
        }

        if ($changingAvatar) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return back()->with('status', __('app.profile_updated'));
    }
}
