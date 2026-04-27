@php
    $eyebrow = $getEyebrow();
    $heading = $getHeading();
    $items = $getItems();
@endphp

<section class="section section-tight" id="faq" data-testid="landing-faq">
    <div class="container">
        <div class="section-head">
            @if (filled($eyebrow))
                <div class="eyebrow">{{ $eyebrow }}</div>
            @endif
            @if (filled($heading))
                <h2 class="section-h">{{ $heading }}</h2>
            @endif
        </div>

        <div class="faq-grid">
            @foreach ($items as $i => $item)
                <div class="faq">
                    <div class="faq-q">
                        <span class="qmark">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h4>{{ $item['question'] ?? '' }}</h4>
                    </div>
                    <p>{!! $item['answer'] ?? '' !!}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
