@php
    $tagline = $getTagline();
    $heading = $getHeading();
    $steps = $getSteps();
@endphp

<section id="how-it-works" class="px-6 py-24 sm:px-10 lg:px-16" data-testid="landing-how-it-works">
    <div class="mx-auto max-w-7xl">
        @if (filled($tagline) || filled($heading))
            <div class="mb-14 max-w-2xl">
                @if (filled($tagline))
                    <div class="mb-4 text-xs font-semibold uppercase tracking-[0.14em] text-gray-500 dark:text-gray-400">{{ $tagline }}</div>
                @endif
                @if (filled($heading))
                    <h2 class="text-[clamp(2rem,3.5vw,3rem)] font-bold leading-tight tracking-tight text-gray-950 dark:text-white">
                        {{ $heading }}
                    </h2>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 gap-12 md:grid-cols-3">
            @foreach ($steps as $step)
                <div class="flex flex-col items-center text-center">
                    <div class="flex size-14 items-center justify-center rounded-full bg-blue-600 text-white shadow-lg shadow-blue-600/30">
                        @svg($step['icon'], 'size-6')
                    </div>
                    <h3 class="mt-6 text-xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $step['title'] }}</h3>
                    <p class="mt-3 max-w-xs text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $step['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
