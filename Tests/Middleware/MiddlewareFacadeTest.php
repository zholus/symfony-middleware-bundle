<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\Middleware;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Zholus\SymfonyMiddleware\Controller\ControllerMetadata;
use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;
use Zholus\SymfonyMiddleware\Middleware\GlobalMiddlewareMapper;
use Zholus\SymfonyMiddleware\Middleware\GlobalMiddlewareWrapper;
use Zholus\SymfonyMiddleware\Middleware\GlobalMiddlewareWrapperSorter;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareFacade;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareMerger;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

final class MiddlewareFacadeTest extends TestCase
{
    /**
     * @var MiddlewareServiceLocator|MockObject
     */
    private $middlewareServiceLocator;

    /**
     * @var MiddlewareMerger|MockObject
     */
    private $middlewareMerger;

    /**
     * @var GlobalMiddlewareMapper|MockObject
     */
    private $globalMiddlewareMapper;

    /**
     * @var GlobalMiddlewareWrapperSorter|MockObject
     */
    private $globalMiddlewareWrapperSorter;

    /**
     * @var Request|MockObject
     */
    private $request;

    public function setUp(): void
    {
        $this->middlewareServiceLocator = $this->createMock(MiddlewareServiceLocator::class);
        $this->middlewareMerger = $this->createMock(MiddlewareMerger::class);
        $this->globalMiddlewareMapper = $this->createMock(GlobalMiddlewareMapper::class);
        $this->globalMiddlewareWrapperSorter = $this->createMock(GlobalMiddlewareWrapperSorter::class);
        $this->request = $this->createMock(Request::class);
    }

    // todo: rename method
    public function testFacade(): void
    {
        $facade = new MiddlewareFacade(
            $this->middlewareServiceLocator,
            $this->middlewareMerger,
            $this->globalMiddlewareMapper,
            $this->globalMiddlewareWrapperSorter
        );

        $controllerMetadata = new ControllerMetadata('fqcn', 'action_name');


        $globalMiddlewareWrappers = [
            new GlobalMiddlewareWrapper($this->createMock(GlobalMiddlewareInterface::class), 1),
            new GlobalMiddlewareWrapper($this->createMock(GlobalMiddlewareInterface::class), 2),
        ];
        $this->middlewareServiceLocator->expects($this->once())
            ->method('getGlobalMiddlewares')
            ->willReturn($globalMiddlewareWrappers);

        $controllerActionMiddlewares = [
            $this->createMock(GlobalMiddlewareInterface::class),
            $this->createMock(GlobalMiddlewareInterface::class),
        ];
        $this->middlewareServiceLocator->expects($this->once())
            ->method('getControllerActionMiddlewares')
            ->with('fqcn', 'action_name')
            ->willReturn($controllerActionMiddlewares);

        $controllerMiddlewares = [
            $this->createMock(GlobalMiddlewareInterface::class),
            $this->createMock(GlobalMiddlewareInterface::class),
        ];
        $this->middlewareServiceLocator->expects($this->once())
            ->method('getControllerMiddlewares')
            ->willReturn($controllerMiddlewares);

        $routeMiddlewares = [
            'route_name' => [
                $this->createMock(GlobalMiddlewareInterface::class),
                $this->createMock(GlobalMiddlewareInterface::class),
            ]
        ];
        $this->middlewareServiceLocator->expects($this->once())
            ->method('getRouteMiddlewares')
            ->willReturn($routeMiddlewares);

        $sortedResult = [
            new GlobalMiddlewareWrapper($this->createMock(GlobalMiddlewareInterface::class), 1),
            new GlobalMiddlewareWrapper($this->createMock(GlobalMiddlewareInterface::class), 2),
        ];

        $this->globalMiddlewareWrapperSorter->expects($this->once())
            ->method('sortDescByPriority')
            ->with($globalMiddlewareWrappers)
            ->willReturn($sortedResult);

        $fromWrapperResult = [
            $this->createMock(GlobalMiddlewareInterface::class),
            $this->createMock(GlobalMiddlewareInterface::class),
        ];
        $this->globalMiddlewareMapper->expects($this->once())
            ->method('fromWrapper')
            ->with($sortedResult)
            ->willReturn($fromWrapperResult);

        $this->middlewareMerger->expects($this->once())
            ->method('merge')
            ->with($fromWrapperResult, $controllerMiddlewares, $controllerActionMiddlewares, $routeMiddlewares['route_name']);

        $this->request->expects($this->once())
            ->method('get')
            ->with('_route', '')
            ->willReturn('route_name');

        $facade->getMiddlewaresToHandle(
            $controllerMetadata,
            $this->request
        );
    }
}
