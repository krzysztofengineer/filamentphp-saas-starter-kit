<link rel="manifest" href="{{ route('pwa.manifest') }}">
<meta name="theme-color" content="#2563EB">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}">
@auth
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if ($vapidKey = config('webpush.vapid.public_key'))
        <meta name="vapid-public-key" content="{{ $vapidKey }}">
        <script src="{{ asset('push-subscribe.js') }}" defer></script>
    @endif
@endauth
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(() => {});
        });
    }
</script>
