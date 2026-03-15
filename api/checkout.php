<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/mailer.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// POST /api/checkout.php?action=submit
if ($method === 'POST' && $action === 'submit') {
    requireLogin();
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = currentUserId();

    $firstname = trim($data['firstname'] ?? '');
    $lastname = trim($data['lastname'] ?? '');
    $address = trim($data['address'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');

    if (!$firstname || !$lastname || !$email || !$phone) {
        jsonResponse(['success' => false, 'message' => 'Required fields missing'], 400);
    }

    // Get cart items
    $stmt = $db->prepare("
        SELECT ci.product_id, ci.quantity, p.name, p.price
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll();

    if (empty($cartItems)) {
        jsonResponse(['success' => false, 'message' => 'Cart is empty'], 400);
    }

    $subtotal = 0;
    foreach ($cartItems as &$item) {
        $item['product_id'] = (int)$item['product_id'];
        $item['quantity'] = (int)$item['quantity'];
        $item['price'] = (float)$item['price'];
        $subtotal += $item['price'] * $item['quantity'];
    }

    $shipping = 85.00;
    $nameFee = !empty($_SESSION['name_on_bottle']) ? 50.00 : 0;
    $discount = 0;

    // Apply promo if stored in session
    if (!empty($_SESSION['promo_discount'])) {
        $discount = round($subtotal * $_SESSION['promo_discount'] / 100, 2);
    }

    $total = $subtotal + $shipping + $nameFee - $discount;

    // Create order
    $stmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'Processing')");
    $stmt->execute([$userId, $total]);
    $orderId = $db->lastInsertId();

    // Create order items
    $stmt = $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, total_price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price'] * $item['quantity']]);
    }

    // Record promo usage if applicable
    if (!empty($_SESSION['promo_id'])) {
        $promoStmt = $db->prepare("INSERT INTO promo_code_usages (promo_id, user_id, order_id) VALUES (?, ?, ?)");
        $promoStmt->execute([$_SESSION['promo_id'], $userId, $orderId]);
    }

    // Clear cart
    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Clear session promo
    unset($_SESSION['promo_code'], $_SESSION['promo_discount'], $_SESSION['promo_id'], $_SESSION['name_on_bottle']);

    // Send order confirmation email
    $customerName = $firstname . ' ' . $lastname;
    sendOrderEmail($email, $customerName, $orderId, $cartItems, $subtotal, $shipping, $discount, $total);

    // Also send a copy to the store owner
    sendOrderEmail('celeste142025@gmail.com', 'YIMYIM Store', $orderId, $cartItems, $subtotal, $shipping, $discount, $total);

    jsonResponse([
        'success' => true,
        'order_id' => $orderId,
        'total' => $total,
        'message' => 'Order placed successfully'
    ], 201);
}

// GET /api/checkout.php?action=summary
if ($method === 'GET' && $action === 'summary') {
    requireLogin();
    $userId = currentUserId();

    $stmt = $db->prepare("
        SELECT ci.product_id, ci.quantity, p.name, p.price, p.image_path, p.type
        FROM cart_items ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$userId]);
    $items = $stmt->fetchAll();

    $subtotal = 0;
    foreach ($items as &$item) {
        $item['product_id'] = (int)$item['product_id'];
        $item['quantity'] = (int)$item['quantity'];
        $item['price'] = (float)$item['price'];
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $subtotal += $item['subtotal'];
    }

    $shipping = 85.00;
    $nameFee = !empty($_SESSION['name_on_bottle']) ? 50.00 : 0;
    $discountPercent = $_SESSION['promo_discount'] ?? 0;
    $discount = round($subtotal * $discountPercent / 100, 2);
    $total = $subtotal + $shipping + $nameFee - $discount;

    jsonResponse([
        'items' => $items,
        'subtotal' => $subtotal,
        'shipping' => $shipping,
        'name_fee' => $nameFee,
        'discount' => $discount,
        'total' => $total
    ]);
}

jsonResponse(['error' => 'Invalid action'], 400);
