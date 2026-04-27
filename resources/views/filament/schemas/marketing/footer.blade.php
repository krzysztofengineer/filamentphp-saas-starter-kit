@php
    $brand = $getBrand();
    $tagline = $getTagline();
    $copyright = $getCopyright();
    $columns = $getLinkColumns();
    $links = $getLinks();
@endphp

<footer class="landing-footer" data-testid="landing-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="#" class="wordmark">
                    <x-marketing.wordmark />
                </a>
                @if (filled($tagline))
                    <p>{{ $tagline }}</p>
                @endif
            </div>

            @foreach ($columns as $column)
                <div class="footer-col">
                    <h5>{{ $column['heading'] ?? '' }}</h5>
                    <ul>
                        @foreach (($column['links'] ?? []) as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            @if (empty($columns) && ! empty($links))
                <div class="footer-col">
                    <h5>Legal</h5>
                    <ul>
                        @foreach ($links as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="footer-bottom">
            <span>{{ $copyright }}</span>
            <x-filament::icon-button
                icon="heroicon-o-moon"
                color="gray"
                label="Switch to light mode"
                x-show="theme === 'dark'"
                x-cloak
                @click="toggle()"
            />
            <x-filament::icon-button
                icon="heroicon-o-sun"
                color="gray"
                label="Switch to dark mode"
                x-show="theme === 'light'"
                x-cloak
                @click="toggle()"
            />
        </div>
    </div>
</footer>
