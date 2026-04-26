@php
    use Filament\Actions\Action;
    use Filament\Actions\ActionGroup;
    use Filament\Support\Enums\IconSize;
    use Filament\Support\View\Components\SectionComponent\IconComponent;
@endphp

@php
    $heading = $getHeading();
    $url = $getUrl();
    $shouldOpenUrlInNewTab = $shouldOpenUrlInNewTab();
    $icon = $getIcon();
    $iconColor = $getIconColor() ?? 'primary';
    $iconSize = $getIconSize();

    $contentSchema = $getChildSchema($schemaComponent::CONTENT_SCHEMA_KEY);
    $contentComponents = $contentSchema?->getComponents() ?? [];
    $contentActions = array_values(array_filter(
        $contentComponents,
        fn ($component) => $component instanceof Action || $component instanceof ActionGroup,
    ));
    foreach ($contentActions as $contentAction) {
        if ($contentAction instanceof Action && $contentAction->getView() === Action::GROUPED_VIEW) {
            $contentAction->button();
        }
    }
    $contentHasOnlyActions = filled($contentComponents) && count($contentActions) === count($contentComponents);
    $contentHtml = $contentHasOnlyActions ? null : $contentSchema?->toHtmlString();
@endphp

<div
    {{
        $attributes
            ->merge($getExtraAttributes(), escape: false)
            ->class([
                'fi-home-grid-item relative flex flex-col gap-4 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10',
            ])
    }}
>
    <div class="flex items-start justify-between gap-2">
        @if (filled($icon))
            <div
                @class([
                    'fi-home-grid-item-icon-bg',
                    'fi-color '.('fi-color-'.$iconColor) => $iconColor !== 'gray',
                ])
            >
                {{
                    \Filament\Support\generate_icon_html($icon, attributes: (new \Illuminate\View\ComponentAttributeBag)
                        ->color(IconComponent::class, $iconColor), size: $iconSize ?? IconSize::Large)
                }}
            </div>
        @else
            <span></span>
        @endif

        <svg
            class="h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
            aria-hidden="true"
        >
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
        </svg>
    </div>

    <h3 class="text-lg font-bold leading-tight text-gray-950 dark:text-white">
        @if ($url)
            <a
                href="{{ $url }}"
                @if ($shouldOpenUrlInNewTab) target="_blank" rel="noopener noreferrer" @endif
                class="after:absolute after:inset-0 after:content-['']"
            >
                {{ $heading }}
            </a>
        @else
            {{ $heading }}
        @endif
    </h3>

    @if ($contentHasOnlyActions)
        <x-filament::actions
            :actions="$contentActions"
            :full-width="true"
            class="fi-home-grid-item-content mt-auto"
        />
    @elseif ($contentHtml)
        <div class="fi-home-grid-item-content">
            {!! $contentHtml !!}
        </div>
    @endif
</div>
