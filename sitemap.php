<?php
/**
 * XML Sitemap Generator
 */
require_once __DIR__ . '/bootstrap.php';

header('Content-Type: application/xml; charset=utf-8');

$db = Database::getInstance();
$baseUrl = APP_URL;

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

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
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($baseUrl . $page['url']) . "</loc>\n";
    echo "    <changefreq>" . $page['changefreq'] . "</changefreq>\n";
    echo "    <priority>" . $page['priority'] . "</priority>\n";
    echo "  </url>\n";
}

// Blog posts
$blogs = $db->fetchAll("SELECT slug, updated_at FROM blogs WHERE status = 'published'");
foreach ($blogs as $blog) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($baseUrl . '/blog/' . $blog['slug']) . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d', strtotime($blog['updated_at'])) . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.7</priority>\n";
    echo "  </url>\n";
}

// Blog listing
echo "  <url>\n";
echo "    <loc>" . htmlspecialchars($baseUrl . '/blog.php') . "</loc>\n";
echo "    <changefreq>daily</changefreq>\n";
echo "    <priority>0.8</priority>\n";
echo "  </url>\n";

// Hall of Fame entries
$hallOfFame = $db->fetchAll("SELECT id, updated_at FROM hall_of_fame");
foreach ($hallOfFame as $entry) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($baseUrl . '/hall-of-fame.php?id=' . $entry['id']) . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d', strtotime($entry['updated_at'])) . "</lastmod>\n";
    echo "    <changefreq>monthly</changefreq>\n";
    echo "    <priority>0.6</priority>\n";
    echo "  </url>\n";
}

// News articles
$news = $db->fetchAll("SELECT slug, updated_at FROM news_articles ORDER BY published_at DESC LIMIT 100");
foreach ($news as $article) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($baseUrl . '/article.php?slug=' . $article['slug']) . "</loc>\n";
    echo "    <lastmod>" . date('Y-m-d', strtotime($article['updated_at'])) . "</lastmod>\n";
    echo "    <changefreq>weekly</changefreq>\n";
    echo "    <priority>0.7</priority>\n";
    echo "  </url>\n";
}

// News listing
echo "  <url>\n";
echo "    <loc>" . htmlspecialchars($baseUrl . '/news.php') . "</loc>\n";
echo "    <changefreq>daily</changefreq>\n";
echo "    <priority>0.8</priority>\n";
echo "  </url>\n";

echo '</urlset>';

