<?php

namespace Abdullah\LaravelSvg\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Abdullah\LaravelSvg\Services\LaravelSvg svgFor(string $userFullName)
 * @method static \Abdullah\LaravelSvg\Services\LaravelSvg logoText(string $logoText = null)
 */
class LaravelSvg extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-svg';
    }
}
