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

<section class="section" id="pricing" data-testid="landing-pricing">
    <div class="container" x-data="{ interval: @js($defaultInterval) }">
        <div class="section-head center">
            @if (filled($eyebrow))
                <div class="eyebrow">{{ $eyebrow }}</div>
            @endif
            @if (filled($tagline))
                <div class="pricing-tagline">{{ $tagline }}</div>
            @endif
            @if (filled($heading))
                <h2 class="section-h" style="text-align:center;">{{ $heading }}</h2>
            @endif
            @if (filled($description))
                <p class="section-sub" style="text-align:center;">{{ $description }}</p>
            @endif

            @if ($showToggle)
                <div class="pricing-toggle" data-testid="pricing-interval-toggle">
                    <button
                        type="button"
                        class="pricing-toggle-btn"
                        x-on:click="interval = 'monthly'"
                        x-bind:class="interval === 'monthly' ? 'is-active' : ''"
                        data-testid="pricing-toggle-monthly"
                    >Monthly</button>
                    <button
                        type="button"
                        class="pricing-toggle-btn"
                        x-on:click="interval = 'yearly'"
                        x-bind:class="interval === 'yearly' ? 'is-active' : ''"
                        data-testid="pricing-toggle-yearly"
                    >Yearly</button>
                </div>
            @endif
        </div>

        <div class="pricing-grid">
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
                @endphp
                <div class="tier @if ($highlighted) highlighted @endif" data-testid="landing-plan-{{ $plan['key'] ?? '' }}">
                    @if (! empty($plan['badge']))
                        <span class="tier-badge">{{ $plan['badge'] }}</span>
                    @endif

                    <div>
                        <div class="tier-name">{{ $plan['name'] ?? '' }}</div>

                        <div x-show="interval === 'monthly'" class="tier-price">
                            <span class="amt">{{ $monthly['label'] ?? '' }}</span>
                            @if (! empty($monthly['period']))
                                <span class="per">{{ $monthly['period'] }}</span>
                            @endif
                        </div>
                        <div x-show="interval === 'yearly'" x-cloak class="tier-price">
                            <span class="amt">{{ $yearly['label'] ?? '' }}</span>
                            @if (! empty($yearly['period']))
                                <span class="per">{{ $yearly['period'] }}</span>
                            @endif
                            @if (! empty($yearly['savings']))
                                <span class="tier-savings">{{ $yearly['savings'] }}</span>
                            @endif
                        </div>
                    </div>

                    @if (! empty($plan['description']))
                        <p class="tier-desc">{!! $plan['description'] !!}</p>
                    @endif

                    <hr>

                    @if (! empty($features))
                        <ul class="tier-features">
                            @foreach ($features as $feature)
                                <li>
                                    <x-filament::icon icon="heroicon-m-check" class="tier-feature-icon" />
                                    <span>{!! $feature !!}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="tier-cta">
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
            <div class="pricing-foot-line">{{ $footnote }}</div>
        @endif
    </div>
</section>
