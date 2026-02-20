<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

if($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$rentalId = (int)$_GET['id'];

$query = "SELECT r.film_id, f.title, f.film_code, u.username 
          FROM rentals r 
          JOIN films f ON r.film_id = f.id 
          JOIN users u ON r.user_id = u.id 
          WHERE r.id = $rentalId";
$result = $db->query($query);
$rental = $result->fetch_assoc();

if($rental) {
    $db->query("UPDATE rentals SET status = 'returned', return_date = NOW() WHERE id = $rentalId");
    $db->query("UPDATE films SET is_available = 1 WHERE id = " . $rental['film_id']);
    logActivity('ADMIN_RETURN',"Admin returned film: {$rental['title']} (rented by: {$rental['username']})",$rental['film_id']);
}

header("Location: admin.php");
?>