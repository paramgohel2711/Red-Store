<?php 
include 'includes/header.php'; 

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = $_GET['id'];
$product = getProductById($conn, $product_id);

// If product not found
if (!$product) {
    header("Location: products.php");
    exit();
}

// Handle add to cart action
if (isset($_POST['add_to_cart'])) {
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    addToCart($product_id, $quantity);
    header("Location: cart.php");
    exit();
}
?>

<!-- Product Details -->
<div class="product-details">
    <div class="container">
        <div class="row">
            <div class="col-2">
                <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="100%">
            </div>
            <div class="col-2">
                <h1><?php echo $product['name']; ?></h1>
                <h4><?php echo formatPrice($product['price']); ?></h4>
                <div class="rating">
                    <?php echo displayRating($product['rating']); ?>
                </div>
                
                <form method="post" action="">
                    <div class="quantity-selector">
                        <label for="quantity">Quantity:</label>
                        <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>
                    
                    <button type="submit" name="add_to_cart" class="btn">Add to Cart</button>
                    <button type="submit" name="buy_now" class="btn" style="background: #555;">Buy Now</button>
                </form>
                
                <div class="wishlist">
                    <i class="far fa-heart"></i>
                </div>
                
                <h3>Product Details</h3>
                <p><?php echo $product['description']; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Related Products -->
<div class="related-products">
    <div class="container">
        <h2>Related Products</h2>
        <div class="row">
            <?php
            $relatedProducts = getAllProducts($conn, 4);
            foreach ($relatedProducts as $relatedProduct) {
                if ($relatedProduct['id'] != $product_id) {
            ?>
            <div class="col-4">
                <div class="product-card">
                    <a href="product-details.php?id=<?php echo $relatedProduct['id']; ?>">
                        <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $relatedProduct['image']; ?>" alt="<?php echo $relatedProduct['name']; ?>" class="product-img">
                        <h4 class="product-title"><?php echo $relatedProduct['name']; ?></h4>
                        <div class="rating">
                            <?php echo displayRating($relatedProduct['rating']); ?>
                        </div>
                        <p class="price"><?php echo formatPrice($relatedProduct['price']); ?></p>
                    </a>
                </div>
            </div>
            <?php 
                }
            } 
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

