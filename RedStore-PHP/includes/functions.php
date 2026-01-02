<?php
// Get cart items from session
function getCartItems() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

// Add to cart
function addToCart($product_id, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// Update cart item
function updateCartItem($product_id, $quantity) {
    if ($quantity <= 0) {
        removeFromCart($product_id);
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
}

// Remove from cart
function removeFromCart($product_id) {
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Calculate cart total
function calculateCartTotal($conn) {
    $total = 0;
    $cart = getCartItems();
    
    foreach ($cart as $product_id => $quantity) {
        $product = getProductById($conn, $product_id);
        if ($product) {
            $total += $product['price'] * $quantity;
        }
    }
    
    return $total;
}

// Get all products
function getAllProducts($conn, $limit = null, $featured = false) {
    $sql = "SELECT * FROM products";
    
    if ($featured) {
        $sql .= " WHERE featured = 1";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT $limit";
    }
    
    $result = mysqli_query($conn, $sql);
    $products = [];
    
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Clean up image path to only use filename
            $row['image'] = basename($row['image']);
            $products[] = $row;
        }
    }
    
    return $products;
}

// Get product by ID
function getProductById($conn, $id) {
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        // Clean up image path to only use filename
        $product['image'] = basename($product['image']);
        return $product;
    }
    
    return null;
}

// Format image URL
function getImageUrl($filename) {
    return IMAGE_PATH . '/' . $filename;
}

// Format price
function formatPrice($price) {
    return '₹' . number_format($price, 2);
}

// Display rating stars
function displayRating($rating) {
    $stars = '';
    $full_stars = floor($rating);
    $half_star = $rating - $full_stars >= 0.5;
    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
    
    for ($i = 0; $i < $full_stars; $i++) {
        $stars .= '<i class="fas fa-star"></i>';
    }
    
    if ($half_star) {
        $stars .= '<i class="fas fa-star-half-alt"></i>';
    }
    
    for ($i = 0; $i < $empty_stars; $i++) {
        $stars .= '<i class="far fa-star"></i>';
    }
    
    return $stars;
}

// Create order
function createOrder($conn, $user_id, $receiver_name, $address, $zip_code, $payment_method) {
    $cart = getCartItems();
    $total_amount = calculateCartTotal($conn);
    
    // Begin transaction
    mysqli_begin_transaction($conn);
    
    try {
        // Insert order
        $sql = "INSERT INTO orders (user_id, total_amount, receiver_name, address, zip_code, payment_method) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "idssss", $user_id, $total_amount, $receiver_name, $address, $zip_code, $payment_method);
        mysqli_stmt_execute($stmt);
        
        $order_id = mysqli_insert_id($conn);
        
        // Insert order items
        foreach ($cart as $product_id => $quantity) {
            $product = getProductById($conn, $product_id);
            
            if ($product) {
                $sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                        VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "iiid", $order_id, $product_id, $quantity, $product['price']);
                mysqli_stmt_execute($stmt);
                
                // Update product stock
                $sql = "UPDATE products SET stock = stock - ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $quantity, $product_id);
                mysqli_stmt_execute($stmt);
            }
        }
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        // Commit transaction
        mysqli_commit($conn);
        
        return $order_id;
    } catch (Exception $e) {
        // Rollback transaction on error
        mysqli_rollback($conn);
        return false;
    }
}
?>

