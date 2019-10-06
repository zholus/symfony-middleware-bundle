<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Zholus\SymfonyMiddleware\Controller\ControllerParser;

final class ControllerParserTest extends TestCase
{
    public function testParseException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $parser = new ControllerParser();

        $parser->parse(static function () {
        });
    }

    public function testParse(): void
    {
        $parser = new ControllerParser();

        $action_method_name = __FUNCTION__;

        $callable = [
            $this,
            $action_method_name
        ];
        $controllerMetadata = $parser->parse($callable);

        $this->assertSame(get_class($this), $controllerMetadata->getControllerFqcn());
        $this->assertSame($action_method_name, $controllerMetadata->getControllerAction());
    }
}
