<?php
// Start the session to check if the user is already logged in
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to the dashboard if logged in
    header("Location: dashboard.php");
    exit;
} else {
    // Redirect to the login page if not logged in
    header("Location: login.php");
    exit;
}
?>
