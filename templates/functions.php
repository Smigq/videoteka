<?php
function getDB() {
    global $db;
    return $db;
}

function isLoggedIn() {
    if(isset($_SESSION['user_id'])) {
        return true;
    }
    return false;
}

function isAdmin() {
    if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
        return true;
    }
    return false;
}

function clean($data) {
    $database = getDB();
    return $database->real_escape_string(trim($data));
}
function logActivity($action, $description = '', $filmId = null) {
    global $db;
    
    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Anonymous';
    $userRole = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
    
    $action = $db->real_escape_string($action);
    $description = $db->real_escape_string($description);
    $username = $db->real_escape_string($username);
    $userRole = $db->real_escape_string($userRole);
    
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $ipAddress = $db->real_escape_string($ipAddress);
    
    $query = "INSERT INTO activity_logs 
              (user_id, username, user_role, action, description, film_id, ip_address, created_at) 
              VALUES 
              (" . ($userId ? $userId : 'NULL') . ", 
               '$username', 
               '$userRole', 
               '$action', 
               '$description', 
               " . ($filmId ? $filmId : 'NULL') . ", 
               '$ipAddress', 
               NOW())";
    
    $db->query($query);
}
?>