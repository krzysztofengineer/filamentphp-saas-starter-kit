@php
    $owner = $data['owner'] ?? ['initials' => 'EK', 'role' => 'owner'];
    $members = $data['members'] ?? [
        ['initials' => 'JM', 'role' => 'member'],
        ['initials' => 'RS', 'role' => 'member'],
        ['initials' => '+3', 'role' => 'invited'],
    ];
@endphp

<div class="grid gap-3.5 rounded-xl border border-dashed border-(--border-2) bg-(--lp-bg) p-[18px]" aria-hidden="true">
    <div class="flex items-center justify-center gap-2.5">
        <div class="grid h-8 w-8 place-items-center rounded-full border border-transparent bg-(--accent) text-[11px] text-(--accent-ink) font-mono">{{ $owner['initials'] }}</div>
        <span class="rounded border border-[color-mix(in_oklab,var(--accent)_40%,var(--border))] bg-(--bg-elev) px-1.5 py-0.5 text-[10px] text-(--accent) font-mono">{{ $owner['role'] }}</span>
    </div>
    <div class="mx-auto h-[18px] w-px bg-(--border-2)"></div>
    <div class="flex justify-center gap-6">
        @foreach ($members as $member)
            <div class="flex flex-col items-center gap-2">
                <div class="grid h-8 w-8 place-items-center rounded-full border border-(--border-2) bg-(--bg-soft) text-[11px] text-(--text-2) font-mono">{{ $member['initials'] }}</div>
                <span class="rounded border border-(--border) bg-(--bg-elev) px-1.5 py-0.5 text-[10px] text-(--text-3) font-mono">{{ $member['role'] }}</span>
            </div>
        @endforeach
    </div>
</div>
