<?php

namespace App\Filament\Schemas\Components\Marketing;

use App\Filament\Schemas\Components\Marketing\Concerns\HasEyebrow;
use Closure;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\HasDescription;
use Filament\Schemas\Components\Concerns\HasHeading;
use Illuminate\Contracts\Support\Htmlable;

class HeroSection extends Component
{
    use HasDescription;
    use HasEyebrow;
    use HasHeading;

    protected string $view = 'filament.schemas.marketing.hero-section';

    protected string|Htmlable|Closure|null $headingAccent = null;

    protected string|Htmlable|Closure|null $primaryCtaLabel = null;

    protected string|Closure|null $primaryCtaUrl = null;

    protected string|Htmlable|Closure|null $secondaryCtaLabel = null;

    protected string|Closure|null $secondaryCtaUrl = null;

    protected string|Closure|null $command = null;

    protected string|Htmlable|Closure|null $commandHint = null;

    protected array|Closure $stackPills = [];

    protected bool|Closure $showInception = true;

    protected string|Closure|null $host = null;

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

    public function command(string|Closure|null $command, string|Htmlable|Closure|null $hint = null): static
    {
        $this->command = $command;
        $this->commandHint = $hint;

        return $this;
    }

    public function stackPills(array|Closure $pills): static
    {
        $this->stackPills = $pills;

        return $this;
    }

    public function showInception(bool|Closure $show = true): static
    {
        $this->showInception = $show;

        return $this;
    }

    public function host(string|Closure|null $host): static
    {
        $this->host = $host;

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

    public function getCommandHint(): string|Htmlable|null
    {
        return $this->evaluate($this->commandHint);
    }

    public function getStackPills(): array
    {
        return $this->evaluate($this->stackPills) ?? [];
    }

    public function shouldShowInception(): bool
    {
        return (bool) $this->evaluate($this->showInception);
    }

    public function getHost(): string
    {
        $value = $this->evaluate($this->host);

        if (filled($value)) {
            return (string) $value;
        }

        $url = (string) config('app.url');

        return (string) (parse_url($url, PHP_URL_HOST) ?: str_replace(['https://', 'http://'], '', $url));
    }
}
