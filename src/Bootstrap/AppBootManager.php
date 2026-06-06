<?php

namespace AbdelwahabT\RoutingExt\Bootstrap;

use ReflectionException;
use AbdelwahabT\RoutingExt\Exceptions\ModuleClassNotFoundException;

final readonly class AppBootManager
{

    public function __construct(
        private RouteAttributesRegistrar $registrar,
    ){}

    /**
     * @param string $basePath
     * @param array<string> $controllersDirs
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    public function boot(string $basePath, array $controllersDirs): void
    {
        $controllers = [];
        foreach ($controllersDirs as $controllersDir) {
            $path = $this->getFilePath($basePath, sprintf('/%s/*', $controllersDir));
            $this->loadControllersFiles($controllers, $basePath, glob($path));
        }
        $this->registrar->register($controllers);
    }

    /**
     * @throws ReflectionException|ModuleClassNotFoundException
     */
    private function loadControllersFiles(array &$controllers, string $basePath, array $controllersPath): void
    {
        $separator = str_contains($basePath, '/') ? '/' : '\\';
        foreach ($controllersPath as $file) {
            if (is_dir($file)) {
                $this->loadControllersFiles($controllers, $basePath, glob($file . $separator . '*'));
                continue;
            }
            if (str_contains($file, '.php')) {
                $controllers[] = $file;
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
