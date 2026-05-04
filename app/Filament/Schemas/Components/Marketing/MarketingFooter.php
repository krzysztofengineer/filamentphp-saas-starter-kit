<?php

namespace App\Filament\Schemas\Components\Marketing;

use Closure;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class MarketingFooter extends Component
{
    protected string $view = 'filament.schemas.marketing.footer';

    protected string|Htmlable|Closure|null $brand = null;

    protected string|Htmlable|Closure|null $tagline = null;

    protected string|Htmlable|Closure|null $copyright = null;

    protected array|Closure $linkColumns = [];

    protected array|Closure $linkList = [];

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function brand(string|Htmlable|Closure|null $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function tagline(string|Htmlable|Closure|null $tagline): static
    {
        $this->tagline = $tagline;

        return $this;
    }

    public function copyright(string|Htmlable|Closure|null $copyright): static
    {
        $this->copyright = $copyright;

        return $this;
    }

    public function linkColumns(array|Closure $linkColumns): static
    {
        $this->linkColumns = $linkColumns;

        return $this;
    }

    public function links(array|Closure $links): static
    {
        $this->linkList = $links;

        return $this;
    }

    public function getBrand(): string|Htmlable|null
    {
        return $this->evaluate($this->brand) ?? config('app.name');
    }

    public function getTagline(): string|Htmlable|null
    {
        return $this->evaluate($this->tagline);
    }

    public function getCopyright(): string|Htmlable|null
    {
        return $this->evaluate($this->copyright) ?? '© '.date('Y').' '.config('app.name');
    }

    public function getLinkColumns(): array
    {
        return $this->evaluate($this->linkColumns) ?? [];
    }

    public function getLinks(): array
    {
        return $this->evaluate($this->linkList) ?? [];
    }
}
