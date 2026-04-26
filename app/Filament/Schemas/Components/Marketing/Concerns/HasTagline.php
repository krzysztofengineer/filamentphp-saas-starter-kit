<?php

namespace App\Filament\Schemas\Components\Marketing\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasTagline
{
    protected string|Htmlable|Closure|null $tagline = null;

    public function tagline(string|Htmlable|Closure|null $tagline): static
    {
        $this->tagline = $tagline;

        return $this;
    }

    public function getTagline(): string|Htmlable|null
    {
        return $this->evaluate($this->tagline);
    }
}
