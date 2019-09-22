<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware;

interface MiddlewareMergerInterface
{
    /**
     * @param GlobalMiddlewareInterface[] $global
     * @param MiddlewareInterface[] $controller
     * @param MiddlewareInterface[] $action
     * @param MiddlewareInterface[] $route
     * @return MiddlewareInterface[]
     */
    public function merge(array $global, array $controller, array $action, array $route): array;
}
