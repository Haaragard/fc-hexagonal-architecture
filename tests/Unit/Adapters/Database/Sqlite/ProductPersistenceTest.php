<?php

namespace Test\Unit\Adapters\Database\Sqlite;

use App\Adapters\Database\Sqlite\ProductPersistence;
use App\Application\Product;
use App\Application\ProductInterface;
use Override;
use PDO;
use PDOStatement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactoryInterface;
use RuntimeException;

class ProductPersistenceTest extends TestCase
{
    private PDO|MockObject $pdo;
    private PDOStatement|MockObject $pdoStatement;
    private UuidFactoryInterface|MockObject $uuidFactoryMock;
    private ProductPersistence $productPersistence;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = $this->createMock(PDO::class);
        $this->pdoStatement = $this->createMock(PDOStatement::class);
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

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT id, name, price, status FROM products WHERE id = :id')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with(['id' => $product->getId()])
            ->willReturn(true);

        $this->pdoStatement->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => $product->getId(),
                'name' => $product->getName(),
                'price' => $product->getPrice(),
                'status' => $product->getStatus(),
            ]);

        $productFound = $this->productPersistence->get($product->getId());

        $this->assertInstanceOf(Product::class, $productFound);
        $this->assertEquals($product->getId(), $productFound->getId());
        $this->assertEquals('Product 1', $productFound->getName());
        $this->assertEquals(100, $productFound->getPrice());
        $this->assertEquals(ProductInterface::DISABLED, $productFound->getStatus());
    }

    public function testGetThrowsExceptionWhenStatementNotPrepared(): void
    {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT id, name, price, status FROM products WHERE id = :id')
            ->willReturn(false);

        $this->expectExceptionMessage('Failed to prepare the statement.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->get(Uuid::uuid4()->toString());
    }

    public function testGetThrowsExceptionWhenExecuteFailed(): void
    {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT id, name, price, status FROM products WHERE id = :id')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with(['id' => 'invalid-id'])
            ->willReturn(false);

        $this->expectExceptionMessage('Product not found.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->get('invalid-id');
    }

    public function testGetThrowsExceptionWhenFetchFailed(): void
    {
        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('SELECT id, name, price, status FROM products WHERE id = :id')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with(['id' => 'valid-uuid'])
            ->willReturn(true);

        $this->pdoStatement->expects($this->once())
            ->method('fetch')
            ->with(PDO::FETCH_ASSOC)
            ->willReturn(false);

        $this->expectExceptionMessage('Failed to retrieve Product.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->get('valid-uuid');
    }

    public function testCreate(): void
    {
        $id = Uuid::uuid4()->toString();
        $product = new Product(null, 'New Product', 200);

        $this->uuidFactoryMock->expects($this->once())
            ->method('uuid4')
            ->willReturn(Uuid::fromString($id));

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO products (id, name, price, status) VALUES (:id, :name, :price, :status)')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([
                'id' => $id,
                'name' => 'New Product',
                'price' => 200,
                'status' => ProductInterface::DISABLED,
            ])
            ->willReturn(true);

        $this->productPersistence->create($product);

        $this->assertNotNull($product->getId());
        $this->assertEquals('New Product', $product->getName());
        $this->assertEquals(200, $product->getPrice());
        $this->assertEquals(ProductInterface::DISABLED, $product->getStatus());
    }

    public function testCreateThrowsExceptionWhenStatementNotPrepared(): void
    {
        $product = new Product(null, 'New Product', 200);

        $this->uuidFactoryMock->expects($this->once())
            ->method('uuid4')
            ->willReturn(Uuid::uuid4());

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO products (id, name, price, status) VALUES (:id, :name, :price, :status)')
            ->willReturn(false);

        $this->expectExceptionMessage('Failed to prepare the statement.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->create($product);
    }

    public function testCreateThrowsExceptionWhenExecuteFailed(): void
    {
        $uuid = Uuid::uuid4();
        $product = new Product(null, 'New Product', 200);

        $this->uuidFactoryMock->expects($this->once())
            ->method('uuid4')
            ->willReturn($uuid );

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('INSERT INTO products (id, name, price, status) VALUES (:id, :name, :price, :status)')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([
                'id' => $uuid->toString(),
                'name' => 'New Product',
                'price' => 200,
                'status' => ProductInterface::DISABLED,
            ])
            ->willReturn(false);

        $this->expectExceptionMessage('The last inserted ID is not the same as the product ID.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->create($product);
    }

    public function testSave(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::DISABLED
        );

        $product->setName('Product 2');
        $product->setPrice(200);
        $product->setStatus(ProductInterface::ENABLED);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET name = :name, price = :price, status = :status WHERE id = :id')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([
                'id' => $product->getId(),
                'name' => 'Product 2',
                'price' => 200,
                'status' => ProductInterface::ENABLED,
            ])
            ->willReturn(true);

        $this->productPersistence->save($product);
    }

    public function testSaveThrowExceptionWhenStatementNotPrepared(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::DISABLED
        );

        $product->setName('Product 2');
        $product->setPrice(200);
        $product->setStatus(ProductInterface::ENABLED);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET name = :name, price = :price, status = :status WHERE id = :id')
            ->willReturn(false);

        $this->expectExceptionMessage('Failed to prepare the statement.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->save($product);
    }

    public function testSaveThrowExceptionWhenExecuteFailed(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::DISABLED
        );

        $product->setName('Product 2');
        $product->setPrice(200);
        $product->setStatus(ProductInterface::ENABLED);

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET name = :name, price = :price, status = :status WHERE id = :id')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with([
                'id' => $product->getId(),
                'name' => 'Product 2',
                'price' => 200,
                'status' => ProductInterface::ENABLED,
            ])
            ->willReturn(false);

        $this->expectExceptionMessage('The product could not be saved.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->save($product);
    }

    public function testEnable(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::DISABLED
        );

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET status = :status WHERE id = :id')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with(['id' => $product->getId(), 'status' => ProductInterface::ENABLED])
            ->willReturn(true);

        $this->productPersistence->enable($product);

        $this->assertEquals(ProductInterface::ENABLED, $product->getStatus());
    }

    public function testEnableThrowsExceptionWhenStatementNotPrepared(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::DISABLED
        );

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET status = :status WHERE id = :id')
            ->willReturn(false);

        $this->expectExceptionMessage('Failed to prepare the statement.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->enable($product);
    }

    public function testEnableThrowsExceptionWhenExecuteFailed(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::DISABLED
        );

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET status = :status WHERE id = :id')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with(['id' => $product->getId(), 'status' => ProductInterface::ENABLED])
            ->willReturn(false);

        $this->expectExceptionMessage('The product could not be enabled.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->enable($product);
    }

    public function testDisable(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::ENABLED
        );

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET status = :status WHERE id = :id')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with(['id' => $product->getId(), 'status' => ProductInterface::DISABLED])
            ->willReturn(true);

        $this->productPersistence->disable($product);

        $this->assertEquals(ProductInterface::DISABLED, $product->getStatus());
    }

    public function testDisableThrowsExceptionWhenStatementNotPrepared(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::ENABLED
        );

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET status = :status WHERE id = :id')
            ->willReturn(false);

        $this->expectExceptionMessage('Failed to prepare the statement.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->disable($product);
    }

    public function testDisableThrowsExceptionWhenExecuteFailed(): void
    {
        $product = new Product(
            id: Uuid::uuid4()->toString(),
            name: 'Product 1',
            price: 100,
            status: ProductInterface::ENABLED
        );

        $this->pdo->expects($this->once())
            ->method('prepare')
            ->with('UPDATE products SET status = :status WHERE id = :id')
            ->willReturn($this->pdoStatement);

        $this->pdoStatement->expects($this->once())
            ->method('execute')
            ->with(['id' => $product->getId(), 'status' => ProductInterface::DISABLED])
            ->willReturn(false);

        $this->expectExceptionMessage('The product could not be disabled.');
        $this->expectException(RuntimeException::class);

        $this->productPersistence->disable($product);
    }
}
