<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Origin: *');

require_once '../db/connection.php';

$result = $conn->query(
    'SELECT id, title, description, tech_stack, image_url, github_url, live_url, created_at
     FROM projects ORDER BY created_at DESC'
);

if ($result === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed: ' . $conn->error]);
    $conn->close();
    exit;
}

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

$conn->close();
echo json_encode($projects);
