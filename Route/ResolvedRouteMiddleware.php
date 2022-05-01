<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Route;

use Zholus\SymfonyMiddleware\MiddlewareInterface;

final class ResolvedRouteMiddleware
{
    public function __construct(
        private string $routeName,
        private MiddlewareInterface $middleware
    ) {
    }

    public function getRouteName(): string
    {
        return $this->routeName;
    }

    public function getMiddleware(): MiddlewareInterface
    {
        return $this->middleware;
    }
}
