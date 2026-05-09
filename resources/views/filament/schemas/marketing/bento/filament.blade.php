@php
    $groups = $data['groups'] ?? [
        ['heading' => 'Workspace', 'items' => [
            ['label' => 'Dashboard'],
            ['label' => 'Customers'],
            ['label' => 'Orders'],
        ]],
        ['heading' => 'Account', 'items' => [
            ['label' => 'Profile'],
            ['label' => 'Billing'],
        ]],
        ['heading' => 'Team', 'items' => [
            ['label' => 'Members', 'active' => true],
            ['label' => 'Invitations'],
        ]],
    ];
    $crumb = $data['crumb'] ?? '/ team / members';
    $title = $data['title'] ?? 'Members · 4';
    $rows = $data['rows'] ?? [
        ['email' => 'elena.k@…', 'role' => 'owner'],
        ['email' => 'jordan.m@…', 'role' => 'member'],
        ['email' => 'rae.s@…', 'role' => 'member'],
        ['email' => 'ben.t@…', 'role' => 'invited'],
    ];
@endphp

<div class="grid grid-cols-[130px_1fr] overflow-hidden rounded-xl border border-dashed border-(--border-2) bg-(--lp-bg)" aria-hidden="true">
    <div class="grid gap-0.5 border-r border-(--border) bg-(--bg-elev) px-2 py-3">
        @foreach ($groups as $group)
            <div class="px-2 pt-2 pb-1 text-[9px] uppercase tracking-[0.06em] text-(--text-3) font-mono">{{ $group['heading'] }}</div>
            @foreach ($group['items'] as $item)
                <div @class([
                    'flex items-center gap-2 rounded px-2 py-1.5 text-[11px]',
                    'bg-(--accent-soft) text-(--accent)' => ! empty($item['active']),
                    'text-(--text-2)' => empty($item['active']),
                ])>
                    <span class="h-1 w-1 rounded-full bg-current opacity-70"></span>{{ $item['label'] }}
                </div>
            @endforeach
        @endforeach
    </div>
    <div class="p-3.5">
        <div class="mb-2 text-[10px] text-(--text-3) font-mono">{{ $crumb }}</div>
        <div class="mb-2.5 text-[13px] font-semibold">{{ $title }}</div>
        <div class="grid gap-1.5">
            @foreach ($rows as $row)
                <div class="grid grid-cols-[24px_1fr_50px] items-center gap-2 rounded-md border border-(--border) p-1.5 text-[11px]">
                    <span class="block h-4 w-4 rounded-full border border-(--border-2) bg-(--bg-soft)"></span>
                    <span class="text-(--text-2)">{{ $row['email'] }}</span>
                    <span class="text-right text-[9px] text-(--accent) font-mono">{{ $row['role'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
