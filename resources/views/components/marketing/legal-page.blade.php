@props([
    'eyebrow' => 'legal',
    'heading' => '',
    'lede' => null,
    'updatedAt' => null,
])

<x-filament-panels::page>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <div class="landing-root">
        <section class="border-b border-[var(--border)] pt-24 pb-8" data-testid="legal-hero">
            <div class="container">
                @if (filled($eyebrow))
                    <div class="mb-3.5 text-xs lowercase text-[var(--accent)] [font-family:var(--mono)]">{{ $eyebrow }}</div>
                @endif
                @if (filled($heading))
                    <h1 class="m-0 mb-[18px] max-w-[22ch] text-[clamp(36px,5vw,56px)] leading-[1.05] font-semibold tracking-[-0.035em] text-balance">{{ $heading }}</h1>
                @endif
                @if (filled($lede))
                    <p class="m-0 mb-[18px] max-w-[56ch] text-[18px] text-[var(--text-2)] text-pretty">{{ $lede }}</p>
                @endif
                @if (filled($updatedAt))
                    <div class="text-xs lowercase text-[var(--text-3)] [font-family:var(--mono)]">last updated · {{ $updatedAt }}</div>
                @endif
            </div>
        </section>

        <section class="pt-16 pb-24">
            <div class="container">
                <article class="max-w-[68ch] leading-[1.7] [&_code]:rounded [&_code]:border [&_code]:border-[var(--border)] [&_code]:bg-[var(--bg-elev)] [&_code]:px-1.5 [&_code]:text-[0.92em] [&_code]:text-[var(--accent)] [&_code]:[font-family:var(--mono)] [&_h2]:mt-9 [&_h2]:mb-3 [&_h2]:text-[22px] [&_h2]:font-semibold [&_h2]:tracking-[-0.015em] [&_h2:first-child]:mt-0 [&_p]:m-0 [&_p]:mb-4 [&_p]:text-base [&_p]:text-[var(--text-2)]">
                    {{ $slot }}
                </article>

                <div class="mt-12 border-t border-[var(--border)] pt-6">
                    <x-filament::link href="/" icon="heroicon-o-arrow-left" icon-position="before">
                        Back to home
                    </x-filament::link>
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
