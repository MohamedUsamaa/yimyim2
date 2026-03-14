<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

// POST /api/promo.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

requireLogin();
$data = json_decode(file_get_contents('php://input'), true);
$code = strtoupper(trim($data['code'] ?? ''));

if (!$code) {
    jsonResponse(['success' => false, 'message' => 'Code is required']);
}

$stmt = $db->prepare("SELECT * FROM promo_codes WHERE code = ?");
$stmt->execute([$code]);
$promo = $stmt->fetch();

if (!$promo) {
    jsonResponse(['success' => false, 'message' => 'Invalid code']);
}

$now = date('Y-m-d H:i:s');
if ($now < $promo['valid_from'] || $now > $promo['valid_to']) {
    jsonResponse(['success' => false, 'message' => 'Code expired']);
}

// Check usage limit
$stmt = $db->prepare("SELECT COUNT(*) FROM promo_code_usages WHERE promo_id = ?");
$stmt->execute([$promo['id']]);
$usageCount = $stmt->fetchColumn();
if ($usageCount >= $promo['usage_limit']) {
    jsonResponse(['success' => false, 'message' => 'Promo usage limit reached']);
}

// Check if user already used it
$stmt = $db->prepare("SELECT id FROM promo_code_usages WHERE promo_id = ? AND user_id = ?");
$stmt->execute([$promo['id'], currentUserId()]);
if ($stmt->fetch()) {
    jsonResponse(['success' => false, 'message' => 'You have already used this code']);
}

// Store in session
$_SESSION['promo_code'] = $promo['code'];
$_SESSION['promo_discount'] = $promo['discount_percentage'];
$_SESSION['promo_id'] = $promo['id'];

jsonResponse([
    'success' => true,
    'discount' => $promo['discount_percentage'],
    'message' => $promo['discount_percentage'] . '% discount applied!'
]);
