    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>Timeline</h4>
                    <ul>
                        <li><a href="/about.php">About Us</a></li>
                        <li><a href="/contact.php">Contact</a></li>
                        <li><a href="/careers.php">Careers</a></li>
                        <li><a href="/privacy.php">Privacy Policy</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Charts</h4>
                    <ul>
                        <li><a href="/charts.php">Hot 100</a></li>
                        <li><a href="/videos.php">Top Videos</a></li>
                        <?php
                        $settings = new Settings();
                        if ($settings->get('page_enabled_richest', 1)):
                        ?>
                        <li><a href="/richest.php">Richest People</a></li>
                        <?php endif; ?>
                        <?php if ($settings->get('page_enabled_business', 1)): ?>
                        <li><a href="/business.php">Business Charts</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Timeline.co.zw. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="/js/imageService.js"></script>
    <script src="/js/images.js"></script>
    <script src="/js/main.js"></script>
    <script src="/js/animations.js"></script>
    <script src="/js/slider.js"></script>
    <?php if (isset($customScripts)): ?>
        <?php foreach ($customScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

