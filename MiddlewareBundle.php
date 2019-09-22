<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass\ControllerMiddlewarePass;
use Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass\GlobalMiddlewarePass;
use Zholus\SymfonyMiddleware\DependencyInjection\CompilerPass\RouteMiddlewarePass;

class MiddlewareBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(MiddlewareInterface::class)
            ->addTag(MiddlewareEnum::ALIAS_SUFFIX);

        $container->registerForAutoconfiguration(GlobalMiddlewareInterface::class)
            ->addTag(MiddlewareEnum::GLOBAL_TAG);

        $container->addCompilerPass(new GlobalMiddlewarePass());
        $container->addCompilerPass(new ControllerMiddlewarePass());
        $container->addCompilerPass(new RouteMiddlewarePass());
    }
}
