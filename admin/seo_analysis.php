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
require_once __DIR__ . '/../seo/image_optimizer.php';

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

// Генерация CSRF токена (должно быть в начале для корректной работы)
$csrf_token = generate_csrf_token();

// Обработка AJAX запросов для SEO настроек
if (isset($_GET['ajax']) && $_GET['ajax'] === 'load_seo_data') {
    $page_key = $_GET['page_key'] ?? '';
    $lang = $_GET['lang'] ?? 'ru';
    
    if ($page_key) {
        try {
            $seo_keys = ['title', 'h1', 'description', 'keywords', 'og_title', 'og_description', 'og_image'];
            $seo_data = [];
            
            foreach ($seo_keys as $key) {
                $setting_key = 'page_' . $page_key . '_' . $lang . '_page_' . $key;
                $setting = $db->select('settings', ['setting_key' => $setting_key], ['limit' => 1]);
                $seo_data[$key] = $setting ? $setting['setting_value'] : '';
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'data' => $seo_data]);
            exit;
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}

// Обработка AJAX запросов для автоперевода
if (isset($_GET['ajax']) && $_GET['ajax'] === 'auto_translate') {
    $page_key = $_GET['page_key'] ?? '';
    $from_lang = $_GET['from_lang'] ?? 'ru';
    $to_lang = $_GET['to_lang'] ?? 'de';
    
    if ($page_key) {
        try {
            require_once __DIR__ . '/../integrations/translation/TranslationManager.php';
            $translation_manager = new TranslationManager();
            
            // Получаем исходные SEO данные
            $seo_keys = ['page_title', 'page_h1', 'page_description', 'page_keywords', 'page_og_title', 'page_og_description'];
            $source_data = [];
            
            foreach ($seo_keys as $key) {
                // Сначала пробуем найти с языком
                $setting_key = 'page_' . $page_key . '_' . $from_lang . '_' . $key;
                $setting = $db->select('settings', ['setting_key' => $setting_key], ['limit' => 1]);
                
                // Если не найдено, пробуем без языка
                if (!$setting || empty($setting['setting_value'])) {
                    $setting_key = 'page_' . $page_key . '_' . $key;
                    $setting = $db->select('settings', ['setting_key' => $setting_key], ['limit' => 1]);
                }
                
                if ($setting && !empty($setting['setting_value'])) {
                    // Убираем префикс 'page_' для перевода
                    $clean_key = str_replace('page_', '', $key);
                    $source_data[$clean_key] = $setting['setting_value'];
                }
            }
            
            if (!empty($source_data)) {
                // Переводим данные
                $translated_data = $translation_manager->autoTranslateContent(
                    'seo_settings', 
                    $page_key . '_' . $to_lang, 
                    $source_data, 
                    $from_lang, 
                    $to_lang
                );
                
                // Сохраняем переведенные данные
                foreach ($translated_data as $key => $translated_text) {
                    $setting_key = 'page_' . $page_key . '_' . $to_lang . '_page_' . $key;
                    
                    $existing = $db->select('settings', ['setting_key' => $setting_key], ['limit' => 1]);
                    
                    if ($existing) {
                        $db->update('settings', 
                            ['setting_value' => $translated_text, 'updated_at' => date('Y-m-d H:i:s')], 
                            ['setting_key' => $setting_key]
                        );
                    } else {
                        $db->insert('settings', [
                            'setting_key' => $setting_key,
                            'setting_value' => $translated_text,
                            'category' => 'seo'
                        ]);
                    }
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Перевод выполнен успешно', 'data' => $translated_data]);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => 'Нет данных для перевода']);
                exit;
            }
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
}

// Обработка POST запросов
if ($_POST && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    $category = $_POST['category'] ?? '';
    $settings_data = $_POST['settings'] ?? [];
    
    // Обработка SEO настроек страниц
    if ($category === 'seo' && isset($_POST['page_key'])) {
        $page_key = $_POST['page_key'];
        $lang = $_POST['lang'] ?? 'ru';
        
        try {
            foreach ($settings_data as $key => $value) {
                $full_key = 'page_' . $page_key . '_' . $lang . '_' . $key;
                
                $existing = $db->select('settings', ['setting_key' => $full_key], ['limit' => 1]);
                
                if ($existing) {
                    $db->update('settings', 
                        ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 
                        ['setting_key' => $full_key]
                    );
                } else {
                    $db->insert('settings', [
                        'setting_key' => $full_key,
                        'setting_value' => $value,
                        'category' => $category
                    ]);
                }
            }
            
            $success_message = 'SEO настройки успешно сохранены';
            log_user_activity('seo_settings_update', 'seo', 0);
            
            // Если это AJAX запрос, возвращаем JSON ответ
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'seo_modal') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $success_message]);
                exit;
            }
            
        } catch (Exception $e) {
            $error_message = 'Ошибка при сохранении SEO настроек';
            write_log("SEO settings update error: " . $e->getMessage(), 'ERROR');
            
            // Если это AJAX запрос, возвращаем JSON ответ с ошибкой
            if (isset($_POST['ajax']) && $_POST['ajax'] === 'seo_modal') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'error' => $error_message]);
                exit;
            }
        }
    }
    
    // Обработка других действий
    switch ($action) {
        case 'analyze_pages':
            $selected_pages = $_POST['pages'] ?? [];
            $analysis_results['analyze_pages'] = analyze_selected_pages($selected_pages);
            break;
            
        case 'optimize_images':
            $result = batch_optimize_images(ASSETS_PATH . 'images/');
            $analysis_results['image_optimization'] = $result;
            break;
            
        case 'generate_sitemap':
            $sitemap_path = generate_sitemap();
            $analysis_results['sitemap_generated'] = $sitemap_path;
            break;
            
        case 'generate_robots':
            $robots_path = generate_robots();
            $analysis_results['robots_generated'] = $robots_path;
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

// Получение текущих настроек
$settings = [];
$all_settings = $db->select('settings', [], ['order' => 'category, setting_key']);

foreach ($all_settings as $setting) {
    $settings[$setting['category']][$setting['setting_key']] = $setting;
}

// CSRF токен уже сгенерирован выше

// Начало контента
ob_start();
?>

<!-- Заголовок удален -->

<!-- SEO Статистика -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <?php render_stat_card([
        'title' => __('seo.pages_analyzed', 'Страниц проанализировано'),
        'value' => $seo_stats['pages_analyzed'],
        'change' => '+5%',
        'icon' => get_icon('search', 'w-5 h-5 text-white'),
        'color' => 'blue'
    ]); ?>
    
    <?php render_stat_card([
        'title' => __('seo.images_optimized', 'Изображений оптимизировано'),
        'value' => $seo_stats['images_optimized'],
        'change' => '+12%',
        'icon' => get_icon('image', 'w-5 h-5 text-white'),
        'color' => 'green'
    ]); ?>
    
    <?php render_stat_card([
        'title' => __('seo.avg_page_score', 'Средний балл страниц'),
        'value' => $seo_stats['avg_page_score'] . '/100',
        'change' => '+8%',
        'icon' => get_icon('chart', 'w-5 h-5 text-white'),
        'color' => 'yellow'
    ]); ?>
    
    <?php render_stat_card([
        'title' => __('seo.issues_found', 'Найдено проблем'),
        'value' => $seo_stats['issues_found'],
        'change' => '-15%',
        'icon' => get_icon('warning', 'w-5 h-5 text-white'),
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
            
            <!-- Выбор страниц для анализа -->
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Выберите страницы для анализа:
                    </label>
                    
                    <!-- Быстрый выбор -->
                    <div class="mb-3">
                        <div class="flex space-x-2">
                            <button type="button" onclick="selectAllPages()" 
                                    class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                Выбрать все
                            </button>
                            <button type="button" onclick="selectRussianPages()" 
                                    class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">
                                Только русские
                            </button>
                            <button type="button" onclick="selectGermanPages()" 
                                    class="px-3 py-1 text-xs bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">
                                Только немецкие
                            </button>
                            <button type="button" onclick="clearAllPages()" 
                                    class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                Очистить
                            </button>
                        </div>
                    </div>
                    
                    <!-- Список страниц -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Русские страницы -->
                        <div class="space-y-2">
                            <h4 class="font-medium text-gray-900 text-sm">🇷🇺 Русские страницы</h4>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="index.php" class="page-checkbox" checked>
                                    <span class="text-sm text-gray-700">Главная страница (/)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="services.php" class="page-checkbox" checked>
                                    <span class="text-sm text-gray-700">Услуги (/services.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="portfolio.php" class="page-checkbox">
                                    <span class="text-sm text-gray-700">Портфолио (/portfolio.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="about.php" class="page-checkbox">
                                    <span class="text-sm text-gray-700">О нас (/about.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="reviews.php" class="page-checkbox">
                                    <span class="text-sm text-gray-700">Отзывы (/reviews.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="blog.php" class="page-checkbox">
                                    <span class="text-sm text-gray-700">Блог (/blog.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="contact.php" class="page-checkbox">
                                    <span class="text-sm text-gray-700">Контакты (/contact.php)</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Немецкие страницы -->
                        <div class="space-y-2">
                            <h4 class="font-medium text-gray-900 text-sm">🇩🇪 Немецкие страницы</h4>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="de/index.php" class="page-checkbox german-page">
                                    <span class="text-sm text-gray-700">Главная страница (/de/)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="de/services.php" class="page-checkbox german-page">
                                    <span class="text-sm text-gray-700">Услуги (/de/services.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="de/portfolio.php" class="page-checkbox german-page">
                                    <span class="text-sm text-gray-700">Портфолио (/de/portfolio.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="de/about.php" class="page-checkbox german-page">
                                    <span class="text-sm text-gray-700">О нас (/de/about.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="de/reviews.php" class="page-checkbox german-page">
                                    <span class="text-sm text-gray-700">Отзывы (/de/reviews.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="de/blog.php" class="page-checkbox german-page">
                                    <span class="text-sm text-gray-700">Блог (/de/blog.php)</span>
                                </label>
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="pages[]" value="de/contact.php" class="page-checkbox german-page">
                                    <span class="text-sm text-gray-700">Контакты (/de/contact.php)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Статистика выбора -->
                    <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Выбрано страниц:</span>
                            <span id="selected-count" class="font-medium text-blue-600">2</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php render_button([
                'type' => 'submit',
                'text' => __('seo.analyze_selected', 'Анализировать выбранные страницы'),
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
    
    <!-- Генерация Robots.txt -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('seo.robots_generation', 'Генерация Robots.txt'); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <?php echo __('seo.robots_description', 'Создание файла robots.txt для управления индексацией поисковыми системами'); ?>
        </p>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="generate_robots">
            
            <?php render_button([
                'text' => __('seo.generate_robots', 'Создать Robots.txt'),
                'variant' => 'primary',
                'type' => 'submit',
                'icon' => get_icon('file', 'w-4 h-4 mr-2')
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
<?php if (!empty($analysis_results) && isset($analysis_results['analyze_pages'])): ?>
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">
            <?php echo __('seo.analysis_results', 'Результаты SEO анализа'); ?>
        </h3>
        
        <div class="space-y-6">
            <?php foreach ($analysis_results['analyze_pages'] as $page => $analysis): ?>
                <div class="border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-lg font-medium text-gray-900">
                            <?php echo htmlspecialchars($page); ?>
                        </h4>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-500">SEO балл:</span>
                            <div class="flex items-center space-x-1">
                                <span class="text-2xl font-bold <?php 
                                    echo $analysis['score'] >= 80 ? 'text-green-600' : 
                                        ($analysis['score'] >= 60 ? 'text-yellow-600' : 'text-red-600'); 
                                ?>">
                                    <?php echo $analysis['score']; ?>/<?php echo $analysis['max_score']; ?>
                                </span>
                                <div class="w-16 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full <?php 
                                        echo $analysis['score'] >= 80 ? 'bg-green-600' : 
                                            ($analysis['score'] >= 60 ? 'bg-yellow-600' : 'bg-red-600'); 
                                    ?>" style="width: <?php echo ($analysis['score'] / $analysis['max_score']) * 100; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                        <?php foreach ($analysis['checks'] as $category => $category_data): ?>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="font-medium text-gray-900 capitalize">
                                        <?php echo str_replace('_', ' ', $category); ?>
                                    </h5>
                                    <span class="text-sm font-medium <?php 
                                        echo $category_data['score'] >= ($category_data['max_score'] * 0.8) ? 'text-green-600' : 
                                            ($category_data['score'] >= ($category_data['max_score'] * 0.6) ? 'text-yellow-600' : 'text-red-600'); 
                                    ?>">
                                        <?php echo $category_data['score']; ?>/<?php echo $category_data['max_score']; ?>
                                    </span>
                                </div>
                                
                                <div class="space-y-2">
                                    <?php foreach ($category_data['checks'] as $check_name => $check): ?>
                                        <div class="flex items-center space-x-2 text-sm">
                                            <span class="w-2 h-2 rounded-full <?php 
                                                echo $check['status'] === 'good' ? 'bg-green-500' : 
                                                    ($check['status'] === 'warning' ? 'bg-yellow-500' : 'bg-red-500'); 
                                            ?>"></span>
                                            <span class="text-gray-600"><?php echo htmlspecialchars($check['message']); ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Критические проблемы -->
                    <?php if (!empty($analysis['critical_issues'])): ?>
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <h5 class="font-medium text-red-900 mb-2">🚨 Критические проблемы</h5>
                            <ul class="space-y-1 text-sm text-red-700">
                                <?php foreach ($analysis['critical_issues'] as $issue): ?>
                                    <li>• <?php echo htmlspecialchars($issue['message']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Предупреждения -->
                    <?php if (!empty($analysis['warnings'])): ?>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <h5 class="font-medium text-yellow-900 mb-2">⚠️ Предупреждения</h5>
                            <ul class="space-y-1 text-sm text-yellow-700">
                                <?php foreach (array_slice($analysis['warnings'], 0, 5) as $warning): ?>
                                    <li>• <?php echo htmlspecialchars($warning['message']); ?></li>
                                <?php endforeach; ?>
                                <?php if (count($analysis['warnings']) > 5): ?>
                                    <li class="text-yellow-600">... и еще <?php echo count($analysis['warnings']) - 5; ?> предупреждений</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Рекомендации -->
                    <?php if (!empty($analysis['recommendations'])): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h5 class="font-medium text-blue-900 mb-2">💡 Рекомендации</h5>
                            <ul class="space-y-1 text-sm text-blue-700">
                                <?php foreach (array_slice($analysis['recommendations'], 0, 5) as $recommendation): ?>
                                    <li>• <?php echo htmlspecialchars($recommendation['message']); ?></li>
                                <?php endforeach; ?>
                                <?php if (count($analysis['recommendations']) > 5): ?>
                                    <li class="text-blue-600">... и еще <?php echo count($analysis['recommendations']) - 5; ?> рекомендаций</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Общая статистика -->
        <?php 
        $total_score = 0;
        $total_max_score = 0;
        $total_pages = count($analysis_results['analyze_pages']);
        foreach ($analysis_results['analyze_pages'] as $analysis) {
            $total_score += $analysis['score'];
            $total_max_score += $analysis['max_score'];
        }
        $average_score = $total_pages > 0 ? round(($total_score / $total_max_score) * 100) : 0;
        ?>
        
        <div class="mt-8 bg-gray-50 rounded-lg p-6">
            <h4 class="text-lg font-medium text-gray-900 mb-4">📊 Общая статистика</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold <?php 
                        echo $average_score >= 80 ? 'text-green-600' : 
                            ($average_score >= 60 ? 'text-yellow-600' : 'text-red-600'); 
                    ?>">
                        <?php echo $average_score; ?>%
                    </div>
                    <div class="text-sm text-gray-600">Средний SEO балл</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600"><?php echo $total_pages; ?></div>
                    <div class="text-sm text-gray-600">Страниц проанализировано</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-purple-600">
                        <?php 
                        $critical_count = 0;
                        foreach ($analysis_results['analyze_pages'] as $analysis) {
                            $critical_count += count($analysis['critical_issues']);
                        }
                        echo $critical_count;
                        ?>
                    </div>
                    <div class="text-sm text-gray-600">Критических проблем</div>
                </div>
            </div>
        </div>
    </div>
<?php elseif (!empty($analysis_results)): ?>
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

<!-- SEO настройки по страницам -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mt-8">
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        SEO настройки по страницам
    </h3>
    
    <div class="space-y-4">
        <p class="text-sm text-gray-600">
            Управление SEO настройками для отдельных страниц сайта
        </p>
        
        <!-- Переключатель языка -->
        <div class="mb-6">
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-gray-700">Язык:</span>
                <div class="flex space-x-2">
                    <button onclick="switchLanguage('ru')" id="lang-ru-btn" 
                            class="px-3 py-2 text-sm font-medium rounded-md bg-primary-600 text-white">
                        Русский
                    </button>
                    <button onclick="switchLanguage('de')" id="lang-de-btn" 
                            class="px-3 py-2 text-sm font-medium rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300">
                        Deutsch
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" id="pages-grid">
            <?php
            $pages_ru = [
                'home' => ['name' => 'Главная', 'url' => '/'],
                'services' => ['name' => 'Услуги', 'url' => '/services.php'],
                'portfolio' => ['name' => 'Портфолио', 'url' => '/portfolio.php'],
                'about' => ['name' => 'О компании', 'url' => '/about.php'],
                'reviews' => ['name' => 'Отзывы', 'url' => '/review.php'],
                'blog' => ['name' => 'Блог/FAQ', 'url' => '/blog.php'],
                'contact' => ['name' => 'Контакты', 'url' => '/contact.php']
            ];
            
            $pages_de = [
                'home' => ['name' => 'Startseite', 'url' => '/de/'],
                'services' => ['name' => 'Dienstleistungen', 'url' => '/de/services.php'],
                'portfolio' => ['name' => 'Portfolio', 'url' => '/de/portfolio.php'],
                'about' => ['name' => 'Über uns', 'url' => '/de/about.php'],
                'reviews' => ['name' => 'Bewertungen', 'url' => '/de/review.php'],
                'blog' => ['name' => 'Blog/FAQ', 'url' => '/de/blog.php'],
                'contact' => ['name' => 'Kontakt', 'url' => '/de/contact.php']
            ];
            
            foreach ($pages_ru as $page_key => $page_info): ?>
            <div class="border border-gray-200 rounded-lg p-4 page-card" data-page="<?php echo $page_key; ?>" data-lang="ru">
                <h4 class="font-medium text-gray-900 mb-2"><?php echo $page_info['name']; ?></h4>
                <p class="text-sm text-gray-600 mb-3"><?php echo $page_info['url']; ?></p>
                <div class="flex space-x-2">
                    <button onclick="openSeoModal('<?php echo $page_key; ?>', '<?php echo $page_info['name']; ?>', 'ru')" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Настроить SEO
                    </button>
                    <button onclick="autoTranslatePage('<?php echo $page_key; ?>', 'ru', 'de')" 
                            class="inline-flex items-center px-2 py-2 border border-blue-300 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            title="Автоперевод на немецкий">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php foreach ($pages_de as $page_key => $page_info): ?>
            <div class="border border-gray-200 rounded-lg p-4 page-card hidden" data-page="<?php echo $page_key; ?>" data-lang="de">
                <h4 class="font-medium text-gray-900 mb-2"><?php echo $page_info['name']; ?></h4>
                <p class="text-sm text-gray-600 mb-3"><?php echo $page_info['url']; ?></p>
                <div class="flex space-x-2">
                    <button onclick="openSeoModal('<?php echo $page_key; ?>', '<?php echo $page_info['name']; ?>', 'de')" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        SEO einstellen
                    </button>
                    <button onclick="autoTranslatePage('<?php echo $page_key; ?>', 'de', 'ru')" 
                            class="inline-flex items-center px-2 py-2 border border-blue-300 shadow-sm text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            title="Автоперевод на русский">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Социальные сети и Open Graph -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mt-8">
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        Социальные сети и Open Graph
    </h3>
    
    <form method="POST" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="category" value="seo">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Open Graph заголовок</label>
                <input type="text" name="settings[og_title]" 
                       value="<?php echo htmlspecialchars($settings['seo']['og_title']['setting_value'] ?? ''); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">Заголовок для социальных сетей</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Open Graph изображение</label>
                <input type="text" name="settings[og_image]" 
                       value="<?php echo htmlspecialchars($settings['seo']['og_image']['setting_value'] ?? ''); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">URL изображения для социальных сетей</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Open Graph описание</label>
            <textarea name="settings[og_description]" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"><?php echo htmlspecialchars($settings['seo']['og_description']['setting_value'] ?? ''); ?></textarea>
            <p class="text-xs text-gray-500 mt-1">Описание для социальных сетей</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Twitter аккаунт</label>
                <input type="text" name="settings[twitter_handle]" 
                       value="<?php echo htmlspecialchars($settings['seo']['twitter_handle']['setting_value'] ?? ''); ?>"
                       placeholder="@username"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">@username в Twitter</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Facebook URL</label>
                <input type="url" name="settings[facebook_url]" 
                       value="<?php echo htmlspecialchars($settings['social']['facebook_url']['setting_value'] ?? ''); ?>"
                       placeholder="https://www.facebook.com/yourpage"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Instagram URL</label>
                <input type="url" name="settings[instagram_url]" 
                       value="<?php echo htmlspecialchars($settings['social']['instagram_url']['setting_value'] ?? ''); ?>"
                       placeholder="https://www.instagram.com/yourpage"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
                <input type="url" name="settings[linkedin_url]" 
                       value="<?php echo htmlspecialchars($settings['social']['linkedin_url']['setting_value'] ?? ''); ?>"
                       placeholder="https://www.linkedin.com/company/yourcompany"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
                <input type="text" name="settings[whatsapp]" 
                       value="<?php echo htmlspecialchars($settings['social']['whatsapp']['setting_value'] ?? ''); ?>"
                       placeholder="+4969123456789"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telegram</label>
                <input type="text" name="settings[telegram]" 
                       value="<?php echo htmlspecialchars($settings['social']['telegram']['setting_value'] ?? ''); ?>"
                       placeholder="@baumaster_frankfurt"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Сохранить
            </button>
        </div>
    </form>
</div>

<!-- Аналитика и отслеживание -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mt-8">
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        Аналитика и отслеживание
    </h3>
    
    <form method="POST" class="space-y-4">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="category" value="seo">
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Google Analytics</label>
            <textarea name="settings[google_analytics]" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"><?php echo htmlspecialchars($settings['seo']['google_analytics']['setting_value'] ?? ''); ?></textarea>
            <p class="text-xs text-gray-500 mt-1">Код отслеживания Google Analytics (gtag или GA4)</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Google Tag Manager</label>
                <input type="text" name="settings[google_tag_manager]" 
                       value="<?php echo htmlspecialchars($settings['seo']['google_tag_manager']['setting_value'] ?? ''); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">ID контейнера GTM</p>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Facebook Pixel</label>
                <input type="text" name="settings[facebook_pixel]" 
                       value="<?php echo htmlspecialchars($settings['seo']['facebook_pixel']['setting_value'] ?? ''); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-1">ID Facebook Pixel</p>
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Пользовательский код в head</label>
            <textarea name="settings[custom_head_code]" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"><?php echo htmlspecialchars($settings['seo']['custom_head_code']['setting_value'] ?? ''); ?></textarea>
            <p class="text-xs text-gray-500 mt-1">Дополнительный HTML код для секции head</p>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" 
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Сохранить
            </button>
        </div>
    </form>
</div>

<!-- Модальное окно для SEO настроек страницы -->
<div id="seoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Заголовок модального окна -->
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900" id="seoModalTitle">
                    SEO настройки
                </h3>
                <button onclick="closeSeoModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Форма SEO настроек -->
            <form id="seoModalForm" method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="category" value="seo">
                <input type="hidden" name="page_key" id="seoModalPageKey" value="">
                <input type="hidden" name="lang" id="seoModalLang" value="ru">
                <input type="hidden" name="ajax" value="seo_modal">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Заголовок страницы</label>
                        <input type="text" name="settings[page_title]" id="seoModalTitleInput" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Уникальный заголовок для этой страницы</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">H1 заголовок</label>
                        <input type="text" name="settings[page_h1]" id="seoModalH1Input" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Основной заголовок страницы</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Мета-описание</label>
                    <textarea name="settings[page_description]" id="seoModalDescriptionInput" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Описание для поисковых систем (до 160 символов)</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ключевые слова</label>
                    <textarea name="settings[page_keywords]" id="seoModalKeywordsInput" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Ключевые слова через запятую</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Open Graph заголовок</label>
                        <input type="text" name="settings[page_og_title]" id="seoModalOgTitleInput" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Заголовок для социальных сетей</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Open Graph изображение</label>
                        <input type="text" name="settings[page_og_image]" id="seoModalOgImageInput" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">URL изображения для социальных сетей</p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Open Graph описание</label>
                    <textarea name="settings[page_og_description]" id="seoModalOgDescriptionInput" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Описание для социальных сетей</p>
                </div>
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" onclick="closeSeoModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Отмена
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Управление модальным окном SEO
    window.openSeoModal = function(pageKey, pageName, lang = 'ru') {
        const modal = document.getElementById('seoModal');
        const title = document.getElementById('seoModalTitle');
        const pageKeyInput = document.getElementById('seoModalPageKey');
        const langInput = document.getElementById('seoModalLang');
        
        // Устанавливаем заголовок, ключ страницы и язык
        title.textContent = `SEO настройки: ${pageName}`;
        pageKeyInput.value = pageKey;
        langInput.value = lang;
        
        // Загружаем существующие данные
        loadSeoData(pageKey, lang);
        
        // Показываем модальное окно
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Блокируем прокрутку фона
    };
    
    window.closeSeoModal = function() {
        const modal = document.getElementById('seoModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto'; // Восстанавливаем прокрутку
    };
    
    // Загрузка существующих SEO данных
    window.loadSeoData = function(pageKey, lang = 'ru') {
        console.log('Загружаем SEO данные для страницы:', pageKey, 'язык:', lang);
        
        // AJAX запрос для загрузки данных
        fetch('?ajax=load_seo_data&page_key=' + encodeURIComponent(pageKey) + '&lang=' + encodeURIComponent(lang))
            .then(response => {
                console.log('Ответ получен:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Данные получены:', data);
                if (data.success) {
                    document.getElementById('seoModalTitleInput').value = data.data.title || '';
                    document.getElementById('seoModalH1Input').value = data.data.h1 || '';
                    document.getElementById('seoModalDescriptionInput').value = data.data.description || '';
                    document.getElementById('seoModalKeywordsInput').value = data.data.keywords || '';
                    document.getElementById('seoModalOgTitleInput').value = data.data.og_title || '';
                    document.getElementById('seoModalOgImageInput').value = data.data.og_image || '';
                    document.getElementById('seoModalOgDescriptionInput').value = data.data.og_description || '';
                } else {
                    console.log('Ошибка в данных:', data.error);
                    // Очищаем поля при ошибке
                    clearSeoFields();
                }
            })
            .catch(error => {
                console.error('Ошибка загрузки SEO данных:', error);
                clearSeoFields();
            });
    };
    
    // Очистка полей SEO
    window.clearSeoFields = function() {
        document.getElementById('seoModalTitleInput').value = '';
        document.getElementById('seoModalH1Input').value = '';
        document.getElementById('seoModalDescriptionInput').value = '';
        document.getElementById('seoModalKeywordsInput').value = '';
        document.getElementById('seoModalOgTitleInput').value = '';
        document.getElementById('seoModalOgImageInput').value = '';
        document.getElementById('seoModalOgDescriptionInput').value = '';
    };
    
    // Закрытие модального окна по клику на фон
    document.getElementById('seoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeSeoModal();
        }
    });
    
    // Закрытие модального окна по клавише Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('seoModal');
            if (!modal.classList.contains('hidden')) {
                closeSeoModal();
            }
        }
    });
    
    // Обработка отправки формы SEO модального окна
    document.getElementById('seoModalForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        // Показываем индикатор загрузки
        submitButton.textContent = 'Сохранение...';
        submitButton.disabled = true;
        
        fetch('', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Показываем сообщение об успехе
                showNotification('Настройки успешно сохранены', 'success');
                closeSeoModal();
            } else {
                showNotification(data.error || 'Ошибка при сохранении', 'error');
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            showNotification('Ошибка при сохранении настроек', 'error');
        })
        .finally(() => {
            // Восстанавливаем кнопку
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
    });
    
    // Функция показа уведомлений
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Переключение языка
    window.switchLanguage = function(lang) {
        const ruBtn = document.getElementById('lang-ru-btn');
        const deBtn = document.getElementById('lang-de-btn');
        const pageCards = document.querySelectorAll('.page-card');
        
        // Обновляем кнопки
        if (lang === 'ru') {
            ruBtn.className = 'px-3 py-2 text-sm font-medium rounded-md bg-primary-600 text-white';
            deBtn.className = 'px-3 py-2 text-sm font-medium rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300';
        } else {
            deBtn.className = 'px-3 py-2 text-sm font-medium rounded-md bg-primary-600 text-white';
            ruBtn.className = 'px-3 py-2 text-sm font-medium rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300';
        }
        
        // Показываем/скрываем карточки страниц
        pageCards.forEach(card => {
            const cardLang = card.getAttribute('data-lang');
            if (cardLang === lang) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    };
    
    // Автоперевод страницы
    window.autoTranslatePage = function(pageKey, fromLang, toLang) {
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        
        // Показываем индикатор загрузки
        button.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>';
        button.disabled = true;
        
        fetch('?ajax=auto_translate&page_key=' + encodeURIComponent(pageKey) + '&from_lang=' + encodeURIComponent(fromLang) + '&to_lang=' + encodeURIComponent(toLang))
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Перевод выполнен успешно', 'success');
                    // Переключаемся на переведенный язык
                    switchLanguage(toLang);
                } else {
                    showNotification(data.error || 'Ошибка при переводе', 'error');
                }
            })
            .catch(error => {
                console.error('Ошибка перевода:', error);
                showNotification('Ошибка при переводе', 'error');
            })
            .finally(() => {
                // Восстанавливаем кнопку
                button.innerHTML = originalText;
                button.disabled = false;
            });
    };
});
</script>

<script>
// Управление выбором страниц для анализа
window.updateSelectedCount = function() {
    const checkboxes = document.querySelectorAll('.page-checkbox:checked');
    const countElement = document.getElementById('selected-count');
    if (countElement) {
        countElement.textContent = checkboxes.length;
    }
};

window.selectAllPages = function() {
    const checkboxes = document.querySelectorAll('.page-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
};

window.selectRussianPages = function() {
    const checkboxes = document.querySelectorAll('.page-checkbox:not(.german-page)');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
};

window.selectGermanPages = function() {
    const checkboxes = document.querySelectorAll('.page-checkbox.german-page');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
    updateSelectedCount();
};

window.clearAllPages = function() {
    const checkboxes = document.querySelectorAll('.page-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedCount();
};

// Добавляем обработчики событий для чекбоксов
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.page-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Обновляем счетчик при загрузке страницы
    updateSelectedCount();
});
</script>

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
 * Анализ выбранных страниц
 */
function analyze_selected_pages($selected_pages = []) {
    require_once __DIR__ . '/../seo/advanced_seo_analyzer.php';
    
    $results = [];
    $site_url = get_site_url();
    
    // Если страницы не выбраны, анализируем все доступные
    if (empty($selected_pages)) {
        $selected_pages = [
            'index.php', 'services.php', 'portfolio.php', 'about.php', 
            'reviews.php', 'blog.php', 'contact.php',
            'de/index.php', 'de/services.php'
        ];
    }
    
    // Маппинг файлов к URL путям
    $page_mapping = [
        'index.php' => '/',
        'services.php' => '/services.php',
        'portfolio.php' => '/portfolio.php',
        'about.php' => '/about.php',
        'reviews.php' => '/reviews.php',
        'blog.php' => '/blog.php',
        'contact.php' => '/contact.php',
        'de/index.php' => '/de/',
        'de/services.php' => '/de/services.php',
        'de/portfolio.php' => '/de/portfolio.php',
        'de/about.php' => '/de/about.php',
        'de/reviews.php' => '/de/reviews.php',
        'de/blog.php' => '/de/blog.php',
        'de/contact.php' => '/de/contact.php'
    ];
    
    foreach ($selected_pages as $file) {
        if (isset($page_mapping[$file]) && file_exists(__DIR__ . '/../' . $file)) {
            $path = $page_mapping[$file];
            $full_url = $site_url . $path;
            $results[$file] = analyze_page_seo($full_url);
        }
    }
    
    return $results;
}

/**
 * Анализ всех страниц (для обратной совместимости)
 */
function analyze_all_pages() {
    return analyze_selected_pages();
}

/**
 * Генерация sitemap
 */
function generate_sitemap() {
    // Используем простой генератор sitemap
    require_once __DIR__ . '/../seo/simple_sitemap_generator.php';
    $sitemap_content = generate_simple_sitemap();
    
    // Путь к файлу sitemap
    $sitemap_path = __DIR__ . '/../sitemap.xml';
    
    // Сохраняем в файл
    if (file_put_contents($sitemap_path, $sitemap_content)) {
        return $sitemap_path;
    }
    
    return false;
}

/**
 * Генерация robots.txt
 */
function generate_robots() {
    // Используем генератор robots.txt
    require_once __DIR__ . '/../seo/generate_robots.php';
    $robots_content = generate_robots_content();
    
    // Путь к файлу robots.txt
    $robots_path = __DIR__ . '/../robots.txt';
    
    // Сохраняем в файл
    if (file_put_contents($robots_path, $robots_content)) {
        return $robots_path;
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

