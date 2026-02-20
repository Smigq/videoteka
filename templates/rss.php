<?php
require_once '../lib/database.php';

$query = "SELECT id, title, description, year, genre FROM films ORDER BY created_at DESC LIMIT 10";
$result = $db->query($query);

header('Content-Type: application/rss+xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="UTF-8" ?>';
echo '<rss version="2.0">';
echo '<channel>';
echo '<title>Videoteka - Latest Films</title>';
echo '<link>http://localhost/videoteka</link>';
echo '<description>Latest films from Videoteka</description>';
echo '<language>en-us</language>';

while ($row = $result->fetch_assoc()) {
    echo '<item>';
    echo '<title>' . htmlspecialchars($row['title']) . '</title>';
    echo '<link>http://localhost/videoteka/pages/film.php?id=' . $row['id'] . '</link>';
    echo '<description>' . htmlspecialchars($row['description']) . '</description>';
    echo '<category>' . htmlspecialchars($row['genre']) . '</category>';
    echo '<pubDate>' . date('r') . '</pubDate>';
    echo '</item>';
}

echo '</channel>';
echo '</rss>';
?>