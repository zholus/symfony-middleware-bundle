<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Controller;

final class ControllerMetadata
{
    public function __construct(
        private string $controllerFqcn,
        private string $controllerAction
    ) {
    }

    public function getControllerFqcn(): string
    {
        return $this->controllerFqcn;
    }

    public function getControllerAction(): string
    {
        return $this->controllerAction;
    }
}
