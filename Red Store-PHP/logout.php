<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth_functions.php';

// Logout user
logoutUser();

// Redirect to home page
header("Location: index.php");
exit();
?>

