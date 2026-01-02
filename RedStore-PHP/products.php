<?php include 'includes/header.php'; ?>

<!-- All Products -->
<div class="all-products">
    <div class="container">
        <h2>All Products</h2>
        <div class="row">
            <?php
            $allProducts = getAllProducts($conn);
            foreach ($allProducts as $product) {
            ?>
            <div class="col-4">
                <div class="product-card">
                    <a href="product-details.php?id=<?php echo $product['id']; ?>">
                        <img src="<?php echo SITE_URL; ?>/assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="product-img">
                        <h4 class="product-title"><?php echo $product['name']; ?></h4>
                        <div class="rating">
                            <?php echo displayRating($product['rating']); ?>
                        </div>
                        <p class="price"><?php echo formatPrice($product['price']); ?></p>
                    </a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

