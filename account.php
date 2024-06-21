<?php


session_start();

//Checks if logged in
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Echeos of Noordheim Forum</title>

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
    <section class ="title">
        <h1>Account</h1>
    </section>
    <p>This is the main content area.</p>
    <!-- Add more content here to enable scrolling -->
</main>
</body>
</html>
