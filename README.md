# Laravel Routing Extension (abdelwahab-t/routing-ext)

A lightweight Laravel package that provides PHP 8 Attribute-based routing specifically designed for modular Laravel applications. 

It automatically scans your controllers and registers routes based on `#[RouteOption]` attributes, keeping your routing definitions right next to your controller methods.

## Installation

You can install the package via composer:

```bash
composer require abdelwahab-t/routing-ext
```

The package will automatically register its service provider (`AbdelwahabT\RoutingExt\Providers\RoutingExtServiceProvider`).

### Publish Configuration

To publish the configuration file, run:

```bash
php artisan vendor:publish --tag=routing-ext-config
```

This will create a `config/routing-ext.php` file in your application where you can customize the controller directories.

## Configuration

The published config file allows you to define which directories should be scanned for controllers with route attributes:

```php
// config/routing-ext.php

return [
    'controllers_directories' => [
        'app/Http/Controllers',
        'app/Modules/*/App/Http/Controllers',
    ],
];
```

Paths are relative to the application base path. You may use wildcard (`*`) patterns for dynamic directory segments (e.g., module names).

## Usage

To use attribute-based routing, simply add the `#[RouteOption]` attribute to the public methods of your controllers. 

The package will automatically discover controllers located in the configured directories and register their routes.

### Example

```php
<?php

namespace App\Modules\Blog\App\Http\Controllers;

use AbdelwahabT\RoutingExt\Routing\Attributes\RouteOption;
use AbdelwahabT\RoutingExt\Enum\HttpMethods;

class PostController
{
    #[RouteOption(
        method: HttpMethods::GET,
        uri: '/posts',
        name: 'posts.index',
        prefix: 'blog',
        middleware: 'auth'
    )]
    public function index()
    {
        // Route will be registered as: 
        // GET /blog/posts with name "posts.index" and middleware "auth"
        return view('blog::index');
    }
}
```

### RouteOption Parameters

The `#[RouteOption]` attribute accepts the following parameters:

| Parameter      | Type                                              | Required | Description                          |
|----------------|---------------------------------------------------|----------|--------------------------------------|
| `method`       | `AbdelwahabT\RoutingExt\Enum\HttpMethods`         | Yes      | HTTP method (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`) |
| `uri`          | `string`                                          | Yes      | The URI for the route                |
| `name`         | `string`                                          | Yes      | The route name                       |
| `prefix`       | `string`                                          | Yes      | The route prefix                     |
| `middleware`   | `Illuminate\Routing\Controllers\Middleware\|string\|null` | No       | Middleware to apply to the route     |

## License

The MIT License (MIT).
