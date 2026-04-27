<?php

namespace App\Filament\Schemas\Components\Marketing;

use App\Filament\Schemas\Components\Marketing\Concerns\HasEyebrow;
use App\Filament\Schemas\Components\Marketing\Concerns\HasTagline;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasHeading;

class FeaturesSection extends Component
{
    use HasDescription;
    use HasEyebrow;
    use HasHeading;
    use HasTagline;

    protected string $view = 'filament.schemas.marketing.features-section';

    /**
     * Bento grid cards. Each card has:
     *   - title: card title
     *   - body: card paragraph (HTML allowed via {!! !!})
     *   - visual: one of 'org-chart', 'billing', 'filament', 'pwa', 'realtime',
     *             'terminal', 'ai', 'strip' — picks the visual partial
     *   - span: 'b-1' through 'b-7' or 'b-strip' — controls grid placement
     *   - data: optional array passed to the visual partial for content overrides
     *
     * @var array<int, array{title?: string, body?: string, visual?: string, span?: string, data?: array<string, mixed>}>|Closure
     */
    protected array|Closure $cards = [];

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<int, array<string, mixed>>|Closure  $cards
     */
    public function cards(array|Closure $cards): static
    {
        $this->cards = $cards;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCards(): array
    {
        return $this->evaluate($this->cards) ?? [];
    }
}
