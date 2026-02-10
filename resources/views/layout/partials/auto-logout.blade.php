@php
    use Illuminate\Support\Facades\Route;
    $isAdminArea = Route::is('admin.*');
    if ($isAdminArea && Route::has('admin.logout')) {
        $logoutUrl = route('admin.logout');
    } elseif (Route::has('tenant.subdomain.logout')) {
        $logoutUrl = route('tenant.subdomain.logout', ['subdomain' => request()->route('subdomain')]);
    } else {
        $logoutUrl = url('/logout');
    }
    // Allow quick testing via query ?idle=1, else use env or session lifetime
    $idleMinutes = (int) request('idle', env('SESSION_IDLE_CLIENT_MINUTES', config('session.lifetime', 10)));
@endphp
<script>
    (function() {
        var INACTIVITY_MS = {{ $idleMinutes }} * 60 * 1000; // minutes → ms
        var timerId;
        var csrf = document.querySelector('meta[name="_token"]')?.getAttribute('content');
        var logoutUrl = @json($logoutUrl);

        function schedule() {
            clearTimeout(timerId);
            timerId = setTimeout(function() {
                try {
                    if (!csrf) {
                        window.location.reload();
                        return;
                    }
                    fetch(logoutUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    }).finally(function() {
                        window.location.reload();
                    });
                } catch (e) {
                    window.location.reload();
                }
            }, INACTIVITY_MS);
        }

        ['mousemove', 'keydown', 'scroll', 'click', 'touchstart', 'focus'].forEach(function(evt) {
            window.addEventListener(evt, schedule, {
                passive: true
            });
        });

        schedule();
    })();
</script>
