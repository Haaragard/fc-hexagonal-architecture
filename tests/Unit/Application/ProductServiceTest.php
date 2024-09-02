<?php

namespace Test\Unit\Application;

use App\Application\Product;
use App\Application\ProductPersistenceInterface;
use App\Application\ProductService;
use Exception;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ProductServiceTest extends TestCase
{
    private ProductPersistenceInterface|MockObject $productPersistenceMock;

    private ProductService $productService;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->productPersistenceMock = $this->createMock(ProductPersistenceInterface::class);
        $this->productService = new ProductService($this->productPersistenceMock);
    }

    public function testGetProduct(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 1
        );

        $this->productPersistenceMock
            ->expects($this->once())
            ->method('get')
            ->willReturn($product);

        $productFound = $this->productService->get($product->id);

        $this->assertInstanceOf(Product::class, $product);

        $this->assertEquals($product->id, $productFound->id);
        $this->assertEquals($product->name, $productFound->name);
        $this->assertEquals($product->price, $productFound->price);
    }

    public function testGetProductThrowError(): void
    {
        $this->productPersistenceMock
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new Exception('Product not found.'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not found.');

        $product = $this->productService->get('non-existent-id');

        $this->assertInstanceOf(Product::class, $product);
    }

    public function testCreateProduct(): void
    {
        $id = Uuid::uuid4()->toString();
        $name = 'Product 1';
        $price = 1;

        $this->productPersistenceMock
            ->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf(Product::class))
            ->willReturnCallback(
                function (Product $product) use ($id) {
                    $product->id = $id;
                }
            );

        $product = $this->productService->create($name, $price);

        $this->assertInstanceOf(Product::class, $product);

        $this->assertEquals($id, $product->id);
        $this->assertEquals($name, $product->name);
        $this->assertEquals($price, $product->price);

        $this->assertTrue(Uuid::isValid($product->id));
    }

    public function testCreateProductThrowError(): void
    {
        $this->productPersistenceMock
            ->expects($this->once())
            ->method('create')
            ->willThrowException(new Exception('Product not created.'));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Product not created.');

        $product = $this->productService->create('Product 1', 1);

        $this->assertInstanceOf(Product::class, $product);
    }

    public function testEnable(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 1
        );

        $this->productPersistenceMock->expects($this->once())
            ->method('enable')
            ->with($product)
            ->willReturnCallback(
                function (Product $product) {
                    $product->enable();
                }
            );

        $this->productService->enable($product);

        $this->assertTrue($product->isEnabled());
    }

    public function testDisable(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 1
        );

        $product->enable();

        $this->productPersistenceMock->expects($this->once())
            ->method('disable')
            ->with($product)
            ->willReturnCallback(
                function (Product $product) {
                    $product->disable();
                }
            );

        $this->productService->disable($product);

        $this->assertFalse($product->isEnabled());
    }
}
