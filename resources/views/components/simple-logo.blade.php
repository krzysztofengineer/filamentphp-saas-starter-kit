@php
    $homeUrl = filament()->getHomeUrl();
@endphp

<div class="absolute start-0 top-0 flex h-16 items-center ps-4 md:ps-6 lg:ps-8">
    @if ($homeUrl)
        <a {{ \Filament\Support\generate_href_html($homeUrl) }}>
            <x-filament-panels::logo />
        </a>
    @else
        <x-filament-panels::logo />
    @endif
</div>
