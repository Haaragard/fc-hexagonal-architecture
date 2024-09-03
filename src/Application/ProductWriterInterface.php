<?php

namespace App\Application;

use Throwable;

interface ProductWriterInterface
{
    /**
     * @throws Throwable
     */
    public function create(ProductInterface $product): void;

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
