<?php

namespace Abdullah\LaravelSvg\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Abdullah\LaravelSvg\Services\LaravelSvg svgFor(string $userFullName)
 */
class LaravelSvg extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-svg';
    }
}
