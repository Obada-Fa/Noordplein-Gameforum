<?php
if (isset($_POST['submit'])) {
    $host = "localhost";
    $database = "echoes_of_noordheim";
    $user = "root";
    $password = "";

    $errors = [];

    // Connect to the database
    $db = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());

    // Get form data
    $username = mysqli_real_escape_string($db, $_POST['username']);
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = $_POST['password'];

    // Validation
    if ($username == '') {
        $errors['username'] = 'Please fill in your username.';
    }
    if ($email == '') {
        $errors['email'] = 'Please fill in your email.';
    }
    if ($password == '') {
        $errors['password'] = 'Please fill in your password.';
    }

    if (empty($errors)) {
        // Create a secure password, with the PHP function password_hash()
        $password = password_hash($password, PASSWORD_DEFAULT);
        // Store the new user in the database.
        $query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
        $result = mysqli_query($db, $query);

        // If query succeeded
        if ($result) {
            // Redirect to login page
            header('Location: login.php');
            // Exit the code
            exit;
        } else {
            $errors['database'] = 'Database insertion failed. Please try again.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Echoes of Noordheim Forum</title>
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
    <form action="" method="post" class="test">
        <div class="form-element">
            <label for="username">Username</label>
            <input id="username" type="text" name="username" placeholder="Type your username here" value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" />
            <p><?php echo isset($errors['username']) ? htmlspecialchars($errors['username']) : ''; ?></p>
        </div>
        <div class="form-element">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" placeholder="Type your email address here" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" />
            <p><?php echo isset($errors['email']) ? htmlspecialchars($errors['email']) : ''; ?></p>
        </div>
        <div class="form-element">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" placeholder="Type your password here">
            <p><?php echo isset($errors['password']) ? htmlspecialchars($errors['password']) : ''; ?></p>
        </div>
        <p>Already have an account? <a href="login.php">Login Here</a>!</p>
        <div class="form-element">
            <button type="submit" name="submit">Register</button>
        </div>
        <p><?php echo isset($errors['database']) ? htmlspecialchars($errors['database']) : ''; ?></p>
    </form>
</main>
</body>
</html>

