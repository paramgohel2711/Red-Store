<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth_functions.php';
require_once '../includes/admin_functions.php';

// Require admin login
requireAdmin();

$product = [
    'id' => '',
    'name' => '',
    'description' => '',
    'price' => '',
    'category_id' => '',
    'stock' => '',
    'featured' => 0,
    'image' => ''
];

$isEdit = false;
$message = '';
$error = '';

// Check if editing existing product
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = $_GET['id'];
    $productData = getProductById($conn, $product_id);
    
    if ($productData) {
        $product = $productData;
        $isEdit = true;
    } else {
        header("Location: products.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $product['name'] = $_POST['name'];
    $product['description'] = $_POST['description'];
    $product['price'] = $_POST['price'];
    $product['category_id'] = $_POST['category_id'];
    $product['stock'] = $_POST['stock'];
    $product['featured'] = isset($_POST['featured']) ? 1 : 0;
    
    // Validate form data
    if (empty($product['name'])) {
        $error = "Product name is required";
    } elseif (empty($product['price']) || !is_numeric($product['price'])) {
        $error = "Valid price is required";
    } elseif (empty($product['stock']) || !is_numeric($product['stock'])) {
        $error = "Valid stock quantity is required";
    } else {
        // Handle image upload
        $uploadImage = false;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $uploadImage = true;
            $image = $_FILES['image'];
            $imageName = time() . '_' . basename($image['name']);
            $targetPath = '../assets/images/' . $imageName;
            
            // Check if file is an image
            $imageFileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($imageFileType, $allowedTypes)) {
                $error = "Only JPG, JPEG, PNG & GIF files are allowed";
            } elseif ($image['size'] > 5000000) { // 5MB max
                $error = "File is too large (max 5MB)";
            } elseif (!move_uploaded_file($image['tmp_name'], $targetPath)) {
                $error = "Failed to upload image";
            } else {
                $product['image'] = $imageName;
            }
        }
        
        if (empty($error)) {
            if ($isEdit) {
                // Update existing product
                $result = updateProduct($conn, $product, $uploadImage);
                if ($result) {
                    $message = "Product updated successfully";
                } else {
                    $error = "Failed to update product";
                }
            } else {
                // Create new product
                if (empty($product['image']) && !$uploadImage) {
                    $error = "Product image is required";
                } else {
                    $result = createProduct($conn, $product);
                    if ($result) {
                        $message = "Product created successfully";
                        // Reset form for new product
                        $product = [
                            'id' => '',
                            'name' => '',
                            'description' => '',
                            'price' => '',
                            'category_id' => '',
                            'stock' => '',
                            'featured' => 0,
                            'image' => ''
                        ];
                    } else {
                        $error = "Failed to create product";
                    }
                }
            }
        }
    }
}

// Get all categories
$categories = getAllCategories($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Product | <?php echo SITE_NAME; ?></title>
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
                <h1><?php echo $isEdit ? 'Edit' : 'Add'; ?> Product</h1>
                <div class="user-info">
                    <span>Welcome, <?php echo $_SESSION['user_name']; ?></span>
                </div>
            </div>
            
            <div class="back-link">
                <a href="products.php"><i class="fas fa-arrow-left"></i> Back to Products</a>
            </div>
            
            <?php if (!empty($message)): ?>
                <div class="message success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="message error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div class="product-form-container">
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="price">Price</label>
                            <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        </div>
                        
                        <div class="form-group half">
                            <label for="stock">Stock</label>
                            <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="category_id">Category</label>
                            <select id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group half checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="featured" <?php echo $product['featured'] ? 'checked' : ''; ?>>
                                Featured Product
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <?php if (!empty($product['image'])): ?>
                            <div class="current-image">
                                <img src="<?php echo getImageUrl($product['image']); ?>" alt="<?php echo $product['name']; ?>">
                                <p>Current image: <?php echo $product['image']; ?></p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="image" name="image" accept="image/*" <?php echo $isEdit ? '' : 'required'; ?>>
                        <p class="help-text">Recommended size: 500x500 pixels. Max file size: 5MB.</p>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn"><?php echo $isEdit ? 'Update' : 'Create'; ?> Product</button>
                        <a href="products.php" class="btn cancel">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
