<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// GET /api/orders.php — list current user's orders
if ($method === 'GET') {
    requireLogin();
    $userId = currentUserId();

    $stmt = $db->prepare("
        SELECT o.id, o.order_date, o.total_amount, o.status
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ");
    $stmt->execute([$userId]);
    $orders = $stmt->fetchAll();

    // Get items for each order
    foreach ($orders as &$order) {
        $stmt = $db->prepare("
            SELECT oi.quantity, oi.total_price, p.name, p.image_path
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order['id']]);
        $order['items'] = $stmt->fetchAll();
    }

    jsonResponse($orders);
}

jsonResponse(['error' => 'Method not allowed'], 405);
