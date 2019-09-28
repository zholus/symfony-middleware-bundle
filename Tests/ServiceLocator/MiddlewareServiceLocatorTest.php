<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\ServiceLocator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;
use Zholus\SymfonyMiddleware\MiddlewareInterface;
use Zholus\SymfonyMiddleware\Route\ResolvedRouteMiddleware;
use Zholus\SymfonyMiddleware\Route\RouteMiddlewareResolver;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

final class MiddlewareServiceLocatorTest extends TestCase
{
    /**
     * @var MiddlewareServiceLocator
     */
    private $locator;
    /**
     * @var RouteMiddlewareResolver|MockObject
     */
    private $routeMiddlewareResolver;

    public function setUp(): void
    {
        $this->routeMiddlewareResolver = $this->createMock(RouteMiddlewareResolver::class);

        $this->locator = new MiddlewareServiceLocator(
            $this->routeMiddlewareResolver
        );
    }

    public function testGlobalMiddlewares(): void
    {
        $mock_1 = $this->createMock(GlobalMiddlewareInterface::class);
        $mock_2 = $this->createMock(GlobalMiddlewareInterface::class);

        $this->locator->addGlobalMiddleware($mock_1, -1);
        $this->locator->addGlobalMiddleware($mock_2, 2);

        $wrappers = $this->locator->getGlobalMiddlewares();

        $this->assertCount(2, $wrappers);

        $this->assertSame($wrappers[0]->getMiddleware(), $mock_1);
        $this->assertSame($wrappers[0]->getPriority(), -1);
        $this->assertSame($wrappers[1]->getMiddleware(), $mock_2);
        $this->assertSame($wrappers[1]->getPriority(), 2);
    }

    public function testControllerMiddleware(): void
    {
        $mock_1 = $this->createMock(MiddlewareInterface::class);
        $mock_2 = $this->createMock(MiddlewareInterface::class);
        $mock_3 = $this->createMock(MiddlewareInterface::class);

        $this->locator->addControllerMiddleware('fqcn_1', $mock_1);
        $this->locator->addControllerMiddleware('fqcn_2', $mock_2);
        $this->locator->addControllerMiddleware('fqcn_2', $mock_3);

        $this->assertSame([$mock_1], $this->locator->getControllerMiddlewares('fqcn_1'));
        $this->assertSame([$mock_2, $mock_3], $this->locator->getControllerMiddlewares('fqcn_2'));
        $this->assertSame([], $this->locator->getControllerMiddlewares('fqcn_3'));
    }

    public function testControllerActionMiddleware(): void
    {
        $mock_1 = $this->createMock(MiddlewareInterface::class);
        $mock_2 = $this->createMock(MiddlewareInterface::class);
        $mock_3 = $this->createMock(MiddlewareInterface::class);
        $mock_4 = $this->createMock(MiddlewareInterface::class);

        $this->locator->addControllerActionMiddleware('fqcn_1', 'fqcn_1_action_1', $mock_1);
        $this->locator->addControllerActionMiddleware('fqcn_2', 'fqcn_2_action_1', $mock_2);
        $this->locator->addControllerActionMiddleware('fqcn_2', 'fqcn_2_action_1', $mock_3);
        $this->locator->addControllerActionMiddleware('fqcn_2', 'fqcn_2_action_2', $mock_4);

        $this->assertSame([$mock_1], $this->locator->getControllerActionMiddlewares('fqcn_1', 'fqcn_1_action_1'));
        $this->assertSame([$mock_2, $mock_3], $this->locator->getControllerActionMiddlewares('fqcn_2', 'fqcn_2_action_1'));
        $this->assertSame([$mock_4], $this->locator->getControllerActionMiddlewares('fqcn_2', 'fqcn_2_action_2'));
        $this->assertSame([], $this->locator->getControllerActionMiddlewares('fqcn_1', 'fqcn_1_action_2'));
        $this->assertSame([], $this->locator->getControllerActionMiddlewares('fqcn_3', 'fqcn_2_action_2'));
    }

    public function testRouteMiddlewares(): void
    {
        /** @var Router|MockObject $router */
        $router = $this->createMock(Router::class);

        $mock_1 = $this->createMock(MiddlewareInterface::class);
        $mock_2 = $this->createMock(MiddlewareInterface::class);

        $resolved_routes = [
            new ResolvedRouteMiddleware('abc', $mock_1),
            new ResolvedRouteMiddleware('abc', $mock_2),
        ];

        $this->routeMiddlewareResolver->expects($this->once())
            ->method('resolveMiddlewaresForCurrentRoute')
            ->with($router)
            ->willReturn($resolved_routes);

        $this->locator->addRouteMiddlewares($router);

        $this->assertSame([$mock_1, $mock_2], $this->locator->getRouteMiddlewares('abc'));
        $this->assertSame([], $this->locator->getRouteMiddlewares('abc2'));
    }
}
