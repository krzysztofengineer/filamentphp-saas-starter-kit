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

<section class="relative pt-[76px] pb-7" data-testid="landing-hero">
    <div class="container">
        @if (filled($eyebrow))
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-[var(--border)] bg-[var(--bg-elev)] py-[5px] pr-3 pl-2 text-xs text-[var(--text-3)] [font-family:var(--mono)]">
                <span class="h-1.5 w-1.5 rounded-full bg-[var(--accent)] shadow-[0_0_0_3px_color-mix(in_oklab,var(--accent)_25%,transparent)]"></span>
                <span>{{ $eyebrow }}</span>
            </div>
        @endif

        @if (filled($heading))
            <h1 class="mb-[22px] max-w-[14ch] text-[clamp(40px,6.2vw,72px)] leading-[1.02] font-semibold tracking-[-0.035em] text-balance">
                {{ $heading }}@if (filled($headingAccent))
                    <span class="text-[var(--accent)]">{{ $headingAccent }}</span>
                @endif
            </h1>
        @endif

        @if (filled($description))
            <p class="mb-8 max-w-[56ch] text-[18px] text-[var(--text-2)] text-pretty">{{ $description }}</p>
        @endif

        @if (filled($primaryLabel) || filled($secondaryLabel))
            <div class="mb-[22px] flex flex-wrap items-center gap-2.5">
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
            <div class="mb-9 flex flex-wrap items-center gap-3">
                <x-marketing.cmd-chip :command="$command" size="lg" />
                @if (filled($commandHint))
                    <span class="text-xs text-[var(--text-3)] [font-family:var(--mono)]">{{ $commandHint }}</span>
                @endif
            </div>
        @endif

        @if (! empty($pills))
            <div class="flex flex-wrap gap-1.5" aria-label="What's included">
                @foreach ($pills as $pill)
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-[var(--border)] bg-[var(--bg-elev)] px-2.5 py-1 text-xs tracking-[-0.005em] text-[var(--text-2)] [font-family:var(--mono)]">
                        <span class="h-[5px] w-[5px] rounded-full bg-[var(--text-3)]"></span>{{ $pill }}
                    </span>
                @endforeach
            </div>
        @endif

        @if ($showInception)
            <div class="browser-wrap">
                <div class="browser">
                    <div class="browser-bar">
                        <div class="traffic"><span></span><span></span><span></span></div>
                        <div class="url">
                            <svg class="lock" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            <span class="url-host">{{ $host }}</span><span class="url-path">/app/acme/settings/subscription</span>
                        </div>
                    </div>
                    <div class="browser-body">
                        <div class="panel-mock" aria-hidden="true">
                            <aside class="pm-side">
                                <div class="pm-brand"><x-marketing.wordmark /></div>
                                <div class="pm-tenant">
                                    <span class="pm-tenant-avatar">A</span>
                                    <span class="pm-tenant-name">acme</span>
                                    <span class="pm-tenant-caret">▾</span>
                                </div>
                                <div class="pm-nav-group">
                                    <div class="pm-nav-label">Account</div>
                                    <span class="pm-nav-item">Profile</span>
                                    <span class="pm-nav-item">Invitations</span>
                                    <span class="pm-nav-item">Advanced</span>
                                </div>
                                <div class="pm-nav-group">
                                    <div class="pm-nav-label">Team</div>
                                    <span class="pm-nav-item">Profile</span>
                                    <span class="pm-nav-item">Members</span>
                                    <span class="pm-nav-item active">Subscription</span>
                                    <span class="pm-nav-item">Advanced</span>
                                </div>
                            </aside>
                            <main class="pm-main">
                                <header class="pm-mobile-top">
                                    <span class="pm-burger" aria-hidden="true">
                                        <span></span><span></span><span></span>
                                    </span>
                                    <span class="pm-brand-mobile"><x-marketing.wordmark /></span>
                                    <span class="pm-tenant-mobile">
                                        <span class="pm-tenant-avatar">A</span>
                                        <span class="pm-tenant-name">acme</span>
                                    </span>
                                    <span class="pm-avatar"></span>
                                </header>
                                <header class="pm-top">
                                    <span class="pm-crumb">Team Settings <span class="pm-crumb-sep">/</span> <strong>Subscription</strong></span>
                                    <span class="pm-avatar pm-avatar-desktop"></span>
                                </header>
                                <div class="pm-body">
                                    <div class="pm-toggle">
                                        <span class="active">Monthly</span>
                                        <span>Yearly</span>
                                    </div>
                                    <div class="pm-plans">
                                        <div class="pm-plan">
                                            <div class="pm-plan-name">Free</div>
                                            <div class="pm-plan-price">$0<span>forever</span></div>
                                            <div class="pm-plan-cta ghost">Current plan</div>
                                            <ul class="pm-plan-feats">
                                                <li><span class="pm-check"></span><span class="pm-feat-bar long"></span></li>
                                                <li><span class="pm-check"></span><span class="pm-feat-bar med"></span></li>
                                                <li><span class="pm-check"></span><span class="pm-feat-bar short"></span></li>
                                            </ul>
                                        </div>
                                        <div class="pm-plan highlight">
                                            <div class="pm-plan-badge">Most popular</div>
                                            <div class="pm-plan-name">Pro</div>
                                            <div class="pm-plan-price">$29<span>per month</span></div>
                                            <div class="pm-plan-cta">Choose plan</div>
                                            <ul class="pm-plan-feats">
                                                <li><span class="pm-check"></span><span class="pm-feat-bar long"></span></li>
                                                <li><span class="pm-check"></span><span class="pm-feat-bar long"></span></li>
                                                <li><span class="pm-check"></span><span class="pm-feat-bar med"></span></li>
                                                <li><span class="pm-check"></span><span class="pm-feat-bar short"></span></li>
                                            </ul>
                                        </div>
                                        <div class="pm-plan">
                                            <div class="pm-plan-name">Studio</div>
                                            <div class="pm-plan-price">$79<span>per month</span></div>
                                            <div class="pm-plan-cta ghost">Choose plan</div>
                                            <ul class="pm-plan-feats">
                                                <li><span class="pm-check"></span><span class="pm-feat-bar long"></span></li>
                                                <li><span class="pm-check"></span><span class="pm-feat-bar med"></span></li>
                                                <li><span class="pm-check"></span><span class="pm-feat-bar short"></span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </main>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
