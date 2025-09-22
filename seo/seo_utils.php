<?php
/**
 * SEO Utilities
 * Baumaster SEO Tools
 */

/**
 * Генерация мета-тегов для страницы
 */
function generate_meta_tags($page_data = []) {
    $defaults = [
        'title' => get_setting('site_title', 'Baumaster Frankfurt - Строительные услуги'),
        'description' => get_setting('site_description', 'Профессиональные строительные и ремонтные услуги во Франкфурте'),
        'keywords' => get_setting('site_keywords', 'строительство Франкфурт, ремонт квартир'),
        'image' => get_setting('site_image', '/assets/images/og-image.jpg'),
        'url' => get_current_url(),
        'type' => 'website'
    ];
    
    $meta = array_merge($defaults, $page_data);
    
    $output = '';
    
    // Basic meta tags
    $output .= '<title>' . htmlspecialchars($meta['title']) . '</title>' . "\n";
    $output .= '<meta name="description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
    $output .= '<meta name="keywords" content="' . htmlspecialchars($meta['keywords']) . '">' . "\n";
    
    // Open Graph tags
    $output .= '<meta property="og:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
    $output .= '<meta property="og:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
    $output .= '<meta property="og:image" content="' . htmlspecialchars($meta['image']) . '">' . "\n";
    $output .= '<meta property="og:url" content="' . htmlspecialchars($meta['url']) . '">' . "\n";
    $output .= '<meta property="og:type" content="' . htmlspecialchars($meta['type']) . '">' . "\n";
    $output .= '<meta property="og:site_name" content="' . htmlspecialchars(get_setting('company_name', 'Baumaster Frankfurt')) . '">' . "\n";
    
    // Twitter Card tags
    $output .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $output .= '<meta name="twitter:title" content="' . htmlspecialchars($meta['title']) . '">' . "\n";
    $output .= '<meta name="twitter:description" content="' . htmlspecialchars($meta['description']) . '">' . "\n";
    $output .= '<meta name="twitter:image" content="' . htmlspecialchars($meta['image']) . '">' . "\n";
    
    // Additional SEO tags
    $output .= '<meta name="robots" content="index, follow">' . "\n";
    $output .= '<meta name="author" content="' . htmlspecialchars(get_setting('company_name', 'Baumaster Frankfurt')) . '">' . "\n";
    $output .= '<link rel="canonical" href="' . htmlspecialchars($meta['url']) . '">' . "\n";
    
    return $output;
}

/**
 * Генерация JSON-LD разметки
 */
function generate_json_ld($type, $data = []) {
    $base_schema = [
        '@context' => 'https://schema.org',
        '@type' => $type
    ];
    
    $schema = array_merge($base_schema, $data);
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>' . "\n";
}

/**
 * Генерация Schema.org для организации
 */
function generate_organization_schema() {
    return generate_json_ld('Organization', [
        'name' => get_setting('company_name', 'Baumaster Frankfurt'),
        'description' => get_setting('company_description', 'Профессиональные строительные услуги'),
        'url' => get_setting('site_url', 'https://baumaster-frankfurt.de'),
        'logo' => get_setting('site_logo', '/assets/images/logo.png'),
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => get_setting('company_address', 'Frankfurt am Main'),
            'addressLocality' => 'Frankfurt am Main',
            'addressCountry' => 'DE'
        ],
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => get_setting('company_phone', '+49 69 123-456-789'),
            'contactType' => 'customer service',
            'availableLanguage' => ['German', 'Russian', 'English']
        ],
        'sameAs' => array_filter([
            get_setting('facebook_url'),
            get_setting('instagram_url'),
            get_setting('linkedin_url')
        ])
    ]);
}

/**
 * Генерация Schema.org для услуги
 */
