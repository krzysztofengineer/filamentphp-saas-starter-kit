@props([
    'icon',
    'iconColor' => 'primary',
    'heading',
    'description' => null,
    'actions' => [],
])

@php
    $visibleActions = collect($actions)->filter(fn ($action) => $action->isVisible())->all();
    $hasDescription = filled($description);
@endphp

<div class="fi-ta-header fi-ta-header-adaptive-actions-position">
    <section class="fi-section fi-section-not-contained fi-section-has-header">
        <header class="fi-section-header">
            {{
                \Filament\Support\generate_icon_html(
                    $icon,
                    attributes: (new \Illuminate\View\ComponentAttributeBag)
                        ->color(\Filament\Support\View\Components\SectionComponent\IconComponent::class, $iconColor),
                    size: \Filament\Support\Enums\IconSize::Large,
                )
            }}

            <div class="fi-section-header-text-ctn">
                <h3 class="fi-section-header-heading">{{ $heading }}</h3>

                @if ($hasDescription)
                    <p class="fi-section-header-description">{{ $description }}</p>
                @endif
            </div>
        </header>
    </section>

    @if (filled($visibleActions))
        <div class="fi-ta-actions fi-align-start fi-wrapped">
            @foreach ($visibleActions as $action)
                {{ $action }}
            @endforeach
        </div>
    @endif
</div>
