<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\Route;

use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Symfony\Component\Routing\Route;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareServiceFetcher;
use Zholus\SymfonyMiddleware\MiddlewareInterface;
use Zholus\SymfonyMiddleware\Route\ResolvedRouteMiddleware;
use Zholus\SymfonyMiddleware\Route\RouteFetcher;
use Zholus\SymfonyMiddleware\Route\RouteMiddlewareResolver;
use Zholus\SymfonyMiddleware\Route\RouteWrapper;

final class RouteMiddlewareResolverTest extends TestCase
{
    private const ROUTE_NAME = 'route_name';

    /**
     * @var MockObject|RouteFetcher
     */
    private $routeFetcher;
    /**
     * @var MockObject|MiddlewareServiceFetcher
     */
    private $middlewareServiceFetcher;

    public function setUp(): void
    {
        $this->routeFetcher = $this->createMock(RouteFetcher::class);
        $this->middlewareServiceFetcher = $this->createMock(MiddlewareServiceFetcher::class);
    }

    public function testResolveMiddlewaresForCurrentRouteWithNullableRouteWrapperOriginalRoute(): void
    {
        /** @var Router|MockObject $router */
        $router = $this->createMock(Router::class);

        $this->routeFetcher->expects($this->once())
            ->method('fetchCurrentRoute')
            ->willReturn(new RouteWrapper(null, 'routeName'));

        $this->middlewareServiceFetcher->expects($this->never())
            ->method('fetchServices');

        $resolver = $this->getResolver();

        $this->assertEmpty($resolver->resolveMiddlewaresForCurrentRoute($router));
    }

    public function testResolveMiddlewaresForCurrentRouteWithNullableRouteWrapperRouteName(): void
    {
        /** @var Router|MockObject $router */
        $router = $this->createMock(Router::class);

        $this->routeFetcher->expects($this->once())
            ->method('fetchCurrentRoute')
            ->willReturn(new RouteWrapper($this->createMock(Route::class), null));

        $this->middlewareServiceFetcher->expects($this->never())
            ->method('fetchServices');

        $resolver = $this->getResolver();

        $this->assertEmpty($resolver->resolveMiddlewaresForCurrentRoute($router));
    }

    public function testResolveMiddlewaresForCurrentRouteWithEmptyMiddlewaresForRoute(): void
    {
        /** @var Router|MockObject $router */
        $router = $this->createMock(Router::class);

        /** @var Route|MockObject $route */
        $route = $this->createMock(Route::class);
        $route->expects($this->once())
            ->method('getOptions')
            ->willReturn([]);

        $this->routeFetcher->expects($this->once())
            ->method('fetchCurrentRoute')
            ->willReturn(new RouteWrapper($route, self::ROUTE_NAME));

        $this->middlewareServiceFetcher->expects($this->never())
            ->method('fetchServices');

        $resolver = $this->getResolver();

        $this->assertEmpty($resolver->resolveMiddlewaresForCurrentRoute($router));
    }

    public function testResolveMiddlewaresForCurrentRouteWithSuccessResult(): void
    {
        /** @var Router|MockObject $router */
        $router = $this->createMock(Router::class);

        /** @var Route|MockObject $route */
        $route = $this->createMock(Route::class);
        $route->expects($this->once())
            ->method('getOptions')
            ->willReturn(['middleware' => ['fqcn_1', 'fqcn_2']]);

        $this->routeFetcher->expects($this->once())
            ->method('fetchCurrentRoute')
            ->willReturn(new RouteWrapper($route, self::ROUTE_NAME));

        $m = $this->createMock(MiddlewareInterface::class);

        $this->middlewareServiceFetcher->expects($this->once())
            ->method('fetchServices')
            ->with(['fqcn_1', 'fqcn_2'])
            ->willReturn([$m]);

        $resolver = $this->getResolver();
        $result = $resolver->resolveMiddlewaresForCurrentRoute($router);

        $this->assertCount(1, $result);

        $resolverMiddleware = $result[0];

        $this->assertInstanceOf(ResolvedRouteMiddleware::class, $resolverMiddleware);
        $this->assertSame(self::ROUTE_NAME, $resolverMiddleware->getRouteName());
        $this->assertSame($m, $resolverMiddleware->getMiddleware());
    }

    private function getResolver(): RouteMiddlewareResolver
    {
        return new RouteMiddlewareResolver($this->routeFetcher, $this->middlewareServiceFetcher);
    }
}
