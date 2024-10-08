<?php

namespace Test\Unit\Application;

use App\Application\Product;
use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class ProductTest extends TestCase
{
    public function testProduct(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 0
        );

        $this->assertInstanceOf(Product::class, $product);
    }

    public function testProductEnable(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 10
        );

        $product->enable();

        $this->assertEquals(Product::ENABLED, $product->getStatus());
    }

    #[DataProvider('productEnableThrowsErrorDataProvider')]
    public function testProductEnableThrowsError(int $price): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The price must be greater than zero to enable the product.');

        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 0
        );
        $product->price = $price;

        $product->enable();

        $this->assertEquals(Product::ENABLED, $product->getStatus());
    }

    /**
     * @return int[][]
     */
    public static function productEnableThrowsErrorDataProvider(): array
    {
        return [
            [0],
            [-1],
            [-10],
        ];
    }

    public function testProductDisable(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 10
        );
        $product->enable();

        $product->disable();

        $this->assertEquals(Product::DISABLED, $product->getStatus());
    }

    public function testProductDisableThrowsError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The product must be enabled to disable it.');

        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 0
        );

        $product->disable();

        $this->assertEquals(Product::DISABLED, $product->getStatus());
    }

    #[DataProvider('productValidateDataProvider')]
    public function testProductValidate(?string $id, string $name, int $price): void
    {
        $product = new Product(
            id: $id,
            name: $name,
            price: $price
        );

        $this->assertInstanceOf(Product::class, $product);
    }

    /**
     * @return string[][]|int[][]
     */
    public static function productValidateDataProvider(): array
    {
        return [
            [Uuid::uuid4()->toString(), 'Product 1', 0],
            [Uuid::uuid4()->toString(), 'Product 2', 1],
            [Uuid::uuid4()->toString(), 'Product 3', 5],
            [Uuid::uuid4()->toString(), 'Product 4', 10],
        ];
    }

    public function testGetId(): void
    {
        $id = Uuid::uuid4()->toString();
        $product = new Product($id, 'Product 1', 10);

        $this->assertEquals($id, $product->getId());
    }

    public function testSetId(): void
    {
        $id = Uuid::uuid4()->toString();
        $product = new Product(Uuid::uuid4()->toString(), 'Product 1', 10);

        $product->setId($id);

        $this->assertEquals($id, $product->getId());
    }

    public function testSetIdThrowError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The product ID is required.');

        $product = new Product(Uuid::uuid4()->toString(), 'Product 1', 10);

        $product->setId('');
    }

    public function testGetName(): void
    {
        $name = 'Product 1';
        $product = new Product(Uuid::uuid4()->toString(), $name, 10);

        $this->assertEquals($name, $product->getName());
    }

    public function testGetNameThrowError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The product name is required.');

        $product = new Product(Uuid::uuid4()->toString(), '', 10);

        $this->assertEquals('', $product->getName());
    }

    public function testGetStatus(): void
    {
        $product = new Product(Uuid::uuid4()->toString(), 'Product 1', 10);

        $this->assertEquals(Product::DISABLED, $product->getStatus());
    }

    public function testSetStatus(): void
    {
        $product = new Product(Uuid::uuid4()->toString(), 'Product 1', 10);

        $product->setStatus(Product::ENABLED);

        $this->assertEquals(Product::ENABLED, $product->getStatus());
    }

    public function testSetStatusThrowError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The product status is invalid.');

        $product = new Product(Uuid::uuid4()->toString(), 'Product 1', 10);

        $product->setStatus('invalid');
    }

    public function testGetPrice(): void
    {
        $price = 10;
        $product = new Product(Uuid::uuid4()->toString(), 'Product 1', $price);

        $this->assertEquals($price, $product->getPrice());
    }

    public function testSetPrice(): void
    {
        $price = 10;
        $product = new Product(Uuid::uuid4()->toString(), 'Product 1', 0);

        $product->setPrice($price);

        $this->assertEquals($price, $product->getPrice());
    }

    public function testSetPriceThrowError(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The product price must be zero or greater to be valid.');

        $product = new Product(Uuid::uuid4()->toString(), 'Product 1', -1);

        $product->setPrice(0);
    }
}
