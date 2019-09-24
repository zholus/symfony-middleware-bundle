<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Controller;

interface ControllerParserInterface
{
    public function parse(callable $controller): ControllerMetadata;
}
