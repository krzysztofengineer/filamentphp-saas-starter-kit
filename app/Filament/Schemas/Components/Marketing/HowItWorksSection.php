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

    /**
     * Steps. Each step has:
     *   - kicker: small label next to the number (e.g. "scaffold")
     *   - title: step title (HTML allowed)
     *   - description: paragraph (HTML allowed)
     *   - command: shell command rendered in the step's code block
     *
     * @var array<int, array{kicker?: string, title?: string, description?: string, command?: string}>|Closure
     */
    protected array|Closure $steps = [];

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    /**
     * @param  array<int, array<string, mixed>>|Closure  $steps
     */
    public function steps(array|Closure $steps): static
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getSteps(): array
    {
        return $this->evaluate($this->steps) ?? [];
    }
}
