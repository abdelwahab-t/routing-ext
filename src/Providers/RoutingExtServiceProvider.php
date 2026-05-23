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

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/main.php', 'routing-ext'
        );
    }

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/main.php' => config_path('routing-ext.php'),
        ], 'routing-config');

        $this->appBootService->boot(
            $this->app->basePath(),
            config('routing-ext.controllers_directories', [])
        );
    }

}