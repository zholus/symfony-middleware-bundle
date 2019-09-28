<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\ServiceLocator;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;
use Zholus\SymfonyMiddleware\Middleware\GlobalMiddlewareWrapper;
use Zholus\SymfonyMiddleware\MiddlewareInterface;
use Zholus\SymfonyMiddleware\Route\RouteMiddlewareResolver;

class MiddlewareServiceLocator
{
    private $globalMiddleware = [];
    private $controllerMiddleware = [];
    private $controllerActionMiddleware = [];
    private $routeMiddleware = [];
    private $routeMiddlewareResolver;

    public function __construct(
        RouteMiddlewareResolver $routeMiddlewareResolver
    ) {
        $this->routeMiddlewareResolver = $routeMiddlewareResolver;
    }

    public function addGlobalMiddleware(GlobalMiddlewareInterface $middleware, int $priority = 0): void
    {
        $this->globalMiddleware[] = new GlobalMiddlewareWrapper($middleware, $priority);
    }

    /**
     * @return GlobalMiddlewareWrapper[]
     */
    public function getGlobalMiddlewares(): array
    {
        return $this->globalMiddleware;
    }

    public function addControllerMiddleware(string $controller_fqcn, MiddlewareInterface $middleware): void
    {
        $this->controllerMiddleware[$controller_fqcn][] = $middleware;
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function getControllerMiddlewares(string $controller_fqcn): array
    {
        return $this->controllerMiddleware[$controller_fqcn] ?? [];
    }

    public function addControllerActionMiddleware(string $controller_fqcn, string $action, MiddlewareInterface $middleware): void
    {
        $this->controllerActionMiddleware[$controller_fqcn][$action][] = $middleware;
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function getControllerActionMiddlewares(string $controller_fqcn, string $action): array
    {
        return $this->controllerActionMiddleware[$controller_fqcn][$action] ?? [];
    }

    public function addRouteMiddlewares(Router $router): void
    {
        $resolved_route_middlewares = $this->routeMiddlewareResolver->resolveMiddlewaresForCurrentRoute($router);

        foreach ($resolved_route_middlewares as $resolved_route_middleware) {
            $this->routeMiddleware[$resolved_route_middleware->getRouteName()][] = $resolved_route_middleware->getMiddleware();
        }
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function getRouteMiddlewares(string $route_name): array
    {
        return $this->routeMiddleware[$route_name] ?? [];
    }
}
