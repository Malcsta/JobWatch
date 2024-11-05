<?php

/*******w******** 
    Name: Malcolm White
    Date: 2024-09-18
    Description: This file contains the logic that is used to create a new job listing.
****************/

session_start();
require('connect.php');

date_default_timezone_set('America/Winnipeg');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $company     = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $location    = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $url         = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL); // Sanitize URL input
    $status      = "New"; 
    $category    = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_id     = $_SESSION['user_id']; 

    if ($title && $description && $location && $url && $category) {
        $posted_date = date('Y-m-d H:i:s'); 

        $query = "INSERT INTO jobs (user_id, title, company, description, posted_date, location, status, category, url) 
                  VALUES (:user_id, :title, :company, :description, :posted_date, :location, :status, :category, :url)";
        $statement = $db->prepare($query);

        $statement->bindValue(':user_id', $user_id);
        $statement->bindValue(':title', $title);
        $statement->bindValue(':company', $company);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':posted_date', $posted_date);
        $statement->bindValue(':location', $location);
        $statement->bindValue(':status', $status);
        $statement->bindValue(':category', $category);
        $statement->bindValue(':url', $url); // Bind the URL

        if ($statement->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $error = "There was a problem submitting your job listing. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>New Job Listing</title>
</head>
<body>
    <div id="main_container">
        <h1>Submit a New Job Listing</h1>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="newPost.php" method="POST">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required><br>

            <label for="title">Company:</label>
            <input type="text" id="company" name="company" required><br>

            <label for="description">Description:</label><br>
            <textarea id="description" name="description" rows="4" required></textarea><br>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required><br>

            <label for="url">URL:</label>
            <input type="url" id="url" name="url" required><br> <!-- New input for URL -->

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required><br>

            <input type="submit" value="Submit Job Listing">
        </form>
    </div>
</body>
</html>