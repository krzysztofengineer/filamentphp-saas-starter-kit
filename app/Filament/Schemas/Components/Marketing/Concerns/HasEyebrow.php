<?php

namespace App\Filament\Schemas\Components\Marketing\Concerns;

use Closure;
use Illuminate\Contracts\Support\Htmlable;

trait HasEyebrow
{
    protected string|Htmlable|Closure|null $eyebrow = null;

    public function eyebrow(string|Htmlable|Closure|null $eyebrow): static
    {
        $this->eyebrow = $eyebrow;

        return $this;
    }

    public function getEyebrow(): string|Htmlable|null
    {
        return $this->evaluate($this->eyebrow);
    }
}
