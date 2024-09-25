<?php

namespace App\Adapters\Web\Server;

use App\Adapters\Web\Request\Request;
use App\Adapters\Web\Response\Response;
use App\Adapters\Web\Route\Router;
use App\Application\ProductInterface;
use App\Application\ProductServiceInterface;
use RuntimeException;
use Throwable;

readonly class HttpServer
{
    private Request $request;
    private Response $response;

    public function __construct(
        private ProductServiceInterface $productService,
        ?Request $request = null,
        ?Response $response = null
    ) {
        $this->request = $request ?? new Request();
        $this->response = $response ?? new Response();
    }

    public function run(): void
    {
        try {
            $method = $this->request->getMethod();
            $uri = $this->request->getSanitizedUri();

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

        $this->response->json([
            'message' => 'Product created',
            'data' => [
                'product' => [
                    'id' => $product->getId(),
                ],
            ],
        ], 201);
    }

    private function getProduct(string $productId): void
    {
        $product = $this->findProduct($productId);

        $this->response->json([
            'message' => 'Product found',
            'data' => [
                'product' => [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $product->getPrice(),
                    'status' => $product->getStatus(),
                ],
            ],
        ]);
    }

    private function enableProduct(string $productId): void
    {
        $product = $this->findProduct($productId);
        $this->productService->enable($product);

        $this->response->json([
            'message' => 'Product enabled',
            'data' => [],
        ], 201);
    }

    private function disableProduct(string $productId): void
    {
        $product = $this->findProduct($productId);
        $this->productService->disable($product);

        $this->response->json([
            'message' => 'Product disabled',
            'data' => [],
        ], 201);
    }

    private function findProduct(string $productId): ProductInterface
    {
        return $this->productService->get($productId);
    }
}
