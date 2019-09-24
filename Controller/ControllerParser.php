<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Controller;

final class ControllerParser implements ControllerParserInterface
{
    public function parse(callable $controller): ControllerMetadata
    {
        if (is_array($controller)) {
            return new ControllerMetadata(get_class($controller[0]), $controller[1]);
        }

        throw new \LogicException('Array supported only');
    }
}
