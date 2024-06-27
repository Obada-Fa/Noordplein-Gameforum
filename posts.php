<?php
session_start();

$host = "localhost";
$database = "echoes_of_noordheim";
$user = "root";
$password = "";

// Connect to MySQL database
$db = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());

// Check if user is logged in
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

// Retrieve posts from the database with usernames and post dates
$query = "
    SELECT posts.id, posts.username, posts.post_description, posts.image, posts.post_date, 
           users.username AS user_username
    FROM posts
    JOIN users ON posts.user_id = users.id
    ORDER BY posts.post_date DESC";
$result = mysqli_query($db, $query);

$posts = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Convert binary image data to base64 if image exists
        if (!empty($row['image'])) {
            $imageData = base64_encode($row['image']);
            $imageType = 'image/jpeg'; // Adjust this based on your image format in the database
            $imageSrc = "data:{$imageType};base64,{$imageData}";
            $row['image_src'] = $imageSrc;
        }

        // Add post data to the array
        $posts[] = $row;
    }
} else {
    die("Error fetching posts: " . mysqli_error($db));
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['loggedInUser']);

// Close the database connection
mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echoes of Noordheim Forum - Posts</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="sidebar">
    <section class="title">
        Echoes of Noordheim Forum
    </section>
    <menu>
        <li><a href="index.php">Home</a></li>
        <li><a href="posts.php">Posts</a></li>
        <li><a href="account.php">Account</a></li>
        <li><a href="aboutus.php">About us</a></li>
    </menu>
</nav>
<main class="main-content">
    <section class="title">
        <h1>Posts</h1>
    </section>
    <?php if ($isLoggedIn): ?>
        <form action="addpost.php" method="get" style="display: inline;">
            <button type="submit" class="user-button">Add Post</button>
        </form>
    <?php endif; ?>
    <section class="post-list">
        <?php foreach ($posts as $post): ?>
            <section class="post-item">
                <section class="published-by">
                    <span>Posted by <?= htmlspecialchars($post['user_username']) ?> on <?= htmlspecialchars($post['post_date']) ?></span>
                </section>
                <p><?= nl2br(htmlspecialchars($post['post_description'])) ?></p>
                <?php if (!empty($post['image'])): ?>
                    <img src="<?= htmlspecialchars($post['image_src']) ?>" alt="Post Image" style="max-width: 100%; height: auto;">
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    </section>
    <footer>
        <span>&copy; Echoes of Noordheim Forum 2024</span>
    </footer>
</main>

</body>
</html>
