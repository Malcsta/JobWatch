<?php
session_start();
require('connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the user ID from the form
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT);

    if ($user_id) {
        // Prepare the DELETE query
        $query = "DELETE FROM users WHERE user_id = :user_id";
        $statement = $db->prepare($query);
        $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        // Execute the query
        if ($statement->execute()) {
            $_SESSION['message'] = "User deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete user.";
        }
    } else {
        $_SESSION['error'] = "Invalid user ID.";
    }

    // Redirect back to the users list
    header('Location: users_list.php');
    exit;
}
?>