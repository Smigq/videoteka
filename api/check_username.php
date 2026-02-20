<?php
require_once '../lib/database.php';

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['username'])) {
    echo json_encode(['available' => false, 'error' => 'No username provided']);
    exit;
}

$username = $data['username'];

if (strlen($username) < 3) {
    echo json_encode(['available' => false, 'message' => 'Username too short']);
    exit;
}

$username = $db->real_escape_string($username);

$query = "SELECT id FROM users WHERE username = '$username'";
$result = $db->query($query);

if (!$result) {
    echo json_encode(['available' => false, 'error' => 'Database error']);
    exit;
}

$available = $result->num_rows == 0;

echo json_encode(['available' => $available]);
?>