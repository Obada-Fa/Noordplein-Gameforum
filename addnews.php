<?php
session_start();

// Check if admin is logged in
$isAdmin = isset($_SESSION['loggedInUser']) && $_SESSION['loggedInUser']['is_admin'] !== "0";

// Redirect non-admin users
if (!$isAdmin) {
    header("Location: index.php");
    exit;
}

$host = "localhost";
$database = "echoes_of_noordheim";
$user = "root";
$password = "";

$db = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newsTitle = mysqli_real_escape_string($db, $_POST['news_title']);
    $newsDescription = mysqli_real_escape_string($db, $_POST['news_description']);

    // File upload handling
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $fileName = mysqli_real_escape_string($db, $file['name']);
        $fileType = $file['type'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];
        $fileSize = $file['size'];

        // Check if file is uploaded without errors
        if ($fileError === 0) {
            $fileData = file_get_contents($fileTmpName);
            $fileData = mysqli_real_escape_string($db, $fileData);

            // Insert news item into database with current timestamp
            $insertQuery = "INSERT INTO news (username, news_title, news_description, image, news_date) 
                            VALUES ('{$_SESSION['loggedInUser']['username']}', '$newsTitle', '$newsDescription', '$fileData', NOW())";
            mysqli_query($db, $insertQuery);

            // Redirect to index.php after successful submission
            header("Location: index.php");
            exit;
        } else {
            $errors['file'] = "Error uploading file.";
        }
    } else {
        $errors['file'] = "Please upload a file.";
    }
}

mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add News</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main>
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
<section>
<main class="main-content">
    <section class="title">
        <h1>Home</h1>
    </section>
    <form action="" method="POST" enctype="multipart/form-data">
        <section class="form-element">
            <label for="news_title">News Title:</label><br>
            <input type="text" id="news_title" name="news_title" required>
        </section>
        <section class="form-element">
            <label for="news_description">News Description:</label><br>
            <textarea id="news_description" name="news_description" rows="4" required></textarea>
        </section>
        <section class="form-element">
            <label for="image">Upload Image:</label><br>
            <input type="file" id="image" name="image" accept="image/jpeg, image/png" required>
        </section>
        <section class="form-element">
            <button type="submit">Post News</button>
        </section>
        <?php if (!empty($errors)): ?>
            <section class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </form>
</section>
    <footer>
        <span>&copy; Echoes of Noordheim Forum 2024</span>
    </footer>
</main>

</body>
</html>
