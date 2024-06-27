<?php
session_start();

if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$database = "echoes_of_noordheim";
$user = "root";
$password = "";

// Connect to MySQL database
$db = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['loggedInUser']['id'];
    $username = $_SESSION['loggedInUser']['username'];
    $post_description = mysqli_real_escape_string($db, $_POST['post_description']);
    $post_date = date('Y-m-d H:i:s');

    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
        $image = mysqli_real_escape_string($db, $image);
    }

    $query = "INSERT INTO posts (user_id, username, post_description, image, post_date) VALUES ('$user_id', '$username', '$post_description', '$image', '$post_date')";
    mysqli_query($db, $query) or die("Error: " . mysqli_error($db));

    header("Location: posts.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Post - Echoes of Noordheim Forum</title>
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
        <h1>Posts</h1>
    </section>
    <form action="addpost.php" method="post" enctype="multipart/form-data">
        <section class="form-element">
            <label for="post_description">Description:</label>
            <textarea id="post_description" name="post_description" required></textarea>
        </section>
        <section class="form-element">
            <label for="image">Image (optional):</label>
            <input type="file" id="image" name="image" accept="image/*">
        </section>
        <button type="submit">Submit</button>
    </form>
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
