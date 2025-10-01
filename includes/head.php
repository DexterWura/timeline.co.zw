<?php
// Get current page title or set default
$page_title = isset($page_title) ? $page_title : 'Timeline.co.zw - African Music & Entertainment Hub';
$page_description = isset($page_description) ? $page_description : 'Discover trending music, videos, and entertainment from Africa and around the world. Zimbabwe\'s premier music and entertainment platform.';
$page_keywords = isset($page_keywords) ? $page_keywords : 'music, videos, entertainment, Africa, Zimbabwe, trending, charts, billionaires, awards';
$canonical_url = isset($canonical_url) ? $canonical_url : 'https://timeline.co.zw';
$og_image = isset($og_image) ? $og_image : 'https://timeline.co.zw/images/og-image.jpg';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($page_keywords); ?>">
    <meta name="author" content="Timeline.co.zw">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($canonical_url); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($canonical_url); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Timeline.co.zw">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($page_description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($og_image); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    
    <!-- Preconnect to external domains -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://www.googleapis.com">
    <link rel="preconnect" href="https://i.ytimg.com">
    <link rel="preconnect" href="https://img.youtube.com">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="css/style.css">
    <?php if (isset($additional_css)): ?>
        <?php foreach ($additional_css as $css_file): ?>
            <link rel="stylesheet" href="<?php echo htmlspecialchars($css_file); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Timeline.co.zw",
        "description": "<?php echo htmlspecialchars($page_description); ?>",
        "url": "<?php echo htmlspecialchars($canonical_url); ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?php echo htmlspecialchars($canonical_url); ?>/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
        },
        "publisher": {
            "@type": "Organization",
            "name": "Timeline.co.zw",
            "logo": {
                "@type": "ImageObject",
                "url": "<?php echo htmlspecialchars($canonical_url); ?>/images/logo.png"
            }
        }
    }
    </script>
    
    <!-- Google Analytics (replace with your tracking ID) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>
    
    <!-- YouTube API Configuration -->
    <script>
        // YouTube API Configuration
        window.YOUTUBE_API_KEY = '<?php echo isset($_ENV['YOUTUBE_API_KEY']) ? $_ENV['YOUTUBE_API_KEY'] : 'YOUR_YOUTUBE_API_KEY'; ?>';
        window.DEFAULT_REGION = '<?php echo isset($_ENV['DEFAULT_REGION']) ? $_ENV['DEFAULT_REGION'] : 'ZW'; ?>'; // Zimbabwe
        window.DEFAULT_LANGUAGE = '<?php echo isset($_ENV['DEFAULT_LANGUAGE']) ? $_ENV['DEFAULT_LANGUAGE'] : 'en'; ?>';
        
        // User location detection
        window.USER_LOCATION = {
            country: '<?php echo isset($_SESSION['user_country']) ? $_SESSION['user_country'] : 'ZW'; ?>',
            region: '<?php echo isset($_SESSION['user_region']) ? $_SESSION['user_region'] : 'Africa'; ?>',
            language: '<?php echo isset($_SESSION['user_language']) ? $_SESSION['user_language'] : 'en'; ?>'
        };
        
        // African countries priority list
        window.AFRICAN_COUNTRIES = [
            'ZW', 'ZA', 'NG', 'KE', 'GH', 'EG', 'MA', 'TN', 'DZ', 'LY', 'SD', 'ET', 'UG', 'TZ', 'RW', 'BI',
            'MW', 'ZM', 'BW', 'SZ', 'LS', 'MZ', 'MG', 'MU', 'SC', 'KM', 'DJ', 'SO', 'ER', 'SS', 'CF', 'TD',
            'NE', 'ML', 'BF', 'CI', 'LR', 'SL', 'GN', 'GW', 'GM', 'SN', 'MR', 'CV', 'AO', 'CD', 'CG', 'GA',
            'GQ', 'ST', 'CM', 'TG', 'BJ'
        ];
    </script>
    
    <!-- Page-specific meta tags -->
    <?php if (isset($page_specific_meta)): ?>
        <?php echo $page_specific_meta; ?>
    <?php endif; ?>
</head>
<body class="<?php echo isset($body_class) ? htmlspecialchars($body_class) : ''; ?>">
