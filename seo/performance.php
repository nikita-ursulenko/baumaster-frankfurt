<?php
/**
 * Performance Optimizer
 * Baumaster SEO Tools - Performance Optimization
 */

/**
 * Минификация CSS
 */
function minify_css($css) {
    // Удаление комментариев
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
    
    // Удаление лишних пробелов
    $css = preg_replace('/\s+/', ' ', $css);
    $css = preg_replace('/\s*{\s*/', '{', $css);
    $css = preg_replace('/;\s*/', ';', $css);
    $css = preg_replace('/\s*}\s*/', '}', $css);
    $css = preg_replace('/\s*,\s*/', ',', $css);
    $css = preg_replace('/\s*:\s*/', ':', $css);
    
    // Удаление пробелов в начале и конце
    $css = trim($css);
    
    return $css;
}

/**
 * Минификация JavaScript
 */
function minify_js($js) {
    // Удаление однострочных комментариев (но не в строках)
    $js = preg_replace('/(?<!["\'])\/\/.*$/m', '', $js);
    
    // Удаление многострочных комментариев
    $js = preg_replace('/\/\*.*?\*\//s', '', $js);
    
    // Удаление лишних пробелов
    $js = preg_replace('/\s+/', ' ', $js);
    $js = preg_replace('/\s*{\s*/', '{', $js);
    $js = preg_replace('/\s*}\s*/', '}', $js);
    $js = preg_replace('/\s*;\s*/', ';', $js);
    $js = preg_replace('/\s*,\s*/', ',', $js);
    $js = preg_replace('/\s*=\s*/', '=', $js);
    $js = preg_replace('/\s*\+\s*/', '+', $js);
    $js = preg_replace('/\s*-\s*/', '-', $js);
    $js = preg_replace('/\s*\*\s*/', '*', $js);
    $js = preg_replace('/\s*\/\s*/', '/', $js);
    
    // Удаление пробелов в начале и конце
    $js = trim($js);
    
    return $js;
}

/**
 * Создание минифицированного CSS файла
 */
function create_minified_css($source_files, $output_file) {
    $combined_css = '';
    
    foreach ($source_files as $file) {
        if (file_exists($file)) {
            $css_content = file_get_contents($file);
            $combined_css .= $css_content . "\n";
        }
    }
    
    $minified_css = minify_css($combined_css);
    
    // Создание директории если не существует
    $output_dir = dirname($output_file);
    if (!is_dir($output_dir)) {
        mkdir($output_dir, 0755, true);
    }
    
    return file_put_contents($output_file, $minified_css) !== false;
}

/**
 * Создание минифицированного JS файла
 */
function create_minified_js($source_files, $output_file) {
    $combined_js = '';
    
    foreach ($source_files as $file) {
        if (file_exists($file)) {
            $js_content = file_get_contents($file);
            $combined_js .= $js_content . ";\n";
        }
    }
    
    $minified_js = minify_js($combined_js);
    
    // Создание директории если не существует
    $output_dir = dirname($output_file);
    if (!is_dir($output_dir)) {
        mkdir($output_dir, 0755, true);
    }
    
    return file_put_contents($output_file, $minified_js) !== false;
}

/**
 * Генерация .htaccess для производительности
 */
function generate_performance_htaccess() {
    $htaccess_content = <<<'HTACCESS'
# Performance Optimization
# Baumaster SEO Tools

# Enable Gzip Compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE image/svg+xml
</IfModule>

# Browser Caching
<IfModule mod_expires.c>
    ExpiresActive On
    
    # Images
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    
    # CSS and JavaScript
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
    
    # Fonts
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType application/font-woff "access plus 1 year"
    ExpiresByType application/font-woff2 "access plus 1 year"
    
    # HTML
    ExpiresByType text/html "access plus 1 hour"
</IfModule>

# Cache Control Headers
<IfModule mod_headers.c>
    # Images
    <FilesMatch "\.(jpg|jpeg|png|gif|webp|svg)$">
        Header set Cache-Control "max-age=2592000, public"
    </FilesMatch>
    
    # CSS and JavaScript
    <FilesMatch "\.(css|js)$">
        Header set Cache-Control "max-age=2592000, public"
    </FilesMatch>
    
    # Fonts
    <FilesMatch "\.(woff|woff2|eot|ttf|otf)$">
        Header set Cache-Control "max-age=31536000, public"
    </FilesMatch>
    
    # HTML
    <FilesMatch "\.(html|htm|php)$">
        Header set Cache-Control "max-age=3600, public"
    </FilesMatch>
</IfModule>

# Remove ETags
<IfModule mod_headers.c>
    Header unset ETag
</IfModule>
FileETag None

# Enable Keep-Alive
<IfModule mod_headers.c>
    Header set Connection keep-alive
</IfModule>

# Disable Server Signature
ServerSignature Off

# Hide Apache Version
<IfModule mod_headers.c>
    Header unset Server
    Header always unset X-Powered-By
</IfModule>
HTACCESS;

    return $htaccess_content;
}

