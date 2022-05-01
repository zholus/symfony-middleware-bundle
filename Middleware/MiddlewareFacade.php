<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Zholus\SymfonyMiddleware\Controller\ControllerMetadata;
use Zholus\SymfonyMiddleware\MiddlewareInterface;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

class MiddlewareFacade
{
    public function __construct(
        private MiddlewareServiceLocator $middlewareServiceLocator,
        private MiddlewareMerger $middlewareMerger,
        private GlobalMiddlewareMapper $globalMiddlewareMapper,
        private GlobalMiddlewareWrapperSorter $globalMiddlewareWrapperSorter
    ) {
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function getMiddlewaresToHandle(ControllerMetadata $controllerMetadata, Request $request): array
    {
        $globalMiddlewares = $this->middlewareServiceLocator->getGlobalMiddlewares();

        $globalMiddlewares = $this->globalMiddlewareWrapperSorter->sortDescByPriority($globalMiddlewares);
        $globalMiddlewares = $this->globalMiddlewareMapper->fromWrapper($globalMiddlewares);

        $controllerActionMiddlewares = $this->middlewareServiceLocator->getControllerActionMiddlewares(
            $controllerMetadata->getControllerFqcn(),
            $controllerMetadata->getControllerAction()
        );

        $controllerMiddlewares = $this->middlewareServiceLocator->getControllerMiddlewares(
            $controllerMetadata->getControllerFqcn()
        );

        $routeMiddlewares = $this->middlewareServiceLocator->getRouteMiddlewares($request->get('_route', ''));

        return $this->middlewareMerger->merge(
            $globalMiddlewares,
            $controllerMiddlewares,
            $controllerActionMiddlewares,
            $routeMiddlewares
        );
    }
}
