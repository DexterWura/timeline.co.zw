    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <p class="copyright">© <span id="currentYear"><?php echo date('Y'); ?></span> <?php echo APP_NAME; ?>. All rights reserved.</p>
            <p class="developer">
                Developed with <i class="fa-solid fa-heart"></i> by 
                <strong>
                    <a href="https://www.linkedin.com/in/dexterity-wurayayi-967a64230" target="_blank" rel="noopener noreferrer" class="developer-link">
                        DexterWura
                        <i class="fa-brands fa-linkedin developer-link-icon"></i>
                    </a>
                </strong>
            </p>
        </div>
    </footer>

    <script src="/admin/assets/js/main.js"></script>
    <?php if (isset($customScripts)): ?>
        <?php foreach ($customScripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>

