<?php

use Illuminate\Support\ServiceProvider;
use Services\AppBootService;

class RoutingExtServiceProvider extends ServiceProvider
{

    private AppBootService $appBootService;

    public function __construct($app)
    {
        $this->appBootService = $app->make(AppBootService::class);
        parent::__construct($app);
    }

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function boot(): void
    {
        $this->appBootService->boot();
    }

}