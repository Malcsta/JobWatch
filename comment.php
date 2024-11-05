<?php

session_start();
require("connect.php");

$user_id = $_SESSION['user_id'];
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $comment_text = htmlspecialchars($_POST['comment_text']);

    $stmt = $db->prepare("INSERT INTO comments (job_id, user_id, comment_text, comment_date) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$job_id, $user_id, $comment_text]); 
    echo "Comment added successfully!";
}

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

</body>
</html>