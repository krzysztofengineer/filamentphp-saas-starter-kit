@php
    $pill = $data['pill'] ?? 'claude-code · connected to my-app';
    $items = $data['items'] ?? [
        'Laravel Boost MCP server',
        'CLAUDE.md project memory',
        'Pest / Filament / Tailwind skills',
    ];
@endphp

<div class="ai-visual" aria-hidden="true">
    <span class="ai-pill"><span class="ai-dot"></span>{{ $pill }}</span>
    <div class="ai-list">
        @foreach ($items as $item)
            <div class="ai-row"><span class="check">✓</span><span>{{ $item }}</span></div>
        @endforeach
    </div>
</div>
