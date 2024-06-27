<?php
session_start();

$errors = [];

if (isset($_POST['submit'])) {
    // Database connection details
    $host = "localhost";
    $database = "echoes_of_noordheim";
    $user = "root";
    $password = "";

    // Connect to MySQL database
    $db = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());

    // Sanitize and validate input data
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = $_POST['password'];

    // Validate username
    if (empty($username)) {
        $errors['username'] = 'Please enter a username';
    }

    // Validate email
    if (empty($email)) {
        $errors['email'] = 'Please enter an email address';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address';
    }

    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Please enter a password';
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Placeholder image for profile pic
        $profilePic = 'img/placeholder.jpeg'; // Ensure this file exists in the specified path

        // Insert user into database
        $query = "INSERT INTO users (username, email, password, profile_pic) VALUES ('$username', '$email', '$passwordHash', '$profilePic')";
        $result = mysqli_query($db, $query);

        if ($result) {
            // Redirect to login page after successful registration
            header("Location: login.php");
            exit;
        } else {
            $errors['database'] = 'Database insertion failed. Please try again.';
        }
    }

    // Close the database connection
    mysqli_close($db);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echoes of Noordheim Forum - Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
    <h1 class="title">Account</h1>
</header>
<section class="sidebar">
    <section class="title">Echoes of Noordheim Forum</section>
    <menu>
        <li><a href="index.php">Home</a></li>
        <li><a href="posts.php">Posts</a></li>
        <li><a href="account.php">Account</a></li>
        <li><a href="aboutus.php">About us</a></li>
    </menu>
</section>
<main class="main-content">
    <section class="title">
        <h1>Register</h1>
    </section>
    <form action="" method="post" class="register-form">
        <section class="form-element">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Enter your username" value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" required>
            <p><?= isset($errors['username']) ? htmlspecialchars($errors['username']) : '' ?></p>
        </section>
        <section class="form-element">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" placeholder="Enter your email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" required>
            <p><?= isset($errors['email']) ? htmlspecialchars($errors['email']) : '' ?></p>
        </section>
        <section class="form-element">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Enter your password" required>
            <p><?= isset($errors['password']) ? htmlspecialchars($errors['password']) : '' ?></p>
        </section>
        <section class="form-element">
            <button type="submit" name="submit">Register</button>
        </section>
        <p>Already have an account? <a href="login.php">Login here</a></p>
        <p><?= isset($errors['database']) ? htmlspecialchars($errors['database']) : '' ?></p>
    </form>
</main>
</body>
</html>

