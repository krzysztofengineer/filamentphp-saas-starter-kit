@php
    $tag = $data['tag'] ?? 'Pro · monthly';
    $price = $data['price'] ?? '$29';
    $rows = $data['rows'] ?? [
        ['label' => 'Pro plan', 'value' => '$29.00'],
        ['label' => 'Tax', 'value' => '$2.61'],
        ['label' => 'Total due today', 'value' => '$31.61'],
    ];
    $payLabel = $data['pay_label'] ?? 'Pay $31.61';
@endphp

<div class="grid grid-cols-2 gap-3 rounded-xl border border-dashed border-(--border-2) bg-(--lp-bg) p-3.5 text-[11px] font-mono" aria-hidden="true">
    <div class="flex flex-col gap-1.5 rounded-lg border border-(--accent) bg-(--bg-elev) p-3">
        <span class="text-[10px] uppercase tracking-[0.04em] text-(--accent)">{{ $tag }}</span>
        <span class="text-[18px] font-semibold text-(--lp-text)">{{ $price }}</span>
        <span class="block h-1 w-3/5 rounded-sm bg-(--border)"></span>
        <span class="block h-1 w-2/5 rounded-sm bg-(--border)"></span>
    </div>
    <div class="grid place-items-center text-(--text-3)">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
    </div>
    <div class="col-span-2 rounded-lg border border-(--border) bg-(--bg-elev) p-2.5 text-[11px] text-(--text-2)">
        @foreach ($rows as $row)
            <div class="flex justify-between py-[3px]"><span class="text-(--text-3)">{{ $row['label'] }}</span><span class="text-(--lp-text)">{{ $row['value'] }}</span></div>
        @endforeach
        <div class="mt-2 rounded-md bg-(--accent) py-1.5 text-center font-semibold text-(--accent-ink)">{{ $payLabel }}</div>
    </div>
</div>
