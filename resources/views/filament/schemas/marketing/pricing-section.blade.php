@php
    $eyebrow = $getEyebrow();
    $tagline = $getTagline();
    $heading = $getHeading();
    $description = $getDescription();
    $plans = $getPlans();
    $footnote = $getFootnote();
    $defaultInterval = $getDefaultInterval();
    $showToggle = $shouldShowIntervalToggle();
    $freeCtaLabel = $getFreeCtaLabel();
    $freeCtaUrl = $getFreeCtaUrl();
    $registerUrl = route('filament.app.auth.register');
@endphp

<section class="py-24" id="pricing" data-testid="landing-pricing">
    <div class="mx-auto max-w-[1200px] px-7 max-[700px]:px-[18px]" x-data="{ interval: @js($defaultInterval) }">
        <div class="flex flex-col items-center text-center">
            @if (filled($eyebrow))
                <div class="mb-3.5 text-xs lowercase text-(--accent) font-mono">{{ $eyebrow }}</div>
            @endif
            @if (filled($tagline))
                <div class="mb-3 text-[13px] text-(--accent) font-mono">{{ $tagline }}</div>
            @endif
            @if (filled($heading))
                <h2 class="mx-auto mb-3.5 max-w-[22ch] text-center text-[clamp(28px,3.6vw,44px)] leading-[1.05] font-semibold tracking-[-0.03em] text-balance">{{ $heading }}</h2>
            @endif
            @if (filled($description))
                <p class="mx-auto mb-12 max-w-[62ch] text-center text-[17px] text-(--text-2) text-pretty">{{ $description }}</p>
            @endif

            @if ($showToggle)
                <div class="mx-auto mt-[18px] mb-7 inline-flex items-center gap-1 rounded-full border border-(--border) bg-(--bg-elev) p-1" data-testid="pricing-interval-toggle">
                    <button
                        type="button"
                        class="rounded-full px-[18px] py-[7px] text-[13px] font-medium transition-colors"
                        x-on:click="interval = 'monthly'"
                        x-bind:class="interval === 'monthly' ? 'bg-(--accent) text-(--accent-ink)' : 'text-(--text-2) hover:text-(--lp-text)'"
                        data-testid="pricing-toggle-monthly"
                    >Monthly</button>
                    <button
                        type="button"
                        class="rounded-full px-[18px] py-[7px] text-[13px] font-medium transition-colors"
                        x-on:click="interval = 'yearly'"
                        x-bind:class="interval === 'yearly' ? 'bg-(--accent) text-(--accent-ink)' : 'text-(--text-2) hover:text-(--lp-text)'"
                        data-testid="pricing-toggle-yearly"
                    >Yearly</button>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 items-stretch gap-3.5 lg:grid-cols-3">
            @foreach ($plans as $plan)
                @php
                    $highlighted = ! empty($plan['highlighted']);
                    $features = $plan['features'] ?? [];
                    $monthly = $plan['prices']['monthly'] ?? [];
                    $yearly = $plan['prices']['yearly'] ?? [];
                    $isFree = ! empty($plan['is_free']);
                    $checkoutUrl = $isFree
                        ? ($freeCtaUrl ?: $registerUrl)
                        : null;
                    $ctaLabel = $isFree ? ($freeCtaLabel ?: 'Get started for free') : 'Sign up to subscribe';
                    $tierClasses = 'relative flex flex-col gap-[18px] rounded-[14px] border bg-(--bg-elev) px-7 py-[30px] '.($highlighted
                        ? 'border-(--accent) bg-[linear-gradient(180deg,color-mix(in_oklab,var(--accent)_8%,var(--bg-elev)),var(--bg-elev)_60%)] shadow-[0_0_0_1px_var(--accent)_inset]'
                        : 'border-(--border)');
                @endphp
                <div class="{{ $tierClasses }}" data-testid="landing-plan-{{ $plan['key'] ?? '' }}">
                    @if (! empty($plan['badge']))
                        <span class="absolute -top-[10px] left-7 rounded-full bg-(--accent) px-2.5 py-[3px] text-[11px] font-semibold tracking-[-0.005em] text-(--accent-ink) font-mono">{{ $plan['badge'] }}</span>
                    @endif

                    <div>
                        <div class="text-sm text-(--text-2) font-mono">{{ $plan['name'] ?? '' }}</div>

                        <div x-show="interval === 'monthly'" class="-mt-2 flex items-baseline gap-2">
                            <span class="text-[44px] font-semibold tracking-[-0.03em]">{{ $monthly['label'] ?? '' }}</span>
                            @if (! empty($monthly['period']))
                                <span class="text-[13px] text-(--text-3) font-mono">{{ $monthly['period'] }}</span>
                            @endif
                        </div>
                        <div x-show="interval === 'yearly'" x-cloak class="-mt-2 flex items-baseline gap-2">
                            <span class="text-[44px] font-semibold tracking-[-0.03em]">{{ $yearly['label'] ?? '' }}</span>
                            @if (! empty($yearly['period']))
                                <span class="text-[13px] text-(--text-3) font-mono">{{ $yearly['period'] }}</span>
                            @endif
                            @if (! empty($yearly['savings']))
                                <span class="ml-1.5 rounded-sm border border-[color-mix(in_oklab,var(--accent)_40%,var(--border))] px-1.5 py-0.5 text-[11px] text-(--accent) font-mono">{{ $yearly['savings'] }}</span>
                            @endif
                        </div>
                    </div>

                    @if (! empty($plan['description']))
                        <p class="m-0 min-h-[42px] text-sm text-(--text-2) [&_strong]:font-semibold [&_strong]:text-(--lp-text)">{!! $plan['description'] !!}</p>
                    @endif

                    <hr class="my-1 border-0 border-t border-(--border)">

                    @if (! empty($features))
                        <ul class="m-0 grid list-none gap-2.5 p-0">
                            @foreach ($features as $feature)
                                <li class="flex items-start gap-2.5 text-sm leading-[1.45] text-(--text-2) [&_strong]:text-(--lp-text)">
                                    <x-filament::icon icon="heroicon-m-check" class="mt-[2px] h-4 w-4 flex-shrink-0 text-(--accent)" />
                                    <span>{!! $feature !!}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="mt-auto">
                        <x-filament::button
                            tag="a"
                            :href="$isFree ? ($freeCtaUrl ?: $registerUrl) : $registerUrl"
                            size="lg"
                            :color="$highlighted ? 'primary' : 'gray'"
                            :outlined="! $highlighted"
                        >
                            {{ $ctaLabel }}
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>

        @if (filled($footnote))
            <div class="mt-7 text-center text-sm text-(--text-3) font-mono">{{ $footnote }}</div>
        @endif
    </div>
</section>
