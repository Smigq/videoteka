<?php
session_start();
if(isset($_SESSION['user_id'])) {
    require_once 'database.php';
    require_once '../templates/functions.php';
    logActivity('LOGOUT', 'User logged out');
}
session_destroy();
header("Location: ../index.php");
exit();
?><?php
