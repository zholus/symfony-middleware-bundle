<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

final class RequestSubscriber implements EventSubscriberInterface
{
    private $middlewareServiceLocator;

    public function __construct(
        MiddlewareServiceLocator $middlewareServiceLocator
    ) {
        $this->middlewareServiceLocator = $middlewareServiceLocator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => [
                ['onControllerExecute', 0],
            ]
        ];
    }

    public function onControllerExecute(ControllerEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $controller = $event->getController();

        if (is_array($controller)) {
            $controller_fqcn = get_class($controller[0]);
            $action = $controller[1];
        } else {
            throw new \LogicException('Array supported only');
        }

        $globalMiddlewares = $this->middlewareServiceLocator->getGlobalMiddlewares();
        $controllerActionMiddlewares = $this->middlewareServiceLocator->getControllerActionMiddlewares($controller_fqcn, $action);
        $controllerMiddlewares = $this->middlewareServiceLocator->getControllerMiddlewares($controller_fqcn);
        $routeMiddlewares = $this->middlewareServiceLocator->getRouteMiddlewares();


        dd($controllerMiddlewares, $controllerActionMiddlewares, $globalMiddlewares, $routeMiddlewares);
        // DONE - 1. get from global
        // DONE - 2. get from controller
        // DONE - 3. get from action
        // DONE - 4. get from route
    }
}
