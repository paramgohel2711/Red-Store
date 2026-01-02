<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth_functions.php';
require_once '../includes/admin_functions.php';

// Require admin login
requireAdmin();

// Get filter parameters
$category = isset($_GET['category']) ? $_GET['category'] : null;
$featured = isset($_GET['featured']) ? $_GET['featured'] : null;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get products
$products = getProductsForAdmin($conn, $limit, $offset, $category, $featured, $search);

// Get total products count for pagination
$sql = "SELECT COUNT(*) as count FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];
$types = "";

if ($category) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category;
    $types .= "i";
}

if ($featured !== null) {
    $sql .= " AND p.featured = ?";
    $params[] = $featured;
    $types .= "i";
}

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$total_products = $row['count'];
$total_pages = ceil($total_products / $limit);

// Get all categories for filter
$categories = getAllCategories($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | <?php echo SITE_NAME; ?></title>
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
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li class="active"><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Manage Products</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                </div>
            </div>
            
            <div class="action-bar">
                <a href="product-form.php" class="btn add-btn"><i class="fas fa-plus"></i> Add New Product</a>
            </div>
            
            <div class="filter-container">
                <form action="" method="get" class="search-form">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    
                    <div class="filter-options">
                        <select name="category">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <select name="featured">
                            <option value="">All Products</option>
                            <option value="1" <?php echo $featured === '1' ? 'selected' : ''; ?>>Featured</option>
                            <option value="0" <?php echo $featured === '0' ? 'selected' : ''; ?>>Not Featured</option>
                        </select>
                        
                        <button type="submit" class="btn filter-btn">Filter</button>
                        <a href="products.php" class="btn reset-btn">Reset</a>
                    </div>
                </form>
            </div>
            
            <div class="products-list">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" class="no-data">No products found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td class="product-image">
                                        <img src="<?php echo getImageUrl($product['image']); ?>" alt="<?php echo $product['name']; ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                    <td><?php echo formatPrice($product['price']); ?></td>
                                    <td><?php echo $product['stock']; ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $product['featured'] ? 'approved' : ''; ?>">
                                            <?php echo $product['featured'] ? 'Featured' : 'Not Featured'; ?>
                                        </span>
                                    </td>
                                    <td class="actions">
                                        <a href="product-form.php?id=<?php echo $product['id']; ?>" class="action-btn edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $product['id']; ?>)" class="action-btn delete">
                                            <i class="fas fa-trash"></i>
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
                            <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $featured !== null ? '&featured=' . $featured : ''; ?>" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $featured !== null ? '&featured=' . $featured : ''; ?>" 
                               class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?><?php echo $category ? '&category=' . $category : ''; ?><?php echo $featured !== null ? '&featured=' . $featured : ''; ?>" class="page-link">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        function confirmDelete(productId) {
            if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                window.location.href = 'delete-product.php?id=' + productId;
            }
        }
    </script>
</body>
</html>
