<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Middleware;

use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;

final class GlobalMiddlewareWrapper
{
    private $middleware;
    private $priority;

    public function __construct(GlobalMiddlewareInterface $middleware, int $priority)
    {
        $this->middleware = $middleware;
        $this->priority = $priority;
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
