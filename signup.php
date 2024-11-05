<?php

session_start();
require('connect.php');

$message = ""; // Empty error message 


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password

    // Check if username or email already exists
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]); 
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch results from db

    if (count($result) > 0) {
        $message = "<p id='signerror'>Username or email already taken!</p>"; // Error message
    } else {
        // Insert a new user
        $stmt = $db->prepare("INSERT INTO users (username, password, email, role_id, is_blocked) VALUES (?, ?, ?, 1, 1)");
        if ($stmt->execute([$username, $email, $password])) {
            $_SESSION['signup_success'] = "Sign-up successful! You can now sign in."; // Set session message
            header("Location: index.php"); // Redirect to index.php
            exit(); // Prevent further code execution
        } else {
            $message = "<p id='signerror'>Error: " . htmlspecialchars($stmt->errorInfo()[2]) . "</p>"; // Display PDO error
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
    <title>Sign Up</title>
</head>
<body>
    <div id="main-header">
        <div id=nav>
            <ul>
                <li><a class="headerlink" href="index.php">HOME</a></li>
                <li><a class="headerlink" href="login.php">LOGIN</a></li>
                <li><a class="headerlink" href="signup.php">SIGN-UP</a></li>
                <li><a class="headerlink" href="about.php">ABOUT</a></li>
            </ul>
        </div>
        <img id="logo"src="images/logo.png">
        <h3 id="future">Sign up below:</h3>
    </div>
    <div id="signup_container">
        <form method="post" action="">
            <div class="input_container">
                <input class="signup" type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input_container">
                <input class="signup" type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input_container">
                <input class="signup" type="password" name="password" placeholder="Password" required>           
            </div>
            <button id="signupbtn" type="submit" name="signup">Sign Up</button>
        </form>
    </div>
    <?php if (!empty($message)) echo $message; // Displaying the error message ?>
</div>
</body>
</html>