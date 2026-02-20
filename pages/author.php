<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Author - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../templates/header.php'; ?>

<div class="container" style="margin-top: 80px; text-align: center;">
    <h1>About Author</h1>

    <div style="max-width: 600px; margin: 0 auto;">
        <div style="background: #2a2a2a; padding: 40px; margin: 30px 0; border-radius: 10px;">
            <div style="margin-bottom: 30px;">
                <img src="../images/avatar.png" alt="Profile" style="width: 150px; height: 150px; border-radius: 50%; border: 3px solid #e50914; margin-bottom: 20px;">
            </div>
            
            <h2 style="color: #e50914; margin-bottom: 25px;">Student Information</h2>
            <div style="text-align: left; display: inline-block;">
                <p style="margin: 10px 0;"><strong style="color: #e50914;">Name:</strong> Marko Šmigoc</p>
                <p style="margin: 10px 0;"><strong style="color: #e50914;">Student ID:</strong> 123456</p>
                <p style="margin: 10px 0;"><strong style="color: #e50914;">Course:</strong> Web Programming</p>
                <p style="margin: 10px 0;"><strong style="color: #e50914;">Year:</strong> 2024/2025</p>
                <p style="margin: 10px 0;"><strong style="color: #e50914;">University:</strong> Aspira University College</p>
                <p style="margin: 10px 0;"><strong style="color: #e50914;">Email:</strong> 
                    <a href="mailto:marko.smigoc@aspira.hr" style="color: #fff; text-decoration: none;">marko.smigoc@aspira.hr</a>
                </p>
            </div>
        </div>

        <div style="background: #2a2a2a; padding: 30px; margin: 30px 0; border-radius: 10px;">
            <h2 style="color: #e50914; margin-bottom: 20px;">Contact & Links</h2>
            <div style="margin-top: 20px;">
                <a href="https://github.com/Smigq" target="_blank" style="color: white; text-decoration: none; margin: 0 10px;">
                    <span style="font-size: 20px;">GitHub</span>
                </a>
                <span style="color: #666;">|</span>
                <a href="https://linkedin.com/in/markosmigoc" target="_blank" style="color: white; text-decoration: none; margin: 0 10px;">
                    <span style="font-size: 20px;">LinkedIn</span>
                </a>
                <span style="color: #666;">|</span>
                <a href="../pages/contact.php" style="color: white; text-decoration: none; margin: 0 10px;">
                    <span style="font-size: 20px;">Contact Form</span>
                </a>
            </div>
        </div>
    </div>
</div>
<?php include '../templates/footer.php'; ?>
</body>
</html>