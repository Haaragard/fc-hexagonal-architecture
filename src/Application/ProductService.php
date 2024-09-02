<?php

namespace App\Application;

readonly class ProductService implements ProductServiceInterface
{
    public function __construct(
        private ProductPersistenceInterface $productPersistence
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get(string $id): ProductInterface
    {
        return $this->productPersistence->get($id);
    }

    /*
     * @inheritDoc
     */
    public function create(string $name, int $price): ProductInterface
    {
        $product = new Product(
            id: null,
            name: $name,
            price: $price
        );

        $this->productPersistence->create($product);

        return $product;
    }

    /*
     * @inheritDoc
     */
    public function enable(ProductInterface $product): void
    {
        $this->productPersistence->enable($product);
    }

    /*
     * @inheritDoc
     */
    public function disable(ProductInterface $product): void
    {
        $this->productPersistence->disable($product);
    }
}
