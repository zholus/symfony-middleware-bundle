<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Zholus\SymfonyMiddleware\MiddlewareMergerInterface;
use Zholus\SymfonyMiddleware\ServiceLocator\MiddlewareServiceLocator;

final class RequestSubscriber implements EventSubscriberInterface
{
    private $middlewareServiceLocator;
    private $middlewareMerger;

    public function __construct(
        MiddlewareServiceLocator $middlewareServiceLocator,
        MiddlewareMergerInterface $middlewareMerger
    ) {
        $this->middlewareServiceLocator = $middlewareServiceLocator;
        $this->middlewareMerger = $middlewareMerger;
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

        $request = $event->getRequest();

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

        $globalMiddlewares = $this->sortByPriority($globalMiddlewares);

        $globalMiddlewares = array_map(static function (array $middleware) {
            return $middleware['middleware'];
        }, $globalMiddlewares);

        $middlewares = $this->middlewareMerger->merge(
            $globalMiddlewares,
            $controllerMiddlewares,
            $controllerActionMiddlewares,
            $routeMiddlewares[$request->get('_route', '')] ?? []
        );

        if (empty($middlewares)) {
            return;
        }

        foreach ($middlewares as $middleware) {
            $middlewareResponse = $middleware->handle($request);

            if ($middlewareResponse !== null) {
                $event->setController(static function () use ($middlewareResponse) {
                    return $middlewareResponse;
                });
                break;
            }
        }
    }

    private function sortByPriority(array $globalMiddlewares): array
    {
        $result = $globalMiddlewares;

        usort($result, static function (array $a, array $b) {
            return $b['priority'] <=> $a['priority'];
        });

        return $result;
    }
}
