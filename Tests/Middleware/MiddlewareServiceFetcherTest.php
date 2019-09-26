<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\Middleware;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareEnum;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareServiceFetcher;
use Zholus\SymfonyMiddleware\MiddlewareInterface;

final class MiddlewareServiceFetcherTest extends TestCase
{
    /**
     * @var MockObject|ContainerInterface
     */
    private $container;

    public function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    public function testFetchServicesException(): void
    {
        $this->expectException(\LogicException::class);

        $this->container->expects($this->once())
            ->method('get')
            ->with('fqcn_1' . MiddlewareEnum::ALIAS_SUFFIX)
            ->willReturn(new \stdClass());

        $fetcher = new MiddlewareServiceFetcher($this->container);

        $fetcher->fetchServices(['fqcn_1']);
    }

    public function testFetcherServices(): void
    {
        $mock_1 = $this->createMock(GlobalMiddlewareInterface::class);
        $mock_2 = $this->createMock(MiddlewareInterface::class);

        $this->container->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['fqcn_1' . MiddlewareEnum::ALIAS_SUFFIX], ['fqcn_2' . MiddlewareEnum::ALIAS_SUFFIX])
            ->willReturnOnConsecutiveCalls($mock_1, $mock_2);

        $fetcher = new MiddlewareServiceFetcher($this->container);

        $result = $fetcher->fetchServices(['fqcn_1', 'fqcn_2']);

        $this->assertCount(2, $result);

        $this->assertSame($mock_1, $result[0]);
        $this->assertSame($mock_2, $result[1]);
    }
}
