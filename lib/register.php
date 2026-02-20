<?php
require_once 'database.php';
require_once '../templates/functions.php';
session_start();

$error = '';
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $db->real_escape_string($_POST['username']);
    $email = $db->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    $recaptcha = $_POST['g-recaptcha-response'];
    $secret = 'YOUR_RECAPTCHA_SECRET_KEY';
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha");
    $captcha_success = json_decode($verify);

    if(!$captcha_success->success) {
        $error = 'Please complete the CAPTCHA.';
    } else if(strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } else if($password != $confirm) {
        $error = 'Passwords do not match.';
    } else if(strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $query = "SELECT id FROM users WHERE username = '$username' OR email = '$email'";
        $result = $db->query($query);

        if($result->num_rows > 0) {
            $error = 'Username or email already exists.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(32));

            $query = "INSERT INTO users (username, email, password, activation_token, is_active) 
                      VALUES ('$username', '$email', '$hashedPassword', '$token', 0)";

            if($db->query($query)) {
                $success = true;
                $_SESSION['activation_token'] = $token;
                logActivity('REGISTER', "New user registered: $username");
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<?php include '../templates/header.php'; ?>
<div class="form-container">
    <?php if(!$success): ?>
        <h2>Register</h2>

        <?php if($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="username" required>
                <div id="username-feedback" class="username-feedback"></div>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <div class="g-recaptcha" data-sitekey="YOUR_RECAPTCHA_SITE_KEY"></div>
            </div>

            <button type="submit" class="btn">Register</button>
        </form>

        <p>Already have an account? <a href="login.php">Login here</a></p>
    <?php else: ?>
        <h2>Registration Successful!</h2>
        <div class="alert alert-success">
            <p>Your account has been created!</p>
            <a href="activate.php?token=<?php echo $_SESSION['activation_token']; ?>">Click here to activate</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../templates/footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>