@php
    $brand = $getBrand();
    $tagline = $getTagline();
    $copyright = $getCopyright();
    $columns = $getLinkColumns();
    $links = $getLinks();
@endphp

<footer class="border-t border-(--border) pt-14 pb-9" data-testid="landing-footer">
    <div class="mx-auto max-w-[1200px] px-7 max-[700px]:px-[18px]">
        <div class="mb-10 grid grid-cols-2 gap-7 md:grid-cols-[1.4fr_1fr_1fr_1fr] md:gap-10">
            <div>
                <a href="#" class="inline-flex items-center gap-[9px] text-[15px] font-semibold tracking-[-0.01em]">
                    <x-marketing.wordmark />
                </a>
                @if (filled($tagline))
                    <p class="mt-3.5 max-w-[30ch] text-[13px] text-(--text-3)">{{ $tagline }}</p>
                @endif
            </div>

            @foreach ($columns as $column)
                <div>
                    <h5 class="mb-3.5 text-[11px] font-medium uppercase tracking-[0.06em] text-(--text-3) font-mono">{{ $column['heading'] ?? '' }}</h5>
                    <ul class="m-0 grid list-none gap-2.5 p-0">
                        @foreach (($column['links'] ?? []) as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="text-sm text-(--text-2) hover:text-(--lp-text)">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            @if (empty($columns) && ! empty($links))
                <div>
                    <h5 class="mb-3.5 text-[11px] font-medium uppercase tracking-[0.06em] text-(--text-3) font-mono">Legal</h5>
                    <ul class="m-0 grid list-none gap-2.5 p-0">
                        @foreach ($links as $link)
                            <li><a href="{{ $link['url'] ?? '#' }}" class="text-sm text-(--text-2) hover:text-(--lp-text)">{{ $link['label'] ?? '' }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3.5 border-t border-(--border) pt-7 text-xs text-(--text-3) font-mono">
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
