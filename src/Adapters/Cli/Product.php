<?php

namespace App\Adapters\Cli;

use App\Application\ProductServiceInterface;

readonly class Product
{
    public function __construct(private ProductServiceInterface $productService)
    {
    }

    public function run(
        string $action,
        string $productId,
        string $productName,
        int $productPrice
    ): void {
        switch ($action) {
            case 'create': {
                $this->create($productName, $productPrice);
                break;
            }
            case 'get': {
                $this->get($productId);
                break;
            }
            case 'enable': {
                $this->enable($productId);
                break;
            }
            case 'disable': {
                $this->disable($productId);
                break;
            }
            default:
                echo 'Invalid action';
        }
    }

    private function create(string $productName, int $productPrice): void
    {
        $product = $this->productService->create($productName, $productPrice);

        echo sprintf('Product created with ID: %s', $product->getId()) . PHP_EOL;
    }

    private function get(string $productId): void
    {
        $product = $this->productService->get($productId);

        echo sprintf(
            'Product with ID: %s, Name: %s, Price: %d and Status: %s',
            $product->getId(),
            $product->getName(),
            $product->getPrice(),
            $product->getStatus()
        ) . PHP_EOL;
    }

    private function enable(string $productId): void
    {
        $product = $this->productService->get($productId);

        $this->productService->enable($product);

        echo sprintf('Product with ID %s enabled', $productId) . PHP_EOL;
    }

    private function disable(string $productId): void
    {
        $product = $this->productService->get($productId);

        $this->productService->disable($product);

        echo sprintf('Product with ID %s disabled', $productId) . PHP_EOL;
    }
}
