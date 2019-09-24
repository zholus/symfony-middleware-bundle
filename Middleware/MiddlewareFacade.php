<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Middleware;

use Symfony\Component\HttpFoundation\Request;
use Zholus\SymfonyMiddleware\Controller\ControllerMetadata;
use Zholus\SymfonyMiddleware\MiddlewareInterface;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

class MiddlewareFacade
{
    private $middlewareServiceLocator;
    private $middlewareMerger;
    private $middlewareTransformer;

    public function __construct(
        MiddlewareServiceLocator $middlewareServiceLocator,
        MiddlewareMerger $middlewareMerger,
        MiddlewareTransformer $middlewareTransformer
    ) {
        $this->middlewareServiceLocator = $middlewareServiceLocator;
        $this->middlewareMerger = $middlewareMerger;
        $this->middlewareTransformer = $middlewareTransformer;
    }

    /**
     * @return MiddlewareInterface[]
     */
    public function getMiddlewaresToHandle(ControllerMetadata $controllerMetadata, Request $request): array
    {
        $globalMiddlewares = $this->middlewareServiceLocator->getGlobalMiddlewares();

        $globalMiddlewares = $this->middlewareTransformer->fromWrapper($globalMiddlewares);

        $controllerActionMiddlewares = $this->middlewareServiceLocator->getControllerActionMiddlewares(
            $controllerMetadata->getControllerFqcn(),
            $controllerMetadata->getControllerAction()
        );

        $controllerMiddlewares = $this->middlewareServiceLocator->getControllerMiddlewares(
            $controllerMetadata->getControllerFqcn()
        );

        $routeMiddlewares = $this->middlewareServiceLocator->getRouteMiddlewares();

        return $this->middlewareMerger->merge(
            $globalMiddlewares,
            $controllerMiddlewares,
            $controllerActionMiddlewares,
            $routeMiddlewares[$request->get('_route', '')] ?? []
        );
    }
}
