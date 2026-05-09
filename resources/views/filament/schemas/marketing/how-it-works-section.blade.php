@php
    $eyebrow = $getEyebrow();
    $heading = $getHeading();
    $description = $getDescription();
    $steps = $getSteps();
@endphp

<section class="py-16" id="how-it-works" data-testid="landing-how-it-works">
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

        <div class="grid grid-cols-1 gap-3.5 md:grid-cols-3">
            @foreach ($steps as $i => $step)
                <div class="relative min-w-0 overflow-hidden rounded-[14px] border border-(--border) bg-(--bg-elev) p-[26px]">
                    <div class="mb-4 flex items-center gap-2 text-[11px] text-(--text-3) font-mono">
                        <span class="grid h-[22px] w-[22px] place-items-center rounded-md border border-[color-mix(in_oklab,var(--accent)_40%,var(--border))] font-semibold text-(--accent)">{{ $i + 1 }}</span>
                        @if (! empty($step['kicker']))
                            <span>{{ $step['kicker'] }}</span>
                        @endif
                    </div>
                    @if (! empty($step['title']))
                        <h3 class="mb-2 text-[15px] font-semibold tracking-[-0.01em] [&_code]:[overflow-wrap:anywhere] [&_code]:break-all [&_code]:font-mono">{!! $step['title'] !!}</h3>
                    @endif
                    @if (! empty($step['description']))
                        <p class="mb-3.5 text-sm text-(--text-2) [overflow-wrap:anywhere] [word-break:break-word] [&_code]:[overflow-wrap:anywhere] [&_code]:break-all [&_code]:font-mono">{!! $step['description'] !!}</p>
                    @endif
                    @if (! empty($step['command']))
                        <code class="block overflow-x-auto rounded-lg border border-(--border) bg-(--lp-bg) px-3 py-2.5 text-xs whitespace-nowrap text-(--text-2) font-mono"><span class="text-(--accent)">$</span> {{ $step['command'] }}</code>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
