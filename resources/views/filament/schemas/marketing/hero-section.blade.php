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
    <div class="mx-auto max-w-[1200px] px-7 max-[700px]:px-[18px]">
        @if (filled($eyebrow))
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-(--border) bg-(--bg-elev) py-[5px] pr-3 pl-2 font-mono text-xs text-(--text-3)">
                <span class="block h-1.5 w-1.5 rounded-full bg-(--accent) shadow-[0_0_0_3px_color-mix(in_oklab,var(--accent)_25%,transparent)]"></span>
                <span>{{ $eyebrow }}</span>
            </div>
        @endif

        @if (filled($heading))
            <h1 class="mb-[22px] max-w-[14ch] text-[clamp(40px,6.2vw,72px)] leading-[1.02] font-semibold tracking-[-0.035em] text-balance">
                {{ $heading }}@if (filled($headingAccent))
                    <span class="text-(--accent)">{{ $headingAccent }}</span>
                @endif
            </h1>
        @endif

        @if (filled($description))
            <p class="mb-8 max-w-[56ch] text-[18px] text-(--text-2) text-pretty">{{ $description }}</p>
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
                    <span class="font-mono text-xs text-(--text-3)">{{ $commandHint }}</span>
                @endif
            </div>
        @endif

        @if (! empty($pills))
            <div class="flex flex-wrap gap-1.5" aria-label="What's included">
                @foreach ($pills as $pill)
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-(--border) bg-(--bg-elev) px-2.5 py-1 font-mono text-xs tracking-[-0.005em] text-(--text-2)">
                        <span class="block h-[5px] w-[5px] rounded-full bg-(--text-3)"></span>{{ $pill }}
                    </span>
                @endforeach
            </div>
        @endif

        @if ($showInception)
            <div class="mt-16 flex flex-col">
                <div class="overflow-hidden rounded-[14px] border border-(--border) bg-(--bg-elev) shadow-[0_30px_60px_-30px_oklch(0_0_0_/_0.6)]">
                    <div class="flex items-center gap-2.5 border-b border-(--border) bg-(--bg-soft) px-3.5 py-2.5">
                        <div class="flex gap-1.5">
                            <span class="block h-[11px] w-[11px] rounded-full bg-[oklch(0_0_0_/_0.12)] dark:bg-[oklch(1_0_0_/_0.14)]"></span>
                            <span class="block h-[11px] w-[11px] rounded-full bg-[oklch(0_0_0_/_0.12)] dark:bg-[oklch(1_0_0_/_0.14)]"></span>
                            <span class="block h-[11px] w-[11px] rounded-full bg-[oklch(0_0_0_/_0.12)] dark:bg-[oklch(1_0_0_/_0.14)]"></span>
                        </div>
                        <div class="flex min-w-0 flex-1 items-center gap-2 overflow-hidden rounded-[7px] border border-(--border) bg-(--lp-bg) px-2.5 py-1.5 font-mono text-xs whitespace-nowrap text-(--text-3)">
                            <svg class="text-(--text-3)" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="5" y="11" width="14" height="10" rx="2"/><path d="M8 11V7a4 4 0 0 1 8 0v4"/></svg>
                            <span class="text-(--text-2)">{{ $host }}</span><span class="text-(--text-3) opacity-85">/app/acme/settings/subscription</span>
                        </div>
                    </div>
                    <div class="bg-(--lp-bg)">
                        <div class="grid min-h-[360px] grid-cols-[168px_1fr] max-[720px]:min-h-0 max-[720px]:grid-cols-1" aria-hidden="true">
                            <aside class="flex flex-col gap-3.5 border-r border-(--border) bg-(--bg-soft) px-3 py-3.5 text-[11px] max-[720px]:hidden">
                                <div class="px-1.5 pb-1 text-[12px] font-semibold"><x-marketing.wordmark /></div>
                                <div class="flex items-center gap-1.5 rounded-[7px] border border-(--border) bg-(--bg-elev) px-2 py-1.5">
                                    <span class="flex h-4 w-4 items-center justify-center rounded bg-(--accent) text-[9px] font-bold text-(--accent-ink)">A</span>
                                    <span class="flex-1 font-medium text-(--lp-text)">acme</span>
                                    <span class="text-[9px] text-(--text-3)">▾</span>
                                </div>
                                <div class="flex flex-col gap-0.5">
                                    <div class="px-2 py-1 font-mono text-[9.5px] uppercase tracking-[0.06em] text-(--text-3)">Account</div>
                                    <span class="rounded-md px-2 py-1 text-[11px] text-(--text-2)">Profile</span>
                                    <span class="rounded-md px-2 py-1 text-[11px] text-(--text-2)">Invitations</span>
                                    <span class="rounded-md px-2 py-1 text-[11px] text-(--text-2)">Advanced</span>
                                </div>
                                <div class="flex flex-col gap-0.5">
                                    <div class="px-2 py-1 font-mono text-[9.5px] uppercase tracking-[0.06em] text-(--text-3)">Team</div>
                                    <span class="rounded-md px-2 py-1 text-[11px] text-(--text-2)">Profile</span>
                                    <span class="rounded-md px-2 py-1 text-[11px] text-(--text-2)">Members</span>
                                    <span class="rounded-md bg-[color-mix(in_oklab,var(--accent)_14%,transparent)] px-2 py-1 text-[11px] font-medium text-(--accent)">Subscription</span>
                                    <span class="rounded-md px-2 py-1 text-[11px] text-(--text-2)">Advanced</span>
                                </div>
                            </aside>
                            <main class="flex flex-col bg-(--lp-bg)">
                                <header class="hidden max-[720px]:flex max-[720px]:items-center max-[720px]:gap-2.5 max-[720px]:border-b max-[720px]:border-(--border) max-[720px]:bg-(--bg-soft) max-[720px]:px-3.5 max-[720px]:py-2.5">
                                    <span class="inline-flex h-3 w-4 flex-shrink-0 flex-col justify-between" aria-hidden="true">
                                        <span class="block h-[1.5px] rounded-sm bg-(--text-2)"></span>
                                        <span class="block h-[1.5px] rounded-sm bg-(--text-2)"></span>
                                        <span class="block h-[1.5px] rounded-sm bg-(--text-2)"></span>
                                    </span>
                                    <span class="text-[12px] font-semibold"><x-marketing.wordmark /></span>
                                    <span class="ml-auto inline-flex items-center gap-1.5 rounded-full border border-(--border) bg-(--bg-elev) py-1 pr-2 pl-1 text-[10.5px] text-(--lp-text)">
                                        <span class="flex h-3.5 w-3.5 items-center justify-center rounded bg-(--accent) text-[8px] font-bold text-(--accent-ink)">A</span>
                                        <span>acme</span>
                                    </span>
                                    <span class="block h-[22px] w-[22px] rounded-full bg-[linear-gradient(135deg,var(--accent),oklch(0.7_0.14_220))]"></span>
                                </header>
                                <header class="flex items-center justify-between border-b border-(--border) px-4.5 py-2.5 text-[11px] max-[720px]:px-3.5">
                                    <span class="font-mono text-[10.5px] text-(--text-3)">Team Settings <span class="mx-1 opacity-50">/</span> <strong class="font-medium text-(--lp-text)">Subscription</strong></span>
                                    <span class="block h-[22px] w-[22px] rounded-full bg-[linear-gradient(135deg,var(--accent),oklch(0.7_0.14_220))] max-[720px]:hidden"></span>
                                </header>
                                <div class="flex flex-col gap-3.5 p-4.5 max-[720px]:p-3.5">
                                    <div class="inline-flex gap-0.5 self-center rounded-full border border-(--border) bg-(--bg-elev) p-[3px] text-[10px]">
                                        <span class="rounded-full bg-(--accent) px-3 py-1 font-medium text-(--accent-ink)">Monthly</span>
                                        <span class="rounded-full px-3 py-1 font-medium text-(--text-3)">Yearly</span>
                                    </div>
                                    <div class="grid grid-cols-3 gap-2.5 max-[720px]:grid-cols-1">
                                        <div class="relative flex flex-col gap-2 rounded-[10px] border border-(--border) bg-(--bg-elev) p-3">
                                            <div class="text-[12px] font-semibold text-(--lp-text)">Free</div>
                                            <div class="text-[18px] leading-none font-bold text-(--lp-text)">$0<span class="ml-1 text-[9px] font-normal text-(--text-3)">forever</span></div>
                                            <div class="rounded-md border border-(--border) bg-transparent py-1.5 text-center text-[10px] font-medium text-(--text-2)">Current plan</div>
                                            <ul class="m-0 flex list-none flex-col gap-1.5 p-0">
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[90%] rounded-sm bg-(--border-2)"></span></li>
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[65%] rounded-sm bg-(--border-2)"></span></li>
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[40%] rounded-sm bg-(--border-2)"></span></li>
                                            </ul>
                                        </div>
                                        <div class="relative flex flex-col gap-2 rounded-[10px] border border-(--accent) bg-(--bg-elev) p-3 shadow-[0_0_0_1px_var(--accent)]">
                                            <div class="absolute -top-[7px] left-1/2 -translate-x-1/2 rounded-full bg-(--accent) px-1.5 py-0.5 text-[8.5px] font-semibold uppercase tracking-[0.04em] text-(--accent-ink)">Most popular</div>
                                            <div class="text-[12px] font-semibold text-(--lp-text)">Pro</div>
                                            <div class="text-[18px] leading-none font-bold text-(--lp-text)">$29<span class="ml-1 text-[9px] font-normal text-(--text-3)">per month</span></div>
                                            <div class="rounded-md bg-(--accent) py-1.5 text-center text-[10px] font-medium text-(--accent-ink)">Choose plan</div>
                                            <ul class="m-0 flex list-none flex-col gap-1.5 p-0">
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[90%] rounded-sm bg-(--border-2)"></span></li>
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[90%] rounded-sm bg-(--border-2)"></span></li>
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[65%] rounded-sm bg-(--border-2)"></span></li>
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[40%] rounded-sm bg-(--border-2)"></span></li>
                                            </ul>
                                        </div>
                                        <div class="relative flex flex-col gap-2 rounded-[10px] border border-(--border) bg-(--bg-elev) p-3">
                                            <div class="text-[12px] font-semibold text-(--lp-text)">Studio</div>
                                            <div class="text-[18px] leading-none font-bold text-(--lp-text)">$79<span class="ml-1 text-[9px] font-normal text-(--text-3)">per month</span></div>
                                            <div class="rounded-md border border-(--border) bg-transparent py-1.5 text-center text-[10px] font-medium text-(--text-2)">Choose plan</div>
                                            <ul class="m-0 flex list-none flex-col gap-1.5 p-0">
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[90%] rounded-sm bg-(--border-2)"></span></li>
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[65%] rounded-sm bg-(--border-2)"></span></li>
                                                <li class="flex items-center gap-1.5"><span class="relative h-2 w-2 flex-shrink-0 rounded-full bg-[color-mix(in_oklab,var(--accent)_22%,transparent)] after:absolute after:top-1/2 after:left-1/2 after:h-1 after:w-1 after:-translate-x-1/2 after:-translate-y-1/2 after:rounded-full after:bg-(--accent) after:content-['']"></span><span class="block h-1 w-[40%] rounded-sm bg-(--border-2)"></span></li>
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
