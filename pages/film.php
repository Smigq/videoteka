<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

$filmId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT * FROM films WHERE id = $filmId";
$result = $db->query($query);
$film = $result->fetch_assoc();

if(!$film) {
    header("Location: ../index.php");
    exit();
}

if(!$film['film_code']) {
    $filmCode = 'FILM-' . str_pad($filmId, 6, '0', STR_PAD_LEFT);
    $db->query("UPDATE films SET film_code = '$filmCode' WHERE id = $filmId");
    $film['film_code'] = $filmCode;
}

$query = "SELECT r.*, u.username FROM film_reviews r JOIN users u ON r.user_id = u.id WHERE r.film_id = $filmId ORDER BY r.created_at DESC";
$result = $db->query($query);
$reviews = [];
while($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isLoggedIn()) {
    $rating = (int)$_POST['rating'];
    $review = $db->real_escape_string($_POST['review']);
    $userId = $_SESSION['user_id'];

    $query = "INSERT INTO film_reviews (user_id, film_id, rating, review) VALUES ($userId, $filmId, $rating, '$review')";
    if($db->query($query)) {
        logActivity('ADD_REVIEW', "Added review for: " . $film['title'], $filmId);
    }

    header("Location: film.php?id=$filmId");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $film['title']; ?> - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        #reviewForm {
            display: none;
            background: #2a2a2a;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .review-btn {
            background: #e50914;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            margin: 10px 0;
        }
        .review-btn:hover {
            background: #cc0812;
        }
        .film-meta {
            background: #2a2a2a;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .film-meta-item {
            display: flex;
            margin: 8px 0;
        }
        .film-meta-label {
            font-weight: bold;
            color: #e50914;
            min-width: 120px;
        }
        .film-code-badge {
            display: inline-block;
            background: #444;
            color: #ffd700;
            padding: 5px 12px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 14px;
            margin-left: 10px;
        }
        .actors-list {
            color: #ccc;
        }
    </style>
</head>
<body>
<?php include '../templates/header.php'; ?>

<div class="container">
    <div class="film-details">
        <div class="film-poster">
            <img src="<?php echo $film['poster']; ?>" alt="<?php echo $film['title']; ?>">
        </div>

        <div class="film-info">
            <h1>
                <?php echo $film['title']; ?>
                <span class="film-code-badge"><?php echo $film['film_code']; ?></span>
            </h1>

            <div class="film-meta">
                <div class="film-meta-item">
                    <span class="film-meta-label">Film ID:</span>
                    <span>#<?php echo str_pad($film['id'], 4, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="film-meta-item">
                    <span class="film-meta-label">Film Code:</span>
                    <span><?php echo $film['film_code']; ?></span>
                </div>
                <div class="film-meta-item">
                    <span class="film-meta-label">Year:</span>
                    <span><?php echo $film['year']; ?></span>
                </div>
                <div class="film-meta-item">
                    <span class="film-meta-label">Genre:</span>
                    <span><?php echo $film['genre']; ?></span>
                </div>
                <div class="film-meta-item">
                    <span class="film-meta-label">Director:</span>
                    <span><?php echo $film['director'] ?: 'Not specified'; ?></span>
                </div>
                <?php if($film['actors']): ?>
                    <div class="film-meta-item">
                        <span class="film-meta-label">Actors:</span>
                        <span class="actors-list"><?php echo $film['actors']; ?></span>
                    </div>
                <?php endif; ?>
                <div class="film-meta-item">
                    <span class="film-meta-label">Rating:</span>
                    <span><?php echo $film['rating']; ?>/10</span>
                </div>
                <div class="film-meta-item">
                    <span class="film-meta-label">Availability:</span>
                    <span style="color: <?php echo $film['is_available'] ? '#4caf50' : '#f44336'; ?>">
                        <?php echo $film['is_available'] ? 'Available' : 'Rented'; ?>
                    </span>
                </div>
            </div>

            <div style="margin: 20px 0;">
                <h3>Description</h3>
                <p><?php echo $film['description']; ?></p>
            </div>

            <?php if(isLoggedIn()): ?>
                <?php if($film['is_available']): ?>
                    <button class="btn" onclick="rentFilm(<?php echo $filmId; ?>)">Rent Film</button>
                <?php else: ?>
                    <button class="btn" disabled style="opacity: 0.5;">Currently Unavailable</button>
                <?php endif; ?>
                <button class="btn" onclick="addToWatchlist(<?php echo $filmId; ?>)">Add to Watchlist</button>
            <?php else: ?>
                <br><p><a href="../lib/login.php">Login</a> to rent this film</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="reviews">
        <h2>Reviews</h2>

        <?php if(isLoggedIn()): ?>
            <button class="review-btn" onclick="toggleReviewForm()">Write a Review</button>

            <div id="reviewForm">
                <h3>Write Your Review</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Rating</label>
                        <select name="rating" required>
                            <option value="">Select Rating</option>
                            <option value="1">1 Star - Poor</option>
                            <option value="2">2 Stars - Fair</option>
                            <option value="3">3 Stars - Good</option>
                            <option value="4">4 Stars - Very Good</option>
                            <option value="5">5 Stars - Excellent</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Your Review</label>
                        <textarea name="review" rows="4" placeholder="What did you think about this film?" required></textarea>
                    </div>
                    <button type="submit" class="btn">Submit Review</button>
                    <button type="button" class="btn" onclick="toggleReviewForm()">Cancel</button>
                </form>
            </div>
        <?php else: ?>
            <p><a href="../lib/login.php">Login</a> to write a review</p>
        <?php endif; ?>

        <div style="margin-top: 30px;">
            <?php if(count($reviews) > 0): ?>
                <?php foreach($reviews as $review): ?>
                    <div style="background: #2a2a2a; padding: 15px; margin: 10px 0; border-radius: 5px;">
                        <div style="display: flex; justify-content: space-between;">
                            <strong><?php echo $review['username']; ?></strong>
                            <span style="color: #999;"><?php echo date('d.m.Y', strtotime($review['created_at'])); ?></span>
                        </div>
                        <div style="color: #ffd700; margin: 5px 0;">
                            <?php for($i = 0; $i < $review['rating']; $i++) echo '★'; ?>
                            <?php for($i = $review['rating']; $i < 5; $i++) echo '☆'; ?>
                        </div>
                        <p><?php echo $review['review']; ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews yet. Be the first to review this film!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
<script src="../js/script.js"></script>
<script>
    function toggleReviewForm() {
        var form = document.getElementById('reviewForm');
        if(form) {
            if(form.style.display === 'none' || form.style.display === '') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    }
</script>
</body>
</html>