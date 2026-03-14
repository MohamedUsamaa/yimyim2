<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// POST /api/auth.php?action=register
if ($method === 'POST' && $action === 'register') {
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$name || !$email || !$password) {
        jsonResponse(['success' => false, 'message' => 'All fields are required'], 400);
    }

    $existing = $db->prepare("SELECT id FROM users WHERE email = ?");
    $existing->execute([$email]);
    if ($existing->fetch()) {
        jsonResponse(['success' => false, 'message' => 'Email already exists']);
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $hash]);

    $userId = $db->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    jsonResponse(['success' => true, 'user' => ['id' => $userId, 'name' => $name, 'email' => $email]]);
}

// POST /api/auth.php?action=login
if ($method === 'POST' && $action === 'login') {
    $data = json_decode(file_get_contents('php://input'), true);
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        jsonResponse(['success' => false, 'message' => 'Email and password are required'], 400);
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        jsonResponse(['success' => false, 'message' => 'Invalid email or password']);
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];

    jsonResponse(['success' => true, 'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']]]);
}

// GET /api/auth.php?action=logout
if ($action === 'logout') {
    session_destroy();
    jsonResponse(['success' => true, 'message' => 'Logged out']);
}

// GET /api/auth.php?action=session
if ($method === 'GET' && $action === 'session') {
    if (isLoggedIn()) {
        jsonResponse([
            'loggedIn' => true,
            'user' => [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'],
                'email' => $_SESSION['user_email']
            ]
        ]);
    } else {
        jsonResponse(['loggedIn' => false]);
    }
}

// GET /api/auth.php?action=profile
if ($method === 'GET' && $action === 'profile') {
    requireLogin();
    $stmt = $db->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->execute([currentUserId()]);
    $user = $stmt->fetch();
    jsonResponse(['success' => true, 'user' => $user]);
}

// POST /api/auth.php?action=update_profile
if ($method === 'POST' && $action === 'update_profile') {
    requireLogin();
    $data = json_decode(file_get_contents('php://input'), true);
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');

    if (!$name || !$email) {
        jsonResponse(['success' => false, 'message' => 'Name and email are required'], 400);
    }

    $stmt = $db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $email, currentUserId()]);

    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    jsonResponse(['success' => true, 'message' => 'Profile updated']);
}

jsonResponse(['error' => 'Invalid action'], 400);
