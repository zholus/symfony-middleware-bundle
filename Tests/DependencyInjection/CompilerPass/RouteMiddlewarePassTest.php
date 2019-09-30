<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\DependencyInjection\CompilerPass;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass\RouteMiddlewarePass;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareEnum;

final class RouteMiddlewarePassTest extends TestCase
{
    public function testProcess(): void
    {
        $compilerPass = new RouteMiddlewarePass();

        $definition = $this->createMock(Definition::class);

        $definition->expects($this->once())
            ->method('addMethodCall')
            ->with('addRouteMiddlewares', [new Reference('router.default')]);

        $builder = $this->createMock(ContainerBuilder::class);


        $builder->expects($this->once())
            ->method('getDefinition')
            ->willReturn($definition);

        $services = [
            'id_1' => [],
            'id_2' => [],
            'id_3' => [],
            'id_4' => [],
        ];

        $servicesAliases = [
            ['id_1' . MiddlewareEnum::ALIAS_SUFFIX, 'id_1'],
            ['id_2' . MiddlewareEnum::ALIAS_SUFFIX, 'id_2'],
            ['id_3' . MiddlewareEnum::ALIAS_SUFFIX, 'id_3'],
            ['id_4' . MiddlewareEnum::ALIAS_SUFFIX, 'id_4'],
        ];

        $alias_1 = $this->createMock(Alias::class);
        $alias_2 = $this->createMock(Alias::class);
        $alias_3 = $this->createMock(Alias::class);
        $alias_4 = $this->createMock(Alias::class);

        $alias_1->expects($this->once())->method('setPublic')->with(true);
        $alias_2->expects($this->once())->method('setPublic')->with(true);
        $alias_3->expects($this->once())->method('setPublic')->with(true);
        $alias_4->expects($this->once())->method('setPublic')->with(true);

        $aliases = [
            $alias_1,
            $alias_2,
            $alias_3,
            $alias_4,
        ];

        $builder->expects($this->once())
            ->method('findTaggedServiceIds')
            ->with(MiddlewareEnum::ALIAS_SUFFIX)
            ->willReturn($services);

        $builder->expects($this->exactly(4))
            ->method('setAlias')
            ->withConsecutive(...$servicesAliases)
            ->willReturnOnConsecutiveCalls(...$aliases);

        $compilerPass->process($builder);
    }
}
