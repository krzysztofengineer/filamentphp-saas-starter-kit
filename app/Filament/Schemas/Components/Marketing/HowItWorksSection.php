<?php

namespace App\Filament\Schemas\Components\Marketing;

use App\Filament\Schemas\Components\Marketing\Concerns\HasTagline;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasHeading;

class HowItWorksSection extends Component
{
    use HasHeading;
    use HasTagline;

    protected string $view = 'filament.schemas.marketing.how-it-works-section';

    /**
     * @var array<int, array{icon: string, title: string, description: string}>|Closure
     */
    protected array|Closure $steps = [];

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<int, array{icon: string, title: string, description: string}>|Closure  $steps
     */
    public function steps(array|Closure $steps): static
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * @return array<int, array{icon: string, title: string, description: string}>
     */
    public function getSteps(): array
    {
        return $this->evaluate($this->steps) ?? [];
    }
}
