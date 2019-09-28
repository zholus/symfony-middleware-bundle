<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;
use Zholus\SymfonyMiddleware\Middleware\GlobalMiddlewareMapper;
use Zholus\SymfonyMiddleware\Middleware\GlobalMiddlewareWrapper;

final class GlobalMiddlewareMapperTest extends TestCase
{
    public function testMapFromWrapper(): void
    {
        $mapper = new GlobalMiddlewareMapper();

        $mock_1 = $this->createMock(GlobalMiddlewareInterface::class);
        $mock_2 = $this->createMock(GlobalMiddlewareInterface::class);

        $result = $mapper->fromWrapper([
            new GlobalMiddlewareWrapper($mock_1, 1),
            new GlobalMiddlewareWrapper($mock_2, 2),
        ]);

        $this->assertSame([
            $mock_1, $mock_2
        ], $result);
    }
}
