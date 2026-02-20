<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

if(!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$query = "SELECT * FROM films ORDER BY created_at DESC";
$result = $db->query($query);   
$films = [];
while($row = $result->fetch_assoc()) {
    $films[] = $row;
}

$query = "SELECT * FROM users WHERE role = 'member'";
$result = $db->query($query);
$members = [];
while($row = $result->fetch_assoc()) {
    $members[] = $row;
}

$query = "SELECT r.*, u.username, f.title, f.film_code 
          FROM rentals r 
          JOIN users u ON r.user_id = u.id 
          JOIN films f ON r.film_id = f.id 
          WHERE r.status = 'active' 
          ORDER BY r.rental_date DESC";
$result = $db->query($query);
$activeRentals = [];
while($row = $result->fetch_assoc()) {
    $activeRentals[] = $row;
}
        
$query = "SELECT r.*, u.username, f.title, f.film_code 
          FROM rentals r 
          JOIN users u ON r.user_id = u.id 
          JOIN films f ON r.film_id = f.id 
          ORDER BY r.rental_date DESC";
$result = $db->query($query);
$allRentals = [];
while($row = $result->fetch_assoc()) {
    $allRentals[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
        }
        .status-active {
            background: #ff5722;
            color: white;
        }
        .status-returned {
            background: #4caf50;
            color: white;
        }
        .returned-by {
            font-size: 11px;
            color: #999;
        }
    </style>
</head>
<body>
<?php include '../templates/header.php'; ?>

<div class="container" style="margin-top: 80px;">
    <h1>Admin Panel</h1>

    <div style="margin: 20px 0;">
        <button onclick="showSection('films')" class="btn">Films</button>
        <button onclick="showSection('members')" class="btn">Members</button>
        <button onclick="showSection('active-rentals')" class="btn">Active Rentals</button>
        <button onclick="showSection('all-rentals')" class="btn">Rental History</button>
        <button onclick="showSection('add')" class="btn">Add Film from OMDb</button>
    </div>

    <div id="films">
        <h2>Manage Films</h2>
        <table class="admin-table">
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Title</th>
                <th>Year</th>
                <th>Genre</th>
                <th>Director</th>
                <th>Actors</th>
                <th>Rating</th>
                <th>Available</th>
                <th>Slider</th>
                <th>Actions</th>
            </tr>
            <?php foreach($films as $film): ?>
                <tr id="film-<?php echo $film['id']; ?>">
                    <td><?php echo $film['id']; ?></td>
                    <td><?php echo $film['film_code'] ?: 'N/A'; ?></td>
                    <td><?php echo $film['title']; ?></td>
                    <td><?php echo $film['year']; ?></td>
                    <td><?php echo $film['genre']; ?></td>
                    <td><?php echo $film['director']; ?></td>
                    <td><?php echo $film['actors'] ? substr($film['actors'], 0, 30) . '...' : 'N/A'; ?></td>
                    <td><?php echo $film['rating']; ?></td>
                    <td><?php echo $film['is_available'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo $film['is_slider'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="edit_film.php?id=<?php echo $film['id']; ?>" class="btn">Edit</a>
                        <button onclick="deleteFilm(<?php echo $film['id']; ?>)" class="btn">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div id="members" style="display:none;">
        <h2>Manage Members</h2>
        <table class="admin-table">
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Active</th>
                <th>Actions</th>
            </tr>
            <?php foreach($members as $member): ?>
                <tr id="member-<?php echo $member['id']; ?>">
                    <td><?php echo $member['username']; ?></td>
                    <td><?php echo $member['email']; ?></td>
                    <td><?php echo $member['is_active'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="edit_member.php?id=<?php echo $member['id']; ?>" class="btn">Edit</a>
                        <button onclick="deleteMember(<?php echo $member['id']; ?>)" class="btn">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div id="active-rentals" style="display:none;">
        <h2>Active Rentals</h2>
        <table class="admin-table">
            <tr>
                <th>Member</th>
                <th>Film</th>
                <th>Film Code</th>
                <th>Rental Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php foreach($activeRentals as $rental): ?>
                <tr id="rental-<?php echo $rental['id']; ?>">
                    <td><?php echo $rental['username']; ?></td>
                    <td><?php echo $rental['title']; ?></td>
                    <td><?php echo $rental['film_code']; ?></td>
                    <td><?php echo date('d.m.Y H:i', strtotime($rental['rental_date'])); ?></td>
                    <td><span class="status-badge status-active">Active</span></td>
                    <td>
                        <button onclick="returnFilm(<?php echo $rental['id']; ?>)" class="btn">Mark as Returned</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div id="all-rentals" style="display:none;">
        <h2>Complete Rental History</h2>
        <table class="admin-table">
            <tr>
                <th>Member</th>
                <th>Film</th>
                <th>Film Code</th>
                <th>Rental Date</th>
                <th>Return Date</th>
                <th>Status</th>
                <th>Returned By</th>
            </tr>
            <?php foreach($allRentals as $rental): ?>
                <tr>
                    <td><?php echo $rental['username']; ?></td>
                    <td><?php echo $rental['title']; ?></td>
                    <td><?php echo $rental['film_code']; ?></td>
                    <td><?php echo date('d.m.Y H:i', strtotime($rental['rental_date'])); ?></td>
                    <td>
                        <?php echo $rental['return_date'] ? date('d.m.Y H:i', strtotime($rental['return_date'])) : '-'; ?>
                    </td>
                    <td>
                        <span class="status-badge <?php echo $rental['status'] == 'active' ? 'status-active' : 'status-returned'; ?>">
                            <?php echo ucfirst($rental['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if($rental['returned_by']): ?>
                            <span class="returned-by"><?php echo ucfirst($rental['returned_by']); ?></span>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <div style="margin: 30px 0;">
            <a href="activity_logs.php" class="btn" style="background: #e50914;">View Activity Logs</a>
        </div>
    </div>

    <div id="add" style="display:none;">
        <h2>Add Film from OMDb</h2>
        <div class="form-group">
            <input type="text" id="omdbSearch" placeholder="Search movie title...">
            <button class="btn" onclick="searchOMDb()">Search</button>
        </div>
        <div id="omdbResults" style="margin-top: 20px;"></div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
<script src="../js/admin.js"></script>
<script>
    function showSection(section) {
        document.getElementById('films').style.display = 'none';
        document.getElementById('members').style.display = 'none';
        document.getElementById('active-rentals').style.display = 'none';
        document.getElementById('all-rentals').style.display = 'none';
        document.getElementById('add').style.display = 'none';
        document.getElementById(section).style.display = 'block';
    }
</script>
</body>
</html>