<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Middleware;

use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;

class MiddlewareTransformer
{
    /**
     * @param GlobalMiddlewareWrapper[] $globalMiddlewares
     * @return GlobalMiddlewareInterface[]
     */
    public function fromWrapper(array $globalMiddlewares): array
    {
        $globalMiddlewares = $this->sortByPriority($globalMiddlewares);

        return $this->mapToMiddleware($globalMiddlewares);
    }

    /**
     * @param GlobalMiddlewareWrapper[] $globalMiddlewares
     * @return GlobalMiddlewareWrapper[]
     */
    private function sortByPriority(array $globalMiddlewares): array
    {
        $result = $globalMiddlewares;

        usort($result, static function (GlobalMiddlewareWrapper $a, GlobalMiddlewareWrapper $b) {
            return $b->getPriority() <=> $a->getPriority();
        });

        return $result;
    }

    /**
     * @param GlobalMiddlewareWrapper[] $globalMiddlewares
     * @return GlobalMiddlewareWrapper[]
     */
    private function mapToMiddleware(array $globalMiddlewares): array
    {
        return array_map(static function (GlobalMiddlewareWrapper $middleware) {
            return $middleware->getMiddleware();
        }, $globalMiddlewares);
    }
}
