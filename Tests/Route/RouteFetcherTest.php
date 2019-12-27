<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\Route;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Zholus\SymfonyMiddleware\Route\RouteFetcher;

final class RouteFetcherTest extends TestCase
{
    public function testFetchCurrentRoute(): void
    {
        $routeName = 'route_name';

        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('get')
            ->with('_route')
            ->willReturn($routeName);

        /** @var RequestStack|MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        /** @var Route|MockObject $route */
        $route = $this->createMock(Route::class);

        /** @var RouteCollection|MockObject $routeCollection */
        $routeCollection = $this->createMock(RouteCollection::class);
        $routeCollection->expects($this->once())
            ->method('get')
            ->with($routeName)
            ->willReturn($route);

        /** @var Router|MockObject $router */
        $router = $this->createMock(Router::class);
        $router->expects($this->once())
            ->method('getRouteCollection')
            ->willReturn($routeCollection);

        $fetcher = new RouteFetcher($requestStack);

        $routeWrapper = $fetcher->fetchCurrentRoute($router);

        $this->assertSame($route, $routeWrapper->getOriginalRoute());
        $this->assertSame($routeName, $routeWrapper->getRouteName());
    }

    public function testWithNoRequests(): void
    {
        /** @var RequestStack|MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn(null);

        /** @var Router|MockObject $router */
        $router = $this->createMock(Router::class);
        $router->expects($this->never())
            ->method('getRouteCollection');

        $fetcher = new RouteFetcher($requestStack);

        $routeWrapper = $fetcher->fetchCurrentRoute($router);

        $this->assertNull($routeWrapper->getRouteName());
        $this->assertNull($routeWrapper->getOriginalRoute());
    }

    public function testWithNullRequestRoute(): void
    {
        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);
        $request->expects($this->once())
            ->method('get')
            ->with('_route')
            ->willReturn(null);

        /** @var RequestStack|MockObject $requestStack */
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->once())
            ->method('getCurrentRequest')
            ->willReturn($request);

        /** @var Router|MockObject $router */
        $router = $this->createMock(Router::class);
        $router->expects($this->never())
            ->method('getRouteCollection');

        $fetcher = new RouteFetcher($requestStack);

        $routeWrapper = $fetcher->fetchCurrentRoute($router);

        $this->assertNull($routeWrapper->getOriginalRoute());
        $this->assertNull($routeWrapper->getRouteName());
    }
}
