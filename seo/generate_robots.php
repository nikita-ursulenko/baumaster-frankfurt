<?php
/**
 * Генератор robots.txt
 * Baumaster SEO Tools
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';

function generate_robots_content() {
    // Получаем URL сайта автоматически
    $site_url = get_site_url();
    
    // Создаем содержимое robots.txt
    $robots_content = "User-agent: *\n";
    $robots_content .= "Allow: /\n\n";
    
    // Добавляем ссылку на sitemap
    $robots_content .= "# Sitemap\n";
    $robots_content .= "Sitemap: " . $site_url . "/sitemap.xml\n\n";
    
    // Запрещаем доступ к служебным папкам
    $robots_content .= "# Disallow admin area\n";
    $robots_content .= "Disallow: /admin/\n";
    $robots_content .= "Disallow: /data/\n";
    $robots_content .= "Disallow: /seo/\n";
    $robots_content .= "Disallow: /components/\n";
    $robots_content .= "Disallow: /ui/\n";
    $robots_content .= "Disallow: /ux/\n";
    $robots_content .= "Disallow: /functions/\n";
    $robots_content .= "Disallow: /includes/\n";
    $robots_content .= "Disallow: /integrations/\n";
    $robots_content .= "Disallow: /lang/\n";
    $robots_content .= "Disallow: /tests/\n";
    $robots_content .= "Disallow: /tools/\n";
    $robots_content .= "Disallow: /node_modules/\n";
    $robots_content .= "Disallow: /docs/\n";
    $robots_content .= "Disallow: /scripts/\n\n";
    
    // Разрешаем доступ к важным страницам
    $robots_content .= "# Allow important pages\n";
    $robots_content .= "Allow: /services/\n";
    $robots_content .= "Allow: /portfolio/\n";
    $robots_content .= "Allow: /blog/\n";
    $robots_content .= "Allow: /reviews/\n";
    $robots_content .= "Allow: /about/\n";
    $robots_content .= "Allow: /contact/\n";
    $robots_content .= "Allow: /assets/\n\n";
    
    // Настройки задержки
    $robots_content .= "# Crawl delay\n";
    $robots_content .= "Crawl-delay: 1\n\n";
    
    return $robots_content;
}

// Если файл вызывается напрямую, генерируем и сохраняем robots.txt
if (basename($_SERVER['PHP_SELF']) === 'generate_robots.php') {
    $robots_content = generate_robots_content();
    
    // Путь к файлу robots.txt
    $robots_path = __DIR__ . '/../robots.txt';
    
    // Сохраняем в файл
    if (file_put_contents($robots_path, $robots_content)) {
        echo "Robots.txt успешно создан: " . $robots_path . "\n";
        echo "Размер файла: " . filesize($robots_path) . " байт\n";
        echo "\nСодержимое:\n";
        echo $robots_content;
    } else {
        echo "Ошибка при создании robots.txt\n";
    }
}
?>
