<?php

namespace App\Mail;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TenantWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public Tenant $tenant;
    public string $loginEmail;
    public string $plainPassword;

    public function __construct(Tenant $tenant, string $loginEmail, string $plainPassword)
    {
        $this->tenant = $tenant;
        $this->loginEmail = $loginEmail;
        $this->plainPassword = $plainPassword;
    }

    public function build(): self
    {
        $baseUrl = config('app.url');
        $scheme = parse_url($baseUrl, PHP_URL_SCHEME) ?: 'http';
        $baseHost = parse_url($baseUrl, PHP_URL_HOST) ?: 'localhost';

        $locale = app()->getLocale() ?: config('app.locale', 'ar');
        $tenantHost = $this->tenant->Subdomain . '.' . $baseHost;
        $loginUrl = sprintf('%s://%s/%s/login', $scheme, $tenantHost, $locale);

        return $this
            ->subject(__('Your workspace is ready'))
            ->view('emails.tenant_welcome')
            ->with([
                'tenant' => $this->tenant,
                'loginEmail' => $this->loginEmail,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => $loginUrl,
            ]);
    }
}
