<?php

namespace App\Application;

interface ProductReaderInterface
{
    public function get(string $id): ProductInterface;
}
