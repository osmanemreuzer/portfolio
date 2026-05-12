<?php
require_once 'db/connection.php';

$username = 'admin';
$password = 'emre123';
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $conn->prepare('INSERT INTO admin_users (username, password_hash) VALUES (?, ?) ON DUPLICATE KEY UPDATE password_hash = ?');
$stmt->bind_param('sss', $username, $hash, $hash);

if ($stmt->execute()) {
    echo 'Admin hesabi olusturuldu. Bu dosyayi silin: setup.php';
} else {
    echo 'Hata: ' . $conn->error;
}
$stmt->close();
$conn->close();
