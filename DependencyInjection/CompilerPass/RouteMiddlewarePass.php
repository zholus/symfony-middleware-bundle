<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareEnum;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

final class RouteMiddlewarePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $def = $container->getDefinition(MiddlewareServiceLocator::class);

        $def->addMethodCall('addRouteMiddlewares', [new Reference('router.default')]);

        foreach ($container->findTaggedServiceIds(MiddlewareEnum::ALIAS_SUFFIX) as $id => $arguments) {
            $container->setAlias($id . MiddlewareEnum::ALIAS_SUFFIX, $id)->setPublic(true);
        }
    }
}
