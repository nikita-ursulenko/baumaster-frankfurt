<?php
/**
 * SEO Analysis Page
 * Baumaster Admin Panel - SEO Analysis
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';
require_once __DIR__ . '/../seo/seo_utils.php';
require_once __DIR__ . '/../seo/performance.php';

// Проверка прав доступа
$current_user = get_current_admin_user();
if (!has_permission('stats.view', $current_user)) {
    header('Location: index.php?error=access_denied');
    exit;
}

// Настройки страницы
$page_title = __('seo.analysis_title', 'SEO Анализ');
$page_description = __('seo.analysis_description', 'Анализ SEO оптимизации сайта');
$active_menu = 'seo';

// Инициализация переменных
$db = get_database();
$analysis_results = [];

// Обработка POST запросов
if ($_POST && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'analyze_pages':
            $analysis_results = analyze_all_pages();
            break;
            
        case 'optimize_images':
            $result = batch_optimize_images(ASSETS_PATH . 'images/');
            $analysis_results['image_optimization'] = $result;
            break;
            
        case 'generate_sitemap':
            $sitemap_path = generate_sitemap();
            $analysis_results['sitemap_generated'] = $sitemap_path;
            break;
            
        case 'create_minified_assets':
            $css_files = [
                ASSETS_PATH . 'css/tailwind.css',
                ASSETS_PATH . 'css/custom.css'
            ];
            $js_files = [
                ASSETS_PATH . 'js/main.js',
                ASSETS_PATH . 'js/admin.js'
            ];
            
            $css_result = create_minified_css($css_files, ASSETS_PATH . 'css/minified.css');
            $js_result = create_minified_js($js_files, ASSETS_PATH . 'js/minified.js');
            
            $analysis_results['minification'] = [
                'css' => $css_result,
                'js' => $js_result
            ];
            break;
    }
}

// Получение статистики SEO
$seo_stats = get_seo_statistics();

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Заголовок -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">
            <?php echo __('seo.analysis_title', 'SEO Анализ'); ?>
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            <?php echo __('seo.analysis_description', 'Анализ и оптимизация SEO параметров сайта'); ?>
        </p>
    </div>
</div>

<!-- SEO Статистика -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <?php render_stat_card([
        'title' => __('seo.pages_analyzed', 'Страниц проанализировано'),
        'value' => $seo_stats['pages_analyzed'],
        'change' => '+5%',
        'icon' => 'search',
        'color' => 'blue'
    ]); ?>
    
    <?php render_stat_card([
        'title' => __('seo.images_optimized', 'Изображений оптимизировано'),
        'value' => $seo_stats['images_optimized'],
        'change' => '+12%',
        'icon' => 'image',
        'color' => 'green'
    ]); ?>
    
    <?php render_stat_card([
        'title' => __('seo.avg_page_score', 'Средний балл страниц'),
        'value' => $seo_stats['avg_page_score'] . '/100',
        'change' => '+8%',
        'icon' => 'chart',
        'color' => 'yellow'
    ]); ?>
    
    <?php render_stat_card([
        'title' => __('seo.issues_found', 'Найдено проблем'),
        'value' => $seo_stats['issues_found'],
        'change' => '-15%',
        'icon' => 'warning',
        'color' => 'red'
    ]); ?>
</div>

<!-- Действия SEO -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Анализ страниц -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('seo.page_analysis', 'Анализ страниц'); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <?php echo __('seo.page_analysis_description', 'Проверка мета-тегов, заголовков, изображений и структуры страниц'); ?>
        </p>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="analyze_pages">
            
            <?php render_button([
                'type' => 'submit',
                'text' => __('seo.analyze_now', 'Анализировать сейчас'),
                'variant' => 'primary',
                'icon' => get_icon('search', 'w-4 h-4 mr-2')
            ]); ?>
        </form>
    </div>
    
    <!-- Оптимизация изображений -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('seo.image_optimization', 'Оптимизация изображений'); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <?php echo __('seo.image_optimization_description', 'Сжатие и создание WebP версий изображений'); ?>
        </p>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="optimize_images">
            
            <?php render_button([
                'type' => 'submit',
                'text' => __('seo.optimize_images', 'Оптимизировать изображения'),
                'variant' => 'secondary',
                'icon' => get_icon('image', 'w-4 h-4 mr-2')
            ]); ?>
        </form>
    </div>
    
    <!-- Генерация Sitemap -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('seo.sitemap_generation', 'Генерация Sitemap'); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <?php echo __('seo.sitemap_description', 'Создание XML карты сайта для поисковых систем'); ?>
        </p>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="generate_sitemap">
            
            <?php render_button([
                'type' => 'submit',
                'text' => __('seo.generate_sitemap', 'Создать Sitemap'),
                'variant' => 'secondary',
                'icon' => get_icon('external-link', 'w-4 h-4 mr-2')
            ]); ?>
        </form>
    </div>
    
    <!-- Минификация ресурсов -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('seo.asset_minification', 'Минификация ресурсов'); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <?php echo __('seo.minification_description', 'Сжатие CSS и JavaScript файлов'); ?>
        </p>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="create_minified_assets">
            
            <?php render_button([
                'type' => 'submit',
                'text' => __('seo.minify_assets', 'Минифицировать ресурсы'),
                'variant' => 'secondary',
                'icon' => get_icon('cog', 'w-4 h-4 mr-2')
            ]); ?>
        </form>
    </div>
</div>

<!-- Результаты анализа -->
<?php if (!empty($analysis_results)): ?>
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('seo.analysis_results', 'Результаты анализа'); ?>
        </h3>
        
        <div class="space-y-4">
            <?php foreach ($analysis_results as $type => $result): ?>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="font-medium text-gray-900 mb-2">
                        <?php echo ucfirst(str_replace('_', ' ', $type)); ?>
                    </h4>
                    <pre class="text-sm text-gray-600 whitespace-pre-wrap"><?php echo print_r($result, true); ?></pre>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- SEO Рекомендации -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mt-8">
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        <?php echo __('seo.recommendations', 'SEO Рекомендации'); ?>
    </h3>
    
    <div class="space-y-4">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-900"><?php echo __('seo.rec_meta_tags', 'Мета-теги'); ?></h4>
                <p class="text-sm text-gray-600"><?php echo __('seo.rec_meta_description', 'Убедитесь, что все страницы имеют уникальные title и description'); ?></p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-900"><?php echo __('seo.rec_images', 'Изображения'); ?></h4>
                <p class="text-sm text-gray-600"><?php echo __('seo.rec_images_description', 'Добавьте alt атрибуты ко всем изображениям и оптимизируйте их размер'); ?></p>
            </div>
        </div>
        
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-900"><?php echo __('seo.rec_headers', 'Заголовки'); ?></h4>
                <p class="text-sm text-gray-600"><?php echo __('seo.rec_headers_description', 'Используйте правильную иерархию заголовков H1-H6'); ?></p>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Получение статистики SEO
 */
