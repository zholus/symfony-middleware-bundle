<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Route;

use Symfony\Component\Routing\Route;

final class RouteWrapper
{
    public function __construct(
        private ?Route $originalRoute,
        private ?string $routeName
    ) {
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
