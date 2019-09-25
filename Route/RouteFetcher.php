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
        $routeCollection = $router->getRouteCollection();

        $routeName = $request->get('_route');
        $route = $routeCollection->get($routeName);

        return new RouteWrapper($route, $routeName);
    }
}
