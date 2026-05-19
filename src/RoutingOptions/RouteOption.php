<?php
namespace Abdelwahab\RoutingExt\RoutingManager\Methods;

use Illuminate\Routing\Controllers\Middleware;

class RouteOption
{
    public function __construct(
        public string $prefix,
        public string $uri,
        public string $name,
        public Middleware|string|null
        $middleware = null
    ) {}
}
