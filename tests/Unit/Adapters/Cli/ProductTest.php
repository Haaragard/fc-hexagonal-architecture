<?php

namespace Test\Unit\Adapters\Cli;

use App\Adapters\Cli\Product as ProductCli;
use App\Application\Product;
use App\Application\ProductServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private ProductServiceInterface|MockObject $productServiceMock;
    private ProductCli $cli;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productServiceMock = $this->createMock(ProductServiceInterface::class);
        $this->cli = new ProductCli($this->productServiceMock);
    }

    public function testRunCreate(): void
    {
        $this->productServiceMock->expects($this->once())
            ->method('create')
            ->with('Product 1', 100)
            ->willReturn(new Product('1', 'Product 1', 100));

        $this->cli->run('create', '', 'Product 1', 100);
    }

    public function testRunEnable(): void
    {
        $this->productServiceMock->expects($this->once())
            ->method('get')
            ->with('1')
            ->willReturn(new Product('1', 'Product 1', 100));
        $this->productServiceMock->expects($this->once())
            ->method('enable')
            ->with(new Product('1', 'Product 1', 100));

        $this->cli->run('enable', '1', '', 0);
    }

    public function testRunDisable(): void
    {
        $this->productServiceMock->expects($this->once())
            ->method('get')
            ->with('1')
            ->willReturn(new Product('1', 'Product 1', 100));
        $this->productServiceMock->expects($this->once())
            ->method('disable')
            ->with(new Product('1', 'Product 1', 100));

        $this->cli->run('disable', '1', '', 0);
    }
}
