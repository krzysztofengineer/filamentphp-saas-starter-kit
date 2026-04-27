<?php

namespace App\Filament\Schemas\Components\Marketing;

use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasHeading;
use Illuminate\Contracts\Support\Htmlable;

class FinalCtaSection extends Component
{
    use HasDescription;
    use HasHeading;

    protected string $view = 'filament.schemas.marketing.final-cta-section';

    protected string|Htmlable|Closure|null $headingAccent = null;

    protected string|Htmlable|Closure|null $primaryCtaLabel = null;

    protected string|Closure|null $primaryCtaUrl = null;

    protected string|Htmlable|Closure|null $secondaryCtaLabel = null;

    protected string|Closure|null $secondaryCtaUrl = null;

    protected string|Closure|null $command = null;

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function headingAccent(string|Htmlable|Closure|null $headingAccent): static
    {
        $this->headingAccent = $headingAccent;

        return $this;
    }

    public function primaryCta(string|Htmlable|Closure|null $label, string|Closure|null $url): static
    {
        $this->primaryCtaLabel = $label;
        $this->primaryCtaUrl = $url;

        return $this;
    }

    public function secondaryCta(string|Htmlable|Closure|null $label, string|Closure|null $url): static
    {
        $this->secondaryCtaLabel = $label;
        $this->secondaryCtaUrl = $url;

        return $this;
    }

    public function command(string|Closure|null $command): static
    {
        $this->command = $command;

        return $this;
    }

    public function getHeadingAccent(): string|Htmlable|null
    {
        return $this->evaluate($this->headingAccent);
    }

    public function getPrimaryCtaLabel(): string|Htmlable|null
    {
        return $this->evaluate($this->primaryCtaLabel);
    }

    public function getPrimaryCtaUrl(): ?string
    {
        return $this->evaluate($this->primaryCtaUrl);
    }

    public function getSecondaryCtaLabel(): string|Htmlable|null
    {
        return $this->evaluate($this->secondaryCtaLabel);
    }

    public function getSecondaryCtaUrl(): ?string
    {
        return $this->evaluate($this->secondaryCtaUrl);
    }

    public function getCommand(): ?string
    {
        return $this->evaluate($this->command);
    }
}
