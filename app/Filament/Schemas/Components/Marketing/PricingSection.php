<?php

namespace App\Filament\Schemas\Components\Marketing;

use App\Filament\Schemas\Components\Marketing\Concerns\HasEyebrow;
use App\Filament\Schemas\Components\Marketing\Concerns\HasTagline;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasHeading;
use Illuminate\Contracts\Support\Htmlable;

class PricingSection extends Component
{
    use HasDescription;
    use HasEyebrow;
    use HasHeading;
    use HasTagline;

    protected string $view = 'filament.schemas.marketing.pricing-section';

    protected string|Htmlable|Closure|null $headline = null;

    protected string|Htmlable|Closure|null $footnote = null;

    /**
     * Pricing plans. Each plan supports:
     *   - key: stable identifier
     *   - name: plan name
     *   - badge: optional badge label
     *   - description: short tagline (HTML allowed)
     *   - features: list of strings (HTML allowed)
     *   - prices.monthly|yearly: { label, period, savings?, checkout_url?, is_current? }
     *   - is_free: bool — render the free / no-checkout CTA variant
     *   - highlighted: bool — apply the accent border + glow
     *   - cta_label, cta_url: override the default Sign-up CTA for the free tier
     *
     * @var array<int, array<string, mixed>>|Closure
     */
    protected array|Closure $plans = [];

    protected string|Closure $defaultInterval = 'monthly';

    protected bool|Closure $showIntervalToggle = true;

    protected string|Htmlable|Closure|null $freeCtaLabel = null;

    protected string|Closure|null $freeCtaUrl = null;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function headline(string|Htmlable|Closure|null $headline): static
    {
        $this->headline = $headline;

        return $this;
    }

    public function footnote(string|Htmlable|Closure|null $footnote): static
    {
        $this->footnote = $footnote;

        return $this;
    }

    /**
     * @param  array<int, array<string, mixed>>|Closure  $plans
     */
    public function plans(array|Closure $plans): static
    {
        $this->plans = $plans;

        return $this;
    }

    public function defaultInterval(string|Closure $interval): static
    {
        $this->defaultInterval = $interval;

        return $this;
    }

    public function showIntervalToggle(bool|Closure $show = true): static
    {
        $this->showIntervalToggle = $show;

        return $this;
    }

    public function freeCta(string|Htmlable|Closure|null $label, string|Closure|null $url): static
    {
        $this->freeCtaLabel = $label;
        $this->freeCtaUrl = $url;

        return $this;
    }

    public function getHeadline(): string|Htmlable|null
    {
        return $this->evaluate($this->headline);
    }

    public function getFootnote(): string|Htmlable|null
    {
        return $this->evaluate($this->footnote);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getPlans(): array
    {
        return $this->evaluate($this->plans) ?? [];
    }

    public function getDefaultInterval(): string
    {
        return (string) $this->evaluate($this->defaultInterval);
    }

    public function shouldShowIntervalToggle(): bool
    {
        return (bool) $this->evaluate($this->showIntervalToggle);
    }

    public function getFreeCtaLabel(): string|Htmlable|null
    {
        return $this->evaluate($this->freeCtaLabel);
    }

    public function getFreeCtaUrl(): ?string
    {
        return $this->evaluate($this->freeCtaUrl);
    }
}
