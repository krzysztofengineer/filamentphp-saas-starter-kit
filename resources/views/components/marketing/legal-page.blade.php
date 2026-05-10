@props([
    'eyebrow' => 'legal',
    'heading' => '',
    'lede' => null,
    'updatedAt' => null,
])

<x-filament-panels::page>
    <section class="border-b border-(--border) pt-24 pb-8" data-testid="legal-hero">
            <div class="mx-auto max-w-[1200px] px-7 max-[700px]:px-[18px]">
                @if (filled($eyebrow))
                    <div class="mb-3.5 text-xs lowercase text-(--accent) font-mono">{{ $eyebrow }}</div>
                @endif
                @if (filled($heading))
                    <h1 class="m-0 mb-[18px] max-w-[22ch] text-[clamp(36px,5vw,56px)] leading-[1.05] font-semibold tracking-[-0.035em] text-balance">{{ $heading }}</h1>
                @endif
                @if (filled($lede))
                    <p class="m-0 mb-[18px] max-w-[56ch] text-[18px] text-(--text-2) text-pretty">{{ $lede }}</p>
                @endif
                @if (filled($updatedAt))
                    <div class="text-xs lowercase text-(--text-3) font-mono">last updated · {{ $updatedAt }}</div>
                @endif
            </div>
        </section>

        <section class="pt-16 pb-24">
            <div class="mx-auto max-w-[1200px] px-7 max-[700px]:px-[18px]">
                <article class="max-w-[68ch] leading-[1.7] [&_code]:rounded [&_code]:border [&_code]:border-(--border) [&_code]:bg-(--bg-elev) [&_code]:px-1.5 [&_code]:text-[0.92em] [&_code]:text-(--accent) [&_code]:font-mono [&_h2]:mt-9 [&_h2]:mb-3 [&_h2]:text-[22px] [&_h2]:font-semibold [&_h2]:tracking-[-0.015em] [&_h2:first-child]:mt-0 [&_p]:m-0 [&_p]:mb-4 [&_p]:text-base [&_p]:text-(--text-2)">
                    {{ $slot }}
                </article>

                <div class="mt-12 border-t border-(--border) pt-6">
                    <x-filament::link href="/" icon="heroicon-o-arrow-left" icon-position="before">
                        Back to home
                    </x-filament::link>
                </div>
            </div>
        </section>
</x-filament-panels::page>
