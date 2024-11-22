<?php
session_start();
require('connect.php');

$query = "
    SELECT *
    FROM users
";
$statement = $db->prepare($query);
$statement->execute();

$users = $statement->fetchAll(PDO::FETCH_ASSOC);

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
                header("Location: admincontrol.php");
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
    <title>JobWatch Control Panel</title>
    <script src="scripts.js" defer></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div id="users_list">
        <h1>All Users</h1>
        <table id="user_table">
            <th>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Email</th>
                </tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['user_id']?></td>
                    <td><?= $user['username']?></td>
                    <td><?= $user['role_id']?></td>
                    <td><?= $user['email']?></td>
                    <td class="useroptions">
                    <form action="edit_user.php" method="GET" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                        <button type="submit">Edit</button>
                    </form>
                    <form action="delete_user.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['user_id']) ?>">
                        <button type="submit">Delete</button>
                    </form>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
        <div id="signup_container">
        <h1>New User</h1>
        <form method="POST" action="admincontrol.php" id="userinfo">
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
            <button id="signupbtn" type="submit" name="signup">New User</button>
        </form>
        <?php if (!empty($message)) echo $message; // Displaying the error message ?>
    </div>
    <div>
</body>
</html>