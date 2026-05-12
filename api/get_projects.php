<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

require_once '../db/connection.php';

$stmt = $conn->prepare(
    'SELECT id, title, description, tech_stack, image_url, github_url, live_url, created_at
     FROM projects ORDER BY created_at DESC'
);
$stmt->execute();
$result = $stmt->get_result();

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($projects);
