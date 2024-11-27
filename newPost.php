<?php

session_start();
require('connect.php');

date_default_timezone_set('America/Winnipeg');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $company     = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $location    = filter_input(INPUT_POST, 'location', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $url         = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL);
    $categoryName = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $user_id     = $_SESSION['user_id']; 

    if ($title && $description && $location && $url && $categoryName) {
        try {
            $db->beginTransaction();

            // Check if the category exists
            $categoryQuery = "SELECT category_id FROM categories WHERE category_name = :categoryName";
            $categoryStmt = $db->prepare($categoryQuery);
            $categoryStmt->bindValue(':categoryName', $categoryName);
            $categoryStmt->execute();
            $category = $categoryStmt->fetch(PDO::FETCH_ASSOC);

            if ($category) {
                // Use existing category ID
                $category_id = $category['category_id'];
            } else {
                // Insert new category and get the ID
                $insertCategoryQuery = "INSERT INTO categories (category_name) VALUES (:categoryName)";
                $insertCategoryStmt = $db->prepare($insertCategoryQuery);
                $insertCategoryStmt->bindValue(':categoryName', $categoryName);
                $insertCategoryStmt->execute();

                $category_id = $db->lastInsertId();
            }

            // Insert the new job listing
            $posted_date = date('Y-m-d H:i:s');
            $jobQuery = "INSERT INTO jobs (user_id, title, company, description, posted_date, location, status, category, url) 
                         VALUES (:user_id, :title, :company, :description, :posted_date, :location, 'New', :category_id, :url)";
            $jobStmt = $db->prepare($jobQuery);
            $jobStmt->bindValue(':user_id', $user_id);
            $jobStmt->bindValue(':title', $title);
            $jobStmt->bindValue(':company', $company);
            $jobStmt->bindValue(':description', $description);
            $jobStmt->bindValue(':posted_date', $posted_date);
            $jobStmt->bindValue(':location', $location);
            $jobStmt->bindValue(':category_id', $category_id);
            $jobStmt->bindValue(':url', $url);

            if ($jobStmt->execute()) {
                $db->commit();
                header("Location: index.php");
                exit();
            } else {
                $db->rollBack();
                $error = "There was a problem submitting your job listing. Please try again.";
            }
        } catch (Exception $e) {
            $db->rollBack();
            $error = "An error occurred: " . $e->getMessage();
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
    <?php include 'header.php'; ?>
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