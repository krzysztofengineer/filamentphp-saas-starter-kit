@php
    $heading = $getHeading();
    $headingAccent = $getHeadingAccent();
    $description = $getDescription();
    $primaryLabel = $getPrimaryCtaLabel();
    $primaryUrl = $getPrimaryCtaUrl();
    $secondaryLabel = $getSecondaryCtaLabel();
    $secondaryUrl = $getSecondaryCtaUrl();
    $command = $getCommand();
@endphp

<section class="border-t border-(--border) bg-[radial-gradient(800px_400px_at_50%_100%,var(--accent-soft),transparent_60%)] pt-[110px] pb-[120px] text-center" data-testid="landing-final-cta">
    <div class="mx-auto max-w-[1200px] px-7 max-[700px]:px-[18px]">
        @if (filled($heading))
            <h2 class="mx-auto mb-6 max-w-[14ch] text-[clamp(36px,5.5vw,64px)] leading-[1.02] font-semibold tracking-[-0.035em] text-balance">
                {{ $heading }}@if (filled($headingAccent))
                    <span class="text-(--accent)">{{ $headingAccent }}</span>
                @endif
            </h2>
        @endif

        @if (filled($description))
            <p class="mx-auto max-w-[50ch] text-center text-[18px] text-(--text-2) text-pretty">{{ $description }}</p>
        @endif

        @if (filled($command))
            <div class="mt-[26px] flex flex-wrap items-center justify-center gap-3">
                <x-marketing.cmd-chip :command="$command" size="lg" />
            </div>
        @endif

        @if (filled($primaryLabel) || filled($secondaryLabel))
            <div class="mt-3.5 flex flex-wrap items-center justify-center gap-3">
                @if (filled($primaryLabel))
                    <x-filament::button
                        tag="a"
                        :href="$primaryUrl"
                        size="xl"
                        color="primary"
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
                    >
                        {{ $secondaryLabel }}
                    </x-filament::button>
                @endif
            </div>
        @endif
    </div>
</section>
