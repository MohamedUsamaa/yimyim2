<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// GET /api/cart.php — view cart
if ($method === 'GET' && $action === '') {
    requireLogin();
    $userId = currentUserId();

    $stmt = $db->prepare("
        SELECT ci.id, ci.product_id, ci.quantity, p.name, p.price, p.image_path, p.type
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll();

    $total = 0;
    foreach ($items as &$item) {
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $total += $item['subtotal'];
    }

    jsonResponse(['items' => $items, 'total' => $total]);
}

// POST /api/cart.php?action=add
if ($method === 'POST' && $action === 'add') {
    requireLogin();
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = (int)($data['product_id'] ?? 0);
    $quantity = (int)($data['quantity'] ?? 1);
    $userId = currentUserId();

    if ($productId <= 0 || $quantity <= 0) {
        jsonResponse(['success' => false, 'message' => 'Invalid product or quantity'], 400);
    }

    // Check product exists
    $stmt = $db->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    if (!$stmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Product not found'], 404);
    }

    // Check if already in cart
    $stmt = $db->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $existing = $stmt->fetch();

    if ($existing) {
        $newQty = $existing['quantity'] + $quantity;
        $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $stmt->execute([$newQty, $existing['id']]);
    } else {
        $stmt = $db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $productId, $quantity]);
    }

    jsonResponse(['success' => true, 'message' => 'Item added to cart']);
}

// POST /api/cart.php?action=remove
if ($method === 'POST' && $action === 'remove') {
    requireLogin();
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = (int)($data['product_id'] ?? 0);
    $userId = currentUserId();

    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);

    jsonResponse(['success' => true, 'message' => 'Item removed']);
}

// POST /api/cart.php?action=update
if ($method === 'POST' && $action === 'update') {
    requireLogin();
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = (int)($data['product_id'] ?? 0);
    $delta = (int)($data['delta'] ?? 0);
    $userId = currentUserId();

    $stmt = $db->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $item = $stmt->fetch();

    if (!$item) {
        jsonResponse(['success' => false, 'message' => 'Item not in cart'], 404);
    }

    $newQty = max(1, $item['quantity'] + $delta);
    $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
    $stmt->execute([$newQty, $item['id']]);

    jsonResponse(['success' => true, 'new_quantity' => $newQty]);
}

jsonResponse(['error' => 'Invalid action'], 400);
