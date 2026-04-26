@php
    $copyright = $getCopyright();
    $links = $getLinks();
@endphp

<footer class="border-t border-gray-200 px-6 py-10 sm:px-10 lg:px-16 dark:border-gray-800" data-testid="landing-footer">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 text-xs text-gray-500 dark:text-gray-400">
        <div>{{ $copyright }}</div>
        @if (filled($links))
            <div class="flex gap-6">
                @foreach ($links as $link)
                    <a href="{{ $link['url'] }}" class="hover:text-gray-900 dark:hover:text-white">{{ $link['label'] }}</a>
                @endforeach
            </div>
        @endif
    </div>
</footer>
