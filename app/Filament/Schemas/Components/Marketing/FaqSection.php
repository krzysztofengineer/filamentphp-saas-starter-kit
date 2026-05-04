<?php

namespace App\Filament\Schemas\Components\Marketing;

use App\Filament\Schemas\Components\Marketing\Concerns\HasEyebrow;
use App\Filament\Schemas\Components\Marketing\Concerns\HasTagline;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasHeading;

class FaqSection extends Component
{
    use HasEyebrow;
    use HasHeading;
    use HasTagline;

    protected string $view = 'filament.schemas.marketing.faq-section';

    protected array|Closure $items = [];

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function items(array|Closure $items): static
    {
        $this->items = $items;

        return $this;
    }

    public function getItems(): array
    {
        return $this->evaluate($this->items) ?? [];
    }
}
