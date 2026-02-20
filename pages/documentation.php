<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Documentation - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../templates/header.php'; ?>

<div class="container" style="margin-top: 80px;">
    <h1>Documentation</h1>

    <div class="doc-section">
        <h2>1. Project Description</h2>
        <div class="content-block" style="background: #2a2a2a; padding: 30px; margin: 20px 0;">
            <p>Videoteka is a video library management system designed for managing film rentals, user accounts, and movie database. The system allows users to browse, rent, and review films while providing administrators with powerful tools for content and user management.</p>
            
            <div class="project-info" style="margin-top: 20px;">
                <h3>Key Features:</h3>
                <ul>
                    <li>User registration and authentication system</li>
                    <li>Film browsing with advanced search and filtering</li>
                    <li>Online film rental system</li>
                    <li>Review and rating system</li>
                    <li>Personal watchlist management</li>
                    <li>Admin panel for complete system control</li>
                    <li>Integration with OMDb API for film data</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="doc-section">
        <h2>2. Technologies Used</h2>
        <div class="content-block" style="background: #2a2a2a; padding: 30px; margin: 20px 0;">
            <div class="tech-category">
                <h3>Backend Technologies:</h3>
                <ul>
                    <li><strong>PHP 7.4+</strong></li>
                    <li><strong>MySQL</strong></li>
                </ul>
            </div>

            <div class="tech-category" style="margin-top: 20px;">
                <h3>Frontend Technologies:</h3>
                <ul>
                    <li><strong>HTML5</strong></li>
                    <li><strong>CSS3</strong></li>
                    <li><strong>JavaScript</strong></li>
                    <li><strong>AJAX</strong></li>
                </ul>
            </div>

            <div class="tech-category" style="margin-top: 20px;">
                <h3>External Services:</h3>
                <ul>
                    <li><strong>OMDb API</strong></li>
                    <li><strong>Google reCAPTCHA</strong></li>
                </ul>
            </div>
        </div>
    </div>  

    <div class="doc-section">
        <h2>3. Project Structure</h2>
        <div class="content-block" style="background: #2a2a2a; padding: 30px; margin: 20px 0;">
            <h3>Directory Organization:</h3>
            <pre style="background: #1a1a1a; padding: 20px; overflow-x: auto;">
videoteka/
│
├── index.php
├── images/
│   └── db-schema.png               
├── css/
│   └── style.css            
├── js/
│   ├── script.js            
│   └── admin.js             
├── lib/
│   ├── database.php         
│   ├── login.php            
│   ├── register.php         
│   ├── logout.php           
│   └── activate.php         
├── pages/
│   ├── film.php             
│   ├── profile.php          
│   ├── contact.php          
│   ├── author.php           
│   └── documentation.php    
├── admin/
│   ├── activity_logs.php
│   ├── admin.php            
│   ├── edit_film.php        
│   ├── edit_member.php      
│   └── return_film.php      
├── api/
│   ├── films.php            
│   ├── members.php         
│   ├── rentals.php          
│   └── check_username.php   
└── templates/
    ├── header.php           
    ├── footer.php           
    ├── functions.php        
    └── rss.php              
            </pre>
        </div>
    </div>

    <div style="background: #2a2a2a; padding: 30px; margin: 20px 0;">
    <h2>4. Database Schema</h2>
    <div style="text-align: center;">
        <img src="../images/db-schema.png" alt="Database Schema Diagram" 
             style="max-width: 100%; height: auto; border: 2px solid #444; border-radius: 5px;">
             <p style="margin-top: 20px; color: #999;">
        <strong>Note:</strong> Activity_logs table intentionally doesn't use foreign keys to preserve audit history even after records are deleted. 
    </p>
    </div>
    </div>

    <div style="background: #2a2a2a; padding: 30px; margin: 20px 0;">
        <h2>5. Default Login</h2>
        <p><strong>Admin Account:</strong></p>
        <p>Username: admin</p>
        <p>Password: admin12</p>

        <p><strong>Test User:</strong></p>
        <p>Username: testuser</p>
        <p>Password: test123</p>
    </div>
</div>    
<?php include '../templates/footer.php'; ?>
</body>
</html>
