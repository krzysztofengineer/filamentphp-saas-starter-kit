<?php

namespace App\Filament\Schemas\Components;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Concerns\CanOpenUrl;
use Filament\Schemas\Components\Concerns\HasHeading;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\HasIcon;
use Filament\Support\Concerns\HasIconColor;
use Filament\Support\Concerns\HasIconSize;
use Illuminate\Contracts\Support\Htmlable;

class HomeGridItem extends Component
{
    use CanOpenUrl;
    use HasHeading;
    use HasIcon;
    use HasIconColor;
    use HasIconSize;

    const CONTENT_SCHEMA_KEY = 'content';

    protected string $view = 'filament.schemas.components.home-grid-item';

    final public function __construct(string|Htmlable|Closure|null $heading = null)
    {
        $this->heading($heading);
    }

    public static function make(string|Htmlable|Closure|null $heading = null): static
    {
        $static = app(static::class, ['heading' => $heading]);
        $static->configure();

        return $static;
    }

    public function content(array|Schema|Component|Action|ActionGroup|string|Htmlable|Closure|null $components): static
    {
        $this->childComponents($components, static::CONTENT_SCHEMA_KEY);

        return $this;
    }

    protected function configureChildSchema(Schema $schema, string $key): Schema
    {
        $schema = parent::configureChildSchema($schema, $key);

        if ($key === static::CONTENT_SCHEMA_KEY) {
            $schema
                ->inline()
                ->embeddedInParentComponent();
        }

        return $schema;
    }
}
