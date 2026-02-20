<?php
require_once 'lib/database.php';
require_once 'templates/functions.php';
session_start();

$search = isset($_GET['search']) ? clean($_GET['search']) : '';
$genre = isset($_GET['genre']) ? clean($_GET['genre']) : '';
$year = isset($_GET['year']) ? clean($_GET['year']) : '';
$rating = isset($_GET['rating']) ? clean($_GET['rating']) : '';

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$per_page = 15;
$offset = ($page - 1) * $per_page;

$query = "SELECT * FROM films WHERE is_slider = 1 ORDER BY created_at DESC";
$result = $db->query($query);
$sliderFilms = [];
while($row = $result->fetch_assoc()) {      
    $sliderFilms[] = $row;
}
?>
<!DOCTYPE html>
<html lang="hr">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videoteka - Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'templates/header.php'; ?>

<div class="hero-slider">
    <?php $index = 0; foreach ($sliderFilms as $film): ?>
        <div class="slide <?php echo $index === 0 ? 'active' : ''; ?>">
            <img src="<?php echo $film['poster']; ?>" alt="<?php echo $film['title']; ?>">
            <div class="slide-content">
                <h1><?php echo $film['title']; ?></h1>
                <p><?php echo $film['year']; ?> | <?php echo $film['genre']; ?> | Rating: <?php echo $film['rating']; ?></p>
                <a href="pages/film.php?id=<?php echo $film['id']; ?>" class="btn">Watch Now</a>
            </div>
        </div>
        <?php $index++; endforeach; ?>

    <button class="slider-btn prev" onclick="changeSlide(-1)">❮</button>
    <button class="slider-btn next" onclick="changeSlide(1)">❯</button>
</div>

<div class="container">
    <div class="search-section">
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search movies..." value="<?php echo $search; ?>">
            <select name="genre">
                <option value="">All Genres</option>
                <?php
                    $genres_result = $db->query("SELECT DISTINCT genre FROM films WHERE genre IS NOT NULL ORDER BY genre");
                    while($g = $genres_result->fetch_assoc()): ?>
                    <option value="<?php echo $g['genre']; ?>" 
                <?php echo ($genre == $g['genre']) ? 'selected' : ''; ?>>
                <?php echo $g['genre']; ?>
                </option>
                <?php endwhile; ?>
            </select>
            <select name="year">
                <option value="">All Years</option>
                <?php for($y = 2025; $y >= 1900; $y--): ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>

            <button type="submit" class="btn">Search</button>
        </form>
    </div>
    <?php
    $sql = "SELECT * FROM films WHERE 1=1";

    if($search) {
        $sql .= " AND title LIKE '%$search%'";
    }
    if($genre) {
        $sql .= " AND genre = '$genre'";
    }
    if($year) {
        $sql .= " AND year = '$year'";
    }
    if($rating) {
        $sql .= " AND rating >= '$rating'";
    }

    $sql .= " ORDER BY created_at DESC LIMIT $per_page OFFSET $offset"; 

    $result = $db->query($sql);
    $films = [];
    while($row = $result->fetch_assoc()) {
        $films[] = $row;
    }
    ?>

    <h2>Films</h2>
    <div class="films-grid">
        <?php foreach ($films as $film): ?>
            <div class="film-card">
                <a href="pages/film.php?id=<?php echo $film['id']; ?>">
                    <img src="<?php echo $film['poster']; ?>" alt="<?php echo $film['title']; ?>">
                    <h3><?php echo $film['title']; ?></h3>
                    <p><?php echo $film['year']; ?></p>
                    <p>Rating: <?php echo $film['rating']; ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
$query_params = [];
if($search) $query_params['search'] = $search;
if($genre) $query_params['genre'] = $genre;
if($year) $query_params['year'] = $year;
if($rating) $query_params['rating'] = $rating;
?>

<div class="pagination" style="text-align: center; margin: 30px 0;">
    <?php if($page > 1): ?>
        <a href="?<?php echo http_build_query(array_merge($query_params, ['page' => $page-1])); ?>" class="btn">← Previous</a>
    <?php endif; ?>
    <span style="margin: 0 20px;">Page <?php echo $page; ?></span>
    <a href="?<?php echo http_build_query(array_merge($query_params, ['page' => $page+1])); ?>" class="btn">Next →</a>
</div>

<?php include 'templates/footer.php'; ?>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');

    function changeSlide(direction) {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + direction + slides.length) % slides.length;
        slides[currentSlide].classList.add('active');
    }

    setInterval(() => changeSlide(1), 5000);
</script>
</body>
</html>