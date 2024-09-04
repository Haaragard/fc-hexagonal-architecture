<?php

require_once __DIR__ . '/../vendor/autoload.php';

$pdo = require __DIR__ . '/../config/pdo.php';

$productPersistence = new \App\Adapters\Database\Sqlite\ProductPersistence(
    $pdo,
    new \Ramsey\Uuid\UuidFactory()
);
$productService = new \App\Application\ProductService($productPersistence);
$productCli = new \App\Adapters\Cli\Product($productService);

$options = getopt('', ['action:', 'id::', 'name::', 'price::']);

$action = isset($options['action']) ? $options['action'] : '';
$productId = isset($options['id']) ? $options['id'] : '';
$productName = isset($options['name']) ? $options['name'] : '';
$productPrice = isset($options['name']) ? filter_var(
    $options['price'],
    FILTER_SANITIZE_NUMBER_INT,
    FILTER_NULL_ON_FAILURE
) : 0;

// Usage: 'php command.php --action=create --name=Product --price=100'
$productCli->run(
    $action,
    $productId,
    $productName,
    $productPrice
);