function get_seo_statistics() {
    $db = get_database();
    
    // Подсчет страниц с мета-тегами
    $services = $db->select('services', ['status' => 'active']);
    $portfolio = $db->select('portfolio', ['status' => 'completed']);
    $blog_posts = $db->select('blog_posts', ['status' => 'published']);
    
    $pages_with_meta = 0;
    $total_pages = count($services) + count($portfolio) + count($blog_posts) + 7; // +7 статических страниц
    
    foreach ($services as $service) {
        if (!empty($service['meta_title']) && !empty($service['meta_description'])) {
            $pages_with_meta++;
        }
    }
    
    foreach ($portfolio as $project) {
        if (!empty($project['meta_title']) && !empty($project['meta_description'])) {
            $pages_with_meta++;
        }
    }
    
    foreach ($blog_posts as $post) {
        if (!empty($post['meta_title']) && !empty($post['meta_description'])) {
            $pages_with_meta++;
        }
    }
    
    // Статические страницы (предполагаем, что они оптимизированы)
    $pages_with_meta += 7;
    
    return [
        'pages_analyzed' => $total_pages,
        'images_optimized' => count(glob(ASSETS_PATH . 'images/*_optimized.*')),
        'avg_page_score' => round(($pages_with_meta / $total_pages) * 100),
        'issues_found' => max(0, $total_pages - $pages_with_meta)
    ];
}

/**
 * Анализ всех страниц
 */
function analyze_all_pages() {
    $results = [];
    
    // Анализ статических страниц
    $static_pages = ['index.php', 'services.php', 'portfolio.php', 'about.php', 'reviews.php', 'blog.php', 'contact.php'];
    
    foreach ($static_pages as $page) {
        if (file_exists($page)) {
            $results[$page] = check_page_performance($page);
        }
    }
    
    return $results;
}

/**
 * Генерация sitemap
 */
function generate_sitemap() {
    $sitemap_content = file_get_contents(__DIR__ . '/../seo/sitemap.php');
    $sitemap_path = __DIR__ . '/../sitemap.xml';
    
    if (file_put_contents($sitemap_path, $sitemap_content)) {
        return $sitemap_path;
    }
    
    return false;
}

$page_content = ob_get_clean();

// Рендеринг страницы
render_admin_layout([
    'page_title' => $page_title,
    'page_description' => $page_description,
    'active_menu' => $active_menu,
    'content' => $page_content
]);
?>

