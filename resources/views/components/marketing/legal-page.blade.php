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
        <section class="legal-hero" data-testid="legal-hero">
            <div class="container">
                @if (filled($eyebrow))
                    <div class="eyebrow">{{ $eyebrow }}</div>
                @endif
                @if (filled($heading))
                    <h1 class="legal-h">{{ $heading }}</h1>
                @endif
                @if (filled($lede))
                    <p class="legal-lede">{{ $lede }}</p>
                @endif
                @if (filled($updatedAt))
                    <div class="legal-meta">last updated · {{ $updatedAt }}</div>
                @endif
            </div>
        </section>

        <section class="legal-body">
            <div class="container">
                <article class="legal-prose">
                    {{ $slot }}
                </article>

                <div class="legal-back">
                    <x-filament::link href="/" icon="heroicon-o-arrow-left" icon-position="before">
                        Back to home
                    </x-filament::link>
                </div>
            </div>
        </section>
    </div>
</x-filament-panels::page>
