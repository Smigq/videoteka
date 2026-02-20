<?php
require_once 'database.php';
require_once '../templates/functions.php';
session_start();

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $db->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $db->query($query);

    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if(password_verify($password, $user['password'])) {
            if($user['is_active']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                logActivity('LOGIN', 'User logged in successfully');
                header("Location: ../index.php");
                exit();
            } else {
                $error = 'Please activate your account first.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../templates/header.php'; ?>
<div class="form-container">
    <h2>Login</h2>

    <?php if($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" class="btn">Login</button>
    </form>

    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>
<?php include '../templates/footer.php'; ?>
</body>
</html>