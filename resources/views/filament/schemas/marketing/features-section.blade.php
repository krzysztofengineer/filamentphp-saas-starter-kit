@php
    $tagline = $getTagline();
    $heading = $getHeading();
    $description = $getDescription();
    $features = $getFeatures();
@endphp

<section id="features" class="border-y border-gray-200 bg-gray-50 px-6 py-24 sm:px-10 lg:px-16 dark:border-gray-800 dark:bg-gray-900/40" data-testid="landing-features">
    <div class="mx-auto max-w-7xl">
        @if (filled($tagline) || filled($heading) || filled($description))
            <div class="mb-14 max-w-2xl">
                @if (filled($tagline))
                    <div class="mb-4 text-xs font-semibold uppercase tracking-[0.14em] text-gray-500 dark:text-gray-400">{{ $tagline }}</div>
                @endif
                @if (filled($heading))
                    <h2 class="text-[clamp(2rem,3.5vw,3rem)] font-bold leading-tight tracking-tight text-gray-950 dark:text-white">
                        {{ $heading }}
                    </h2>
                @endif
                @if (filled($description))
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as $feature)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                    <div class="mb-4 inline-flex size-11 items-center justify-center rounded-xl bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
                        @svg($feature['icon'], 'size-6')
                    </div>
                    <h3 class="text-lg font-bold tracking-tight text-gray-950 dark:text-white">{{ $feature['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $feature['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
