<?php

namespace App\Adapters\Web\Server;

use App\Adapters\Web\Request\Request;
use App\Adapters\Web\Route\Router;
use App\Application\ProductInterface;
use App\Application\ProductServiceInterface;
use RuntimeException;
use Throwable;

readonly class HttpServer
{
    private Request $request;

    public function __construct(
        private ProductServiceInterface $productService
    ) {
        $this->request = new Request();
    }

    public function run(): void
    {
        try {
            $method = $this->request->getMethod();
            $uri = $this->request->sanitizedUri();

            $executableMethod = Router::getExecutableMethod($method, $uri);

            switch ($executableMethod) {
                case 'getProduct': {
                    $this->getProduct($this->request->getParam('id'));
                    break;
                }
                case 'createProduct': {
                    $this->createProduct(
                        productName: $this->request->getParam('name'),
                        productPrice: $this->request->getParam('price')
                    );
                    break;
                }
                case 'enableProduct': {
                    $this->enableProduct($this->request->getParam('id'));
                    break;
                }
                case 'disableProduct': {
                    $this->disableProduct($this->request->getParam('id'));
                    break;
                }
                default: {
                    throw new RuntimeException('Feature does not exists.');
                }
            }
        } catch (Throwable $throwable) {
            echo $throwable->getMessage() . PHP_EOL;
        }
    }

    private function createProduct(string $productName, int $productPrice): void
    {
        $product = $this->productService->create($productName, $productPrice);

        echo sprintf('Product created with ID: %s', $product->getId()) . PHP_EOL;
    }

    private function findProduct(string $productId): ProductInterface
    {
        return $this->productService->get($productId);
    }

    private function getProduct(string $productId): void
    {
        $product = $this->findProduct($productId);

        echo sprintf(
            'Product with ID: %s, Name: %s, Price: %d and Status: %s',
            $product->getId(),
            $product->getName(),
            $product->getPrice(),
            $product->getStatus()
        ) . PHP_EOL;
    }

    private function enableProduct(string $productId): void
    {
        $product = $this->findProduct($this->request->getParam('id'));
        $this->productService->enable($product);

        echo sprintf('Product with ID: %s enabled', $productId) . PHP_EOL;
    }

    private function disableProduct(string $productId): void
    {
        $product = $this->findProduct($this->request->getParam('id'));
        $this->productService->disable($product);

        echo sprintf('Product with ID: %s disabled', $productId) . PHP_EOL;
    }
}
