<?php
session_start();
require('connect.php');

$message = ""; // Initialize an empty message

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signin'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user data
    
    // Verify user and password
    if ($user && password_verify($password, $user['password'])) {
        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['signin_success'] = "Welcome, " . htmlspecialchars($user['username']) . "!"; // Success message
        $_SESSION['welcome_message'] = "Sign in successful!";
        header("Location: index.php"); // Redirect to the index page
        exit(); // Prevent further code execution
    } else {
        $message = "<p id='signerror'>Invalid username or password!</p>"; // Error message for invalid credentials
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="images/icon.png">
    <script src="scripts.js" defer></script>
    <title>JobWatch</title>
</head>
<body>
    <?php include 'header.php'; ?>
    <div id="signin_container">
        <form method="post" action="">
            <div class="input_container">
                <input class="signup" type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input_container">
                <input class="signup" type="password" name="password" placeholder="Password" required>
            </div>
            <button id="signinbtn" type="submit" name="signin">Sign In</button>
        </form>
    </div>
    <?php if (!empty($message)) echo $message; // Display the error message ?>
</body>
</html>