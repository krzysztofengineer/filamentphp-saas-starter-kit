@php
    $path = $data['path'] ?? 'app/Actions/';
    $files = $data['files'] ?? [
        'AcceptTeamInvitation.php',
        'ChangeTeamRole.php',
        'CreateTeamForUser.php',
        'DeleteTeam.php',
        'InviteToTeam.php',
        'LeaveTeam.php',
        'RemoveTeamMember.php',
        'ScheduleAccountDeletion.php',
        'TransferTeamOwnership.php',
    ];
    $highlight = $data['highlight'] ?? 'InviteToTeam.php';
@endphp

<div class="grid grid-cols-[minmax(0,1fr)_minmax(0,1.25fr)] gap-2.5 rounded-xl border border-dashed border-(--border-2) bg-(--lp-bg) p-3 text-[11.5px] leading-[1.55] font-mono max-[720px]:grid-cols-1" aria-hidden="true">
    <div class="overflow-hidden rounded-lg border border-(--border) bg-(--bg-elev) px-3 py-2.5 text-(--text-2)">
        <div class="mb-1.5 text-[10px] text-(--text-3)">{{ $path }}</div>
        @foreach ($files as $file)
            @php
                $glyph = $loop->last ? '└─' : '├─';
                $isActive = $file === $highlight;
            @endphp
            <div @class([
                'truncate py-px',
                'font-semibold text-(--accent)' => $isActive,
            ])>
                <span class="text-(--text-3)">{{ $glyph }}</span> {{ $file }}
            </div>
        @endforeach
    </div>
    <div class="overflow-hidden rounded-lg border border-(--border) bg-(--bg-elev) px-3 py-2.5 text-(--text-2)">
        <div class="mb-1.5 text-[10px] text-(--text-3)">// {{ $highlight }}</div>
        <div class="truncate py-px"><span class="text-[oklch(0.7_0.16_290)]">class</span> <span class="text-[oklch(0.72_0.14_200)]">InviteToTeam</span> {</div>
        <div class="truncate py-px pl-3.5"><span class="text-[oklch(0.7_0.16_290)]">public function</span> <span class="text-[oklch(0.78_0.14_110)]">handle</span>(</div>
        <div class="truncate py-px pl-7"><span class="text-[oklch(0.72_0.14_200)]">Team</span> $team,</div>
        <div class="truncate py-px pl-7"><span class="text-[oklch(0.7_0.16_290)]">string</span> $email,</div>
        <div class="truncate py-px pl-7"><span class="text-[oklch(0.72_0.14_200)]">Role</span> $role,</div>
        <div class="truncate py-px pl-3.5">): <span class="text-[oklch(0.72_0.14_200)]">TeamInvitation</span></div>
        <div class="truncate py-px">{</div>
        <div class="truncate py-px pl-3.5"><span class="text-(--text-3) italic">// every write op gets its own class.</span></div>
        <div class="truncate py-px">}</div>
    </div>
</div>
