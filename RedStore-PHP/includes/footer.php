<footer>
        <div class="container">
            <div class="row">
                <div class="footer-col">
                    <h3>Download Our App</h3>
                    <p>Download App for Android and iOS mobile phone.</p>
                    <div class="app-logo">
                        <img src="<?php echo SITE_URL; ?>/assets/images/play-store.png" alt="Play Store">
                        <img src="<?php echo SITE_URL; ?>/assets/images/app-store.png" alt="App Store">
                    </div>
                </div>
                <div class="footer-col">
                    <h3>RedStore</h3>
                    <p>Our Purpose Is To Sustainably Make the Pleasure and Benefits of Sports Accessible to the Many.</p>
                </div>
                <div class="footer-col">
                    <h3>Useful Links</h3>
                    <ul>
                        <li><a href="#">Coupons</a></li>
                        <li><a href="#">Blog Post</a></li>
                        <li><a href="#">Return Policy</a></li>
                        <li><a href="#">Join Affiliate</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Follow Us</h3>
                    <ul>
                        <li><a href="#">Facebook</a></li>
                        <li><a href="#">Twitter</a></li>
                        <li><a href="#">Instagram</a></li>
                        <li><a href="#">YouTube</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <p class="copyright">Copyright &copy; 2024 - RedStore</p>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            var MenuItems = document.getElementById("MenuItems");
            if (MenuItems.style.maxHeight == "0px" || !MenuItems.style.maxHeight) {
                MenuItems.style.maxHeight = "200px";
            } else {
                MenuItems.style.maxHeight = "0px";
            }
        }
    </script>
</body>
</html>

