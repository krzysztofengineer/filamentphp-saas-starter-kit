@php
    $eyebrow = $getEyebrow();
    $heading = $getHeading();
    $description = $getDescription();
    $cards = $getCards();
@endphp

<section class="py-24" id="features" data-testid="landing-features">
    <div class="container">
        <div class="flex flex-col items-start">
            @if (filled($eyebrow))
                <div class="mb-3.5 text-xs lowercase text-[var(--accent)] [font-family:var(--mono)]">{{ $eyebrow }}</div>
            @endif
            @if (filled($heading))
                <h2 class="m-0 mb-3.5 max-w-[22ch] text-[clamp(28px,3.6vw,44px)] leading-[1.05] font-semibold tracking-[-0.03em] text-balance">{{ $heading }}</h2>
            @endif
            @if (filled($description))
                <p class="mb-12 max-w-[62ch] text-[17px] text-[var(--text-2)] text-pretty">{{ $description }}</p>
            @endif
        </div>

        <div class="bento">
            @foreach ($cards as $card)
                @php
                    $visual = $card['visual'] ?? null;
                    $span = $card['span'] ?? 'b-4';
                @endphp
                <article class="{{ $span }} relative flex flex-col overflow-hidden rounded-[var(--radius)] border border-[var(--border)] bg-[var(--bg-elev)] p-[26px] transition-[border-color,transform,background] duration-200 hover:border-[var(--border-2)] hover:bg-[color-mix(in_oklab,var(--bg-elev)_92%,var(--lp-text)_8%)]">
                    @if (! empty($card['title']))
                        <h3 class="mb-2 text-[18px] font-semibold tracking-[-0.015em]">{{ $card['title'] }}</h3>
                    @endif
                    @if (! empty($card['body']))
                        <p class="m-0 max-w-[48ch] text-[14.5px] leading-[1.55] text-[var(--text-2)] [&_code]:text-[0.92em] [&_code]:text-[var(--accent)] [&_code]:[font-family:var(--mono)]">{!! $card['body'] !!}</p>
                    @endif
                    @if ($visual)
                        <div class="mt-auto pt-[22px]">
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
