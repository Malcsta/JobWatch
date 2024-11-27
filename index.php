<?php

session_start();
require('connect.php');

$selectedCategory = filter_input(INPUT_GET, 'category', FILTER_VALIDATE_INT);
$query = "
    SELECT jobs.job_id, jobs.title, jobs.company, jobs.description, jobs.posted_date, 
           jobs.location, jobs.status, jobs.category, users.username, categories.category_name
    FROM jobs
    JOIN users ON jobs.user_id = users.user_id
    JOIN categories ON jobs.category = categories.category_id
";
if ($selectedCategory) {
    $query .= " WHERE jobs.category = :selectedCategory";
}
$query .= " ORDER BY jobs.posted_date DESC LIMIT 10";
$statement = $db->prepare($query);

if ($selectedCategory) {
    $statement->bindValue(':selectedCategory', $selectedCategory, PDO::PARAM_INT);
}

$statement->execute();
$postings = $statement->fetchAll(PDO::FETCH_ASSOC);

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

// Query to get the number of comments for each job posting
$commentsQuery = "
    SELECT job_id, COUNT(*) AS comment_count
    FROM comments
    GROUP BY job_id
";
$commentsStatement = $db->prepare($commentsQuery);
$commentsStatement->execute();
$commentCounts = $commentsStatement->fetchAll(PDO::FETCH_ASSOC);

// Create an associative array of job_id => comment_count for easy lookup
$commentCountMap = [];
foreach ($commentCounts as $comment) {
    $commentCountMap[$comment['job_id']] = $comment['comment_count'];
}

// Query to get categories
$categoriesQuery = "SELECT category_id, category_name FROM categories";
$categoriesStatement = $db->prepare($categoriesQuery);
$categoriesStatement->execute();
$categories = $categoriesStatement->fetchAll(PDO::FETCH_ASSOC);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="images/icon.png">
    <title>JobWatch</title>
    <script src="scripts.js" defer></script>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php if (isset($_SESSION['welcome_message'])): ?>
        <script>
            // Display a dialog box when the sign-in is successful
            alert("<?php echo $_SESSION['welcome_message']; ?>");
        </script>
        <?php unset($_SESSION['welcome_message']); // Unset the message after displaying ?>
    <?php endif; ?>
    <?php include 'search.php'; ?>
    <div id="main-container">
        <div id="listings">
            <p class="title">New Jobs in your area</p>
            <p class="updated">Last updated: <?= htmlspecialchars($formattedLastUpdatedDate) ?></p>
            <div id="filter">
                <form method="GET" action="index.php">
                    <select name="category" id="category" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category['category_id']) ?>" 
                                <?= $selectedCategory == $category['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        <?php foreach ($postings as $post): ?>
            <?php include 'listing.php'; ?>
        <?php endforeach; ?>
        </div>
        <?php if (isset($_SESSION['username'])): ?>
            <div id ="postListing">
                <button class="btn" onclick="window.location.href='newPost.php'">Post a new listing!</button>
            </div>
        <?php endif; ?>
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