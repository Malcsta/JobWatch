<?php
session_start();
require('connect.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page or display an error message
    header('Location: login.php');
    exit();
}

// Get the job_id from URL
$job_id = isset($_GET['job_id']) ? $_GET['job_id'] : 0;

// Check if user is logged in (session-based user validation)
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to comment.";
    exit;
}

$user_id = $_SESSION['user_id'];  // The user who is posting the comment
$captcha_error = ""; // Initialize captcha error message
$comment_text = "";  // Initialize comment text to retain user input

// Generate a random CAPTCHA
function generateCaptcha() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    $captcha_string = '';
    for ($i = 0; $i < 7; $i++) {
        $captcha_string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }
    $_SESSION['captcha'] = $captcha_string;  // Store the CAPTCHA in session

    // Create the CAPTCHA image
    $image = imagecreate(120, 40);
    $bg_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);  
    $line_color = imagecolorallocate($image, 64, 64, 64);  

    imagefilledrectangle($image, 0, 0, 120, 40, $bg_color);
    for ($i = 0; $i < 5; $i++) {
        imageline($image, mt_rand(0, 120), mt_rand(0, 40), mt_rand(0, 120), mt_rand(0, 40), $line_color);
    }
    imagestring($image, 5, 30, 10, $captcha_string, $text_color);

    // Output the image to the browser
    header('Content-Type: image/png');
    imagepng($image);
    imagedestroy($image);
}

// If the CAPTCHA is requested, generate and display i
if (isset($_GET['captcha']) && $_GET['captcha'] == 1) {
    generateCaptcha();
    exit; 
}

// Verify CAPTCHA and process the form with POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_field'], $_POST['captcha_input'])) {
    $comment_text = htmlspecialchars_decode($_POST['comment_field']); // Retain user commedt

    if ($_POST['captcha_input'] !== $_SESSION['captcha']) {
        $captcha_error = "Incorrect CAPTCHA. Please try again."; // Set error message
    } else {
        // Ensure that the comment text isn't empty
        if (!empty($comment_text)) {
            // Prepare the query to insert the comment
            $stmt = $db->prepare("INSERT INTO comments (user_id, job_id, comment_text, comment_date) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$user_id, $job_id, $comment_text]);

            // Redirect to the fullpost page with success message
            header("Location: fullpost.php?id=" . $job_id);
            exit;
        } else {
            echo "Comment cannot be empty.";
        }
    }
}
?>

<!-- Comment submission form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comment on a Job Posting</title>
    <link rel="stylesheet" href="styles.css">
    <script src="js/scripts.js" defer></script>
</head>
<body>
    <?php include 'header.php'; ?>
        <form action="comment.php?job_id=<?= $job_id ?>" method="POST">
            <div id="comment_container_post">
                <div class="input-container">
                    <input type="text" name="comment_field" id="comment_field" required placeholder="Enter your comment" value="<?= htmlspecialchars($comment_text) ?>">
                </div>
                
                <!-- CAPTCHA -->
                <div class="captcha-container">
                    <img src="comment.php?captcha=1" alt="CAPTCHA" id="captcha_image">
                    <input type="text" name="captcha_input" id="captcha_input" required placeholder="Enter CAPTCHA">
                    
                    <!-- Display CAPTCHA error if it exists -->
                    <?php if (!empty($captcha_error)) : ?>
                        <p id="captcha_error" style="color:red;"><?= $captcha_error ?></p>
                    <?php endif; ?>

                    <button type="submit">Submit Comment</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>