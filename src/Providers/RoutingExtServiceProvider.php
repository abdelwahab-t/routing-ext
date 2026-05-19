<?php

namespace Abdelwahab\RoutingExt;

use ReflectionException;
use Illuminate\Support\ServiceProvider;
use Abdelwahab\RoutingExt\Exceptions\ModuleClassNotFoundException;
use Abdelwahab\RoutingExt\Services\AppBootService;

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