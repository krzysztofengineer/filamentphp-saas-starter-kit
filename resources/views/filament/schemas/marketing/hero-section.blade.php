@php
    $heading = $getHeading();
    $description = $getDescription();
    $primaryCtaLabel = $getPrimaryCtaLabel();
    $primaryCtaUrl = $getPrimaryCtaUrl();
    $secondaryCtaLabel = $getSecondaryCtaLabel();
    $secondaryCtaUrl = $getSecondaryCtaUrl();
@endphp

<section class="relative overflow-hidden px-6 pt-12 pb-20 sm:px-10 lg:px-16 lg:pt-20 lg:pb-28" data-testid="landing-hero">
    <div class="absolute -top-24 -right-24 h-[32rem] w-[32rem] rounded-full bg-blue-200/60 blur-3xl pointer-events-none dark:bg-blue-900/30"></div>
    <div class="absolute -bottom-32 left-1/4 h-[28rem] w-[28rem] rounded-full bg-blue-100/50 blur-3xl pointer-events-none dark:bg-blue-950/40"></div>

    <div class="relative mx-auto max-w-3xl text-center">
        @if (filled($heading))
            <h1 class="text-[clamp(2.5rem,5vw,4.5rem)] font-extrabold leading-[1.05] tracking-[-0.035em] text-gray-950 dark:text-white">
                {{ $heading }}
            </h1>
        @endif

        @if (filled($description))
            <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-gray-600 dark:text-gray-400">
                {{ $description }}
            </p>
        @endif

        @if (filled($primaryCtaLabel) || filled($secondaryCtaLabel))
            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                @if (filled($primaryCtaLabel))
                    <x-filament::button tag="a" :href="$primaryCtaUrl" size="xl" color="primary" icon="heroicon-o-arrow-right" icon-position="after" data-testid="hero-cta-register">
                        {{ $primaryCtaLabel }}
                    </x-filament::button>
                @endif

                @if (filled($secondaryCtaLabel))
                    <x-filament::button tag="a" :href="$secondaryCtaUrl" size="xl" color="gray" outlined data-testid="hero-cta-features">
                        {{ $secondaryCtaLabel }}
                    </x-filament::button>
                @endif
            </div>
        @endif
    </div>
</section>
