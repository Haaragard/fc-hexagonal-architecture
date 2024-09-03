<?php

namespace App\Application;

use Throwable;

interface ProductServiceInterface
{
    /**
     * @throws Throwable
     */
    public function get(string $id): ProductInterface;

    /**
     * @throws Throwable
     */
    public function create(string $name, int $price): ProductInterface;

    /**
     * @throws Throwable
     */
    public function save(ProductInterface $product): void;

    /**
     * @throws Throwable
     */
    public function enable(ProductInterface $product): void;

    /**
     * @throws Throwable
     */
    public function disable(ProductInterface $product): void;
}
