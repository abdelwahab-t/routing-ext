<?php

namespace AbdelwahabT\RoutingExt\Bootstrap;

use ReflectionException;
use AbdelwahabT\RoutingExt\Routing\Discovery\RegisterRoutesService;
use AbdelwahabT\RoutingExt\Exceptions\ModuleClassNotFoundException;

final readonly class AppBootManager
{

    public function __construct(
        private RegisterRoutesService $registerRoutesService,
    ){}

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function boot(string $basePath): void
    {
        foreach (glob( $basePath . 'modules/*/App/Http/Controllers/*.php' ) as $file) {
            $this->registerRoutesService->register($file);
        }
    }

}