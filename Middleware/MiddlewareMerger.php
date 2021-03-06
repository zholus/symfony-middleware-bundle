<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Middleware;

use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;
use Zholus\SymfonyMiddleware\MiddlewareInterface;

class MiddlewareMerger
{
    /**
     * @param GlobalMiddlewareInterface[] $global
     * @param MiddlewareInterface[] $controller
     * @param MiddlewareInterface[] $action
     * @param MiddlewareInterface[] $route
     * @return MiddlewareInterface[]
     */
    public function merge(array $global, array $controller, array $action, array $route): array
    {
        return array_values($this->unique(
            array_merge($global, $controller, $action, $route)
        ));
    }

    /**
     * @param MiddlewareInterface[] $middlewares
     * @return MiddlewareInterface[]
     */
    private function unique(array $middlewares): array
    {
        $result = [];

        foreach ($middlewares as $middleware) {
            $result[get_class($middleware)] = $middleware;
        }

        return $result;
    }
}
