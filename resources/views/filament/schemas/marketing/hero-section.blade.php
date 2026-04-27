@php
    $eyebrow = $getEyebrow();
    $heading = $getHeading();
    $headingAccent = $getHeadingAccent();
    $description = $getDescription();
    $primaryLabel = $getPrimaryCtaLabel();
    $primaryUrl = $getPrimaryCtaUrl();
    $secondaryLabel = $getSecondaryCtaLabel();
    $secondaryUrl = $getSecondaryCtaUrl();
    $command = $getCommand();
    $commandHint = $getCommandHint();
    $pills = $getStackPills();
    $showInception = $shouldShowInception();
    $host = $getHost();
@endphp

<section class="hero" data-testid="landing-hero">
    <div class="container">
        @if (filled($eyebrow))
            <div class="hero-eyebrow">
                <span class="dot"></span>
                <span>{{ $eyebrow }}</span>
            </div>
        @endif

        @if (filled($heading))
            <h1 class="hero-h">
                {{ $heading }}@if (filled($headingAccent))
                    <span class="accent">{{ $headingAccent }}</span>
                @endif
            </h1>
        @endif

        @if (filled($description))
            <p class="hero-sub">{{ $description }}</p>
        @endif

        @if (filled($primaryLabel) || filled($secondaryLabel))
            <div class="hero-ctas">
                @if (filled($primaryLabel))
                    <x-filament::button
                        tag="a"
                        :href="$primaryUrl"
                        size="xl"
                        color="primary"
                        icon="heroicon-o-arrow-right"
                        icon-position="after"
                        data-testid="hero-cta-primary"
                    >
                        {{ $primaryLabel }}
                    </x-filament::button>
                @endif

                @if (filled($secondaryLabel))
                    <x-filament::button
                        tag="a"
                        :href="$secondaryUrl"
                        size="xl"
                        color="gray"
                        outlined
                        icon="phosphor-github-logo"
                        data-testid="hero-cta-secondary"
                    >
                        {{ $secondaryLabel }}
                    </x-filament::button>
                @endif
            </div>
        @endif

        @if (filled($command))
            <div class="hero-cmd-row">
                <x-marketing.cmd-chip :command="$command" size="lg" />
                @if (filled($commandHint))
                    <span class="hint">{{ $commandHint }}</span>
                @endif
            </div>
        @endif

        @if (! empty($pills))
            <div class="pills" aria-label="What's included">
                @foreach ($pills as $pill)
                    <span class="pill"><span class="pill-dot"></span>{{ $pill }}</span>
                @endforeach
            </div>
        @endif

        @if ($showInception)
            <div class="browser-wrap">
                <div class="inception-note">
                    <span class="dot"></span>
                    <span>↓ this page, inside this page</span>
                </div>
                <div class="browser">
                    <div class="browser-bar">
                        <div class="traffic"><span></span><span></span><span></span></div>
                        <div class="url">
                            <svg class="lock" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            <span class="url-host">{{ $host }}</span><span style="opacity:.5">/</span>
                        </div>
                    </div>
                    <div class="browser-body">
                        <div class="mini-page">
                            <div class="mini-nav">
                                <div class="mini-mark"><x-marketing.wordmark /></div>
                                <div class="mini-links">
                                    <span>features</span><span>pricing</span><span>faq</span>
                                    @if (filled($primaryLabel))
                                        <span class="mini-cta">{{ $primaryLabel }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mini-hero">
                                @if (filled($heading))
                                    <h3 class="mini-h">
                                        {{ $heading }}@if (filled($headingAccent))
                                            <span class="ac">{{ $headingAccent }}</span>
                                        @endif
                                    </h3>
                                @endif
                                @if (filled($description))
                                    <p class="mini-sub">{{ Str::limit($description, 120) }}</p>
                                @endif
                                <div class="mini-row">
                                    @if (filled($primaryLabel))
                                        <span class="mini-btn primary">{{ $primaryLabel }}</span>
                                    @endif
                                    @if (filled($secondaryLabel))
                                        <span class="mini-btn">{{ $secondaryLabel }}</span>
                                    @endif
                                    @if (filled($command))
                                        <span class="mini-cmd"><span class="p">$</span>{{ $command }}</span>
                                    @endif
                                </div>
                                @if (! empty($pills))
                                    <div class="mini-pills">
                                        @foreach (array_slice($pills, 0, 7) as $pill)
                                            <span>{{ $pill }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mini-frame" aria-hidden="true">
                                    <div class="mini-frame-bar">
                                        <i></i><i></i><i></i>
                                        <span class="u">{{ $host }}/</span>
                                    </div>
                                    <div class="mini-frame-body">
                                        <div class="ln short"></div>
                                        <div class="ln med"></div>
                                        <div class="ln long"></div>
                                        <div class="ln long"></div>
                                        <div class="ln short"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
