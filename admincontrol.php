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

// Fetch the sorting parameters from the URL, with defaults
$sort_column = $_GET['column'] ?? 'title';
$sort_order = $_GET['order'] ?? 'asc';

// Sanitize input to prevent SQL injection
$valid_columns = ['title', 'posted_date', 'updated_at'];
$sort_column = in_array($sort_column, $valid_columns) ? $sort_column : 'title';
$sort_order = ($sort_order === 'desc') ? 'desc' : 'asc';

// Toggle the sort order for the next click
$next_order = ($sort_order === 'asc') ? 'desc' : 'asc';

// Fetch sorted data from the database
$query = "SELECT job_id, user_id, title, company, posted_date, status, category FROM jobs 
          ORDER BY $sort_column $sort_order";
$jobs = $db->query($query)->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page or display an error message
    header('Location: login.php');
    exit();
}

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

// Determine which column the table is sorted by
$sort_column = isset($_GET['column']) ? $_GET['column'] : null;
$sort_order = isset($_GET['order']) ? $_GET['order'] : null;

// Default message
$sort_message = "Table is sorted by: ";

switch ($sort_column) {
    case 'title':
        $sort_message .= "Title";
        break;
    case 'company':
        $sort_message .= "Company";
        break;
    case 'posted_date':
        $sort_message .= "Date Posted";
        break;
    case 'status':
        $sort_message .= "Status";
        break;
    default:
        $sort_message .= "Default Order";
        break;
}

// Add the order if available
if ($sort_order) {
    $sort_message .= " in " . ($sort_order === 'asc' ? "Ascending" : "Descending") . " Order";
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
    <script src="scripts.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <div id="users_list">
    <div id="allposts">
        <h1>All Posts</h1>
        <p><?= $sort_message ?></p>
        <table id="posts_table">
            <tr>
                <th><a class="sort" href="?column=title&order=<?=$next_order?>">Title</a></th>
                <th>User ID</th>
                <th><a class="sort" href="?column=company&order=<?=$next_order?>">Company</a></th>
                <th><a class="sort" href="?column=posted_date&order=<?=$next_order?>">Date Posted</a></th>
                <th><a class="sort" href="?column=status&order=<?=$next_order?>">Status</a></th>
                <th>Category</th>
            </tr>
            <?php foreach ($jobs as $job): ?>
                <tr>
                    <td><?=$job['title']?></td>
                    <td><?=$job['user_id']?></td>
                    <td><?=$job['company']?></td>
                    <td><?=$job['posted_date']?></td>
                    <td><?=$job['status']?></td>
                    <td><?=$job['category']?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
        <h1>All Users</h1>
        <table id="user_table">
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
</body>
</html>