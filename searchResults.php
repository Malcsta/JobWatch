<?php
session_start();
require('connect.php');

// Get the search term
$searchTerm = $_POST['search'] ?? '';

// Prepare the query to search jobs
$query = "
    SELECT jobs.job_id, jobs.title, jobs.company, jobs.description, jobs.posted_date, 
           jobs.location, jobs.status, jobs.category, users.username
    FROM jobs
    JOIN users ON jobs.user_id = users.user_id
    WHERE jobs.title LIKE :searchTerm
       OR jobs.company LIKE :searchTerm
       OR jobs.description LIKE :searchTerm
    ORDER BY jobs.posted_date DESC
";
$statement = $db->prepare($query);

// Bind the search term with wildcards for partial matches
$statement->bindValue(':searchTerm', "%$searchTerm%");
$statement->execute();

// Fetch the results
$postings = $statement->fetchAll(PDO::FETCH_ASSOC);
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
    <?php include 'search.php'; ?>
    <div id="main-container">
        <div id="listings">
            <p class="title">Search Results</p>
            <?php if (!empty($postings)): ?>
                <?php foreach ($postings as $post): ?>
                    <?php include 'listing.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p id="no_results">No listings found. Try a different search term.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>