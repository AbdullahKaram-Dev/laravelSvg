<?php

namespace Abdullah\LaravelSvg\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelSvg extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-svg';
    }
}
