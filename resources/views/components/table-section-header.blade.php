@props([
    'icon',
    'iconColor' => 'primary',
    'heading',
    'description' => null,
    'actions' => [],
])

@php
    $visibleActions = collect($actions)->filter(fn ($action) => $action->isVisible())->all();
@endphp

<div class="fi-ta-header fi-ta-header-adaptive-actions-position">
    <x-filament::section
        :heading="$heading"
        :description="$description"
        :icon="$icon"
        :icon-color="$iconColor"
        :contained="false"
    />

    @if (filled($visibleActions))
        <div class="fi-ta-actions fi-align-start fi-wrapped">
            @foreach ($visibleActions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</div>
