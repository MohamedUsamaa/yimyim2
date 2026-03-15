<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

// GET /api/products.php — list all products
// GET /api/products.php?id=5 — single product
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = $_GET['id'] ?? null;

    if ($id) {
        $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([(int)$id]);
        $product = $stmt->fetch();
        if ($product) {
            $product['id'] = (int)$product['id'];
            $product['price'] = (float)$product['price'];
            jsonResponse($product);
        } else {
            jsonResponse(['error' => 'Product not found'], 404);
        }
    } else {
        $products = $db->query("SELECT * FROM products")->fetchAll();
        foreach ($products as &$p) {
            $p['id'] = (int)$p['id'];
            $p['price'] = (float)$p['price'];
        }
        jsonResponse($products);
    }
}

jsonResponse(['error' => 'Method not allowed'], 405);
