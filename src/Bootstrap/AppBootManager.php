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
        foreach (glob( $basePath . 'modules/*/App/Http/Controllers/*.php' ) as $file) {
            $this->registrar->register($file);
        }
    }

}