<?php

namespace AbdelwahabT\RoutingExt\Providers;

use ReflectionException;
use Illuminate\Support\ServiceProvider;
use AbdelwahabT\RoutingExt\Bootstrap\AppBootManager;
use AbdelwahabT\RoutingExt\Exceptions\ModuleClassNotFoundException;

class RoutingExtServiceProvider extends ServiceProvider
{

    private AppBootManager $appBootService;

    public function __construct($app)
    {
        $this->appBootService = $app->make(AppBootManager::class);
        parent::__construct($app);
    }

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            $this->appBootService->boot(
                $this->app->basePath()
            );
        });
    }

}