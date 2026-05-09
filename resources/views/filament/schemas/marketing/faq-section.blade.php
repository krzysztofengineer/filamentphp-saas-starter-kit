@php
    $eyebrow = $getEyebrow();
    $heading = $getHeading();
    $items = $getItems();
@endphp

<section class="py-16" id="faq" data-testid="landing-faq">
    <div class="mx-auto max-w-[1200px] px-7 max-[700px]:px-[18px]">
        <div class="flex flex-col items-start">
            @if (filled($eyebrow))
                <div class="mb-3.5 text-xs lowercase text-(--accent) font-mono">{{ $eyebrow }}</div>
            @endif
            @if (filled($heading))
                <h2 class="m-0 mb-3.5 max-w-[22ch] text-[clamp(28px,3.6vw,44px)] leading-[1.05] font-semibold tracking-[-0.03em] text-balance">{{ $heading }}</h2>
            @endif
        </div>

        <div class="grid grid-cols-1 border-t border-(--border) md:grid-cols-2">
            @foreach ($items as $i => $item)
                <div class="border-b border-(--border) px-0 py-[22px] md:border-r md:px-1 md:py-6 md:even:border-r-0">
                    <div class="flex items-baseline gap-3">
                        <span class="text-xs text-(--accent) font-mono">{{ str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) }}</span>
                        <h4 class="m-0 mb-2 pr-6 text-[16px] font-semibold tracking-[-0.01em]">{{ $item['question'] ?? '' }}</h4>
                    </div>
                    <p class="m-0 max-w-[56ch] text-[14.5px] leading-[1.55] text-(--text-2) [&_code]:font-mono">{!! $item['answer'] ?? '' !!}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
