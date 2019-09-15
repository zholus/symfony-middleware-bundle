<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zholus\SymfonyMiddleware\MiddlewareEnum;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

final class GlobalMiddlewarePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $def = $container->getDefinition(MiddlewareServiceLocator::class);

        foreach ($container->findTaggedServiceIds(MiddlewareEnum::GLOBAL_TAG) as $id => $attributes) {
            $def->addMethodCall('addGlobalMiddleware', [new Reference($id)]);
        }
    }
}
