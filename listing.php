<?php
// Check if a post deletion request is made
if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3) && isset($_GET['delete_post_id'])) {
    $post_id = $_GET['delete_post_id'];

    // Prepare and execute the delete query
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
?>

<div class="listing_container">
    <div class="title-status-container">
        <a href="fullpost.php?id=<?= $post['job_id'] ?>"><h3 id="post_title"><?= htmlspecialchars_decode($post['title']) ?></h3></a>
        <div class="status 
            <?= $post['status'] === 'New' ? 'status-new' : '' ?>
            <?= $post['status'] === 'Active' ? 'status-active' : '' ?>
            <?= $post['status'] === 'Old' ? 'status-old' : '' ?>
            <?= $post['status'] === 'Closed' ? 'status-closed' : '' ?>">
            <p id="statustext"><?= htmlspecialchars_decode($post['status']) ?></p>
        </div>
        <div id="comment_icon">
            <a href="comment.php?job_id=<?= $post['job_id'] ?>" class="comment-link" title="Post a Comment">
                <img id="comment" src="images/chat.png" alt="Comment">
                <span class="comment-number">
                    <?php 
                        // Check if there are any comments otherwise display 0
                        echo isset($commentCountMap[$post['job_id']]) ? $commentCountMap[$post['job_id']] : 0;
                    ?>
                </span>
            </a>
        </div>
    </div>
    <p class="company"><?= htmlspecialchars_decode($post['company']) ?></p>
    <p class="location"><?= htmlspecialchars_decode($post['location']) ?> <img id="locationimg" src="images/mapicon.png"></p>
    <p><?= htmlspecialchars_decode($post['category_name']) ?></p>
    <p class="content"><?= nl2br(htmlspecialchars_decode($post['description'])) ?></p> <!-- Display the full description -->
    <p class="timestamp">Posted by <?= htmlspecialchars($post['username']) ?> on <?= htmlspecialchars_decode(date('F j, Y, g:i A', strtotime($post['posted_date']))) ?></p>
    <?php if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3)): ?>
        <a href="editpost.php?id=<?= $post['job_id'] ?>" title="Edit Post">
            <img src="images/edit.png" id="edit_post" alt="Edit" style="cursor: pointer">
        </a>
        <a href="fullpost.php?delete_post_id=<?= $post['job_id'] ?>" title="Delete Post" onclick="return confirm('Are you sure you want to delete this post?');">
            <img src="images/delete2.png" id="delete_comment" alt="Delete" style="cursor: pointer">
        </a>
    <?php endif; ?>
</div>