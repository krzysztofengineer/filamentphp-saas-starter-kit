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

<section class="closing" data-testid="landing-final-cta">
    <div class="container">
        @if (filled($heading))
            <h2 class="closing-h">
                {{ $heading }}@if (filled($headingAccent))
                    <span class="ac">{{ $headingAccent }}</span>
                @endif
            </h2>
        @endif

        @if (filled($description))
            <p class="hero-sub" style="margin: 0 auto; max-width: 50ch; text-align: center;">{{ $description }}</p>
        @endif

        @if (filled($command))
            <div class="closing-row">
                <x-marketing.cmd-chip :command="$command" size="lg" />
            </div>
        @endif

        @if (filled($primaryLabel) || filled($secondaryLabel))
            <div class="closing-row" style="margin-top: 14px;">
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
