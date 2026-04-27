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

<div class="billing-visual" aria-hidden="true">
    <div class="price-card h">
        <span class="pc-tag">{{ $tag }}</span>
        <span class="pc-price">{{ $price }}</span>
        <span class="pc-line"></span>
        <span class="pc-line short"></span>
    </div>
    <div class="arrow-mid">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
    </div>
    <div class="checkout-snip">
        @foreach ($rows as $row)
            <div class="cs-row"><span class="k">{{ $row['label'] }}</span><span class="v">{{ $row['value'] }}</span></div>
        @endforeach
        <div class="cs-pay">{{ $payLabel }}</div>
    </div>
</div>
