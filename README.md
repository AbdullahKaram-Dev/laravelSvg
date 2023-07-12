# Laravel SVG

A Laravel package for generating SVG images from user full names or initials with some pretty cool customization
options.

## Requirements

* This package requires PHP 8.0 or higher.

## Installation

You can install the package via Composer by running the following command:

```
composer require your-vendor/laravel-svg
```

## Usage

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
    }
}


you will get the following result here:

[
    'svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100"><text x="50" y="50" text-anchor="middle" dominant-baseline="central" font-family="Arial" font-size="50" fill="#000000">AK</text></svg>',
    'initials' => 'AK',
    'size' => 100,
    'color' => '#000000',
    'font' => 'Arial',
]
```
![Abdullah Karam](https://i.ibb.co/Z69kP7p/img.png)

This will generate an SVG image with the user's initials in the center. You can also customize the size and color of the
image by passing additional parameters to the `generate()` method.

## Customization

You can customize the appearance of the SVG image by modifying the `config/laravel-svg.php` file. Here's an example of
what the file might look like:

```php
return [
    'ize' => 100,
    'color' => '#000000',
    'font' => 'Arial',
];
```

You can modify the `size` parameter to change the size of the generated SVG image. You can also modify the `color`
parameter to change the color of the initials in the image. Finally, you can modify the `font` parameter to change the
font used to display the initials.

## License

This package is open-source software licensed under the MIT license.
