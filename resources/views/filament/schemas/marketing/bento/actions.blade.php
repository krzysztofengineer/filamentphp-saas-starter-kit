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

<div class="actions-visual" aria-hidden="true">
    <div class="actions-pane">
        <div class="actions-pane-head">{{ $path }}</div>
        @foreach ($files as $file)
            @php
                $glyph = $loop->last ? '└─' : '├─';
            @endphp
            <div class="actions-tree-row @if ($file === $highlight) active @endif">
                <span class="actions-glyph">{{ $glyph }}</span> {{ $file }}
            </div>
        @endforeach
    </div>
    <div class="actions-pane">
        <div class="actions-pane-head">// {{ $highlight }}</div>
        <div class="actions-code-row"><span class="kw">class</span> <span class="cn">InviteToTeam</span> {</div>
        <div class="actions-code-row indent"><span class="kw">public function</span> <span class="fn">handle</span>(</div>
        <div class="actions-code-row indent2"><span class="cn">Team</span> $team,</div>
        <div class="actions-code-row indent2"><span class="kw">string</span> $email,</div>
        <div class="actions-code-row indent2"><span class="cn">Role</span> $role,</div>
        <div class="actions-code-row indent">): <span class="cn">TeamInvitation</span></div>
        <div class="actions-code-row">{</div>
        <div class="actions-code-row indent"><span class="cm">// one class. one purpose. yours to edit.</span></div>
        <div class="actions-code-row">}</div>
    </div>
</div>
