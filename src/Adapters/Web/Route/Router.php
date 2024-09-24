<?php

namespace App\Adapters\Web\Route;

use App\Adapters\Web\Request\Enums\RequestMethod;
use RuntimeException;

final readonly class Router
{
    public const array ROUTES = [
        RequestMethod::GET->value => [
            '/products/find' => 'getProduct',
        ],
        RequestMethod::POST->value => [
            '/products' => 'createProduct',
        ],
        RequestMethod::PUT->value => [
            '/products/enable' => 'enableProduct',
            '/products/disable' => 'disableProduct',
        ],
    ];

    public static function getExecutableMethod(RequestMethod $method, string $uri): string
    {
        return self::ROUTES[$method->value][$uri] ?? throw new RuntimeException('Route not found');
    }
}
