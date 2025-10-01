    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-logo">
                        <img src="images/logo.svg" alt="Timeline.co.zw" class="logo">
                        <h3>Timeline.co.zw</h3>
                    </div>
                    <p>Africa's premier music and entertainment platform. Discover trending content from Zimbabwe and across the continent.</p>
                    <div class="social-links">
                        <a href="#" class="social-link" aria-label="Facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="social-link" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Music & Charts</h4>
                    <ul class="footer-links">
                        <li><a href="charts.php">Hot 100 Music</a></li>
                        <li><a href="videos.php">Top 100 Videos</a></li>
                        <li><a href="trending.php">Trending Now</a></li>
                        <li><a href="african-music.php">African Music</a></li>
                        <li><a href="zimbabwe-music.php">Zimbabwe Music</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Entertainment</h4>
                    <ul class="footer-links">
                        <li><a href="richest.php">Richest People</a></li>
                        <li><a href="awards.php">Awards</a></li>
                        <li><a href="business.php">Business Charts</a></li>
                        <li><a href="news.php">Entertainment News</a></li>
                        <li><a href="events.php">Events</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul class="footer-links">
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                        <li><a href="privacy.php">Privacy Policy</a></li>
                        <li><a href="terms.php">Terms of Service</a></li>
                        <li><a href="help.php">Help Center</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Regional Content</h4>
                    <div class="region-selector">
                        <select id="region-selector" onchange="changeRegion(this.value)">
                            <option value="global" <?php echo (isset($_SESSION['user_region']) && $_SESSION['user_region'] === 'global') ? 'selected' : ''; ?>>Global</option>
                            <option value="africa" <?php echo (isset($_SESSION['user_region']) && $_SESSION['user_region'] === 'africa') ? 'selected' : ''; ?>>Africa</option>
                            <option value="zimbabwe" <?php echo (isset($_SESSION['user_region']) && $_SESSION['user_region'] === 'zimbabwe') ? 'selected' : ''; ?>>Zimbabwe</option>
                            <option value="south-africa" <?php echo (isset($_SESSION['user_region']) && $_SESSION['user_region'] === 'south-africa') ? 'selected' : ''; ?>>South Africa</option>
                            <option value="nigeria" <?php echo (isset($_SESSION['user_region']) && $_SESSION['user_region'] === 'nigeria') ? 'selected' : ''; ?>>Nigeria</option>
                            <option value="kenya" <?php echo (isset($_SESSION['user_region']) && $_SESSION['user_region'] === 'kenya') ? 'selected' : ''; ?>>Kenya</option>
                        </select>
                    </div>
                    <div class="language-selector">
                        <select id="language-selector" onchange="changeLanguage(this.value)">
                            <option value="en" <?php echo (isset($_SESSION['user_language']) && $_SESSION['user_language'] === 'en') ? 'selected' : ''; ?>>English</option>
                            <option value="sn" <?php echo (isset($_SESSION['user_language']) && $_SESSION['user_language'] === 'sn') ? 'selected' : ''; ?>>Shona</option>
                            <option value="nd" <?php echo (isset($_SESSION['user_language']) && $_SESSION['user_language'] === 'nd') ? 'selected' : ''; ?>>Ndebele</option>
                            <option value="fr" <?php echo (isset($_SESSION['user_language']) && $_SESSION['user_language'] === 'fr') ? 'selected' : ''; ?>>Français</option>
                            <option value="pt" <?php echo (isset($_SESSION['user_language']) && $_SESSION['user_language'] === 'pt') ? 'selected' : ''; ?>>Português</option>
                            <option value="ar" <?php echo (isset($_SESSION['user_language']) && $_SESSION['user_language'] === 'ar') ? 'selected' : ''; ?>>العربية</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <div class="footer-bottom-content">
                    <p>&copy; <?php echo date('Y'); ?> Timeline.co.zw. All rights reserved.</p>
                    <div class="footer-bottom-links">
                        <span>Made with ❤️ in Zimbabwe</span>
                        <span>•</span>
                        <span>Powered by YouTube API</span>
                        <span>•</span>
                        <span>African Music Hub</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Files -->
    <script src="js/locationService.js"></script>
    <script src="js/youtubeApi.js"></script>
    <script src="js/billionaireApi.js"></script>
    <script src="js/imageService.js"></script>
    <script src="js/images.js"></script>
    <script src="js/main.js"></script>
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js_file): ?>
            <script src="<?php echo htmlspecialchars($js_file); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    <script src="js/animations.js"></script>
    
    <!-- Location and Region Management -->
    <script>
        // Region and Language Management
        function changeRegion(region) {
            // Update session via AJAX
            fetch('api/update-region.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ region: region })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show new regional content
                    location.reload();
                } else {
                    console.error('Failed to update region:', data.error);
                }
            })
            .catch(error => {
                console.error('Error updating region:', error);
            });
        }
        
        function changeLanguage(language) {
            // Update session via AJAX
            fetch('api/update-language.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ language: language })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show new language
                    location.reload();
                } else {
                    console.error('Failed to update language:', data.error);
                }
            })
            .catch(error => {
                console.error('Error updating language:', error);
            });
        }
        
        // Auto-detect user location on first visit
        document.addEventListener('DOMContentLoaded', function() {
            if (!sessionStorage.getItem('location_detected')) {
                detectUserLocation();
            }
        });
        
        function detectUserLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        // Get country from coordinates
                        fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${position.coords.latitude}&longitude=${position.coords.longitude}&localityLanguage=en`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.countryCode) {
                                updateUserLocation(data.countryCode, data.principalSubdivision);
                            }
                        })
                        .catch(error => {
                            console.log('Geolocation API failed, using IP detection');
                            detectLocationByIP();
                        });
                    },
                    function(error) {
                        console.log('Geolocation failed, using IP detection');
                        detectLocationByIP();
                    }
                );
            } else {
                detectLocationByIP();
            }
        }
        
        function detectLocationByIP() {
            fetch('https://ipapi.co/json/')
            .then(response => response.json())
            .then(data => {
                if (data.country_code) {
                    updateUserLocation(data.country_code, data.region);
                }
            })
            .catch(error => {
                console.log('IP detection failed, using default location');
            });
        }
        
        function updateUserLocation(countryCode, region) {
            fetch('api/update-location.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ 
                    country: countryCode, 
                    region: region 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    sessionStorage.setItem('location_detected', 'true');
                    // Update UI to show regional content
                    if (window.AFRICAN_COUNTRIES.includes(countryCode)) {
                        showAfricanContent();
                    }
                }
            })
            .catch(error => {
                console.error('Error updating location:', error);
            });
        }
        
        function showAfricanContent() {
            // Add African content indicators
            document.body.classList.add('african-user');
            
            // Show African content badges
            const badges = document.querySelectorAll('.african-badge');
            badges.forEach(badge => badge.style.display = 'inline-block');
        }
    </script>
    
    <!-- Page-specific scripts -->
    <?php if (isset($page_specific_scripts)): ?>
        <?php echo $page_specific_scripts; ?>
    <?php endif; ?>
</body>
</html>
