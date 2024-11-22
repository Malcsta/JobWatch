<?php
session_start();
require('connect.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page or display an error message
    header('Location: login.php');
    exit();
}

if (!isset($_GET['user_id'])) {
    header('Location: admincontrol.php');
    exit();
}

$user_id = $_GET['user_id'];

// Fetch user data
$query = "SELECT * FROM users WHERE user_id = ?";
$statement = $db->prepare($query);
$statement->execute([$user_id]);
$user = $statement->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];

    $query = "UPDATE users SET username = ?, email = ?, role_id = ? WHERE user_id = ?";
    $stmt = $db->prepare($query);

    if ($stmt->execute([$username, $email, $role_id, $user_id])) {
        $_SESSION['edit_success'] = "User information updated successfully.";
        header('Location: admincontrol.php');
        exit();
    } else {
        $message = "<p>Error: " . htmlspecialchars($stmt->errorInfo()[2]) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include ('header.php'); ?>
    <div id="edit_users_list">
        <h1>Edit User</h1>
        <?php if (!empty($message)) echo $message; ?>
        <form method="POST" action="edit_user.php?user_id=<?= htmlspecialchars($user_id) ?>">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username2" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="form-group">
                <label for="role_id">Role:</label>
                <input type="number" id="role_id" name="role_id" value="<?= htmlspecialchars($user['role_id']) ?>" required>
            </div>
            <div class="form-actions">
                <button type="submit">Save Changes</button>
            </div>
        </form>
        <a href="admincontrol.php">Cancel</a>
    </div>
</body>
</html>