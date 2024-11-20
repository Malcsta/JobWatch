<?php
session_start();
require('connect.php');

if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3) && isset($_GET['delete_post_id'])) {
    $post_id = $_GET['delete_post_id'];

    // Prepare the delete query
    $delete_post_query = "DELETE FROM jobs WHERE job_id = :post_id";
    $statement = $db->prepare($delete_post_query);
    $statement->bindValue(':post_id', $post_id, PDO::PARAM_INT);

    if ($statement->execute()) {
        // Redirect to home page after successful deletion
        header("Location: index.php");
        exit;
    } else {
        echo "Error: Unable to delete the post.";
    }
}

// Check if a comment deletion request is made
if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3) && isset($_GET['delete_comment_id']) && isset($_GET['id'])) {
    $comment_id = $_GET['delete_comment_id'];
    $job_id = $_GET['id'];

    // Prepare and execute the delete query
    $delete_query = "DELETE FROM comments WHERE comment_id = :comment_id";
    $statement = $db->prepare($delete_query);
    $statement->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);
    
    if ($statement->execute()) {
        // Redirect to the same page without the delete_comment_id parameter
        header("Location: fullpost.php?id=" . $job_id);
        exit;
    } else {
        echo "Error: Unable to delete the comment.";
    }
}

// Check if job_id is set in the URL to display the job post
if (isset($_GET['id'])) {
    $job_id = $_GET['id'];

    // Prepare the query to fetch the specific job posting
    $query = "
        SELECT jobs.job_id, jobs.url, jobs.title, jobs.company, jobs.description, jobs.posted_date, 
               jobs.location, jobs.status, jobs.category, users.username
        FROM jobs
        JOIN users ON jobs.user_id = users.user_id
        WHERE jobs.job_id = :job_id
    ";
    $statement = $db->prepare($query);
    $statement->bindValue(':job_id', $job_id, PDO::PARAM_INT);
    $statement->execute();

    // Fetch specific job posting
    $post = $statement->fetch(PDO::FETCH_ASSOC);

    // If no post is found display error
    if (!$post) {
        echo "This job posting does not exist.";
        exit;
    }

    $comments_query = "
        SELECT comments.comment_id, comments.comment_text, comments.comment_date, users.username
        FROM comments
        JOIN users ON comments.user_id = users.user_id
        WHERE comments.job_id = :job_id
        ORDER BY comments.comment_date DESC
    ";

    $comments_statement = $db->prepare($comments_query);
    $comments_statement->bindValue(':job_id', $job_id, PDO::PARAM_INT);
    $comments_statement->execute();
    $comments = $comments_statement->fetchAll(PDO::FETCH_ASSOC);

} else {
    // Redirect to home page if job_id is not set
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> - JobWatch</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js" defer></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'search.php'; ?>
    <div id="main-container">
        <?php include 'listing.php'; ?>
        <div id="comment_container">
            <?php foreach ($comments as $comment): ?>
                <div id="individual_comment">
                    <span id="user_line"><img id="user_icon" src="images/user.png"><p><?= htmlspecialchars_decode($comment['username'])?></p></span>
                    <p><?= htmlspecialchars_decode($comment['comment_text'])?></p>
                    <p id="comment_time">Posted on <?= htmlspecialchars_decode($comment['comment_date'])?></p>
                    <?php if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3)): ?>
                        <a href="fullpost.php?id=<?= $post['job_id'] ?>&delete_comment_id=<?= $comment['comment_id'] ?>" onclick="return confirm('Are you sure you want to delete this comment?');">
                            <img src="images/delete.png" id="delete_comment" alt="Delete" style="cursor: pointer; width: 20px; height: 20px;">
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>