<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

if(!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$memberId = (int)$_GET['id'];
$message = '';

$query = "SELECT * FROM users WHERE id = $memberId";
$result = $db->query($query);
$member = $result->fetch_assoc();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $db->real_escape_string($_POST['username']);
    $email = $db->real_escape_string($_POST['email']);
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    $query = "UPDATE users SET username='$username', email='$email', is_active=$isActive WHERE id=$memberId";

    if($db->query($query)) {
        $message = 'Member updated!';
        logActivity('EDIT_MEMBER', "Updated member: $username");
        $query = "SELECT * FROM users WHERE id = $memberId";
        $result = $db->query($query);
        $member = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Member - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="form-container">
    <h2>Edit Member</h2>

    <?php if($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo $member['username']; ?>" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo $member['email']; ?>" required>
        </div>

        <div class="form-group">
            <label>
                <input type="checkbox" name="is_active" <?php echo $member['is_active'] ? 'checked' : ''; ?>>
                Active
            </label>
        </div>

        <button type="submit" class="btn">Update</button>
        <a href="admin.php" class="btn">Cancel</a>
    </form>
</div>
</body>
</html>