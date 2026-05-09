@php
    $owner = $data['owner'] ?? ['initials' => 'EK', 'role' => 'owner'];
    $members = $data['members'] ?? [
        ['initials' => 'JM', 'role' => 'member'],
        ['initials' => 'RS', 'role' => 'member'],
        ['initials' => '+3', 'role' => 'invited'],
    ];
@endphp

<div class="grid gap-3.5 rounded-xl border border-dashed border-[var(--border-2)] bg-[var(--lp-bg)] p-[18px]" aria-hidden="true">
    <div class="flex items-center justify-center gap-2.5">
        <div class="grid h-8 w-8 place-items-center rounded-full border border-transparent bg-[var(--accent)] text-[11px] text-[var(--accent-ink)] [font-family:var(--mono)]">{{ $owner['initials'] }}</div>
        <span class="rounded border border-[color-mix(in_oklab,var(--accent)_40%,var(--border))] bg-[var(--bg-elev)] px-1.5 py-0.5 text-[10px] text-[var(--accent)] [font-family:var(--mono)]">{{ $owner['role'] }}</span>
    </div>
    <div class="mx-auto h-[18px] w-px bg-[var(--border-2)]"></div>
    <div class="flex justify-center gap-6">
        @foreach ($members as $member)
            <div class="flex flex-col items-center gap-2">
                <div class="grid h-8 w-8 place-items-center rounded-full border border-[var(--border-2)] bg-[var(--bg-soft)] text-[11px] text-[var(--text-2)] [font-family:var(--mono)]">{{ $member['initials'] }}</div>
                <span class="rounded border border-[var(--border)] bg-[var(--bg-elev)] px-1.5 py-0.5 text-[10px] text-[var(--text-3)] [font-family:var(--mono)]">{{ $member['role'] }}</span>
            </div>
        @endforeach
    </div>
</div>
