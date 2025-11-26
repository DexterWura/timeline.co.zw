<?php
/**
 * Add page content management table
 */
class V006__add_page_content {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function getDescription() {
        return 'Add page_content table for managing Terms, Privacy Policy, and other static pages';
    }
    
    public function up() {
        // Page content table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS page_content (
                id INT AUTO_INCREMENT PRIMARY KEY,
                page_key VARCHAR(100) NOT NULL UNIQUE,
                page_title VARCHAR(255) NOT NULL,
                content LONGTEXT,
                meta_description TEXT,
                meta_keywords TEXT,
                is_active BOOLEAN DEFAULT 1,
                created_by INT,
                updated_by INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
                FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_page_key (page_key),
                INDEX idx_is_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        
        // Insert default page content
        $defaultPages = [
            [
                'page_key' => 'terms',
                'page_title' => 'Terms and Conditions',
                'content' => '<h2>Acceptance of Terms</h2>
<p>By accessing and using ' . APP_NAME . ', you accept and agree to be bound by the terms and provision of this agreement.</p>

<h2>Use License</h2>
<p>Permission is granted to temporarily access the materials on ' . APP_NAME . ' for personal, non-commercial transitory viewing only.</p>

<h2>User Accounts</h2>
<p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>

<h2>Content</h2>
<p>All content on this website is for informational purposes only. We strive to provide accurate information but make no warranties about the completeness or accuracy of the information.</p>

<h2>Contact Us</h2>
<p>If you have questions about these Terms, please contact us at contact@timeline.co.zw</p>',
                'meta_description' => 'Terms and Conditions for ' . APP_NAME,
                'is_active' => 1
            ],
            [
                'page_key' => 'privacy',
                'page_title' => 'Privacy Policy',
                'content' => '<h2>Information We Collect</h2>
<p>We collect information that you provide directly to us, such as when you create an account, subscribe to our newsletter, or contact us.</p>

<h2>How We Use Your Information</h2>
<p>We use the information we collect to provide, maintain, and improve our services, process transactions, and send you updates.</p>

<h2>Data Security</h2>
<p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>

<h2>Cookies</h2>
<p>We use cookies to enhance your experience on our website. You can choose to disable cookies through your browser settings.</p>

<h2>Contact Us</h2>
<p>If you have questions about this Privacy Policy, please contact us at contact@timeline.co.zw</p>',
                'meta_description' => 'Privacy Policy for ' . APP_NAME,
                'is_active' => 1
            ],
            [
                'page_key' => 'about',
                'page_title' => 'About Us',
                'content' => '<h2>Who We Are</h2>
<p>' . APP_NAME . ' is a leading platform for music charts, entertainment news, and industry insights. We provide real-time data on the hottest songs, trending videos, and the latest news from the music industry.</p>

<h2>Our Mission</h2>
<p>To deliver accurate, up-to-date music chart data and entertainment content to music lovers, industry professionals, and fans worldwide.</p>

<h2>What We Offer</h2>
<ul>
<li>Real-time music charts (Hot 100, Global 200, Artist 100)</li>
<li>Top trending music videos</li>
<li>Entertainment news and articles</li>
<li>Music awards and industry recognition</li>
<li>Business insights and analytics</li>
</ul>',
                'meta_description' => 'Learn more about ' . APP_NAME . ' - Your source for music charts, entertainment news, and industry insights.',
                'is_active' => 1
            ]
        ];
        
        foreach ($defaultPages as $page) {
            try {
                $this->db->insert('page_content', $page);
            } catch (Exception $e) {
                // Page might already exist, skip
            }
        }
    }
}

