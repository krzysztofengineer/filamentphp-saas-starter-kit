@php
    $tagline = $getTagline();
    $heading = $getHeading();
    $description = $getDescription();
    $plans = $getPlans();
@endphp

<section id="pricing" class="border-t border-gray-200 bg-gray-50 px-6 py-24 sm:px-10 lg:px-16 dark:border-gray-800 dark:bg-gray-900/40" data-testid="landing-pricing">
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
                    <p class="mt-4 text-lg leading-relaxed text-gray-600 dark:text-gray-400">{{ $description }}</p>
                @endif
            </div>
        @endif

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($plans as $plan)
                <div @class([
                    'relative flex flex-col rounded-3xl p-6 sm:p-8',
                    'bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-800' => ! ($plan['featured'] ?? false),
                    'bg-white ring-2 ring-blue-500 shadow-xl shadow-blue-500/10 dark:bg-gray-900 dark:shadow-blue-500/20' => ($plan['featured'] ?? false),
                ])>
                    @if (filled($plan['badge'] ?? null))
                        <span class="absolute -top-3 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-blue-600 px-3 py-1 text-[0.6875rem] font-semibold uppercase tracking-wider text-white">
                            {{ $plan['badge'] }}
                        </span>
                    @endif

                    <h3 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">{{ $plan['name'] }}</h3>
                    <p class="mt-2 min-h-[3rem] text-sm leading-relaxed text-gray-600 dark:text-gray-400">{{ $plan['description'] ?? '' }}</p>

                    <div class="mt-6">
                        <div class="flex items-baseline gap-2">
                            <span class="text-4xl font-bold tracking-tight text-gray-950 sm:text-5xl dark:text-white">{{ $plan['price'] }}</span>
                        </div>
                        <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $plan['period'] ?? '' }}</div>
                    </div>

                    <div class="mt-6">
                        <x-filament::button
                            tag="a"
                            :href="$plan['href']"
                            size="lg"
                            :color="($plan['featured'] ?? false) ? 'primary' : 'gray'"
                            :outlined="! ($plan['featured'] ?? false)"
                            class="w-full justify-center"
                        >
                            {{ $plan['cta'] }}
                        </x-filament::button>
                    </div>

                    <div class="my-6 h-px bg-gray-200 dark:bg-gray-800"></div>

                    <ul class="flex flex-col gap-3">
                        @foreach ($plan['features'] ?? [] as $feature)
                            <li class="flex gap-2.5 text-sm text-gray-700 dark:text-gray-300">
                                @svg('heroicon-o-check', 'mt-0.5 size-4 shrink-0 text-blue-600 dark:text-blue-400')
                                <span>{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</section>
