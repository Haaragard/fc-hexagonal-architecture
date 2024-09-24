<?php

require_once __DIR__ . '/../bootstrap/bootstrap.php';

$pdo = require BASE_DIR . '/config/pdo.php';


$productPersistence = new \App\Adapters\Database\Sqlite\ProductPersistence(
    $pdo,
    new \Ramsey\Uuid\UuidFactory()
);
$productService = new \App\Application\ProductService($productPersistence);
$server = new \App\Adapters\Web\Server\HttpServer($productService);

$server->run();
