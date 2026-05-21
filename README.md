# Laravel Routing Extension (abdelwahab-t/routing-ext)

A lightweight Laravel package that provides PHP 8 Attribute-based routing specifically designed for modular Laravel applications. 

It automatically scans your modules' controllers and registers routes based on `#[RouteOption]` attributes, keeping your routing definitions right next to your controller methods.

## Installation

You can install the package via composer:

```bash
composer require abdelwahab-t/routing-ext
```

The package will automatically register its service provider (`AbdelwahabT\RoutingExt\Providers\RoutingExtServiceProvider`).

## Usage

To use attribute-based routing, simply add the `#[RouteOption]` attribute to the public methods of your module's controllers. 

The package will automatically discover controllers located in `modules/*/App/Http/Controllers/*.php` and register their routes.

### Example

```php
<?php

namespace Modules\Blog\App\Http\Controllers;

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

- **`method`** (`AbdelwahabT\RoutingExt\Enum\HttpMethods`): The HTTP method for the route (`GET`, `POST`, `PUT`, `PATCH`, `DELETE`).
- **`uri`** (`string`): The URI for the route.
- **`name`** (`string`): The route name.
- **`prefix`** (`string`): The route prefix.
- **`middleware`** (`Illuminate\Routing\Controllers\Middleware|string|null`): Optional middleware to apply to the route.

## License

The MIT License (MIT).
