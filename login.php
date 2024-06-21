<?php
$host = "localhost";
$database = "echoes_of_noordheim";
$user = "root";
$password = "";

$errors = [];

$db = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());
session_start();

$login = false;

// Is user logged in?
if (isset($_SESSION['loggedInUser'])) {
    $login = true;
    $user = $_SESSION['loggedInUser'];
}

if (isset($_POST['submit'])) {
    // Get form data
    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = $_POST['password'];

    // Server-side validation
    if ($email == '') {
        $errors['email'] = 'Please fill in your email.';
    }
    if ($password == '') {
        $errors['password'] = 'Please fill in your password.';
    }

    // If data valid
    if (empty($errors)) {
        // SELECT the user from the database, based on the email address.
        $query = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($db, $query);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $login = true;

            // Store the user in the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['loggedInUser'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'username' => $user['username'],
                'is_admin' => $user['is_admin'],
            ];

            // Redirect to secure page
            header('Location: index.php'); // Change this to your secure page
            exit;
        } else {
            $errors['loginFailed'] = 'The provided credentials do not match.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
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
    <?php if ($login) { ?>
        <p>You've been logged in!</p>
        <?php $user = $_SESSION['loggedInUser']; ?>
        <?php if ($user['is_admin'] == "1") { ?>
            <a href="news.php">See assignments</a>
        <?php } ?>
        <p><a href="logout.php">Logout</a></p>
    <?php } else { ?>
        <form action="" method="post" class="test">
            <div class="form-element">
                <label for="email">E-mail</label>
                <input id="email" type="email" name="email" placeholder="Type your email address here" value="<?php echo htmlspecialchars(isset($email) ? $email : ''); ?>" />
                <p><?php echo htmlspecialchars(isset($errors['email']) ? $errors['email'] : ''); ?></p>
            </div>
            <div class="form-element">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Type your password here">
                <p><?php echo htmlspecialchars(isset($errors['password']) ? $errors['password'] : ''); ?></p>
                <?php if (isset($errors['loginFailed'])) { ?>
                    <div><?php echo htmlspecialchars($errors['loginFailed']); ?></div>
                <?php } ?>
            </div>
            <p>No account? <a href="register.php">Register here</a>!</p>
            <div class="form-element">
                <button type="submit" name="submit">Login</button>
            </div>
        </form>
    <?php } ?>
</main>
</body>
</html>

