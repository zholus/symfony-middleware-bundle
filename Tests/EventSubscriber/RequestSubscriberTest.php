<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\EventSubscriber;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Zholus\SymfonyMiddleware\Controller\ControllerMetadata;
use Zholus\SymfonyMiddleware\Controller\ControllerParserInterface;
use Zholus\SymfonyMiddleware\EventSubscriber\RequestSubscriber;
use Zholus\SymfonyMiddleware\GlobalMiddlewareInterface;
use Zholus\SymfonyMiddleware\Middleware\MiddlewareFacade;
use Zholus\SymfonyMiddleware\MiddlewareInterface;

final class RequestSubscriberTest extends TestCase
{
    /**
     * @var ControllerParserInterface|MockObject
     */
    private $controllerParser;

    /**
     * @var MiddlewareFacade|MockObject
     */
    private $middlewareFacade;

    /**
     * @var RequestSubscriber
     */
    private $requestSubscriber;

    public function setUp(): void
    {
        $this->controllerParser = $this->createMock(ControllerParserInterface::class);
        $this->middlewareFacade = $this->createMock(MiddlewareFacade::class);

        $this->requestSubscriber = new RequestSubscriber(
            $this->controllerParser,
            $this->middlewareFacade
        );
    }

    public function testOnControllerExecuteOnNotMasterRequest(): void
    {
        $event = $this->createMock(ControllerEvent::class);
        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(false);

        $this->controllerParser->expects($this->never())
            ->method('parse');

        $this->middlewareFacade->expects($this->never())
            ->method('getMiddlewaresToHandle');

        $this->requestSubscriber->onControllerExecute($event);
    }

    public function testOnControllerExecuteWithMiddlewaresWithNoResponse(): void
    {
        $event = $this->createMock(ControllerEvent::class);
        $event->expects($this->never())
            ->method('setController');

        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);

        $request = $this->createMock(Request::class);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $callable = [
            $this,
            __FUNCTION__
        ];
        $event->expects($this->once())
            ->method('getController')
            ->willReturn($callable);

        $controllerMetadata = new ControllerMetadata('fqcn', 'action_name');
        $this->controllerParser->expects($this->once())
            ->method('parse')
            ->with($callable)
            ->willReturn($controllerMetadata);

        $mock_1 = $this->createMock(GlobalMiddlewareInterface::class);
        $mock_1->expects($this->once())
            ->method('handle')
            ->willReturn(null);

        $mock_2 = $this->createMock(MiddlewareInterface::class);
        $mock_2->expects($this->once())
            ->method('handle')
            ->willReturn(null);

        $this->middlewareFacade->expects($this->once())
            ->method('getMiddlewaresToHandle')
            ->with($controllerMetadata, $request)
            ->willReturn([$mock_1, $mock_2]);

        $this->requestSubscriber->onControllerExecute($event);
    }

    public function testOnControllerExecuteWithMiddlewaresWithResponse(): void
    {
        $event = $this->createMock(ControllerEvent::class);

        $event->expects($this->once())
            ->method('setController');

        $event
            ->expects($this->once())
            ->method('isMasterRequest')
            ->willReturn(true);

        $request = $this->createMock(Request::class);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn($request);

        $callable = [
            $this,
            __FUNCTION__
        ];
        $event->expects($this->once())
            ->method('getController')
            ->willReturn($callable);

        $controllerMetadata = new ControllerMetadata('fqcn', 'action_name');
        $this->controllerParser->expects($this->once())
            ->method('parse')
            ->with($callable)
            ->willReturn($controllerMetadata);

        $mock_1 = $this->createMock(GlobalMiddlewareInterface::class);
        $mock_1->expects($this->once())
            ->method('handle')
            ->willReturn(null);

        $mock_2 = $this->createMock(MiddlewareInterface::class);
        $mock_2->expects($this->once())
            ->method('handle')
            ->willReturn(new Response('content'));

        $mock_3 = $this->createMock(MiddlewareInterface::class);
        $mock_3->expects($this->never())
            ->method('handle')
            ->willReturn(null);

        $this->middlewareFacade->expects($this->once())
            ->method('getMiddlewaresToHandle')
            ->with($controllerMetadata, $request)
            ->willReturn([$mock_1, $mock_2, $mock_3]);

        $this->requestSubscriber->onControllerExecute($event);
    }
}
