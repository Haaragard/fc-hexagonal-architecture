<?php

namespace App\Adapters\Database\Sqlite;

use App\Application\Product;
use App\Application\ProductInterface;
use App\Application\ProductPersistenceInterface;
use PDO;
use Ramsey\Uuid\UuidFactoryInterface;
use RuntimeException;

readonly class ProductPersistence implements ProductPersistenceInterface
{
    public function __construct(
        private PDO $connection,
        private UuidFactoryInterface $uuidFactory
    ) {
    }

    public function get(string $id): ProductInterface
    {
        $statement = $this->connection->prepare('SELECT id, name, price, status FROM products WHERE id = :id');
        if ($statement === false) {
            throw new RuntimeException('Failed to prepare the statement.');
        }

        $result = $statement->execute(['id' => $id]);
        if (! $result) {
            throw new RuntimeException('Product not found.');
        }

        /**
         * @var int[]|string[] $result
         */
        $result = $statement->fetch(mode: PDO::FETCH_ASSOC);
        if ($result === false) {
            throw new RuntimeException('Failed to retrieve Product.');
        }

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

        $statement = $this->connection
            ->prepare('INSERT INTO products (id, name, price, status) VALUES (:id, :name, :price, :status)');
        if ($statement === false) {
            throw new RuntimeException('Failed to prepare the statement.');
        }

        $result = $statement->execute([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'status' => $product->getStatus(),
        ]);
        if (! $result) {
            throw new RuntimeException('The last inserted ID is not the same as the product ID.');
        }
    }

    public function save(ProductInterface $product): void
    {
        $statement = $this->connection
            ->prepare('UPDATE products SET name = :name, price = :price, status = :status WHERE id = :id');
        if ($statement === false) {
            throw new RuntimeException('Failed to prepare the statement.');
        }

        $result = $statement->execute([
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'status' => $product->getStatus(),
        ]);
        if (! $result) {
            throw new RuntimeException('The product could not be saved.');
        }
    }

    public function enable(ProductInterface $product): void
    {
        $statement = $this->connection->prepare('UPDATE products SET status = :status WHERE id = :id');
        if ($statement === false) {
            throw new RuntimeException('Failed to prepare the statement.');
        }

        $result = $statement->execute(['id' => $product->getId(), 'status' => ProductInterface::ENABLED]);
        if (! $result) {
            throw new RuntimeException('The product could not be enabled.');
        }
    }

    public function disable(ProductInterface $product): void
    {
        $statement = $this->connection->prepare('UPDATE products SET status = :status WHERE id = :id');
        if ($statement === false) {
            throw new RuntimeException('Failed to prepare the statement.');
        }

        $result = $statement->execute(['id' => $product->getId(), 'status' => ProductInterface::DISABLED]);
        if (! $result) {
            throw new RuntimeException('The product could not be disabled.');
        }
    }
}
