<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Route;

use Symfony\Component\Routing\Route;

final class RouteWrapper
{
    private $originalRoute;
    private $routeName;

    public function __construct(?Route $originalRoute, ?string $routeName)
    {
        $this->originalRoute = $originalRoute;
        $this->routeName = $routeName;
    }

    public function getOriginalRoute(): ?Route
    {
        return $this->originalRoute;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }
}
