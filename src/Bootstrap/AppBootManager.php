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
     * @param string $basePath
     * @param array<string> $controllersDirs
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function boot(string $basePath, array $controllersDirs): void
    {
        foreach ($controllersDirs as $controllersDir) {

            $path = $this->getFilePath(
                $basePath,
                sprintf('/%s/*.php', $controllersDir)
            );

            foreach (glob($path) as $file) {
                $this->registrar->register($file);
            }

        }
    }

    private function getFilePath(string $basePath, string $file): string
    {
        return $basePath . (str_contains($basePath, '/') ?
            str_replace('\\', '/', $file) :
            str_replace('/', '\\', $file));
    }

}