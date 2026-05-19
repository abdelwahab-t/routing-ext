<?php

namespace AbdelwahabT\RoutingExt\Services;

use ReflectionException;
use Illuminate\Support\Facades\App;
use AbdelwahabT\RoutingExt\Exceptions\ModuleClassNotFoundException;

final readonly class AppBootService
{

    public function __construct(
        private App $app,
        private RegisterRoutesService $registerRoutesService,
    ){}

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function boot(): void
    {
        foreach (glob( $this->app->basePath() . 'modules/*/App/Http/Controllers/*.php' ) as $file) {
            $this->registerRoutesService->register($file);
        }
    }

}