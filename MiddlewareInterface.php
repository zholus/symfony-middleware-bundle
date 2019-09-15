<?php
declare(strict_types=1);

namespace Zholus\SymfonyMiddleware;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface MiddlewareInterface
{
    public function handle(Request $request): ?Response;
}
