<?php
/**
 * SEO Helper Class
 */
class SEO {
    private $title;
    private $description;
    private $keywords;
    private $image;
    private $url;
    private $type;
    
    public function __construct() {
        $this->url = APP_URL . $_SERVER['REQUEST_URI'];
        $this->type = 'website';
    }
    
    public function setTitle($title) {
        $this->title = $title . ' - ' . APP_NAME;
        return $this;
    }
    
    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }
    
    public function setKeywords($keywords) {
        if (is_array($keywords)) {
            $keywords = implode(', ', $keywords);
        }
        $this->keywords = $keywords;
        return $this;
    }
    
    public function setImage($image) {
        $this->image = $image;
        return $this;
    }
    
    public function setType($type) {
        $this->type = $type;
        return $this;
    }
    
    public function setURL($url) {
        $this->url = $url;
        return $this;
    }
    
    public function render() {
        $html = '';
        
        // Basic meta tags
        $html .= '<meta name="description" content="' . htmlspecialchars($this->description ?? 'Music charts, trending videos, and entertainment news') . '">' . "\n";
        if ($this->keywords) {
            $html .= '<meta name="keywords" content="' . htmlspecialchars($this->keywords) . '">' . "\n";
        }
        
        // Open Graph
        $html .= '<meta property="og:title" content="' . htmlspecialchars($this->title ?? APP_NAME) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . htmlspecialchars($this->description ?? 'Music charts and entertainment') . '">' . "\n";
        $html .= '<meta property="og:type" content="' . htmlspecialchars($this->type) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . htmlspecialchars($this->url) . '">' . "\n";
        if ($this->image) {
            $html .= '<meta property="og:image" content="' . htmlspecialchars($this->image) . '">' . "\n";
        }
        
        // Twitter Card
        $html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
        $html .= '<meta name="twitter:title" content="' . htmlspecialchars($this->title ?? APP_NAME) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . htmlspecialchars($this->description ?? 'Music charts and entertainment') . '">' . "\n";
        if ($this->image) {
            $html .= '<meta name="twitter:image" content="' . htmlspecialchars($this->image) . '">' . "\n";
        }
        
        // Canonical URL
        $html .= '<link rel="canonical" href="' . htmlspecialchars($this->url) . '">' . "\n";
        
        return $html;
    }
    
    public function getTitle() {
        return $this->title ?? APP_NAME;
    }
    
    public static function generateStructuredData($type, $data) {
        $structured = [
            '@context' => 'https://schema.org',
            '@type' => $type
        ];
        
        $structured = array_merge($structured, $data);
        
        return '<script type="application/ld+json">' . json_encode($structured, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>' . "\n";
    }
}

