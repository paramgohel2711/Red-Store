<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth_functions.php';
require_once '../includes/admin_functions.php';

// Require admin login
requireAdmin();

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];

// Delete product
if (deleteProduct($conn, $product_id)) {
    // Redirect with success message
    header("Location: products.php?deleted=1");
} else {
    // Redirect with error message
    header("Location: products.php?error=1");
}
exit();
