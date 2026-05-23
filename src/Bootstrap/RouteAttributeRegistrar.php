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