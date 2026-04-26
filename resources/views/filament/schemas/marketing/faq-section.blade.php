@php
    $tagline = $getTagline();
    $heading = $getHeading();
    $items = $getItems();
@endphp

<section id="faq" class="px-6 py-24 sm:px-10 lg:px-16" data-testid="landing-faq">
    <div class="mx-auto max-w-3xl">
        @if (filled($tagline) || filled($heading))
            <div class="mb-12 text-center">
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

        <div class="divide-y divide-gray-200 border-y border-gray-200 dark:divide-gray-800 dark:border-gray-800">
            @foreach ($items as $item)
                <details class="group py-6">
                    <summary class="flex cursor-pointer list-none items-center justify-between text-lg font-semibold text-gray-950 dark:text-white">
                        {{ $item['question'] }}
                        <span class="ml-6 text-2xl text-gray-400 transition group-open:rotate-45 group-open:text-blue-600 dark:text-gray-500 dark:group-open:text-blue-400">+</span>
                    </summary>
                    <p class="mt-4 text-base leading-relaxed text-gray-600 dark:text-gray-400">{{ $item['answer'] }}</p>
                </details>
            @endforeach
        </div>
    </div>
</section>
