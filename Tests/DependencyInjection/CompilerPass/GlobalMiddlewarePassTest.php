<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass\GlobalMiddlewarePass;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareEnum;

final class GlobalMiddlewarePassTest extends TestCase
{
    public function testProcess(): void
    {
        $compilerPass = new GlobalMiddlewarePass();

        $services = [
            'id_1' => [['priority' => -1], []],
            'id_2' => [[]],
            'id_3' => [],
            'id_4' => [['priority' => 88]],
        ];

        $definition = $this->createMock(Definition::class);

        $definition->expects($this->exactly(4))
            ->method('addMethodCall')
            ->withConsecutive(
                ['addGlobalMiddleware', [new Reference('id_1'), -1]],
                ['addGlobalMiddleware', [new Reference('id_2'), 0]],
                ['addGlobalMiddleware', [new Reference('id_3'), 0]],
                ['addGlobalMiddleware', [new Reference('id_4'), 88]]
            );

        $builder = $this->createMock(ContainerBuilder::class);
        $builder->expects($this->once())
            ->method('getDefinition')
        ->willReturn($definition);

        $builder->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(MiddlewareEnum::GLOBAL_TAG)
            ->willReturn($services);

        $compilerPass->process($builder);
    }
}
