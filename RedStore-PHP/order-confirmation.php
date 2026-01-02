<?php 
include 'includes/header.php'; 

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['id'];

// Get order details
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$order = mysqli_fetch_assoc($result);

// Get order items
$sql = "SELECT oi.*, p.name, p.image FROM order_items oi 
        JOIN products p ON oi.product_id = p.id 
        WHERE oi.order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$orderItems = [];

while ($row = mysqli_fetch_assoc($result)) {
    $orderItems[] = $row;
}
?>
<style>
    /* Container padding */
.order-items {
    padding: 20px;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    margin: 20px 0;
    max-width: 100%;
    overflow-x: auto;
}

/* Heading */
.order-items h3 {
    margin-bottom: 15px;
    font-size: 22px;
    color: #333;
}

/* Table styles */
.order-items table {
    width: 100%;
    border-collapse: collapse;
    font-size: 16px;
}

/* Table headers */
.order-items th {
    background-color: #f7f7f7;
    text-align: left;
    padding: 12px;
    color: #555;
    border-bottom: 2px solid #ddd;
}

/* Table cells */
.order-items td {
    padding: 12px;
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

/* Item info with image and name */
.item-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.item-info img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
}

.item-info p {
    margin: 0;
    font-weight: 500;
    color: #333;
}

/* Highlight total rows */
.total-row {
    font-weight: bold;
    background-color: #f9f9f9;
}

/* Button styles */
.confirmation-actions {
    margin-top: 20px;
    text-align: right;
}

.confirmation-actions .btn {
    background-color: #007bff;
    color: #fff;
    padding: 10px 18px;
    text-decoration: none;
    border-radius: 6px;
    font-size: 16px;
    transition: background-color 0.2s ease-in-out;
}

.confirmation-actions .btn:hover {
    background-color: #0056b3;
}

</style>

<!-- Order Confirmation Page -->
<div class="order-confirmation">
    <div class="container">
        <div class="confirmation-box">
            <h2>Order Confirmation</h2>
            <div class="success-message">
                <i class="fas fa-check-circle"></i>
                <p>Your order has been placed successfully!</p>
                <p>Order ID: #<?php echo $order_id; ?></p>
            </div>
            
            <div class="order-details">
                <h3>Order Details</h3>
                <table>
                    <tr>
                        <td>Receiver's Name:</td>
                        <td><?php echo $order['receiver_name']; ?></td>
                    </tr>
                    <tr>
                        <td>Address:</td>
                        <td><?php echo $order['address']; ?></td>
                    </tr>
                    <tr>
                        <td>Zip Code:</td>
                        <td><?php echo $order['zip_code']; ?></td>
                    </tr>
                    <tr>
                        <td>Payment Method:</td>
                        <td><?php echo $order['payment_method']; ?></td>
                    </tr>
                    <tr>
                        <td>Order Date:</td>
                        <td><?php echo date('F j, Y, g:i a', strtotime($order['created_at'])); ?></td>
                    </tr>
                    <tr>
                        <td>Order Status:</td>
                        <td><?php echo ucfirst($order['status']); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="order-items">
                <h3>Order Items</h3>
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                    
                    <?php foreach ($orderItems as $item) { ?>
                    <tr>
                        <td>
                            <div class="item-info">
                                <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                <p><?php echo $item['name']; ?></p>
                            </div>
                        </td>
                        <td><?php echo formatPrice($item['price']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                    </tr>
                    <?php } ?>
                    
                    <tr class="total-row">
                        <td colspan="3">Subtotal</td>
                        <td><?php echo formatPrice($order['total_amount']); ?></td>
                    </tr>
                    <tr>
                        <td colspan="3">Tax</td>
                        <td><?php echo formatPrice($order['total_amount'] * 0.05); ?></td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="3">Total</td>
                        <td><?php echo formatPrice($order['total_amount'] * 1.05); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="confirmation-actions">
                <a href="index.php" class="btn">Continue Shopping</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

