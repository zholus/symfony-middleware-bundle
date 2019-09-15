<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Route;

use Zholus\SymfonyMiddleware\MiddlewareInterface;

final class ResolvedRouteMiddleware
{
    private $routeName;
    private $middleware;

    public function __construct(string $routeName, MiddlewareInterface $middleware)
    {
        $this->routeName = $routeName;
        $this->middleware = $middleware;
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
