<?php

namespace App\Filament\Support;

use Illuminate\Support\HtmlString;

class CategoryHeading
{
    public static function make(string $icon, string $color, string $text): HtmlString
    {
        $iconSvg = svg($icon, 'fi-icon fi-color fi-color-'.$color, [
            'style' => 'width:1.5rem;height:1.5rem;',
        ])->toHtml();

        return new HtmlString(
            '<span style="display:inline-flex;align-items:center;gap:0.75rem;">'
            .'<span class="fi-home-grid-item-icon-bg fi-color fi-color-'.$color.'">'.$iconSvg.'</span>'
            .'<span>'.e($text).'</span>'
            .'</span>'
        );
    }
}
