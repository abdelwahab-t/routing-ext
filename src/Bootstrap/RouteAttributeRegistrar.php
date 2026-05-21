<?php

namespace AbdelwahabT\RoutingExt\Bootstrap;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Illuminate\Support\Facades\Route;
use AbdelwahabT\RoutingExt\Exceptions\ModuleClassNotFoundException;

final readonly class RouteAttributeRegistrar
{

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function register(string $file): void
    {
        $this->registerControllersRoutes(
            $this->getClass($file)
        );
    }

    /**
     * @throws ModuleClassNotFoundException
     */
    private function getClass(string $file): ReflectionClass
    {

        $class = "Modules\\" . basename(dirname($file, 4)) . "\\App\\Http\\Controllers" .
            '\\' . pathinfo($file, PATHINFO_FILENAME);

        if (!class_exists($class)) {
            require_once $file;
            if (!class_exists($class)) {
                throw new ModuleClassNotFoundException;
            }
        }

        return $class;

    }

    /**
     * @throws ReflectionException
     */
    private function registerControllersRoutes(string $class): void
    {
        foreach ((new ReflectionClass($class))->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes() as $attribute) {
                $this->registerRoute($attribute->newInstance(), $class, $method);
            }
        }
    }

    private function registerRoute(object $instance, string $class, ReflectionMethod $method): void
    {
        $route = Route::name($instance->name);

        if ($instance->prefix) {
            $route->prefix($instance->prefix);
        }

        $route->{$instance->method->value}(
            $instance->uri, [$class, $method->getName()]
        )->name($instance->name);

        if($instance?->middleware){
            $route->middleware([$instance->middleware]);
        }
    }

}