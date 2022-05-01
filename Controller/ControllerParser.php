<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Controller;

final class ControllerParser implements ControllerParserInterface
{
    public function parse(callable|object|array $controller): ControllerMetadata
    {
        if (is_array($controller)) {
            return new ControllerMetadata(get_class($controller[0]), $controller[1]);
        }

        return new ControllerMetadata(get_class($controller), '__invoke');
    }
}
