@php
    $eyebrow = $getEyebrow();
    $heading = $getHeading();
    $description = $getDescription();
    $cards = $getCards();
@endphp

<section class="py-24" id="features" data-testid="landing-features">
    <div class="mx-auto max-w-[1200px] px-7 max-[700px]:px-[18px]">
        <div class="flex flex-col items-start">
            @if (filled($eyebrow))
                <div class="mb-3.5 text-xs lowercase text-(--accent) font-mono">{{ $eyebrow }}</div>
            @endif
            @if (filled($heading))
                <h2 class="m-0 mb-3.5 max-w-[22ch] text-[clamp(28px,3.6vw,44px)] leading-[1.05] font-semibold tracking-[-0.03em] text-balance">{{ $heading }}</h2>
            @endif
            @if (filled($description))
                <p class="mb-12 max-w-[62ch] text-[17px] text-(--text-2) text-pretty">{{ $description }}</p>
            @endif
        </div>

        <div class="grid auto-rows-[minmax(220px,auto)] grid-cols-6 gap-3.5 [grid-template-areas:'a_a_a_b_b_b'_'c_c_c_f_f_f'_'d_d_e_e_g_g'_'h_h_h_h_h_h'] max-[980px]:grid-cols-1 max-[980px]:[grid-template-areas:none]">
            @foreach ($cards as $card)
                @php
                    $visual = $card['visual'] ?? null;
                    $span = $card['span'] ?? 'b-4';
                    $gridAreaClass = match ($span) {
                        'b-1' => '[grid-area:a]',
                        'b-2' => '[grid-area:b]',
                        'b-3' => '[grid-area:c]',
                        'b-4' => '[grid-area:d]',
                        'b-5' => '[grid-area:e]',
                        'b-6' => '[grid-area:f]',
                        'b-7' => '[grid-area:g]',
                        'b-8' => '[grid-area:h]',
                        default => '[grid-area:d]',
                    };
                @endphp
                <article class="{{ $gridAreaClass }} max-[980px]:[grid-area:auto] relative flex flex-col overflow-hidden rounded-[14px] border border-(--border) bg-(--bg-elev) p-[26px] transition-[border-color,transform,background] duration-200 hover:border-(--border-2) hover:bg-[color-mix(in_oklab,var(--bg-elev)_92%,var(--lp-text)_8%)]">
                    @if (! empty($card['title']))
                        <h3 class="mb-2 text-[18px] font-semibold tracking-[-0.015em]">{{ $card['title'] }}</h3>
                    @endif
                    @if (! empty($card['body']))
                        <p class="m-0 max-w-[48ch] text-[14.5px] leading-[1.55] text-(--text-2) [&_code]:text-[0.92em] [&_code]:text-(--accent) [&_code]:font-mono">{!! $card['body'] !!}</p>
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
