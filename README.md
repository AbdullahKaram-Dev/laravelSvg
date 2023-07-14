![](https://img.shields.io/github/issues/AbdullahKaram-Dev/laravelSvg)
![](https://img.shields.io/github/stars/AbdullahKaram-Dev/laravelSvg)
![](https://img.shields.io/github/forks/AbdullahKaram-Dev/laravelSvg)
![](https://img.shields.io/github/license/AbdullahKaram-Dev/laravelSvg)
[![Total Downloads](https://img.shields.io/packagist/dt/abdullah-karam/laravel-svg.svg?style=flat-square)](https://packagist.org/packages/abdullah-karam/laravel-svg)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/abdullah-karam/laravel-svg.svg?style=flat-square)](https://packagist.org/packages/abdullah-karam/laravel-svg)


# Laravel SVG

A Laravel package for generating SVG images from user full names or initials with some pretty cool customization
options.

## Requirements

* This package requires PHP 8.0 or higher.

## Installation

You can install the package via Composer by running the following command:

```
composer require abdullah-karam/laravel-svg
```

## Usage

# example (1)

To generate an SVG image from a user's full name, you can use the `svgFor()` method on the `LaravelSvg` facade.
The `svgFor()` method accepts a single parameter, which is the user's full name. Here's an example of how you might use
the `svgFor()` method:

```php
<?php

namespace App\Http\Controllers;

use Abdullah\LaravelSvg\Facades\LaravelSvg;
use App\Models\User;

class UserController extends Controller
{
    public function generateSvg(User $user)
    {
        $svgDetails = LaravelSvg::svgFor(userFullName: $user->fullname)->generate(); 
        dd($svgDetails);
    }
}

```

This will generate an SVG image with the user's initials in the center.

![Abdullah Karam](https://i.ibb.co/Jvj2YZs/3a18c24e-894a-4dcf-9938-801fe9598529.jpg)

```php
array:6 [▼
  "name" => "64af002c8ba51.svg"
  "path" => "avatars/64af002c8ba51.svg"
  "full_path" => "http://localhost:8000/storage/avatars/64af002c8ba51.svg"
  "mime_type" => "image/svg+xml"
  "size" => 422
  "disk" => "public"
]
```


# example (2)

You can also generate an SVG image from a user's initials with logo text by using the `svgFor()` method on the
`LaravelSvg` facade. The `svgFor()` method accepts a single parameter, which is the user's initials and chain with method `logoText()`. Here's an example of how you might use

```php
<?php

namespace App\Http\Controllers;

use Abdullah\LaravelSvg\Facades\LaravelSvg;
use App\Models\User;

class UserController extends Controller
{
    public function generateSvg(User $user)
    {
        $svgDetails = LaravelSvg::svgFor(userFullName: $user->fullname)
                                  ->logoText()->generate(); 
        dd($svgDetails);
    }
}

```

This will generate an SVG image with the user's initials in the center and logo text.
with default logo text. if you want to change logo text you can use `logoText()` method with parameter.
like this `logoText('logo')` or define it once in `config/laravel-svg.php` file.

![Abdullah Karam](https://i.ibb.co/3mr8Pd9/ee1590e3-eb5e-40de-ad0e-9657e68cb251.jpg)

```php
array:6 [▼ 
  "name" => "64af03da5f35c.svg"
  "path" => "avatars/64af03da5f35c.svg"
  "full_path" => "http://localhost:8000/storage/avatars/64af03da5f35c.svg"
  "mime_type" => "image/svg+xml"
  "size" => 414
  "disk" => "public"
]
```

## Configuration

You can publish the configuration file using this command:

```
php artisan vendor:publish --provider="Abdullah\LaravelSvg\LaravelSvgServiceProvider" --tag="config"
```

This is the contents of the published config file:

```php
<?php

return [
    'disk' => 'public', ## storage disk name
    'default_logo_text' => 'logo', ## default logo text
    'logo_text_color' => '#000000',
    'avatar_text_color' => '#f1c40f',
    'avatar_background_color' => '#ffffff',
    'default_svg_path' => 'avatars', ## folder name will be created in storage/app/public
    'hash_svg_name' => true ## if you want to hash svg name by default true
];
```

you can change all configuration options as you want.

## License

This package is open-source software licensed under the MIT license (MIT). Please see [License File](LICENSE.md) for
more information.

## Contact

If you have any questions or feedback, feel free to contact me via e-mail at <abdallakaramdev@gmail.com>