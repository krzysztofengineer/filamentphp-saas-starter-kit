<?php

namespace App\Filament\Schemas\Components\Marketing;

use App\Filament\Schemas\Components\Marketing\Concerns\HasTagline;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasHeading;

class PricingSection extends Component
{
    use HasDescription;
    use HasHeading;
    use HasTagline;

    protected string $view = 'filament.schemas.marketing.pricing-section';

    /**
     * @var array<int, array<string, mixed>>|Closure
     */
    protected array|Closure $plans = [];

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<int, array<string, mixed>>|Closure  $plans
     */
    public function plans(array|Closure $plans): static
    {
        $this->plans = $plans;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getPlans(): array
    {
        return $this->evaluate($this->plans) ?? [];
    }
}