function generate_service_schema($service) {
    return generate_json_ld('Service', [
        'name' => $service['title'],
        'description' => $service['description'],
        'provider' => [
            '@type' => 'Organization',
            'name' => get_setting('company_name', 'Baumaster Frankfurt')
        ],
        'offers' => [
            '@type' => 'Offer',
            'price' => $service['price'],
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock'
        ],
        'areaServed' => [
            '@type' => 'City',
            'name' => 'Frankfurt am Main'
        ]
    ]);
}

/**
 * Генерация Schema.org для проекта портфолио
 */
function generate_project_schema($project) {
    return generate_json_ld('CreativeWork', [
        'name' => $project['title'],
        'description' => $project['description'],
        'creator' => [
            '@type' => 'Organization',
            'name' => get_setting('company_name', 'Baumaster Frankfurt')
        ],
        'dateCreated' => $project['created_at'],
        'dateModified' => $project['updated_at'],
        'image' => $project['featured_image'],
        'keywords' => $project['tags']
    ]);
}

/**
 * Генерация Schema.org для отзыва
 */
function generate_review_schema($review) {
    return generate_json_ld('Review', [
        'itemReviewed' => [
            '@type' => 'Organization',
            'name' => get_setting('company_name', 'Baumaster Frankfurt')
        ],
        'author' => [
            '@type' => 'Person',
            'name' => $review['client_name']
        ],
        'reviewRating' => [
            '@type' => 'Rating',
            'ratingValue' => $review['rating'],
            'bestRating' => 5
        ],
        'reviewBody' => $review['review_text'],
        'datePublished' => $review['review_date']
    ]);
}

/**
 * Генерация Schema.org для статьи блога
 */
function generate_article_schema($article) {
    return generate_json_ld('Article', [
        'headline' => $article['title'],
        'description' => $article['excerpt'],
        'author' => [
            '@type' => 'Organization',
            'name' => get_setting('company_name', 'Baumaster Frankfurt')
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => get_setting('company_name', 'Baumaster Frankfurt'),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => get_setting('site_logo', '/assets/images/logo.png')
            ]
        ],
        'datePublished' => $article['published_at'],
        'dateModified' => $article['updated_at'],
        'image' => $article['featured_image'],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => get_current_url()
        ]
    ]);
}

/**
 * Получение текущего URL
 */
function get_current_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    return $protocol . '://' . $host . $uri;
}

/**
 * Оптимизация изображения для SEO
 */
function optimize_image_for_seo($image_path, $alt_text = '', $title = '') {
    if (empty($alt_text)) {
        $alt_text = basename($image_path, '.' . pathinfo($image_path, PATHINFO_EXTENSION));
    }
    
    return [
        'src' => $image_path,
        'alt' => htmlspecialchars($alt_text),
        'title' => htmlspecialchars($title ?: $alt_text),
        'loading' => 'lazy',
        'width' => 'auto',
        'height' => 'auto'
    ];
}

/**
 * Генерация хлебных крошек
 */
function generate_breadcrumbs($items) {
    $output = '<nav aria-label="Breadcrumb" class="breadcrumbs">' . "\n";
    $output .= '  <ol class="flex items-center space-x-2 text-sm text-gray-500">' . "\n";
    
    foreach ($items as $index => $item) {
        $is_last = $index === count($items) - 1;
        
        if ($is_last) {
            $output .= '    <li class="flex items-center">' . "\n";
            $output .= '      <span class="text-gray-900 font-medium">' . htmlspecialchars($item['title']) . '</span>' . "\n";
            $output .= '    </li>' . "\n";
        } else {
            $output .= '    <li class="flex items-center">' . "\n";
            $output .= '      <a href="' . htmlspecialchars($item['url']) . '" class="hover:text-primary-600">' . htmlspecialchars($item['title']) . '</a>' . "\n";
            $output .= '      <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>' . "\n";
            $output .= '    </li>' . "\n";
        }
    }
    
    $output .= '  </ol>' . "\n";
    $output .= '</nav>' . "\n";
    
    return $output;
}
?>

