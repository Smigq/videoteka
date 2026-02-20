<?php
require_once 'database.php';

$message = '';
$success = false;

if(isset($_GET['token'])) {
    $token = $db->real_escape_string($_GET['token']);

    $query = "SELECT id FROM users WHERE activation_token = '$token' AND is_active = 0";
    $result = $db->query($query);

    if($result->num_rows > 0) {
        $query = "UPDATE users SET is_active = 1, activation_token = NULL WHERE activation_token = '$token'";
        if($db->query($query)) {
            $success = true;
            $message = 'Account activated successfully!';
        }
    } else {
        $message = 'Invalid activation token or account already activated.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Activate Account</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="form-container">
    <h2>Account Activation</h2>

    <?php if($success): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
            <p><a href="login.php">Go to Login</a></p>
        </div>
    <?php else: ?>
        <div class="alert alert-error">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>