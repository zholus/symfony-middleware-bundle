<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Route;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareServiceFetcher;

final class RouteMiddlewareResolver
{
    private $routeFetcher;
    private $middlewareServiceFetcher;

    public function __construct(
        RouteFetcher $routeFetcher,
        MiddlewareServiceFetcher $middlewareServiceFetcher
    ) {
        $this->routeFetcher = $routeFetcher;
        $this->middlewareServiceFetcher = $middlewareServiceFetcher;
    }

    /**
     * @return ResolvedRouteMiddleware[]
     */
    public function resolveMiddlewaresForCurrentRoute(Router $router): array
    {
        $result = [];

        $routeWrapper = $this->routeFetcher->fetchCurrentRoute($router);

        if ($routeWrapper->getOriginalRoute() === null || $routeWrapper->getRouteName() === null) {
            return $result;
        }

        $middlewares = $routeWrapper->getOriginalRoute()->getOptions()['middleware'] ?? [];

        if (!empty($middlewares)) {
            $instances = $this->middlewareServiceFetcher->fetchServices($middlewares);

            foreach ($instances as $instance) {
                $result[] = new ResolvedRouteMiddleware($routeWrapper->getRouteName(), $instance);
            }
        }

        return $result;
    }
}
