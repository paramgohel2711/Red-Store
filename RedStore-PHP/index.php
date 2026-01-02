<?php include 'includes/header.php'; ?>

<style>
    
.col-2 img{
  max-width: 100%;
  padding: 50px 0;
}

.featured-banner .col-2 img{
  max-width: 100%;
  padding: 0 50px ;
  margin-top: 65px;
}
.featured-banner{
    background: radial-gradient(#fff,#ffd6d6);
    height: 80vh;
}

</style>

<!-- Hero Section -->
<div class="hero">
    <div class="container">
        <div class="row">
            <div class="col-2">
                <h1>Give Your Workout<br>A New Style</h1>
                <p>Success isn't always about greatness. It's about consistency. Consistent<br>hard work gains success. Greatness will come.</p>
                <a href="products.php" class="btn">Explore Now &#8594;</a>
            </div>
            <div class="col-2">
                <img src="<?php echo SITE_URL; ?>/assets/images/image1.png" alt="Hero Image" >
            </div>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="featured-products">
    <div class="container">
        <h2>Featured Products</h2>
        <div class="row">
            <?php
            $featuredProducts = getAllProducts($conn, 4, true);
            foreach ($featuredProducts as $product) {
            ?>
            <div class="col-4">
                <div class="product-card">
                    <a href="product-details.php?id=<?php echo $product['id']; ?>">
                        <img src="<?php echo getImageUrl($product['image']); ?>" alt="<?php echo $product['name']; ?>" class="product-img">
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

<!-- Featured Banner -->
<div class="featured-banner">
    <div class="container">
        <div class="row">
            <div class="col-2">
                <img src="<?php echo getImageUrl('exclusive.png'); ?>" alt="Smart Band 4">
            </div>
            <div class="col-2">
                <h4>Exclusively Available On RedStore</h4>
                <h1>Smart Band 4</h1>
                <p>The Mi Smart Band 4 features a 39.9% larger (than Mi Band 3) AMOLED color full display with adjustable brightness, so everything is clear as can be.</p>
                <a href="product-details.php?id=9" class="btn">Buy Now &#8594;</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

