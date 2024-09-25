<?php

namespace Test\Unit\Adapters\Web\Request;

use App\Adapters\Web\Request\Enums\RequestMethod;
use App\Adapters\Web\Request\PhpInput;
use App\Adapters\Web\Request\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function testGetServer(): void
    {
        // Arrange
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
        ];

        // Act
        $request = new Request();

        // Assert
        $this->assertEquals([
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
        ], $request->getServer());
    }

    public function testGetParams(): void
    {
        // Arrange
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI' => '/test',
        ];

        $inputMock = $this->createMock(PhpInput::class);
        $inputMock->expects($this->once())
            ->method('getGet')
            ->willReturn([
                'GET_PARAM' => 'GET',
            ]);
        $inputMock->expects($this->once())
            ->method('getPost')
            ->willReturn([
                'POST_PARAM' => 'POST',
            ]);
        $inputMock->expects($this->once())
            ->method('getJson')
            ->willReturn([
                'JSON_PARAM' => 'JSON',
            ]);

        // Act
        $request = new Request($inputMock);

        // Assert
        $this->assertEquals('GET', $request->getParam('GET_PARAM'));
        $this->assertEquals('POST', $request->getParam('POST_PARAM'));
        $this->assertEquals('JSON', $request->getParam('JSON_PARAM'));
    }

    public function testGetMethod(): void
    {
        // Arrange
        $_SERVER = [
            'REQUEST_METHOD' => 'GET',
        ];

        // Act
        $request = new Request();

        // Assert
        $this->assertEquals(RequestMethod::GET, $request->getMethod());
    }

    public function testGetUri(): void
    {
        // Arrange
        $_SERVER = [
            'REQUEST_URI' => '/test?param=value',
        ];

        // Act
        $request = new Request();

        // Assert
        $this->assertEquals('/test?param=value', $request->getUri());
    }

    public function testGetSanitizedUri(): void
    {
        // Arrange
        $_SERVER = [
            'REQUEST_URI' => '/test?param=value',
        ];

        // Act
        $request = new Request();

        // Assert
        $this->assertEquals('/test', $request->getSanitizedUri());
    }
}
