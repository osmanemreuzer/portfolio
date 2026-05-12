<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

require_once '../db/connection.php';

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Server-side validation
$errors = [];
if (strlen($name)    < 2)  $errors[] = 'Name must be at least 2 characters.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email address.';
if (strlen($subject) < 3)  $errors[] = 'Subject must be at least 3 characters.';
if (strlen($message) < 10) $errors[] = 'Message must be at least 10 characters.';

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

$stmt = $conn->prepare(
    'INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)'
);
$stmt->bind_param('ssss', $name, $email, $subject, $message);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Message received!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save message. Try again.']);
}

$stmt->close();
$conn->close();
