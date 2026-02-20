<nav class="navbar">
    <div class="nav-container">
        <a href="/videoteka/index.php" class="logo">VIDEOTEKA</a>
        <ul class="nav-menu">
            <li><a href="/videoteka/index.php">Home</a></li>
            <li><a href="/videoteka/pages/contact.php">Contact</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <li><a href="/videoteka/admin/admin.php">Admin</a></li>
                <?php endif; ?>
                <li><a href="/videoteka/pages/profile.php">Profile</a></li>
                <li><a href="/videoteka/lib/logout.php">Logout</a></li>
                <li><span>Hi, <?php echo $_SESSION['username']; ?></span></li>
            <?php else: ?>
                <li><a href="/videoteka/lib/login.php">Login</a></li>
                <li><a href="/videoteka/lib/register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>