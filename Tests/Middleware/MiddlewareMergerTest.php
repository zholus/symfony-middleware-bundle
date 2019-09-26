<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareMerger;
use Zholus\SymfonyMiddleware\MiddlewareInterface;

final class MiddlewareMergerTest extends TestCase
{
    public function testMerge(): void
    {
        $merger = new MiddlewareMerger();

        $result = $merger->merge(
            $global_mock = [$this->createMock(GlobalMiddlewareInterface::class)],
            $controller_mock = [$this->getMockBuilder(MiddlewareInterface::class)->setMockClassName('a')->getMock()],
            $action_mock = [$this->getMockBuilder(MiddlewareInterface::class)->setMockClassName('b')->getMock()],
            $route_mock = [$this->getMockBuilder(MiddlewareInterface::class)->setMockClassName('c')->getMock()]
        );

        $this->assertSame([
            $global_mock[0], $controller_mock[0], $action_mock[0], $route_mock[0]
        ], $result);

        // ------------------

        $result = $merger->merge(
            $global_mock = [$this->createMock(GlobalMiddlewareInterface::class)],
            $controller_mock = [$this->getMockBuilder(MiddlewareInterface::class)->setMockClassName('a')->getMock()],
            $action_mock = [$controller_mock[0], $this->getMockBuilder(MiddlewareInterface::class)->setMockClassName('b')->getMock()],
            $route_mock = [$action_mock[1], $this->getMockBuilder(MiddlewareInterface::class)->setMockClassName('c')->getMock(), $action_mock[1]]
        );

        $this->assertSame([
            $global_mock[0], $controller_mock[0], $action_mock[1], $route_mock[1]
        ], $result);

        // ------------------

        $result = $merger->merge(
            $global_mock = [$this->createMock(GlobalMiddlewareInterface::class)],
            $controller_mock = [$this->getMockBuilder(MiddlewareInterface::class)->setMockClassName('a')->getMock(), $global_mock[0]],
            $action_mock = [$controller_mock[0]],
            $route_mock = [$action_mock[0], $this->getMockBuilder(MiddlewareInterface::class)->setMockClassName('c')->getMock()]
        );

        $this->assertSame([
            $global_mock[0], $controller_mock[0], $route_mock[1]
        ], $result);
    }
}
