<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth_functions.php';
require_once '../includes/admin_functions.php';

// Require admin login
requireAdmin();

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = $_GET['id'];
$order = getOrderWithItems($conn, $order_id);

// If order not found
if (!$order) {
    header("Location: orders.php");
    exit();
}

// Handle status update
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $notes = $_POST['admin_notes'];
    
    if (updateOrderStatus($conn, $order_id, $status, $notes)) {
        $message = "Order status updated successfully";
        $order['approval_status'] = $status;
        $order['admin_notes'] = $notes;
    } else {
        $message = "Failed to update order status";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details | <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <img src="<?php echo getImageUrl('logo.png'); ?>" alt="RedStore">
                <h2>Admin Panel</h2>
            </div>
            
            <ul class="menu">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="active"><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Order Details</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                </div>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="order-details-container">
                <div class="back-link">
                    <a href="orders.php"><i class="fas fa-arrow-left"></i> Back to Orders</a>
                </div>
                
                <div class="order-header">
                    <div class="order-info">
                        <h2>Order #<?php echo $order['id']; ?></h2>
                        <p>Date: <?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></p>
                        <p>Status: <span class="status-badge <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></p>
                        <p>Approval: <span class="status-badge <?php echo $order['approval_status']; ?>"><?php echo ucfirst($order['approval_status']); ?></span></p>
                    </div>
                    
                    <div class="order-total">
                        <h3>Total: <?php echo formatPrice($order['total_amount']); ?></h3>
                    </div>
                </div>
                
                <div class="order-sections">
                    <div class="order-section">
                        <h3>Customer Information</h3>
                        <div class="info-group">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['user_phone'] ?? 'N/A'); ?></p>
                        </div>
                    </div>
                    
                    <div class="order-section">
                        <h3>Shipping Information</h3>
                        <div class="info-group">
                            <p><strong>Receiver:</strong> <?php echo htmlspecialchars($order['receiver_name']); ?></p>
                            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                            <p><strong>Zip Code:</strong> <?php echo htmlspecialchars($order['zip_code']); ?></p>
                        </div>
                    </div>
                    
                    <div class="order-section">
                        <h3>Payment Information</h3>
                        <div class="info-group">
                            <p><strong>Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="order-items">
                    <h3>Order Items</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order['items'] as $item): ?>
                                <tr>
                                    <td class="product-cell">
                                        <img src="<?php echo getImageUrl($item['product_image']); ?>" alt="<?php echo $item['product_name']; ?>">
                                        <span><?php echo htmlspecialchars($item['product_name']); ?></span>
                                    </td>
                                    <td><?php echo formatPrice($item['price']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Subtotal:</strong></td>
                                <td><?php echo formatPrice($order['total_amount']); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Tax (5%):</strong></td>
                                <td><?php echo formatPrice($order['total_amount'] * 0.05); ?></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td><?php echo formatPrice($order['total_amount'] * 1.05); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="order-approval">
                    <h3>Order Approval</h3>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="status">Approval Status</label>
                            <select id="status" name="status" required>
                                <option value="pending" <?php echo $order['approval_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $order['approval_status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo $order['approval_status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="admin_notes">Admin Notes</label>
                            <textarea id="admin_notes" name="admin_notes" rows="4"><?php echo htmlspecialchars($order['admin_notes'] ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" name="update_status" class="btn">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