/**
 * Оптимизация HTML
 */
function optimize_html($html) {
    // Удаление HTML комментариев (кроме IE условных)
    $html = preg_replace('/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s', '', $html);
    
    // Удаление лишних пробелов между тегами
    $html = preg_replace('/>\s+</', '><', $html);
    
    // Удаление пробелов в начале и конце строк
    $html = preg_replace('/^\s+|\s+$/m', '', $html);
    
    // Удаление пустых строк
    $html = preg_replace('/\n\s*\n/', "\n", $html);
    
    return trim($html);
}

/**
 * Генерация критического CSS
 */
function generate_critical_css($html, $css_files) {
    $critical_css = '';
    
    // Извлечение классов из HTML
    preg_match_all('/class="([^"]+)"/', $html, $matches);
    $used_classes = [];
    foreach ($matches[1] as $class_list) {
        $classes = explode(' ', $class_list);
        $used_classes = array_merge($used_classes, $classes);
    }
    $used_classes = array_unique($used_classes);
    
    // Загрузка CSS файлов
    foreach ($css_files as $css_file) {
        if (file_exists($css_file)) {
            $css_content = file_get_contents($css_file);
            
            // Извлечение правил для используемых классов
            foreach ($used_classes as $class) {
                $pattern = '/\.' . preg_quote($class, '/') . '\s*\{[^}]*\}/';
                if (preg_match($pattern, $css_content, $matches)) {
                    $critical_css .= $matches[0] . "\n";
                }
            }
        }
    }
    
    return minify_css($critical_css);
}

/**
 * Проверка производительности страницы
 */
function check_page_performance($url) {
    $performance_data = [
        'url' => $url,
        'timestamp' => time(),
        'checks' => []
    ];
    
    // Проверка размера страницы
    $content = file_get_contents($url);
    $page_size = strlen($content);
    $performance_data['checks']['page_size'] = [
        'value' => $page_size,
        'formatted' => format_file_size($page_size),
        'status' => $page_size < 100000 ? 'good' : ($page_size < 500000 ? 'warning' : 'bad')
    ];
    
    // Проверка количества изображений
    preg_match_all('/<img[^>]+>/i', $content, $images);
    $image_count = count($images[0]);
    $performance_data['checks']['image_count'] = [
        'value' => $image_count,
        'status' => $image_count < 10 ? 'good' : ($image_count < 20 ? 'warning' : 'bad')
    ];
    
    // Проверка alt атрибутов изображений
    $images_without_alt = 0;
    foreach ($images[0] as $img) {
        if (!preg_match('/alt\s*=/i', $img)) {
            $images_without_alt++;
        }
    }
    $performance_data['checks']['images_without_alt'] = [
        'value' => $images_without_alt,
        'percentage' => $image_count > 0 ? round(($images_without_alt / $image_count) * 100, 2) : 0,
        'status' => $images_without_alt === 0 ? 'good' : ($images_without_alt < $image_count * 0.2 ? 'warning' : 'bad')
    ];
    
    // Проверка заголовков H1
    preg_match_all('/<h1[^>]*>(.*?)<\/h1>/i', $content, $h1_tags);
    $h1_count = count($h1_tags[0]);
    $performance_data['checks']['h1_count'] = [
        'value' => $h1_count,
        'status' => $h1_count === 1 ? 'good' : ($h1_count === 0 ? 'bad' : 'warning')
    ];
    
    return $performance_data;
}
?>

