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

<div class="overflow-hidden rounded-xl border border-dashed border-(--border-2) bg-[oklch(0.16_0.012_250)] text-[12px] font-mono dark:bg-[oklch(0.14_0.012_250)]" aria-hidden="true">
    <div class="flex items-center gap-1.5 border-b border-[oklch(1_0_0_/_0.06)] px-2.5 py-1.5">
        <i class="block h-2 w-2 rounded-full bg-[oklch(1_0_0_/_0.16)]"></i>
        <i class="block h-2 w-2 rounded-full bg-[oklch(1_0_0_/_0.16)]"></i>
        <i class="block h-2 w-2 rounded-full bg-[oklch(1_0_0_/_0.16)]"></i>
        <span class="ml-1.5 text-[10px] text-[oklch(0.6_0.012_250)]">{{ $cwd }}</span>
    </div>
    <div class="px-3.5 py-3 text-[oklch(0.85_0.012_250)]">
        <div class="py-[1.5px]"><span class="text-(--accent)">$</span> {{ $command }}</div>
        @foreach ($suites as $suite)
            <div class="py-[1.5px] text-[oklch(0.55_0.012_250)]"> PASS  {{ $suite['name'] }}</div>
            <div class="py-[1.5px] tracking-[2px] text-[oklch(0.78_0.16_150)]">  {{ $suite['dots'] }}</div>
        @endforeach
        <div class="py-[1.5px] text-[oklch(0.78_0.16_150)]">{{ $summary }}</div>
        <div class="py-[1.5px] text-[oklch(0.55_0.012_250)]">{{ $duration }}</div>
    </div>
</div>
