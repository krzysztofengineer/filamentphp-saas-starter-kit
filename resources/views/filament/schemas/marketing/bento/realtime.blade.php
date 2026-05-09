@php
    $rows = $data['rows'] ?? [
        ['name' => 'elena', 'activity' => 'scheduled deletion · 14d left', 'cursor' => ''],
        ['name' => 'jordan', 'activity' => 'cancelled · account restored', 'cursor' => 'g'],
        ['name' => 'rae', 'activity' => 'purged · pii erased', 'cursor' => 'b'],
    ];
@endphp

<div class="grid gap-1.5 rounded-xl border border-dashed border-(--border-2) bg-(--lp-bg) p-3.5 text-[11px] font-mono" aria-hidden="true">
    @foreach ($rows as $row)
        @php
            $cursorClasses = match ($row['cursor'] ?? '') {
                'b' => 'bg-[oklch(0.7_0.14_220)] shadow-[0_0_0_3px_oklch(0.7_0.14_220_/_0.25)]',
                'g' => 'bg-[oklch(0.75_0.14_150)] shadow-[0_0_0_3px_oklch(0.75_0.14_150_/_0.25)]',
                default => 'bg-(--accent) shadow-[0_0_0_3px_color-mix(in_oklab,var(--accent)_25%,transparent)]',
            };
        @endphp
        <div class="flex items-center gap-2 py-[5px]">
            <span class="block h-2 w-2 rounded-full {{ $cursorClasses }}"></span>
            <span class="text-(--text-2)">{{ $row['name'] }}</span>
            <span class="ml-auto text-(--text-3)">{{ $row['activity'] }}</span>
        </div>
    @endforeach
    <div class="mt-1 h-px animate-[landing-pulse_2.4s_linear_infinite] bg-[linear-gradient(90deg,transparent,var(--accent),transparent)]"></div>
</div>
