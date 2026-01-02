<?php
// Database connection
$server = "localhost";
$username = "root";
$password = "";
$database = "redstore";

$conn = mysqli_connect($server, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Site settings
define("SITE_NAME", "RedStore");
define("SITE_URL", "http://localhost/newredstore");

// Image paths
define("IMAGE_PATH", SITE_URL . "/assets/images");
?>

