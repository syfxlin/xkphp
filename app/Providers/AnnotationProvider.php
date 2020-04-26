<?php

namespace App\Providers;

use App\Facades\Annotation;
use App\Facades\File;
use App\Facades\Route;
use App\Kernel\Controller;
use App\Kernel\RouteManager;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionClass;
use ReflectionMethod;
use function app_path;
use function array_map;
use function class_exists;
use function config;
use function config_path;
use function str_replace;
use function strtoupper;
use function substr;

class AnnotationProvider extends Provider
{
    public function register(): void
    {
        $this->bootAnnotation();
    }

    public function boot(): void
    {
        $this->parseAnnotation();
    }

    protected function bootAnnotation(): void
    {
        AnnotationRegistry::registerLoader('class_exists');
    }

    protected function parseAnnotation(): void
    {
        $config = config('annotation');
        if (empty($config['middleware']) && empty($config['route'])) {
            return;
        }
        $files = File::allFiles(app_path('Controllers'));
        foreach ($files as $file) {
            $class_name = Controller::getFull(
                str_replace('/', '\\', substr($file, 0, -4))
            );
            if (class_exists($class_name)) {
                $class = new ReflectionClass($class_name);
                $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    // 是否开启了中间件注解
                    if (!empty($config['middleware'])) {
                        $this->parseMiddlewareAnnotation($method);
                    }
                    // 是否开启了路由注解
                    if (!empty($config['route'])) {
                        $this->parseRouteAnnotation($method);
                    }
                }
            }
        }
    }

    protected function parseMiddlewareAnnotation(ReflectionMethod $method): void
    {
        $middlewares = Annotation::getList(
            $method,
            'App\Annotations\Middleware'
        );
        if ($middlewares !== []) {
            RouteManager::$annotationMiddlewares[
                "$method->class@$method->name"
            ] = array_map(function ($prop) {
                return $prop->value;
            }, $middlewares);
        }
    }

    protected function parseRouteAnnotation(ReflectionMethod $method): void
    {
        $anno_class = [
            'Get',
            'Post',
            'Delete',
            'Put',
            'Patch',
            'Head',
            'Route'
        ];
        foreach ($anno_class as $anno) {
            $route = Annotation::get($method, "App\Annotations\Route\\$anno");
            if ($route !== null) {
                RouteManager::$annotationRoute[] = function () use (
                    $route,
                    $method,
                    $anno
                ) {
                    $handler = "$method->class@$method->name";
                    if ($anno === 'Route' && $route->method === null) {
                        Route::any($route->value, $handler);
                    } else {
                        Route::match(
                            $route->method ?? [strtoupper($anno)],
                            $route->value,
                            $handler
                        );
                    }
                };
            }
        }
    }
}
