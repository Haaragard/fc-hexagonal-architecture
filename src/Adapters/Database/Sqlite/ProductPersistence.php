<?php

namespace App\Adapters\Database\Sqlite;

use App\Application\Product;
use App\Application\ProductInterface;
use PDO;
use Ramsey\Uuid\UuidFactoryInterface;
use RuntimeException;

readonly class ProductPersistence
{
    public function __construct(
        private PDO $connection,
        private UuidFactoryInterface $uuidFactory
    ) {
    }

    public function get(string $id): ProductInterface
    {
        $statement = $this->connection->prepare('SELECT id, name, price, status FROM products WHERE id = :id');
        $statement->execute(['id' => $id]);

        /**
         * @var int[]|string[] $result
         */
        $result = $statement->fetch(mode: PDO::FETCH_ASSOC);

        return new Product(
            id: $result['id'],
            name: $result['name'],
            price: $result['price'],
            status: $result['status']
        );
    }

    public function create(ProductInterface $product): void
    {
        $id = $this->uuidFactory->uuid4()->toString();
        $product->setId($id);

        $statement = $this->connection->prepare('INSERT INTO products (id, name, price, status) VALUES (:id, :name, :price, :status)');
        $statement->execute([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'status' => $product->getStatus(),
        ]);

        $lastInsertedId = $this->connection->lastInsertId('products');
        if (! $lastInsertedId) {
            throw new RuntimeException('The last inserted ID is not the same as the product ID.');
        }
    }

    public function enable(ProductInterface $product): void
    {
        $product->enable();

        $statement = $this->connection->prepare('UPDATE products SET status = :status WHERE id = :id');
        $statement->execute(['id' => $product->getId(), 'status' => ProductInterface::ENABLED]);
    }

    public function disable(ProductInterface $product): void
    {
        $product->disable();

        $statement = $this->connection->prepare('UPDATE products SET status = :status WHERE id = :id');
        $statement->execute(['id' => $product->getId(), 'status' => ProductInterface::DISABLED]);
    }
}
