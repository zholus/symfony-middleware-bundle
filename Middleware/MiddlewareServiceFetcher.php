<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Middleware;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Zholus\SymfonyMiddleware\MiddlewareInterface;

class MiddlewareServiceFetcher
{
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    /**
     * @param string[] $middlewares
     * @return MiddlewareInterface[]
     */
    public function fetchServices(array $middlewares): array
    {
        $result = [];

        foreach ($middlewares as $middleware_id) {
            $middleware = $this->container->get($middleware_id . MiddlewareEnum::ALIAS_SUFFIX);

            if (!$middleware instanceof MiddlewareInterface) {
                throw new \LogicException(
                    sprintf('Middleware [%s] must be instance of [%s]', $middleware_id, MiddlewareInterface::class)
                );
            }

            $result[] = $middleware;
        }

        return $result;
    }
}
