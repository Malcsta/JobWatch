<?php
session_start();
require('connect.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page or display an error message
    header('Location: login.php');
    exit();
}

// Check if the user is authorized to edit posts
if (!isset($_SESSION['role_id']) || ($_SESSION['role_id'] != 2 && $_SESSION['role_id'] != 3)) {
    header("Location: index.php");
    exit;
}

// Check if the job id is set and fetch the post details
if (isset($_GET['id'])) {
    $job_id = $_GET['id'];

    $query = "SELECT * FROM jobs WHERE job_id = :job_id";
    $statement = $db->prepare($query);
    $statement->bindValue(':job_id', $job_id, PDO::PARAM_INT);
    $statement->execute();
    $post = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        echo "Post not found.";
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

// Handling form submission to update the post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $company = $_POST['company'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $status = $_POST['status'];

    $update_query = "
        UPDATE jobs 
        SET title = :title, company = :company, description = :description, 
            location = :location, category = :category, status = :status
        WHERE job_id = :job_id
    ";
    $update_statement = $db->prepare($update_query);
    $update_statement->bindValue(':title', $title, PDO::PARAM_STR);
    $update_statement->bindValue(':company', $company, PDO::PARAM_STR);
    $update_statement->bindValue(':description', $description, PDO::PARAM_STR);
    $update_statement->bindValue(':location', $location, PDO::PARAM_STR);
    $update_statement->bindValue(':category', $category, PDO::PARAM_STR);
    $update_statement->bindValue(':status', $status, PDO::PARAM_STR);
    $update_statement->bindValue(':job_id', $job_id, PDO::PARAM_INT);

    if ($update_statement->execute()) {
        header("Location: fullpost.php?id=" . $job_id);
        exit;
    } else {
        echo "Error updating the post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'search.php'; ?>
    <div id="main-container">
        <form method="POST">
            <div id="edit_container">
                <input type="text" id="edit_title" name="title" value="<?= htmlspecialchars_decode($post['title']) ?>" required>

                <input type="text" id="edit_company" name="company" value="<?= htmlspecialchars_decode($post['company']) ?>" required>

                <input type="text" id="edit_location" name="location" value="<?= htmlspecialchars_decode($post['location']) ?>" required>

                <input type="text" id="edit_category" name="category" value="<?= htmlspecialchars_decode($post['category']) ?>" required>

                <textarea id="edit_content" name="description" required><?= htmlspecialchars_decode($post['description']) ?></textarea>

                <select id="edit_status" name="status" required>
                    <option value="New" <?= $post['status'] === 'New' ? 'selected' : '' ?>>New</option>
                    <option value="Active" <?= $post['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                    <option value="Old" <?= $post['status'] === 'Old' ? 'selected' : '' ?>>Old</option>
                    <option value="Closed" <?= $post['status'] === 'Closed' ? 'selected' : '' ?>>Closed</option>
                </select>

                <button type="submit" id="update_post_button">Update Post</button>
            </div>
        </form>
    </div>
</body>
</html>