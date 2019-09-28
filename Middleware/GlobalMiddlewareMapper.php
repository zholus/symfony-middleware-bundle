<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Middleware;

use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;

class GlobalMiddlewareMapper
{
    /**
     * @param GlobalMiddlewareWrapper[] $globalMiddlewares
     * @return GlobalMiddlewareInterface[]
     */
    public function fromWrapper(array $globalMiddlewares): array
    {
        return array_map(static function (GlobalMiddlewareWrapper $middleware) {
            return $middleware->getMiddleware();
        }, $globalMiddlewares);
    }
}
