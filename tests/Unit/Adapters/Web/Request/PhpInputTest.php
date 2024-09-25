<?php

namespace Test\Unit\Adapters\Web\Request;

use App\Adapters\Web\Request\PhpInput;
use PHPUnit\Framework\TestCase;

class PhpInputTest extends TestCase
{
    public function testGetGet(): void
    {
        // Arrange
        $_GET = ['key' => 'value'];
        $phpInput = new PhpInput();

        // Act
        $result = $phpInput->getGet();

        // Assert
        $this->assertEquals(['key' => 'value'], $result);
    }

    public function testGetPost(): void
    {
        // Arrange
        $_POST = ['key' => 'value'];
        $phpInput = new PhpInput();

        // Act
        $result = $phpInput->getPost();

        // Assert
        $this->assertEquals(['key' => 'value'], $result);
    }

    public function testGetJson(): void
    {
        // Arrange
        $phpInput = new PhpInput();

        // Act
        $result = $phpInput->getJson();

        // Assert
        $this->assertEquals([], $result);
    }
}
