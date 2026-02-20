<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'];

switch($action) {
    case 'add':
        if($_SESSION['role'] != 'admin') {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $title = $db->real_escape_string($data['title']);
        $year = (int)$data['year'];
        $genre = $db->real_escape_string($data['genre']);
        $director = $db->real_escape_string($data['director']);
        $actors = isset($data['actors']) ? $db->real_escape_string($data['actors']) : '';
        $description = $db->real_escape_string($data['description']);
        $rating = isset($data['rating']) ? (float)$data['rating'] : 0;
        $poster = isset($data['poster']) ? $db->real_escape_string($data['poster']) : '';
        $imdb_id = isset($data['imdb_id']) ? $db->real_escape_string($data['imdb_id']) : '';

        if($imdb_id) {
            $check = "SELECT id FROM films WHERE imdb_id = '$imdb_id'";
            $result = $db->query($check);
            if($result->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Film already exists in database']);
                exit;
            }
        }

        $result = $db->query("SELECT MAX(id) as max_id FROM films");
        $row = $result->fetch_assoc();
        $nextId = ($row['max_id'] ?? 0) + 1;
        $filmCode = 'FILM-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        $query = "INSERT INTO films (title, year, genre, director, actors, description, rating, poster, imdb_id, film_code, is_available, is_slider, is_featured, is_popular) 
                  VALUES ('$title', $year, '$genre', '$director', '$actors', '$description', $rating, '$poster', '$imdb_id', '$filmCode', 1, 0, 0, 0)";

        $result = $db->query($query);

        if($result) {
            logActivity('ADD_FILM', "Added new film: $title", $db->insert_id);
            echo json_encode(['success' => true, 'message' => 'Film added successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $db->error]);
        }
        break;

    case 'delete':
        if($_SESSION['role'] != 'admin') {
        echo json_encode(['success' => false]);
        exit;
        }

        $filmId = (int)$data['film_id'];

        $filmQuery = "SELECT title FROM films WHERE id = $filmId";
        $filmResult = $db->query($filmQuery);
        $filmData = $filmResult->fetch_assoc();
    
        $db->query("DELETE FROM film_reviews WHERE film_id = $filmId");
        $db->query("DELETE FROM rentals WHERE film_id = $filmId");
        $db->query("DELETE FROM watchlist WHERE film_id = $filmId");

        $query = "DELETE FROM films WHERE id = $filmId";
        $result = $db->query($query);

        if($result && $filmData) {
        logActivity('DELETE_FILM', "Deleted film: " . $filmData['title'], $filmId);
        }
        echo json_encode(['success' => (bool)$result]);
    break;

    case 'watchlist':
        if(!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Please login']);
            exit;
        }

        $userId = $_SESSION['user_id'];
        $filmId = (int)$data['film_id'];

        $query = "SELECT id FROM watchlist WHERE user_id = $userId AND film_id = $filmId";
        $result = $db->query($query);

        if($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Already in watchlist']);
        } else {
            $query = "INSERT INTO watchlist (user_id, film_id) VALUES ($userId, $filmId)";
            $result = $db->query($query);
            if($result) {
            $filmQuery = "SELECT title FROM films WHERE id = $filmId";
            $filmResult = $db->query($filmQuery);
            $filmData = $filmResult->fetch_assoc();
            logActivity('ADD_WATCHLIST', "Added to watchlist: " . $filmData['title'], $filmId);
        }
    
            echo json_encode(['success' => $result, 'message' => 'Added to watchlist']);
        }
        break;

    case 'remove_watchlist':
    if(!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Please login']);
        exit;
    }

    $userId = $_SESSION['user_id'];
    $filmId = (int)$data['film_id'];

    $query = "SELECT id FROM watchlist WHERE user_id = $userId AND film_id = $filmId";
    $result = $db->query($query);

    if($result->num_rows > 0) {
        $query = "DELETE FROM watchlist WHERE user_id = $userId AND film_id = $filmId";
        $result = $db->query($query);
        
        if($result) {
            $filmQuery = "SELECT title FROM films WHERE id = $filmId";
            $filmResult = $db->query($filmQuery);
            $filmData = $filmResult->fetch_assoc();
            logActivity('REMOVE_WATCHLIST', "Removed from watchlist: " . $filmData['title'], $filmId);
            
            echo json_encode(['success' => true, 'message' => 'Removed from watchlist']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error removing from watchlist']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Not in watchlist']);
    }
    break;
}
?>