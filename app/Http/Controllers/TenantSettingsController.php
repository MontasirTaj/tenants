<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantSettingsController extends Controller
{
    public function edit(string $subdomain)
    {
        $user = Auth::guard('tenant')->user();
        if (! $user || ! ($user->hasRole('admin') || $user->hasRole('Manager'))) {
            abort(403);
        }

        $setting = TenantSetting::query()->first();

        if (! $setting) {
            $mainTenant = Tenant::where('Subdomain', $subdomain)->first();
            $setting = TenantSetting::create([
                'name' => $mainTenant->TenantName ?? null,
                'primary_color' => null,
                'logo_path' => null,
            ]);
        }

        return view('pages.tenant.settings.index', [
            'user' => $user,
            'subdomain' => $subdomain,
            'setting' => $setting,
        ]);
    }

    public function update(Request $request, string $subdomain)
    {
        $user = Auth::guard('tenant')->user();
        if (! $user || ! ($user->hasRole('admin') || $user->hasRole('Manager'))) {
            abort(403);
        }

        $setting = TenantSetting::query()->first();
        if (! $setting) {
            $setting = new TenantSetting();
        }

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['nullable', 'regex:/^#?[0-9A-Fa-f]{3,6}$/'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $setting->name = $data['name'] ?? $setting->name;

        if (! empty($data['primary_color'])) {
            $color = $data['primary_color'];
            if ($color[0] !== '#') {
                $color = '#' . $color;
            }
            $setting->primary_color = $color;
        }

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store("tenants/{$subdomain}/branding", 'public');
            $setting->logo_path = $path;
        }

        $setting->save();

        return back()->with('status', __('app.tenant_settings_saved'));
    }
}
