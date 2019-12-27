<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Route;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;

class RouteFetcher
{
    private $requestStack;

    public function __construct(
        RequestStack $requestStack
    ) {
        $this->requestStack = $requestStack;
    }

    public function fetchCurrentRoute(Router $router): RouteWrapper
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return new RouteWrapper(null, null);
        }

        $routeName = $request->get('_route');

        if (!is_string($routeName)) {
            return new RouteWrapper(null, null);
        }

        $routeCollection = $router->getRouteCollection();

        $route = $routeCollection->get($routeName);

        return new RouteWrapper($route, $routeName);
    }
}
