<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'];

switch($action) {
    case 'rent':
        $userId = $_SESSION['user_id'];
        $filmId = (int)$data['film_id'];

        $query = "SELECT is_available FROM films WHERE id = $filmId";
        $result = $db->query($query);
        $film = $result->fetch_assoc();

        if(!$film || !$film['is_available']) {
            echo json_encode(['success' => false, 'message' => 'Film not available']);
            exit;
        }

        $query = "SELECT id FROM rentals WHERE user_id = $userId AND film_id = $filmId AND status = 'active'";
        $result = $db->query($query);

        if($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'You already have this film rented']);
        } else {
            $query = "INSERT INTO rentals (user_id, film_id, rental_date, status) 
                      VALUES ($userId, $filmId, NOW(), 'active')";
            $result = $db->query($query);

            if($result) {
                $db->query("UPDATE films SET is_available = 0 WHERE id = $filmId");
                $filmResult = $db->query($filmQuery);
                $filmData = $filmResult->fetch_assoc();
                logActivity('RENT_FILM', "Rented: " . $filmData['title'], $filmId);
                echo json_encode(['success' => true, 'message' => 'Film rented successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error renting film']);
            }
        }
        break;

    case 'return':
        $userId = $_SESSION['user_id'];
        $rentalId = (int)$data['rental_id'];

        $query = "SELECT film_id FROM rentals WHERE id = $rentalId AND user_id = $userId AND status = 'active'";
        $result = $db->query($query);
        $rental = $result->fetch_assoc();

        if($rental) {
            $db->query("UPDATE rentals SET status = 'returned', return_date = NOW(), returned_by = 'user' WHERE id = $rentalId");
            $db->query("UPDATE films SET is_available = 1 WHERE id = " . $rental['film_id']);
            $filmQuery = "SELECT title FROM films WHERE id = " . $rental['film_id'];
            $filmResult = $db->query($filmQuery);
            $filmData = $filmResult->fetch_assoc();
            logActivity('RETURN_FILM', "Returned: " . $filmData['title'], $rental['film_id']);
            echo json_encode(['success' => true, 'message' => 'Film returned successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Rental not found or already returned']);
        }
        break;

    case 'return_admin':
        if($_SESSION['role'] != 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $rentalId = (int)$data['rental_id'];

        $query = "SELECT film_id FROM rentals WHERE id = $rentalId AND status = 'active'";
        $result = $db->query($query);
        $rental = $result->fetch_assoc();

        if($rental) {
            $db->query("UPDATE rentals SET status = 'returned', return_date = NOW(), returned_by = 'admin' WHERE id = $rentalId");
            $db->query("UPDATE films SET is_available = 1 WHERE id = " . $rental['film_id']);
            $filmQuery = "SELECT title FROM films WHERE id = " . $rental['film_id'];
            $filmResult = $db->query($filmQuery);
            $filmData = $filmResult->fetch_assoc();
            logActivity('ADMIN_RETURN', "Admin returned: " . $filmData['title'], $rental['film_id']);
            echo json_encode(['success' => true, 'message' => 'Film marked as returned by admin']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Rental not found or already returned']);
        }
        break;
}
?>