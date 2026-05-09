@php
    $pill = $data['pill'] ?? 'claude-code · connected to my-app';
    $items = $data['items'] ?? [
        'Laravel Boost MCP server',
        'CLAUDE.md project memory',
        'Pest / Filament / Tailwind skills',
    ];
@endphp

<div class="grid gap-2 rounded-xl border border-dashed border-(--border-2) bg-(--lp-bg) p-3.5 text-[11px] font-mono" aria-hidden="true">
    <span class="inline-flex w-fit items-center gap-2 rounded-full border border-(--border) bg-(--bg-elev) px-2.5 py-1.5 text-[11px]">
        <span class="block h-1.5 w-1.5 rounded-full bg-(--accent)"></span>{{ $pill }}
    </span>
    <div class="grid gap-1 text-(--text-2)">
        @foreach ($items as $item)
            <div class="flex items-center gap-2"><span class="text-(--accent)">✓</span><span>{{ $item }}</span></div>
        @endforeach
    </div>
</div>
