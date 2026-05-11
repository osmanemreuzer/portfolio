<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header('Location: login.php'); exit; }

require_once '../db/connection.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: projects.php?error=' . urlencode('Invalid project ID.'));
    exit;
}

$stmt = $conn->prepare('DELETE FROM projects WHERE id = ?');
$stmt->bind_param('i', $id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    header('Location: projects.php?success=' . urlencode('Project deleted successfully.'));
} else {
    header('Location: projects.php?error=' . urlencode('Could not delete project.'));
}

$stmt->close();
$conn->close();
exit;
