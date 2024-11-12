<?php
session_start();
require('connect.php');

$message = ""; // Empty error message

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $message = "<p id='signerror'>Passwords do not match!</p>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Check if username or email already exists
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($result) > 0) {
            $message = "<p id='signerror'>Username or email already taken!</p>";
        } else {
            // Insert new user
            $stmt = $db->prepare("INSERT INTO users (username, password, email, role_id, is_blocked) VALUES (?, ?, ?, 1, 0)");
            if ($stmt->execute([$username, $hashedPassword, $email])) {
                $_SESSION['signup_success'] = "Sign-up successful! You can now sign in.";
                header("Location: index.php");
                exit();
            } else {
                $message = "<p id='signerror'>Error: " . htmlspecialchars($stmt->errorInfo()[2]) . "</p>";
            }
        }
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
    <div id="signup_container">
        <h2 id="info">Enter your information to get started:</h2>
        <form method="post" action="" id="userinfo">
            <div class="input_container">
                <input class="signup" type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input_container">
                <input class="signup" type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input_container">
                <input class="signup" type="password" name="password" placeholder="Password" required>           
            </div>
            <div class="input_container">
                <input class="signup" type="password" name="confirm_password" placeholder="Confirm Password" required>           
            </div>
            <button id="signupbtn" type="submit" name="signup">Sign Up</button>
        </form>
    </div>
    <?php if (!empty($message)) echo $message; // Displaying the error message ?>
</div>
</body>
</html>