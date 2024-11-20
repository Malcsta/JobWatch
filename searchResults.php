<?php
session_start();
require('connect.php');

// Get the search term and current page number
$searchTerm = $_POST['search'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$resultsPerPage = 5; // Number of results per page
$offset = ($page - 1) * $resultsPerPage;

// Prepare the query to search jobs with pagination
$query = "
    SELECT jobs.job_id, jobs.title, jobs.company, jobs.description, jobs.posted_date, 
           jobs.location, jobs.status, jobs.category, users.username
    FROM jobs
    JOIN users ON jobs.user_id = users.user_id
    WHERE jobs.title LIKE :searchTerm
       OR jobs.company LIKE :searchTerm
       OR jobs.description LIKE :searchTerm
       OR jobs.location LIKE :searchTerm
    ORDER BY jobs.posted_date DESC
    LIMIT :limit OFFSET :offset
";
$statement = $db->prepare($query);

// Bind the parameters
$statement->bindValue(':searchTerm', "%$searchTerm%");
$statement->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
$statement->bindValue(':offset', $offset, PDO::PARAM_INT);
$statement->execute();

// Fetch the results
$postings = $statement->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of results for pagination
$totalQuery = "
    SELECT COUNT(*) AS total
    FROM jobs
    JOIN users ON jobs.user_id = users.user_id
    WHERE jobs.title LIKE :searchTerm
       OR jobs.company LIKE :searchTerm
       OR jobs.description LIKE :searchTerm
       OR jobs.location LIKE :searchTerm
";
$totalStatement = $db->prepare($totalQuery);
$totalStatement->bindValue(':searchTerm', "%$searchTerm%");
$totalStatement->execute();
$totalResults = $totalStatement->fetch(PDO::FETCH_ASSOC)['total'];

// Calculate the total number of pages
$totalPages = ceil($totalResults / $resultsPerPage);
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
            <div id="pagination">
                <!-- Loop through page numbers -->
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a id="page_number" href="?search=<?= urlencode($searchTerm) ?>&page=<?= $i ?>" class="pagination-link <?= ($i == $page) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</body>
</html>