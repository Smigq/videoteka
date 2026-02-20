<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

if(!isLoggedIn()) {
header("Location: ../lib/login.php");
exit();
}

$userId = $_SESSION['user_id'];

if(isset($_POST['return_film'])) {
$rentalId = (int)$_POST['rental_id'];

$query = "SELECT film_id FROM rentals WHERE id = $rentalId AND user_id = $userId AND status = 'active'";
$result = $db->query($query);
$rental = $result->fetch_assoc();

if($rental) {
$db->query("UPDATE rentals SET status = 'returned', return_date = NOW(), returned_by = 'user' WHERE id = $rentalId");
$db->query("UPDATE films SET is_available = 1 WHERE id = " . $rental['film_id']);
header("Location: profile.php?msg=returned");
exit();
}
}

if(isset($_POST['remove_watchlist'])) {
    $watchlistId = (int)$_POST['watchlist_id'];
    $filmId = (int)$_POST['film_id'];
    $query = "DELETE FROM watchlist 
              WHERE user_id = $userId AND film_id = $filmId";
    
    if($db->query($query)) {
            $filmQuery = "SELECT title FROM films WHERE id = $filmId";
        $filmResult = $db->query($filmQuery);
        $filmData = $filmResult->fetch_assoc();
        
        logActivity('REMOVE_WATCHLIST', "Removed from watchlist: " . $filmData['title'], $filmId);
        header("Location: profile.php?msg=removed_watchlist");
        exit();
    }
}
$query = "SELECT r.*, f.title, f.poster, f.film_code
FROM rentals r
JOIN films f ON r.film_id = f.id
WHERE r.user_id = $userId AND r.status = 'active'
ORDER BY r.rental_date DESC";
$result = $db->query($query);
$activeRentals = [];
while($row = $result->fetch_assoc()) {
$activeRentals[] = $row;
}

$query = "SELECT r.*, f.title, f.poster, f.film_code
FROM rentals r
JOIN films f ON r.film_id = f.id
WHERE r.user_id = $userId AND r.status = 'returned'
ORDER BY r.return_date DESC";
$result = $db->query($query);
$rentalHistory = [];
while($row = $result->fetch_assoc()) {
$rentalHistory[] = $row;
}

$query = "SELECT w.*, f.title, f.poster, f.year, f.film_code
FROM watchlist w
JOIN films f ON w.film_id = f.id
WHERE w.user_id = $userId";
$result = $db->query($query);
$watchlist = [];
while($row = $result->fetch_assoc()) {
$watchlist[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .return-btn {
            background: #ff9800;
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            border-radius: 3px;
        }
        .return-btn:hover {
            background: #f57c00;
        }
        .success-msg {
            background: #4caf50;
            color: white;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .film-code {
            background: #444;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 12px;
            color: #ffd700;
        }
        .rental-status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 12px;
            margin-top: 5px;
        }
        .status-active {
            background: #ff5722;
            color: white;
        }
        .status-returned {
            background: #4caf50;
            color: white;
        }
        .btn-remove {
            background: #f44336;
            color: white;
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            margin-top: 5px;
            border-radius: 3px;
        }
        .btn-remove:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
<?php include '../templates/header.php'; ?>

<div class="container" style="margin-top: 80px;">
    <h1>My Profile</h1>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'returned'): ?>
        <div class="success-msg">Film successfully returned!</div>
    <?php endif; ?>

    <br>
    <h2>Active Rentals</h2>
    <?php if(count($activeRentals) > 0): ?>
        <div class="films-grid">
            <?php foreach($activeRentals as $rental): ?>
                <div class="film-card">
                    <img src="<?php echo $rental['poster']; ?>" alt="<?php echo $rental['title']; ?>">
                    <h3><?php echo $rental['title']; ?></h3>
                    <span class="film-code"><?php echo $rental['film_code']; ?></span>
                    <p>Rented: <?php echo date('d.m.Y', strtotime($rental['rental_date'])); ?></p>
                    <span class="rental-status status-active">Status: Active</span>
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="rental_id" value="<?php echo $rental['id']; ?>">
                        <button type="submit" name="return_film" class="return-btn"
                                onclick="return confirm('Are you sure you want to return this film?')">
                            Return Film
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No active rentals.</p>
    <?php endif; ?>

    <br>
    <h2>Rental History</h2>
    <?php if(count($rentalHistory) > 0): ?>
        <div class="films-grid">
            <?php foreach($rentalHistory as $rental): ?>
                <div class="film-card">
                    <img src="<?php echo $rental['poster']; ?>" alt="<?php echo $rental['title']; ?>">
                    <h3><?php echo $rental['title']; ?></h3>
                    <span class="film-code"><?php echo $rental['film_code']; ?></span>
                    <p>Rented: <?php echo date('d.m.Y', strtotime($rental['rental_date'])); ?></p>
                    <p>Returned: <?php echo date('d.m.Y', strtotime($rental['return_date'])); ?></p>
                    <span class="rental-status status-returned">Returned by: <?php echo ucfirst($rental['returned_by']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No rental history.</p>
    <?php endif; ?>

    <br>
    <h2>Watchlist</h2>
    <?php if(count($watchlist) > 0): ?>
        <div class="films-grid">
            <?php foreach($watchlist as $item): ?>
                <div class="film-card">
                    <a href="film.php?id=<?php echo $item['film_id']; ?>">
                        <img src="<?php echo $item['poster']; ?>" alt="<?php echo $item['title']; ?>">
                        <h3><?php echo $item['title']; ?></h3>
                        <span class="film-code"><?php echo $item['film_code']; ?></span>
                        <p><?php echo $item['year']; ?></p>
                    </a>
                    <form method="POST" style="margin: 10px 0;">
                    <input type="hidden" name="watchlist_id" value="<?php echo $item['id']; ?>">
                    <input type="hidden" name="film_id" value="<?php echo $item['film_id']; ?>">
                    <button type="submit" name="remove_watchlist" class="btn-remove"
                            onclick="return confirm('Remove from watchlist?')">
                        Remove from Watchlist
                    </button>
                </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No films in watchlist.</p>
    <?php endif; ?>
</div>

<?php include '../templates/footer.php'; ?>
</body>
</html>