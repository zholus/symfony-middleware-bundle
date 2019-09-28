<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;
use Zholus\SymfonyMiddleware\Middleware\GlobalMiddlewareWrapper;
use Zholus\SymfonyMiddleware\Middleware\GlobalMiddlewareWrapperSorter;

final class GlobalMiddlewareWrapperSorterTest extends TestCase
{
    public function testSort(): void
    {
        $sorter = new GlobalMiddlewareWrapperSorter();

        $wrapper_1 = $this->createWrapper(-2);
        $wrapper_2 = $this->createWrapper(-1);
        $wrapper_3 = $this->createWrapper(0);
        $wrapper_4 = $this->createWrapper(5);
        $wrapper_5 = $this->createWrapper(7);

        $unsorted = [
            $wrapper_1, $wrapper_2, $wrapper_3, $wrapper_4, $wrapper_5
        ];

        $sorted = [
            $wrapper_5, $wrapper_4, $wrapper_3, $wrapper_2, $wrapper_1
        ];

        $result = $sorter->sortDescByPriority($unsorted);

        $this->assertSame($sorted, $result);
    }

    private function createWrapper(int $priority): GlobalMiddlewareWrapper
    {
        return new GlobalMiddlewareWrapper(
            $this->createMock(GlobalMiddlewareInterface::class),
            $priority
        );
    }
}
