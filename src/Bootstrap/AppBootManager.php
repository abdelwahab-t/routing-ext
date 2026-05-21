<?php

namespace AbdelwahabT\RoutingExt\Bootstrap;

use ReflectionException;
use AbdelwahabT\RoutingExt\Exceptions\ModuleClassNotFoundException;

final readonly class AppBootManager
{

    public function __construct(
        private RouteAttributeRegistrar $registrar,
    ){}

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function boot(string $basePath): void
    {
        foreach (glob( $basePath . '/app/Modules/*/App/Http/Controllers/*.php' ) as $file) {
            $this->registrar->register(
                $this->getFilePath($basePath, $file)
            );
        }
    }

    private function getFilePath(string $basePath, string $file): string
    {
        return str_contains($basePath, '/') ?
            str_replace('\\', '/', $file) :
            str_replace('/', '\\', $file);
    }

}