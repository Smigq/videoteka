<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

header('Content-Type: application/json');

if($_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'];

switch($action) {
    case 'delete':
        $memberId = (int)$data['id'];

        $db->query("DELETE FROM film_reviews WHERE user_id = $memberId");
        $db->query("DELETE FROM rentals WHERE user_id = $memberId");
        $db->query("DELETE FROM watchlist WHERE user_id = $memberId");

        $query = "DELETE FROM users WHERE id = $memberId";
        $result = $db->query($query);
        if($result && $memberData) {
        logActivity('DELETE_MEMBER', "Deleted member: " . $memberData['username']);
        }
        echo json_encode(['success' => $result]);
        break;
}
?>