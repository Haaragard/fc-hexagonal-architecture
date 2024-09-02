<?php

namespace App\Application;

use Exception;

interface ProductServiceInterface
{
    /**
     * @throws Exception
     */
    public function get(string $id): ProductInterface;

    /**
     * @throws Exception
     */
    public function create(string $name, int $price): ProductInterface;

    /**
     * @throws Exception
     */
    public function enable(ProductInterface $product): void;

    /**
     * @throws Exception
     */
    public function disable(ProductInterface $product): void;
}
