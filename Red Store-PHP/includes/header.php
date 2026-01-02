<?php 
require_once 'config.php';
require_once 'functions.php';
require_once 'auth_functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | Athletic Store</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <?php if (basename($_SERVER['PHP_SELF']) == 'login.php' || 
              basename($_SERVER['PHP_SELF']) == 'signup.php' || 
              basename($_SERVER['PHP_SELF']) == 'account.php'): ?>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/account.css">
    <?php endif; ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <a href="<?php echo SITE_URL; ?>">
                        <img src="<?php echo getImageUrl('logo.png'); ?>" alt="RedStore" width="125px">
                    </a>
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="products.php">Products</a></li>
                        <li><a href="about.php">About</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="account.php">My Account</a></li>
                            <li><a href="logout.php">Logout</a></li>
                        <?php else: ?>
                            <li><a href="login.php">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <div class="cart-icon">
                    <a href="<?php echo SITE_URL; ?>/cart.php">
                        <img src="<?php echo getImageUrl('cart.png'); ?>" alt="Cart" width="30px" height="30px">
                        <?php 
                        $cart = getCartItems();
                        $cartCount = count($cart);
                        if ($cartCount > 0) {
                            echo '<span class="cart-count">' . $cartCount . '</span>';
                        }
                        ?>
                    </a>
                </div>
                <div class="menu-icon" onclick="toggleMenu()">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
        </div>
    </header>
