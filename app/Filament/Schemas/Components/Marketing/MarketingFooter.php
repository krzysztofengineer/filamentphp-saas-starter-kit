<?php

namespace App\Filament\Schemas\Components\Marketing;

use Closure;
use Filament\Schemas\Components\Component;
use Illuminate\Contracts\Support\Htmlable;

class MarketingFooter extends Component
{
    protected string $view = 'filament.schemas.marketing.footer';

    protected string|Htmlable|Closure|null $copyright = null;

    /**
     * @var array<int, array{label: string, url: string}>|Closure
     */
    protected array|Closure $links = [];

    public static function make(): static
    {
        $static = app(static::class);
        $static->configure();

        return $static;
    }

    public function copyright(string|Htmlable|Closure|null $copyright): static
    {
        $this->copyright = $copyright;

        return $this;
    }

    /**
     * @param  array<int, array{label: string, url: string}>|Closure  $links
     */
    public function links(array|Closure $links): static
    {
        $this->links = $links;

        return $this;
    }

    public function getCopyright(): string|Htmlable|null
    {
        return $this->evaluate($this->copyright) ?? '© '.date('Y').' '.config('app.name');
    }

    /**
     * @return array<int, array{label: string, url: string}>
     */
    public function getLinks(): array
    {
        return $this->evaluate($this->links) ?? [];
    }
}
