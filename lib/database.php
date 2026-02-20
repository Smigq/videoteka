<?php
$server = 'localhost';
$username = 'root';
$password = '';
$database = 'videoteka';

$db = new MySQLi($server, $username, $password, $database);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}
?>