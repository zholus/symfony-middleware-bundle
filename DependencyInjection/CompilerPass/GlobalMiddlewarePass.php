<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareEnum;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

final class GlobalMiddlewarePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $def = $container->getDefinition(MiddlewareServiceLocator::class);

        foreach ($container->findTaggedServiceIds(MiddlewareEnum::GLOBAL_TAG) as $id => $attributes) {
            $attributes = $this->clearEmpty($attributes);

            $priority = 0;

            foreach ($attributes as $attribute) {
                if (array_key_exists('priority', $attribute)) {
                    $priority = $attribute['priority'];
                    break;
                }
            }

            $def->addMethodCall('addGlobalMiddleware', [new Reference($id), $priority]);
        }
    }

    private function clearEmpty(array $attributes): array
    {
        return array_filter($attributes, function ($e) {
            return !empty($e);
        });
    }
}
