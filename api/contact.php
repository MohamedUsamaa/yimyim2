<?php
require_once __DIR__ . '/config.php';
header('Content-Type: application/json');

// POST /api/contact.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$phone = trim($data['phone'] ?? '');
$message = trim($data['message'] ?? '');

if (!$name || !$email || !$message) {
    jsonResponse(['success' => false, 'message' => 'Name, email and message are required'], 400);
}

// Send email (using PHP mail or could be configured for SMTP)
$to = 'celeste142025@gmail.com';
$subject = 'New Contact Message from ' . $name;
$body = "Name: $name\nEmail: $email\nPhone: $phone\nMessage:\n$message";
$headers = "From: $email\r\nReply-To: $email\r\n";

$sent = @mail($to, $subject, $body, $headers);

jsonResponse([
    'success' => true,
    'message' => 'Message sent successfully!'
]);
