@php
    $eyebrow = $getEyebrow();
    $heading = $getHeading();
    $description = $getDescription();
    $cards = $getCards();
@endphp

<section class="section" id="features" data-testid="landing-features">
    <div class="container">
        <div class="section-head">
            @if (filled($eyebrow))
                <div class="eyebrow">{{ $eyebrow }}</div>
            @endif
            @if (filled($heading))
                <h2 class="section-h">{{ $heading }}</h2>
            @endif
            @if (filled($description))
                <p class="section-sub">{{ $description }}</p>
            @endif
        </div>

        <div class="bento">
            @foreach ($cards as $card)
                @php
                    $visual = $card['visual'] ?? null;
                    $span = $card['span'] ?? 'b-4';
                @endphp
                <article class="card {{ $span }}">
                    @if (! empty($card['title']))
                        <h3 class="card-title">{{ $card['title'] }}</h3>
                    @endif
                    @if (! empty($card['body']))
                        <p class="card-body">{!! $card['body'] !!}</p>
                    @endif
                    @if ($visual)
                        <div class="card-visual">
                            @includeIf(
                                'filament.schemas.marketing.bento.' . $visual,
                                ['data' => $card['data'] ?? []]
                            )
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
    </div>
</section>
