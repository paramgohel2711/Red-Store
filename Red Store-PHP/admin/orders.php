<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth_functions.php';
require_once '../includes/admin_functions.php';

// Require admin login
requireAdmin();

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get orders
$orders = getAllOrders($conn, $limit, $offset, $status);

// Get total orders count for pagination
$sql = "SELECT COUNT(*) as count FROM orders";
if ($status) {
    $sql .= " WHERE approval_status = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $status);
} else {
    $stmt = mysqli_prepare($conn, $sql);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$total_orders = $row['count'];
$total_pages = ceil($total_orders / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | <?php echo SITE_NAME; ?></title>
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
                <h1>Manage Orders</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                </div>
            </div>
            
            <div class="filter-container">
                <div class="filters">
                    <a href="orders.php" class="filter-btn <?php echo !$status ? 'active' : ''; ?>">All</a>
                    <a href="orders.php?status=pending" class="filter-btn <?php echo $status == 'pending' ? 'active' : ''; ?>">Pending</a>
                    <a href="orders.php?status=approved" class="filter-btn <?php echo $status == 'approved' ? 'active' : ''; ?>">Approved</a>
                    <a href="orders.php?status=rejected" class="filter-btn <?php echo $status == 'rejected' ? 'active' : ''; ?>">Rejected</a>
                </div>
            </div>
            
            <div class="orders-list">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Approval</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="no-data">No orders found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                    <td><?php echo formatPrice($order['total_amount']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo ucfirst($order['status']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $order['approval_status']; ?>">
                                            <?php echo ucfirst($order['approval_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="order-details.php?id=<?php echo $order['id']; ?>" class="action-btn view">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $status ? '&status=' . $status : ''; ?>" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $status ? '&status=' . $status : ''; ?>" 
                               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $status ? '&status=' . $status : ''; ?>" class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

