<?php

namespace App\Filament\Schemas\Components\Marketing;

use App\Filament\Schemas\Components\Marketing\Concerns\HasEyebrow;
use App\Filament\Schemas\Components\Marketing\Concerns\HasTagline;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasHeading;

class HowItWorksSection extends Component
{
    use HasDescription;
    use HasEyebrow;
    use HasHeading;
    use HasTagline;

    protected string $view = 'filament.schemas.marketing.how-it-works-section';

    protected array|Closure $steps = [];

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function steps(array|Closure $steps): static
    {
        $this->steps = $steps;

        return $this;
    }

    public function getSteps(): array
    {
        return $this->evaluate($this->steps) ?? [];
    }
}
