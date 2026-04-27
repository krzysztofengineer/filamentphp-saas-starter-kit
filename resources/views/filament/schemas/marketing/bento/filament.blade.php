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

<div class="filament-mock" aria-hidden="true">
    <div class="fm-side">
        @foreach ($groups as $group)
            <div class="fm-group">{{ $group['heading'] }}</div>
            @foreach ($group['items'] as $item)
                <div class="fm-item @if (! empty($item['active'])) active @endif">
                    <span class="fm-dot"></span>{{ $item['label'] }}
                </div>
            @endforeach
        @endforeach
    </div>
    <div class="fm-main">
        <div class="fm-crumb">{{ $crumb }}</div>
        <div class="fm-title">{{ $title }}</div>
        <div class="fm-table">
            @foreach ($rows as $row)
                <div class="fm-row">
                    <span class="fm-av"></span>
                    <span class="fm-name">{{ $row['email'] }}</span>
                    <span class="fm-st">{{ $row['role'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
