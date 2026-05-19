<?php

namespace AbdelwahabT\RoutingExt\Services;

use ReflectionException;
use Illuminate\Support\Facades\App;
use AbdelwahabT\RoutingExt\Exceptions\ModuleClassNotFoundException;

final readonly class AppBootService
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