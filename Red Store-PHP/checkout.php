<?php 
include 'includes/header.php'; 

// Check if cart is empty
$cart = getCartItems();
if (empty($cart)) {
    header("Location: cart.php");
    exit();
}

// Handle checkout form submission
if (isset($_POST['place_order'])) {
    $receiver_name = $_POST['receiver_name'];
    $address = $_POST['address'];
    $zip_code = $_POST['zip_code'];
    $payment_method = $_POST['payment_method'];
    
    // Validate form data
    $errors = [];
    
    if (empty($receiver_name)) {
        $errors[] = "Receiver name is required";
    }
    
    if (empty($address)) {
        $errors[] = "Address is required";
    }
    
    if (empty($zip_code)) {
        $errors[] = "Zip code is required";
    }
    
    if (empty($payment_method)) {
        $errors[] = "Payment method is required";
    }
    
    // If no errors, create order
    if (empty($errors)) {
        // For demo purposes, we'll use user_id = 1
        // In a real application, you would get this from the logged-in user
        $user_id = 1;
        
        $order_id = createOrder($conn, $user_id, $receiver_name, $address, $zip_code, $payment_method);
        
        if ($order_id) {
            // Redirect to order confirmation page
            header("Location: order-confirmation.php?id=" . $order_id);
            exit();
        } else {
            $errors[] = "Failed to create order. Please try again.";
        }
    }
}

$cartTotal = calculateCartTotal($conn);
?>

<!-- Checkout Page -->
<div class="checkout-page">
    <div class="container">
        <div class="checkout-form">
            <h2>Order Details</h2>
            
            <?php if (isset($errors) && !empty($errors)) { ?>
                <div class="error-message">
                    <ul>
                        <?php foreach ($errors as $error) { ?>
                            <li><?php echo $error; ?></li>
                        <?php } ?>
                    </ul>
                </div>
            <?php } ?>
            
            <form method="post" action="">
                <div class="form-group">
                    <label for="receiver_name">Receiver's Name:</label>
                    <input type="text" id="receiver_name" name="receiver_name" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" required>
                </div>
                
                <div class="form-group">
                    <label for="zip_code">Zip Code:</label>
                    <input type="text" id="zip_code" name="zip_code" required>
                </div>
                
                <div class="form-group">
                    <label for="payment_method">Payment Method:</label>
                    <select id="payment_method" name="payment_method" required>
                        <option value="">Select Payment Method</option>
                        <option value="Cash On Delivery">Cash On Delivery</option>
                        <option value="Credit Card">Credit Card</option>
                        <option value="PayPal">PayPal</option>
                    </select>
                </div>
                
                <div class="order-summary">
                    <h3>Order Summary</h3>
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
                
                <button type="submit" name="place_order" class="btn">Place Order</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

