<?php
// Get all orders with user details
function getAllOrders($conn, $limit = null, $offset = 0, $status = null) {
    $sql = "SELECT o.*, u.name as user_name, u.email as user_email 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id";
    
    if ($status) {
        $sql .= " WHERE o.approval_status = ?";
    }
    
    $sql .= " ORDER BY o.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?, ?";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($status && $limit) {
        mysqli_stmt_bind_param($stmt, "sii", $status, $offset, $limit);
    } elseif ($status) {
        mysqli_stmt_bind_param($stmt, "s", $status);
    } elseif ($limit) {
        mysqli_stmt_bind_param($stmt, "ii", $offset, $limit);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $orders = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    return $orders;
}

// Get order details with items
function getOrderWithItems($conn, $order_id) {
    // Get order details
    $sql = "SELECT o.*, u.name as user_name, u.email as user_email, u.phone as user_phone 
            FROM orders o 
            LEFT JOIN users u ON o.user_id = u.id 
            WHERE o.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        return null;
    }
    
    $order = mysqli_fetch_assoc($result);
    
    // Get order items
    $sql = "SELECT oi.*, p.name as product_name, p.image as product_image 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $order['items'] = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['product_image'] = basename($row['product_image']);
        $order['items'][] = $row;
    }
    
    return $order;
}

// Update order approval status
function updateOrderStatus($conn, $order_id, $status, $notes = null) {
    $sql = "UPDATE orders SET approval_status = ?, admin_notes = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $status, $notes, $order_id);
    
    return mysqli_stmt_execute($stmt);
}

// Get dashboard statistics
function getDashboardStats($conn) {
    $stats = [
        'total_orders' => 0,
        'pending_approvals' => 0,
        'approved_orders' => 0,
        'rejected_orders' => 0,
        'total_revenue' => 0,
        'total_users' => 0
    ];
    
    // Total orders
    $sql = "SELECT COUNT(*) as count FROM orders";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['total_orders'] = $row['count'];
    
    // Pending approvals
    $sql = "SELECT COUNT(*) as count FROM orders WHERE approval_status = 'pending'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['pending_approvals'] = $row['count'];
    
    // Approved orders
    $sql = "SELECT COUNT(*) as count FROM orders WHERE approval_status = 'approved'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['approved_orders'] = $row['count'];
    
    // Rejected orders
    $sql = "SELECT COUNT(*) as count FROM orders WHERE approval_status = 'rejected'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['rejected_orders'] = $row['count'];
    
    // Total revenue from approved orders
    $sql = "SELECT SUM(total_amount) as total FROM orders WHERE approval_status = 'approved'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['total_revenue'] = $row['total'] ? $row['total'] : 0;
    
    // Total users
    $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'user'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $stats['total_users'] = $row['count'];
    
    return $stats;
}

// Get all products for admin
function getProductsForAdmin($conn, $limit = null, $offset = 0, $category = null, $featured = null, $search = null) {
    $sql = "SELECT p.*, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE 1=1";
    
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
    
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
        $types .= "ii";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Clean up image path to only use filename
        $row['image'] = basename($row['image']);
        $products[] = $row;
    }
    
    return $products;
}

// Get all categories
function getAllCategories($conn) {
    $sql = "SELECT * FROM categories ORDER BY name ASC";
    $result = mysqli_query($conn, $sql);
    
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

// Create new product
function createProduct($conn, $product) {
    $sql = "INSERT INTO products (name, description, price, category_id, stock, featured, image, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssdiiss", 
        $product['name'], 
        $product['description'], 
        $product['price'], 
        $product['category_id'], 
        $product['stock'], 
        $product['featured'], 
        $product['image']
    );
    
    return mysqli_stmt_execute($stmt);
}

// Update existing product
function updateProduct($conn, $product, $updateImage = false) {
    if ($updateImage) {
        $sql = "UPDATE products SET 
                name = ?, 
                description = ?, 
                price = ?, 
                category_id = ?, 
                stock = ?, 
                featured = ?, 
                image = ? 
                WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdiiisi", 
            $product['name'], 
            $product['description'], 
            $product['price'], 
            $product['category_id'], 
            $product['stock'], 
            $product['featured'], 
            $product['image'], 
            $product['id']
        );
    } else {
        $sql = "UPDATE products SET 
                name = ?, 
                description = ?, 
                price = ?, 
                category_id = ?, 
                stock = ?, 
                featured = ? 
                WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssdiiii", 
            $product['name'], 
            $product['description'], 
            $product['price'], 
            $product['category_id'], 
            $product['stock'], 
            $product['featured'], 
            $product['id']
        );
    }
    
    return mysqli_stmt_execute($stmt);
}

// Delete product
function deleteProduct($conn, $product_id) {
    // Get product image to delete file
    $sql = "SELECT image FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        $imagePath = '../assets/images/' . $product['image'];
        
        // Delete image file if it exists and is not a default image
        if (file_exists($imagePath) && !strpos($product['image'], 'product-') && !strpos($product['image'], 'exclusive.png')) {
            @unlink($imagePath);
        }
    }
    
    // Delete product from database
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    
    return mysqli_stmt_execute($stmt);
}

// Get users for admin
function getUsersForAdmin($conn, $limit = null, $offset = 0, $role = null, $search = null) {
    $sql = "SELECT * FROM users WHERE 1=1";
    
    $params = [];
    $types = "";
    
    if ($role) {
        $sql .= " AND role = ?";
        $params[] = $role;
        $types .= "s";
    }
    
    if (!empty($search)) {
        $sql .= " AND (name LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $types .= "ss";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $limit;
        $types .= "ii";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    return $users;
}

// Change user role
function changeUserRole($conn, $user_id, $role) {
    $sql = "UPDATE users SET role = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $role, $user_id);
    
    return mysqli_stmt_execute($stmt);
}
?>

