<?php

namespace Test\Unit\Adapters\Web\Route;

use App\Adapters\Web\Request\Enums\RequestMethod;
use App\Adapters\Web\Route\Router;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    #[DataProvider('getExecutableMethodsDataProvider')]
    public function testGetExecutableMethod(RequestMethod $method, string $uri, string $expectedMethod): void
    {
        // Arrange

        // Act
        $result = Router::getExecutableMethod($method, $uri);

        // Assert
        $this->assertEquals($expectedMethod, $result);
    }

    public static function getExecutableMethodsDataProvider(): array
    {
        return [
            [RequestMethod::GET, '/products/find', 'getProduct'],
            [RequestMethod::POST, '/products', 'createProduct'],
            [RequestMethod::PUT, '/products/enable', 'enableProduct'],
            [RequestMethod::PUT, '/products/disable', 'disableProduct'],
        ];
    }
}
