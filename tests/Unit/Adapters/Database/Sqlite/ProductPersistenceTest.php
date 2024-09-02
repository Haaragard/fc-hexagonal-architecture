<?php

namespace Test\Unit\Adapters\Database\Sqlite;

use App\Adapters\Database\Sqlite\ProductPersistence;
use App\Application\Product;
use App\Application\ProductInterface;
use Override;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;

class ProductPersistenceTest extends TestCase
{
    private UuidFactoryInterface|MockObject $uuidFactoryMock;
    private ProductPersistence $productPersistence;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->uuidFactoryMock = $this->createMock(UuidFactoryInterface::class);
        $this->productPersistence = new ProductPersistence($this->pdo, $this->uuidFactoryMock);
    }

    public function testGet(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::DISABLED
        );

        $this->insertProduct($product);

        $productFound = $this->productPersistence->get($product->getId());

        $this->assertInstanceOf(Product::class, $productFound);
        $this->assertEquals($product->getId(), $productFound->getId());
        $this->assertEquals('Product 1', $productFound->getName());
        $this->assertEquals(100, $productFound->getPrice());
        $this->assertEquals(ProductInterface::DISABLED, $productFound->getStatus());
    }

    public function testCreate(): void
    {
        $id = Uuid::uuid4()->toString();
        $product = new Product(null, 'New Product', 200);

        $this->uuidFactoryMock->expects($this->once())
            ->method('uuid4')
            ->willReturn(Uuid::fromString($id));

        $this->productPersistence->create($product);

        $this->assertNotNull($product->getId());
        $this->assertEquals('New Product', $product->getName());
        $this->assertEquals(200, $product->getPrice());
        $this->assertEquals(ProductInterface::DISABLED, $product->getStatus());
    }

    public function testEnable(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::DISABLED
        );

        $this->insertProduct($product);

        $this->productPersistence->enable($product);

        $productFound = $this->productPersistence->get($product->getId());

        $this->assertEquals(ProductInterface::ENABLED, $productFound->getStatus());
    }

    public function testDisable(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::ENABLED
        );

        $this->insertProduct($product);

        $this->productPersistence->enable($product);

        $productFound = $this->productPersistence->get($product->getId());

        $this->assertEquals(ProductInterface::ENABLED, $productFound->getStatus());

        $this->productPersistence->disable($product);

        $productFound = $this->productPersistence->get($product->getId());

        $this->assertEquals(ProductInterface::DISABLED, $productFound->getStatus());
    }

    private function insertProduct(ProductInterface $product): void
    {
        $id = Uuid::uuid4()->toString();

        $product->setId($id);

        $statement = $this->pdo->prepare('INSERT INTO products (id, name, price, status) VALUES (:id, :name, :price, :status)');
        $statement->execute([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'status' => $product->getStatus(),
        ]);
    }
}
