<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Zholus\SymfonyMiddleware\Controller\ControllerParserInterface;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareFacade;

final class RequestSubscriber implements EventSubscriberInterface
{
    private ControllerParserInterface $controllerParser;
    private MiddlewareFacade $middlewareFacade;

    public function __construct(
        ControllerParserInterface $controllerParser,
        MiddlewareFacade $middlewareFacade
    ) {
        $this->controllerParser = $controllerParser;
        $this->middlewareFacade = $middlewareFacade;
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
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $controller = $event->getController();

        $controllerMetadata = $this->controllerParser->parse($controller);

        $middlewares = $this->middlewareFacade->getMiddlewaresToHandle($controllerMetadata, $request);

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
}
