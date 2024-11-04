<?php

require('connect.php');

$query = "SELECT job_id, user_id, title, description, posted_date, location, status, category FROM jobs ORDER BY posted_date DESC LIMIT 10";
$statement = $db->prepare($query);
$statement->execute();

$postings = $statement->fetchAll();

$lastUpdatedQuery = "SELECT MAX(posted_date) AS last_updated FROM jobs";
$lastUpdatedStatement = $db->prepare($lastUpdatedQuery);
$lastUpdatedStatement->execute();
$lastUpdatedResult = $lastUpdatedStatement->fetch(PDO::FETCH_ASSOC);
$lastUpdatedDate = $lastUpdatedResult['last_updated'];

if ($lastUpdatedDate) {
    $formattedLastUpdatedDate = date('F j, Y, g:i A', strtotime($lastUpdatedDate));
} else {
    $formattedLastUpdatedDate = 'No postings available';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>JobWatch</title>
</head>
<body>
    <div id="main-header">
        <div id=nav>
            <ul>
                <li>HOME</li>
                <li>LOGIN</li>
                <li>SIGN-UP</li>
                <li>ABOUT</li>
            </ul>
        </div>
        <img id="logo"src="images/logo.png">
        <h3 id="future">Find your future.</h3>
    </div>
    <div id="search">
        <form method="POST" action="">
            <label id="searchlabel" for="search">Search job listings:</label>
            <input type="text" id="searchinput" name="search" required>
            <button class="btn" type="submit">Search</button>
        </form>
    </div>
    <div id="main-container">
        <div id="listings">
            <p class="title">New Jobs in your area</p>
            <p class="updated">Last updated: <?= htmlspecialchars($formattedLastUpdatedDate) ?></p>
            <?php foreach ($postings as $post): ?>
            <div class="listing_container">
                <a href="posting.php?id=<?= $post['job_id'] ?>"><h3><?= htmlspecialchars_decode($post['title']) ?></h3></a>
                <div class="status">
                     <?= htmlspecialchars_decode($post['status']) ?>
                </div>
                <p><?= htmlspecialchars_decode($post['location']) ?></p>
                <p>Category: <?= htmlspecialchars_decode($post['category']) ?></p>
                <?php
                    if (strlen($post['description']) > 200) {
                        $truncated_content = substr($post['description'], 0, 200) . '... ';
                        echo '<p class="content">' . nl2br(htmlspecialchars_decode($truncated_content)) . '<a href="post.php?id=' . $blogPost['id'] . '">Read full post</a></p>';
                    } else {
                        echo '<p class="content">' . nl2br(htmlspecialchars_decode($post['description'])) . '</p>';
                    }
                ?>

                <p class="timestamp">Posted on <?= htmlspecialchars_decode(date('F j, Y, g:i A', strtotime($post['posted_date']))) ?></p>
            </div>
        <?php endforeach; ?>
        </div>
        <div id ="postListing">
            <button class="btn" onclick="window.location.href='newPost.php'">Post a new listing!</button>
        </div>
        <div id="news">
            <p class="title">News</p>
            <p class="updated">Last updated: filler</p>
        </div>
        <div id="resources">
            <p class="title">Resources</p>
            <p class="updated">Last updated: filler</p>
        </div>
    </div>
    <div class="aboutdiv">
        <p class="about">JobWatch is a web app designed by students, for students, to aid them in their job search.</p>
        <p class="about">Signing up is quick and simple, just hit the button above to get started.</p>
        <p class="about"><p>
        <p class="about2">Copyright © 2024 By <a id="malcolm" href="https://github.com/Malcsta">Malcolm White</a>, for JobWatch Ltd.</p>
    </div>
</body>
</html>