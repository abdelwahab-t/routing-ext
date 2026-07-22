<?php

namespace AbdelwahabT\RoutingExt\Bootstrap;

use AbdelwahabT\RoutingExt\Routing\Attributes\ResourceRoute;
use AbdelwahabT\RoutingExt\Routing\Attributes\RouteOption;
use Illuminate\Routing\Middleware\SubstituteBindings;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Illuminate\Support\Facades\Route;
use AbdelwahabT\RoutingExt\Exceptions\ModuleClassNotFoundException;

final readonly class RouteAttributesRegistrar
{

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function register(array $controllers): void
    {
        $this->registerControllersRoutes(
            $controllers
        );
    }

    /**
     * @throws ModuleClassNotFoundException
     */
    private function getClass(string $file): string
    {
        $class = $this->resolveClassFromFile($file);

        if (!class_exists($class)) {
            require_once $file;
            if (!class_exists($class)) {
                throw new ModuleClassNotFoundException($class);
            }
        }

        return $class;
    }

    private function resolveClassFromFile(string $file): string
    {
        $tokens = token_get_all(file_get_contents($file));

        $namespace = '';
        $class = '';

        for ($i = 0, $count = count($tokens); $i < $count; $i++) {
            if (is_array($tokens[$i]) && $tokens[$i][0] === T_NAMESPACE) {
                $namespace = $this->extractName($tokens, $i);
            }

            if (is_array($tokens[$i]) && $tokens[$i][0] === T_CLASS) {
                if (isset($tokens[$i - 1]) && is_array($tokens[$i - 1]) && $tokens[$i - 1][0] === T_DOUBLE_COLON) {
                    continue;
                }
                $class = $this->extractName($tokens, $i);
                break;
            }
        }

        return $namespace ? $namespace . '\\' . $class : $class;
    }

    private function extractName(array $tokens, int &$index): string
    {
        $name = '';
        $index++;

        while (isset($tokens[$index])) {
            if (is_array($tokens[$index])) {
                if (in_array($tokens[$index][0], [
                    T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED, T_NS_SEPARATOR
                ], true)) {
                    $name .= $tokens[$index][1];
                }else if ($tokens[$index][0] !== T_WHITESPACE) {
                    break;
                }
            }
            $index++;
        }

        return $name;
    }

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    private function registerControllersRoutes(array $controllers): void
    {
        $this->registerRoutes(
            $this->loadControllersInstances($controllers)
        );
    }

    /**
     * @throws ModuleClassNotFoundException|ReflectionException
     */
    private function loadControllersInstances(array $controllers): array
    {
        $routes = [];
        foreach ($controllers as $controller) {
            $class = $this->getClass($controller);
            $reflectionClass = new ReflectionClass($class);

            $this->attachMethodsRoutesIfExists($routes, $reflectionClass, $class);
            $this->attachResourceRouteIfExists($routes, $reflectionClass, $class);

        }

        return $routes;
    }

    private function attachResourceRouteIfExists(
        array &$routes, ReflectionClass $reflectionClass, string $class
    ): void
    {
        $controllerAttribute = $reflectionClass->getAttributes(ResourceRoute::class)[0] ?? null;
        $resourceRoute = $controllerAttribute?->newInstance();

        if ($resourceRoute) {
            $routes[$resourceRoute->prefix][] = [
                'resourceRoute' => $resourceRoute,
                'class'         => $class,
            ];
        }
    }

    private function attachMethodsRoutesIfExists(
        array &$routes, ReflectionClass $reflectionClass, string $class
    ): void
    {
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            foreach ($method->getAttributes() as $attribute) {
                $route = $attribute->newInstance();
                if ($route instanceof RouteOption) {
                    $routes[$route->prefix][] = [
                        'routeOption' => $route,
                        'class' => $class,
                        'method' => $method,
                    ];
                }
            }
        }
    }

    private function registerRoutes(array $groupedRoutes): void
    {
        foreach ($groupedRoutes as $prefix => $routes) {
            Route::prefix($prefix)->group(function () use ($routes) {
                foreach ($routes as $route) {
                    if (isset($route['routeOption'])) {
                        $this->registerRoute(...$route);
                    } else if (isset($route['resourceRoute'])) {
                        $this->registerResourceRoute(...$route);
                    }
                }
            });
        }
    }

    private function registerResourceRoute(ResourceRoute $resourceRoute, string $class): void
    {
        $registration = Route::resource($resourceRoute->name, $class);

        if ($resourceRoute->only) {
            $registration->only($resourceRoute->only);
        } elseif ($resourceRoute->except) {
            $registration->except($resourceRoute->except);
        }

        if ($resourceRoute->parameterName) {
            $registration->parameters([$resourceRoute->name => $resourceRoute->parameterName]);
        }

        $middlewares = $this->getMiddlewares($resourceRoute);

        if (!empty($middlewares)) {
            $registration->middleware($middlewares);
        }

        $registration->register();

        $this->applyPerActionAbilities($resourceRoute);
    }

    private function applyPerActionAbilities(ResourceRoute $resourceRoute): void
    {
        if (!is_array($resourceRoute->ability) || !$this->isAssoc($resourceRoute->ability)) {
            return;
        }

        foreach ($resourceRoute->ability as $action => $ability) {
            $route = Route::getRoutes()->getByName("{$resourceRoute->name}.{$action}");

            if (!$route) {
                continue;
            }

            $route->middleware(
                is_array($ability)
                    ? array_map(fn (string $a) => "can:$a", $ability)
                    : "can:$ability"
            );
        }
    }

    private function isAssoc(array $array): bool
    {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    private function registerRoute(RouteOption $routeOption, string $class, ReflectionMethod $method): void
    {
        $route = Route::name($routeOption->name);

        $middlewares = $this->getMiddlewares($routeOption);

        if (!empty($middlewares)) {
            $route->middleware($middlewares);
        }

        $route->{$routeOption->method->value}(
            $routeOption->uri, [$class, $method->getName()]
        );

    }

    private function getMiddlewares(RouteOption|ResourceRoute $routeOption): array
    {

        $middlewares = [SubstituteBindings::class];

        if ($routeOption->middleware) {
            if (is_array($routeOption->middleware)) {
                $middlewares = $routeOption->middleware;
            } else {
                $middlewares[] = $routeOption->middleware;
            }
        }

        if ($routeOption->ability) {
            if (is_array($routeOption->ability)) {
                if (!$this->isAssoc($routeOption->ability)) {
                    $middlewares = array_merge($middlewares, array_map(fn (string $middleware) => "can:$middleware", $routeOption->ability));
                }
            } else {
                $middlewares[] = "can:$routeOption->ability";
            }
        }

        return $middlewares;

    }

}