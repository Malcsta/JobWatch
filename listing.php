<div class="listing_container">
    <div class="title-status-container">
        <a href="fullpost.php?id=<?= $post['job_id'] ?>"><h3><?= htmlspecialchars_decode($post['title']) ?></h3></a>
        <div class="status 
            <?= $post['status'] === 'New' ? 'status-new' : '' ?>
            <?= $post['status'] === 'Active' ? 'status-active' : '' ?>
            <?= $post['status'] === 'Old' ? 'status-old' : '' ?>
            <?= $post['status'] === 'Closed' ? 'status-closed' : '' ?>">
            <p id="statustext"><?= htmlspecialchars_decode($post['status']) ?></p>
        </div>
        <div id="comment_icon">
            <a href="comment.php?job_id=<?= $post['job_id'] ?>" class="comment-link">
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
    <p>Category: <?= htmlspecialchars_decode($post['category']) ?></p>
    <?php
        if (strlen($post['description']) > 200) {
            $truncated_content = substr($post['description'], 0, 200) . '... ';
            echo '<p class="content">' . nl2br(htmlspecialchars_decode($truncated_content)) . '<a href="post.php?id=' . $post['job_id'] . '">Read full post</a></p>';
        } else {
            echo '<p class="content">' . nl2br(htmlspecialchars_decode($post['description'])) . '</p>';
        }
    ?>
    <p class="timestamp">Posted by <?= htmlspecialchars($post['username']) ?> on <?= htmlspecialchars_decode(date('F j, Y, g:i A', strtotime($post['posted_date']))) ?></p>
</div>