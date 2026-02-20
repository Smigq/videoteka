<?php
session_start();
$message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $msg = $_POST['message'];
    $message = 'Thank you for contacting us!';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Contact - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../templates/header.php'; ?>

<div class="form-container">
    <h2>Contact Us</h2>

    <?php if($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Subject</label>
            <input type="text" name="subject" required>
        </div>

        <div class="form-group">
            <label>Message</label>
            <textarea name="message" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn">Send</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>
</body>
</html>