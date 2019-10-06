<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\DependencyInjection\CompilerPass;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass\ControllerMiddlewarePass;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareEnum;

final class ControllerMiddlewarePassTest extends TestCase
{
    /**
     * @var Definition|MockObject
     */
    private $definition;

    /**
     * @var ContainerBuilder|MockObject
     */
    private $builder;

    public function setUp(): void
    {
        $this->definition = $this->createMock(Definition::class);
        $this->builder = $this->createMock(ContainerBuilder::class);
    }

    public function testProcessThrowExceptionOnMissingAnyAttributeKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $compiler_pass = new ControllerMiddlewarePass();

        $this->builder->expects($this->once())
            ->method('getDefinition')
            ->willReturn($this->definition);

        $this->builder->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(MiddlewareEnum::CONTROLLER_TAG)
            ->willReturn([
                'id_1' => [],
            ]);

        $compiler_pass->process($this->builder);
    }

    public function testProcessThrowExceptionOnMissingMiddlewareAttributeKey(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $compiler_pass = new ControllerMiddlewarePass();

        $this->builder->expects($this->once())
            ->method('getDefinition')
            ->willReturn($this->definition);

        $this->builder->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(MiddlewareEnum::CONTROLLER_TAG)
            ->willReturn([
                'id_1' => [['any']],
            ]);

        $compiler_pass->process($this->builder);
    }

    public function testProcess(): void
    {
        $services = [
            'id_1' => [['middleware' => 'fqcn_1']],
            'id_2' => [['middleware' => 'fqcn_2', 'action' => 'action_name']],
            'id_3' => [['middleware' => 'fqcn_3']],
        ];

        $compiler_pass = new ControllerMiddlewarePass();

        $this->builder->expects($this->once())
            ->method('getDefinition')
            ->willReturn($this->definition);

        $this->builder->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(MiddlewareEnum::CONTROLLER_TAG)
            ->willReturn($services);

        $this->definition->expects($this->exactly(3))
            ->method('addMethodCall')
            ->withConsecutive(
                ['addControllerMiddleware', ['id_1', new Reference('fqcn_1')]],
                ['addControllerActionMiddleware', ['id_2', 'action_name', new Reference('fqcn_2')]],
                ['addControllerMiddleware', ['id_3', new Reference('fqcn_3')]]
            );

        $compiler_pass->process($this->builder);
    }
}
