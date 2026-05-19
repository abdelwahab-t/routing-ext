<?php

namespace Services;

use App\RoutingManager\Methods\DELETE;
use App\RoutingManager\Methods\GET;
use App\RoutingManager\Methods\PATCH;
use App\RoutingManager\Methods\POST;
use App\RoutingManager\Methods\PUT;
use Illuminate\Support\Facades\Route;
use ModuleClassNotFoundException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

final readonly class RegisterRoutesService
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
        $routeMethod = match ($instance::class){
            GET::class      => 'get',
            POST::class     => 'post',
            PATCH::class    => 'patch',
            PUT::class      => 'put',
            DELETE::class   => 'delete',
        };

        $route = Route::prefix($instance->prefix)->{$routeMethod}(
            $instance->uri, [$class, $method->getName()]
        )->name($instance->name);

        if($instance?->middleware){
            $route->middleware([$instance->middleware]);
        }
    }

}