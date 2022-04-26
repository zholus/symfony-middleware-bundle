<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Middleware;

use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;

final class GlobalMiddlewareWrapper
{
    public function __construct(
        private GlobalMiddlewareInterface $middleware,
        private int $priority
    ) {
    }

    public function getMiddleware(): GlobalMiddlewareInterface
    {
        return $this->middleware;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
