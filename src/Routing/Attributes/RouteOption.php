<?php
namespace AbdelwahabT\RoutingExt\Routing\Attributes;

use Attribute;
use AbdelwahabT\RoutingExt\Enum\HttpMethods;
use Illuminate\Routing\Controllers\Middleware;

#[Attribute(Attribute::TARGET_METHOD|Attribute::TARGET_CLASS)]
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

    public static function post(): self
    {
        return new self(HttpMethods::POST);
    }

    public static function get(): self
    {
        return new self(HttpMethods::GET);
    }

    public static function put(): self
    {
        return new self(HttpMethods::PUT);
    }

    public static function patch(): self
    {
        return new self(HttpMethods::PATCH);
    }

    public static function delete(): self
    {
        return new self(HttpMethods::DELETE);
    }

    public static function resource(string $name, string $uri): self
    {
        return new self(null, $uri, $name);
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function uri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;
        return $this;
    }

    public function middleware(array|Middleware|string|null $middleware): self
    {
        $this->middleware = $middleware;
        return $this;
    }

    public function ability(array|string|null $ability): self
    {
        $this->ability = $ability;
        return $this;
    }

}
