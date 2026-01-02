<?php 
include 'includes/header.php'; 

// Handle cart actions
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $product_id => $quantity) {
        updateCartItem($product_id, $quantity);
    }
}

if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    removeFromCart($product_id);
}

$cart = getCartItems();
$cartTotal = calculateCartTotal($conn);
?>

<!-- Cart Page -->
<div class="cart-page">
    <div class="container">
        <h2>View Cart</h2>
        
        <?php if (empty($cart)) { ?>
            <div class="empty-cart">
                <p>Your cart is empty.</p>
                <a href="products.php" class="btn">Continue Shopping</a>
            </div>
        <?php } else { ?>
            <form method="post" action="">
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                    
                    <?php foreach ($cart as $product_id => $quantity) {
                        $product = getProductById($conn, $product_id);
                        if ($product) {
                            $subtotal = $product['price'] * $quantity;
                    ?>
                    <tr>
                        <td>
                            <div class="cart-info">
                                <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                                <div>
                                    <p><?php echo $product['name']; ?></p>
                                    <small>Price: <?php echo formatPrice($product['price']); ?></small>
                                    <br>
                                    <a href="cart.php?remove=<?php echo $product_id; ?>">Remove</a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="number" name="quantity[<?php echo $product_id; ?>]" value="<?php echo $quantity; ?>" min="1" max="<?php echo $product['stock']; ?>">
                        </td>
                        <td><?php echo formatPrice($subtotal); ?></td>
                    </tr>
                    <?php 
                        }
                    } 
                    ?>
                </table>
                
                <div class="total-price">
                    <table>
                        <tr>
                            <td>Subtotal</td>
                            <td><?php echo formatPrice($cartTotal); ?></td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td><?php echo formatPrice($cartTotal * 0.05); ?></td>
                        </tr>
                        <tr>
                            <td>Total</td>
                            <td><?php echo formatPrice($cartTotal * 1.05); ?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="cart-actions">
                    <button type="submit" name="update_cart" class="btn">Update Cart</button>
                    <center><a href="checkout.php" class="btn">Order Now</a></center>
                </div>
            </form>
        <?php } ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

