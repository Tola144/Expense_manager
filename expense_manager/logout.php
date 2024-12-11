<?php
// Start the session
session_start();
// Destroy all session data
session_unset();
session_destroy();
// Redirect to the login page
header("Location: login.php");
exit();


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>