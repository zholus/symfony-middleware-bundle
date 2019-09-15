<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Route;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class RouteFetcher
{
    private $container;

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
    }

    public function fetchCurrentRoute(Router $router): RouteWrapper
    {
        /** @var RequestStack $request_stack */
        $request_stack = $this->container->get('request_stack');
        $request = $request_stack->getCurrentRequest();
        $routeCollection = $router->getRouteCollection();

        $routeName = $request->get('_route');
        $route = $routeCollection->get($routeName);

        return new RouteWrapper($route, $routeName);
    }
}
