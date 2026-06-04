<?php
namespace AbdelwahabT\RoutingExt\Routing\Attributes;

use Attribute;
use AbdelwahabT\RoutingExt\Enum\HttpMethods;
use Illuminate\Routing\Controllers\Middleware;

#[Attribute(Attribute::TARGET_METHOD)]
class RouteOption
{
    public function __construct(
        public ?HttpMethods $method = null,
        public ?string $uri = null,
        public ?string $name = null,
        public ?string $prefix = null,
        public array|Middleware|string|null $middleware = null,
        public array|string|null $ability = null,
    ) {}

}
