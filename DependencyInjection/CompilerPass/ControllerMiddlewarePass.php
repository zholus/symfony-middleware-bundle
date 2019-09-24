<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareEnum;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

final class ControllerMiddlewarePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $def = $container->getDefinition(MiddlewareServiceLocator::class);

        foreach ($container->findTaggedServiceIds(MiddlewareEnum::CONTROLLER_TAG) as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!array_key_exists('middleware', $attribute)) {
                    throw new \InvalidArgumentException('No "middleware" attribute was found');
                }

                if (array_key_exists('action', $attribute)) {
                    $def->addMethodCall(
                        'addControllerActionMiddlewareReferences',
                        [$id, $attribute['action'], new Reference($attribute['middleware'])]
                    );
                } else {
                    $def->addMethodCall(
                        'addControllerMiddlewareReferences',
                        [$id, new Reference($attribute['middleware'])]
                    );
                }
            }
        }
    }
}
