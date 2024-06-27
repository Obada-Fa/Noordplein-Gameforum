<?php
session_start();

$host = "localhost";
$database = "echoes_of_noordheim";
$user = "root";
$password = "";

// Connect to MySQL database
$db = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());

// Retrieve news items from the database
$query = "SELECT id, username, news_title, news_date, image, news_description FROM news ORDER BY news_date DESC";
$result = mysqli_query($db, $query);

$newsItems = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Convert binary image data to base64
        $imageData = base64_encode($row['image']);
        $imageType = 'image/jpeg'; // Adjust this based on your image format in the database

        $imageSrc = "data:{$imageType};base64,{$imageData}";

        // Store news item with image source
        $row['image_src'] = $imageSrc;

        $newsItems[] = $row;
    }
}

// Check if admin is logged in
$isAdmin = isset($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']['is_admin'] !== "0";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echoes of Noordheim Forum</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<section class="sidebar">
    <section class="title">
        Echoes of Noordheim Forum
    </section>
    <menu>
        <li><a href="index.php">Home</a></li>
        <li><a href="posts.php">Posts</a></li>
        <li><a href="account.php">Account</a></li>
        <li><a href="aboutus.php">About us</a></li>
    </menu>
</section>
<main class="main-content">
    <section class="title">
        <h1>Home</h1>
    </section>
        <?php if ($isAdmin): ?>
            <form action="addnews.php" method="get" style="display: inline;">
                <button type="submit" class="admin-button">Add News</button>
            </form>
        <?php endif; ?>

    <section class="news-list">
        <?php foreach ($newsItems as $news): ?>
            <section class="news-item">
                <?php if (!empty($news['image'])): ?>
                    <img src="<?= $news['image_src'] ?>" alt="<?= htmlspecialchars($news['news_title']) ?>">
                <?php endif; ?>
                <div class="content">
                    <small>Published by <?= htmlspecialchars($news['username']) ?> on <?= htmlspecialchars($news['news_date']) ?></small>
                    <h2><?= htmlspecialchars($news['news_title']) ?></h2>
                    <p><?= nl2br(htmlspecialchars($news['news_description'])) ?></p>
                </div>
            </section>
        <?php endforeach; ?>
    </section>

    <footer>
        <span>&copy; Echoes of Noordheim Forum 2024</span>
    </footer>
</main>

</body>
</html>

<?php
// Close the database connection
mysqli_close($db);
?>
