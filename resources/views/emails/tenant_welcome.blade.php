<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <title>{{ __('Your workspace is ready') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f5f7fb; padding:20px;">
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px; margin:0 auto; background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.06);">
    <tr>
      <td style="background:#102c4f; color:#ffffff; padding:16px 20px;">
        <h2 style="margin:0; font-size:20px;">{{ config('app.name', 'DataInsight Tenants') }}</h2>
      </td>
    </tr>
    <tr>
      <td style="padding:20px; color:#222222; font-size:14px; line-height:1.6;">
        <p style="margin-top:0;">{{ __('Hello') }} {{ $tenant->OwnerName ?: $tenant->TenantName }},</p>
        <p>{{ __('Your workspace has been created and is ready to use.') }}</p>

        <p>{{ __('You can access your dashboard using the following link:') }}</p>
        <p>
          <a href="{{ $loginUrl }}" style="color:#102c4f; text-decoration:none; font-weight:bold;" target="_blank">{{ $loginUrl }}</a>
        </p>

        <p>{{ __('Your temporary login credentials:') }}</p>
        <ul>
          <li><strong>{{ __('Email') }}:</strong> {{ $loginEmail }}</li>
          <li><strong>{{ __('Password') }}:</strong> {{ $plainPassword }}</li>
        </ul>

        <p style="color:#555;">
          {{ __('For your security, we recommend that you sign in and change your password from your profile settings after the first login.') }}
        </p>

        <p style="margin-bottom:0;">{{ __('Thank you') }},<br>{{ config('app.name', 'DataInsight Tenants') }}</p>
      </td>
    </tr>
  </table>
</body>
</html>
