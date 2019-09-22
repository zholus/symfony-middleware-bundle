<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware;

final class MiddlewareMerger implements MiddlewareMergerInterface
{
    /**
     * {@inheritDoc}
     */
    public function merge(array $global, array $controller, array $action, array $route): array
    {
        return $this->unique(
            array_merge($global, $controller, $action, $route)
        );
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
