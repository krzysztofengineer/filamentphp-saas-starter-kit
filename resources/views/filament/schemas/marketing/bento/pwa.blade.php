@php
    $appName = $data['app_name'] ?? config('app.name');
    $appHost = $data['app_host'] ?? str_replace(['https://', 'http://'], '', config('app.url'));
    $appInitial = $data['app_initial'] ?? mb_substr($appName, 0, 1);
    $notifTitle = $data['notif_title'] ?? mb_strtoupper($appName) . ' · now';
    $notifBody = $data['notif_body'] ?? 'New invoice from Acme — $1,240';
    $notifSub = $data['notif_sub'] ?? 'Tap to view in dashboard';
@endphp

<div class="pwa-visual" aria-hidden="true">
    <div class="install-prompt">
        <span class="install-icon">{{ $appInitial }}</span>
        <div class="ip-text">
            <div class="ip-title">Install {{ $appName }}</div>
            <div class="ip-sub">{{ $appHost }}</div>
        </div>
        <span class="ip-btn">Install</span>
    </div>
    <div class="push-notif">
        <div class="pn-head"><i></i><span>{{ $notifTitle }}</span></div>
        <div class="pn-body">{{ $notifBody }}</div>
        <div class="pn-sub">{{ $notifSub }}</div>
    </div>
</div>
