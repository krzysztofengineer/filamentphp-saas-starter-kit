@php
    $cwd = $data['cwd'] ?? '~/my-app';
    $command = $data['command'] ?? 'php artisan test';
    $suites = $data['suites'] ?? [
        ['name' => 'Tests\\Feature\\AuthTest', 'dots' => '✓ ✓ ✓ ✓ ✓'],
        ['name' => 'Tests\\Feature\\TeamsTest', 'dots' => '✓ ✓ ✓ ✓ ✓ ✓ ✓ ✓'],
        ['name' => 'Tests\\Feature\\BillingTest', 'dots' => '✓ ✓ ✓ ✓ ✓ ✓'],
    ];
    $summary = $data['summary'] ?? 'Tests:  142 passed (412 assertions)';
    $duration = $data['duration'] ?? 'Duration: 2.84s';
@endphp

<div class="terminal" aria-hidden="true">
    <div class="terminal-bar"><i></i><i></i><i></i><span class="tt">{{ $cwd }}</span></div>
    <div class="terminal-body">
        <div class="row"><span class="prompt">$</span> {{ $command }}</div>
        @foreach ($suites as $suite)
            <div class="row dim"> PASS  {{ $suite['name'] }}</div>
            <div class="row dots">  {{ $suite['dots'] }}</div>
        @endforeach
        <div class="row ok">{{ $summary }}</div>
        <div class="row dim">{{ $duration }}</div>
    </div>
</div>
