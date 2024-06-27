<?php
session_start();

// Check if logged in
if (!isset($_SESSION['loggedInUser'])) {
    header("Location: login.php");
    exit;
}

// Database connection details
$host = "localhost";
$database = "echoes_of_noordheim";
$user = "root";
$password = "";

// Connect to MySQL database
$db = mysqli_connect($host, $user, $password, $database) or die("Error: " . mysqli_connect_error());

// Function to escape HTML characters
function escape($html) {
    return htmlspecialchars($html, ENT_QUOTES | ENT_SUBSTITUTE, "UTF-8");
}

// Fetch logged-in user's details
$loggedInUser = $_SESSION['loggedInUser'];
$userId = $loggedInUser['id'];

// Handle profile picture upload/update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profilePic"])) {
    if ($_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
        $fileType = exif_imagetype($_FILES['profilePic']['tmp_name']);
        if ($fileType !== false) {
            // Prepare file name using username to ensure uniqueness
            $username = escape($loggedInUser['username']);
            $profilePic = $username . '_' . $_FILES['profilePic']['name']; // Unique filename based on username

            // Move uploaded file to current directory
            if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $profilePic)) {
                // Update profile picture in database
                $updateQuery = "UPDATE users SET profile_pic = '$profilePic' WHERE id = $userId";
                mysqli_query($db, $updateQuery);

                // Redirect back to account.php after successful update
                header("Location: account.php");
                exit;
            } else {
                echo "Failed to move uploaded file.";
            }
        } else {
            echo "File is not an image.";
        }
    } else {
        echo "Failed to upload file.";
    }
}

// Retrieve user's profile information including the updated profile picture
$queryUser = "SELECT * FROM users WHERE id = $userId";
$resultUser = mysqli_query($db, $queryUser);
$userData = mysqli_fetch_assoc($resultUser);

// Retrieve user's posts
$queryPosts = "SELECT * FROM posts WHERE user_id = $userId ORDER BY post_date DESC";
$resultPosts = mysqli_query($db, $queryPosts);
$userPosts = [];
while ($row = mysqli_fetch_assoc($resultPosts)) {
    // Convert binary image data to base64
    $imageData = base64_encode($row['image']);
    $imageType = 'image/jpeg'; // Adjust this based on your image format in the database

    $imageSrc = "data:{$imageType};base64,{$imageData}";

    // Store post with image source
    $row['image_src'] = $imageSrc;

    $userPosts[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echoes of Noordheim Forum - Account</title>
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
        <h1>Account</h1>
    </section>

    <section class="profile-section">
        <section class="profile-info">
            <?php if (!empty($userData['profile_pic'])): ?>
                <img src="<?= escape($userData['profile_pic']) ?>" alt="Profile Picture" class="profile-pic">
            <?php else: ?>
                <img src="img/placeholder.jpeg" alt="Profile Picture" class="profile-pic">
            <?php endif; ?>
            <h2><?= escape($userData['username']) ?></h2>
        </section>

        <section class="profile-actions">
            <h3>Update Profile Picture:</h3>
            <form action="account.php" method="post" enctype="multipart/form-data">
                <section class="form-element">
                    <label for="profilePic">Choose Image:</label>
                    <input type="file" id="profilePic" name="profilePic" accept="image/*" required>
                </section>
                <button type="submit">Upload</button>
            </form>
        </section>
    </section>

    <section class="user-posts">
        <h3>Your Posts:</h3>
        <?php if (!empty($userPosts)): ?>
            <?php foreach ($userPosts as $post): ?>
                <div class="post-item">
                    <small>Published on <?= htmlspecialchars($post['post_date']) ?></small>
                    <p><?= nl2br(htmlspecialchars($post['post_description'])) ?></p>
                    <?php if (!empty($post['image'])): ?>
                        <img src="<?= $post['image_src'] ?>" alt="Post Image" style="max-width: 100%; height: auto;">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No posts yet.</p>
        <?php endif; ?>
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
