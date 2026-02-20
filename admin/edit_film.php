<?php
require_once '../lib/database.php';
require_once '../templates/functions.php';
session_start();

if(!isAdmin()) {
    header("Location: ../index.php");
    exit();
}

$filmId = (int)$_GET['id'];
$message = '';

$query = "SELECT * FROM films WHERE id = $filmId";
$result = $db->query($query);
$film = $result->fetch_assoc();

if(!$film['film_code']) {
    $filmCode = 'FILM-' . str_pad($filmId, 6, '0', STR_PAD_LEFT);
    $db->query("UPDATE films SET film_code = '$filmCode' WHERE id = $filmId");
    $film['film_code'] = $filmCode;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $db->real_escape_string($_POST['title']);
    $year = (int)$_POST['year'];
    $genre = $db->real_escape_string($_POST['genre']);
    $director = $db->real_escape_string($_POST['director']);
    $actors = $db->real_escape_string($_POST['actors']);
    $description = $db->real_escape_string($_POST['description']);
    $rating = (float)$_POST['rating'];
    $poster = $db->real_escape_string($_POST['poster']);
    $filmCode = $db->real_escape_string($_POST['film_code']);
    $isSlider = isset($_POST['is_slider']) ? 1 : 0;
    $isAvailable = isset($_POST['is_available']) ? 1 : 0;

    $query = "UPDATE films SET 
              title='$title', 
              year=$year, 
              genre='$genre', 
              director='$director',
              actors='$actors', 
              description='$description', 
              rating=$rating, 
              poster='$poster',
              film_code='$filmCode',
              is_slider=$isSlider,
              is_available=$isAvailable
              WHERE id=$filmId";

    if($db->query($query)) {
        $message = 'Film updated successfully!';
        logActivity('EDIT_FILM', "Updated film: $title", $filmId);
        $query = "SELECT * FROM films WHERE id = $filmId";
        $result = $db->query($query);
        $film = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Film - Videoteka</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .film-id-info {
            background: #333;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid #e50914;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .info-label {
            color: #999;
        }
        .info-value {
            color: #ffd700;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="form-container" style="max-width: 600px;">
    <h2>Edit Film</h2>

    <?php if($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="film-id-info">
        <div class="info-row">
            <span class="info-label">Film ID:</span>
            <span class="info-value">#<?php echo str_pad($film['id'], 4, '0', STR_PAD_LEFT); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Current Code:</span>
            <span class="info-value"><?php echo $film['film_code']; ?></span>
        </div>
    </div>

    <form method="POST">
        <div class="form-group">
            <label>Film Code</label>
            <input type="text" name="film_code" value="<?php echo $film['film_code']; ?>" placeholder="e.g., FILM-000001">
            <small style="color: #999;">Leave blank to auto-generate</small>
        </div>

        <div class="form-group">
            <label>Title</label>
            <input type="text" name="title" value="<?php echo $film['title']; ?>" required>
        </div>

        <div class="form-group">
            <label>Year</label>
            <input type="number" name="year" value="<?php echo $film['year']; ?>" required>
        </div>

        <div class="form-group">
            <label>Genre</label>
            <input type="text" name="genre" value="<?php echo $film['genre']; ?>" required>
        </div>

        <div class="form-group">
            <label>Director</label>
            <input type="text" name="director" value="<?php echo $film['director']; ?>" placeholder="e.g., Christopher Nolan">
        </div>

        <div class="form-group">
            <label>Actors</label>
            <textarea name="actors" rows="3" placeholder="e.g., Leonardo DiCaprio, Tom Hardy, Marion Cotillard"><?php echo $film['actors']; ?></textarea>
            <small style="color: #999;">Separate multiple actors with commas</small>
        </div>

        <div class="form-group">
            <label>Rating (0-10)</label>
            <input type="number" name="rating" value="<?php echo $film['rating']; ?>" min="0" max="10" step="0.1">
        </div>

        <div class="form-group">
            <label>Poster URL</label>
            <input type="text" name="poster" value="<?php echo $film['poster']; ?>">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="4"><?php echo $film['description']; ?></textarea>
        </div>

        <div style="background: #333; padding: 15px; margin: 20px 0; border-radius: 5px;">
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_slider" <?php echo $film['is_slider'] ? 'checked' : ''; ?>>
                    Show in Homepage Slider
                </label>
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_available" <?php echo $film['is_available'] ? 'checked' : ''; ?>>
                    Available for Rent
                </label>
            </div>
        </div>

        <button type="submit" class="btn">Update Film</button>
        <a href="admin.php" class="btn">Cancel</a>
    </form>
</div>
</body>
</html>