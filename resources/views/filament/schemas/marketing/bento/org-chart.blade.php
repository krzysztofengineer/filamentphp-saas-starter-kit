@php
    $owner = $data['owner'] ?? ['initials' => 'EK', 'role' => 'owner'];
    $members = $data['members'] ?? [
        ['initials' => 'JM', 'role' => 'member'],
        ['initials' => 'RS', 'role' => 'member'],
        ['initials' => '+3', 'role' => 'invited'],
    ];
@endphp

<div class="org-chart" aria-hidden="true">
    <div class="org-row">
        <div class="avatar owner">{{ $owner['initials'] }}</div>
        <span class="role-badge owner-b">{{ $owner['role'] }}</span>
    </div>
    <div class="org-line"></div>
    <div class="org-fan">
        @foreach ($members as $member)
            <div class="org-fan-line">
                <div class="avatar">{{ $member['initials'] }}</div>
                <span class="role-badge">{{ $member['role'] }}</span>
            </div>
        @endforeach
    </div>
</div>
