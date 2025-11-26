<?php
/**
 * Sitemap Generator with Configurable Frequency
 */
class SitemapGenerator {
    private $db;
    private $settings;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->settings = new Settings();
    }
    
    public function generate() {
        $baseUrl = APP_URL;
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Static pages
        $staticPages = [
            ['url' => '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => '/charts.php', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/music.php', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/videos.php', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => '/richest.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/awards.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/hall-of-fame.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/blog.php', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => '/news.php', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => '/business.php', 'priority' => '0.8', 'changefreq' => 'weekly'],
        ];
        
        foreach ($staticPages as $page) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($baseUrl . $page['url']) . "</loc>\n";
            $xml .= "    <changefreq>" . $page['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $page['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        
        // Blog posts
        $blogs = $this->db->fetchAll("SELECT slug, updated_at FROM blogs WHERE status = 'published'");
        foreach ($blogs as $blog) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($baseUrl . '/blog/' . $blog['slug']) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d', strtotime($blog['updated_at'])) . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }
        
        // News articles (only published/admin-written, not API-sourced for privacy)
        $news = $this->db->fetchAll(
            "SELECT slug, updated_at FROM news_articles WHERE is_from_api = 0 OR published_at IS NOT NULL ORDER BY published_at DESC LIMIT 100"
        );
        foreach ($news as $article) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($baseUrl . '/news/' . $article['slug']) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d', strtotime($article['updated_at'])) . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.7</priority>\n";
            $xml .= "  </url>\n";
        }
        
        // Hall of Fame entries
        $hallOfFame = $this->db->fetchAll("SELECT id, updated_at FROM hall_of_fame");
        foreach ($hallOfFame as $entry) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($baseUrl . '/hall-of-fame.php?id=' . $entry['id']) . "</loc>\n";
            $xml .= "    <lastmod>" . date('Y-m-d', strtotime($entry['updated_at'])) . "</lastmod>\n";
            $xml .= "    <changefreq>monthly</changefreq>\n";
            $xml .= "    <priority>0.6</priority>\n";
            $xml .= "  </url>\n";
        }
        
        $xml .= '</urlset>';
        
        // Save to file
        $sitemapPath = BASE_PATH . '/sitemap.xml';
        file_put_contents($sitemapPath, $xml);
        
        // Update last generated time
        $this->db->query("UPDATE sitemap_settings SET last_generated = NOW() WHERE id = 1");
        
        return $sitemapPath;
    }
    
    public function shouldGenerate() {
        $settings = $this->db->fetchOne("SELECT * FROM sitemap_settings WHERE id = 1");
        
        if (!$settings || !$settings['auto_generate']) {
            return false;
        }
        
        if (!$settings['last_generated']) {
            return true;
        }
        
        $frequency = (int)$settings['generation_frequency']; // days
        $lastGenerated = strtotime($settings['last_generated']);
        $daysSince = (time() - $lastGenerated) / 86400;
        
        return $daysSince >= $frequency;
    }
}

