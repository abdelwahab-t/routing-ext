<?php

namespace AbdelwahabT\RoutingExt\Routing\Attributes;

use Attribute;
use Illuminate\Routing\Controllers\Middleware;

#[Attribute(Attribute::TARGET_CLASS)]
class ResourceRoute
{
    public function __construct(
        public ?string $name = null,
        public ?string $prefix = null,
        public Middleware|array|string|null $middleware = null,
        public array|string|null $ability = null
    ){}
}