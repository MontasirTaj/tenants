<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\TenantActivityLog;

if (! function_exists('active_class')) {
    /**
     * Return a CSS class if the current request path matches any of the patterns.
     *
     * @param  string|array  $patterns
     * @param  string  $class
     * @return string
     */
    function active_class($patterns, $class = 'active')
    {
        $patterns = (array) $patterns;

        foreach ($patterns as $pattern) {
            if ($pattern === '/' || $pattern === '') {
                if (url()->current() === url('/')) {
                    return $class;
                }
                continue;
            }

            if (request()->is($pattern)) {
                return $class;
            }
        }

        return '';
    }
}

if (! function_exists('is_active_route')) {
    /**
     * Return 'true' if route matches any of the patterns, otherwise 'false'.
     * Useful for aria-expanded attributes.
     *
     * @param  string|array  $patterns
     * @return string
     */
    function is_active_route($patterns)
    {
        $patterns = (array) $patterns;

        foreach ($patterns as $pattern) {
            if ($pattern === '/' || $pattern === '') {
                if (url()->current() === url('/')) {
                    return 'true';
                }
                continue;
            }

            if (request()->is($pattern)) {
                return 'true';
            }
        }

        return 'false';
    }
}

if (! function_exists('show_class')) {
    /**
     * Return a CSS class (default 'show') if the current request path matches.
     *
     * @param  string|array  $patterns
     * @param  string  $class
     * @return string
     */
    function show_class($patterns, $class = 'show')
    {
        $patterns = (array) $patterns;

        foreach ($patterns as $pattern) {
            if ($pattern === '/' || $pattern === '') {
                if (url()->current() === url('/')) {
                    return $class;
                }
                continue;
            }

            if (request()->is($pattern)) {
                return $class;
            }
        }

        return '';
    }
}

if (! function_exists('tenant_activity')) {
    /**
     * سجل حركة في جدول activity_logs الخاص بالمستأجر الحالي.
     */
    function tenant_activity(?string $event = null, ?string $action = null, $subject = null, array $extra = []): void
    {
        try {
            if (config('database.connections.tenant.database') === null) {
                return;
            }

            $user = Auth::guard('tenant')->user();

            $subjectType = null;
            $subjectId = null;
            if ($subject instanceof \Illuminate\Database\Eloquent\Model) {
                $subjectType = get_class($subject);
                $subjectId = $subject->getKey();
            } elseif (is_array($subject) && isset($subject['type'], $subject['id'])) {
                $subjectType = $subject['type'];
                $subjectId = $subject['id'];
            }

            TenantActivityLog::create([
                'user_id' => $user?->id,
                'guard' => 'tenant',
                'event' => $event ?? Route::currentRouteName(),
                'action' => $action,
                'subject_type' => $subjectType,
                'subject_id' => $subjectId,
                'description' => $extra['description'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 255),
                'extra' => $extra ?: null,
            ]);
        } catch (\Throwable $e) {
            // لا نكسر الطلب لو فشل التسجيل
        }
    }
}
