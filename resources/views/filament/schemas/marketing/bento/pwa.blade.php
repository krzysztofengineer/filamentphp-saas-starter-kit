@php
    $appName = $data['app_name'] ?? config('app.name');
    $appHost = $data['app_host'] ?? str_replace(['https://', 'http://'], '', config('app.url'));
    $appInitial = $data['app_initial'] ?? mb_substr($appName, 0, 1);
    $notifTitle = $data['notif_title'] ?? mb_strtoupper($appName) . ' · now';
    $notifBody = $data['notif_body'] ?? 'New invoice from Acme — $1,240';
    $notifSub = $data['notif_sub'] ?? 'Tap to view in dashboard';
@endphp

<div class="grid gap-3 rounded-xl border border-dashed border-(--border-2) bg-(--lp-bg) p-3.5" aria-hidden="true">
    <div class="flex items-center gap-2.5 rounded-lg border border-(--border) bg-(--bg-elev) px-3 py-2.5">
        <span class="relative grid h-7 w-7 place-items-center rounded-md bg-(--lp-text) text-[12px] font-semibold text-(--lp-bg) font-mono after:absolute after:top-1 after:right-1 after:h-1 after:w-1 after:rounded-full after:bg-(--accent) after:content-['']">{{ $appInitial }}</span>
        <div class="flex-1">
            <div class="text-[11px] font-semibold">Install {{ $appName }}</div>
            <div class="text-[10px] text-(--text-3) font-mono">{{ $appHost }}</div>
        </div>
        <span class="rounded-md bg-(--accent) px-2.5 py-1 text-[10px] font-semibold text-(--accent-ink)">Install</span>
    </div>
    <div class="rounded-lg border border-(--border) bg-(--bg-elev) px-3 py-2.5">
        <div class="mb-1 flex items-center gap-1.5 text-[9px] text-(--text-3) font-mono">
            <i class="inline-block h-2 w-2 rounded-sm bg-(--accent)"></i><span>{{ $notifTitle }}</span>
        </div>
        <div class="text-[11px]">{{ $notifBody }}</div>
        <div class="text-[10px] text-(--text-3)">{{ $notifSub }}</div>
    </div>
</div>
