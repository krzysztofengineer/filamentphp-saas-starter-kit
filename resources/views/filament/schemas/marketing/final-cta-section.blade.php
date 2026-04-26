@php
    $heading = $getHeading();
    $description = $getDescription();
    $primaryCtaLabel = $getPrimaryCtaLabel();
    $primaryCtaUrl = $getPrimaryCtaUrl();
    $secondaryCtaLabel = $getSecondaryCtaLabel();
    $secondaryCtaUrl = $getSecondaryCtaUrl();
@endphp

<section class="px-6 py-24 sm:px-10 lg:px-16">
    <div class="mx-auto max-w-4xl">
        <div class="relative overflow-hidden rounded-[2.5rem] border border-gray-200 bg-white px-8 py-20 text-center sm:px-16 dark:border-gray-800 dark:bg-gray-900">
            <div class="absolute -top-32 -left-16 h-96 w-96 rounded-full bg-blue-100/60 blur-3xl dark:bg-blue-500/10"></div>
            <div class="absolute -bottom-32 -right-16 h-80 w-80 rounded-full bg-blue-200/50 blur-3xl dark:bg-blue-500/10"></div>

            <div class="relative">
                @if (filled($heading))
                    <h2 class="text-[clamp(2rem,4vw,3.5rem)] font-bold leading-tight tracking-tight text-gray-950 dark:text-white">
                        {{ $heading }}
                    </h2>
                @endif

                @if (filled($description))
                    <p class="mx-auto mt-5 max-w-xl text-lg text-gray-600 dark:text-gray-400">
                        {{ $description }}
                    </p>
                @endif

                @if (filled($primaryCtaLabel) || filled($secondaryCtaLabel))
                    <div class="mt-8 flex flex-wrap justify-center gap-3">
                        @if (filled($primaryCtaLabel))
                            <x-filament::button tag="a" :href="$primaryCtaUrl" size="xl" color="primary" icon="heroicon-o-arrow-right" icon-position="after" data-testid="cta-register">
                                {{ $primaryCtaLabel }}
                            </x-filament::button>
                        @endif

                        @if (filled($secondaryCtaLabel))
                            <x-filament::button tag="a" :href="$secondaryCtaUrl" size="xl" color="gray" outlined>
                                {{ $secondaryCtaLabel }}
                            </x-filament::button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
