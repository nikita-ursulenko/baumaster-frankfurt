<?php
/**
 * Страница статистики и аналитики
 * Baumaster Admin Panel
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';

// Проверка прав доступа
$current_user = get_current_admin_user();
if (!has_permission('stats.view', $current_user)) {
    header('Location: index.php?error=access_denied');
    exit;
}

// Настройки страницы
$page_title = __('stats.title', 'Статистика и аналитика');
$page_description = __('stats.description', 'Анализ данных и статистика работы сайта');
$active_menu = 'stats';

// Инициализация переменных
$db = get_database();
$period = $_GET['period'] ?? '30'; // дней
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime("-{$period} days"));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Получение статистики
$stats = get_statistics_data($date_from, $date_to);

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Заголовок и фильтры -->
<div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">
            <?php echo __('stats.title', 'Статистика и аналитика'); ?>
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            <?php echo __('stats.period', 'Период'); ?>: <?php echo format_date($date_from); ?> - <?php echo format_date($date_to); ?>
        </p>
    </div>
    
    <!-- Фильтры периода -->
    <div class="flex flex-col sm:flex-row gap-2">
        <form method="GET" class="flex gap-2">
            <?php render_dropdown_field([
                'name' => 'period',
                'value' => $period,
                'options' => [
                    ['value' => '7', 'text' => __('stats.last_7_days', 'Последние 7 дней')],
                    ['value' => '30', 'text' => __('stats.last_30_days', 'Последние 30 дней')],
                    ['value' => '90', 'text' => __('stats.last_90_days', 'Последние 90 дней')],
                    ['value' => '365', 'text' => __('stats.last_year', 'Последний год')]
                ],
                'placeholder' => 'Выберите период',
                'onchange' => 'this.form.submit()',
                'class' => 'w-auto'
            ]); ?>
        </form>
        
        <form method="GET" class="flex gap-2">
            <input type="date" name="date_from" value="<?php echo $date_from; ?>" 
                   class="px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
            <input type="date" name="date_to" value="<?php echo $date_to; ?>" 
                   class="px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                <?php echo __('common.filter', 'Фильтр'); ?>
            </button>
        </form>
        
        <?php render_button([
            'href' => 'stats_export.php?period=' . $period . '&date_from=' . $date_from . '&date_to=' . $date_to,
            'text' => __('stats.export', 'Экспорт'),
            'variant' => 'secondary',
            'icon' => get_icon('download', 'w-4 h-4 mr-2')
        ]); ?>
    </div>
</div>

<!-- Основная статистика -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php render_stat_card([
        'title' => __('stats.total_services', 'Всего услуг'),
        'value' => $stats['services']['total'],
        'change' => $stats['services']['change'],
        'icon' => 'services',
        'color' => 'blue'
    ]); ?>
    
    <?php render_stat_card([
        'title' => __('stats.total_portfolio', 'Проектов в портфолио'),
        'value' => $stats['portfolio']['total'],
        'change' => $stats['portfolio']['change'],
        'icon' => 'portfolio',
        'color' => 'green'
    ]); ?>
    
    <?php render_stat_card([
        'title' => __('stats.total_reviews', 'Отзывов клиентов'),
        'value' => $stats['reviews']['total'],
        'change' => $stats['reviews']['change'],
        'icon' => 'reviews',
        'color' => 'yellow'
    ]); ?>
    
    <?php render_stat_card([
        'title' => __('stats.total_blog', 'Статей в блоге'),
        'value' => $stats['blog']['total'],
        'change' => $stats['blog']['change'],
        'icon' => 'blog',
        'color' => 'purple'
    ]); ?>
</div>

<!-- Графики и детальная статистика -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- График активности по дням -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('stats.activity_chart', 'Активность по дням'); ?>
        </h3>
        <div class="h-64 flex items-center justify-center text-gray-500">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <p><?php echo __('stats.chart_placeholder', 'График активности (Chart.js интеграция)'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Топ контента -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('stats.top_content', 'Популярный контент'); ?>
        </h3>
        <div class="space-y-3">
            <?php foreach ($stats['top_content'] as $item): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['title']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo $item['type']; ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-primary-600"><?php echo $item['views']; ?></p>
                        <p class="text-xs text-gray-500"><?php echo __('stats.views', 'просмотров'); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Детальная статистика по разделам -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Статистика услуг -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('stats.services_stats', 'Статистика услуг'); ?>
        </h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600"><?php echo __('stats.active_services', 'Активных услуг'); ?></span>
                <span class="text-sm font-semibold"><?php echo $stats['services']['active']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600"><?php echo __('stats.featured_services', 'Рекомендуемых'); ?></span>
                <span class="text-sm font-semibold"><?php echo $stats['services']['featured']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600"><?php echo __('stats.avg_price', 'Средняя цена'); ?></span>
                <span class="text-sm font-semibold"><?php echo format_price($stats['services']['avg_price']); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Статистика портфолио -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('stats.portfolio_stats', 'Статистика портфолио'); ?>
        </h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600"><?php echo __('stats.completed_projects', 'Завершенных проектов'); ?></span>
                <span class="text-sm font-semibold"><?php echo $stats['portfolio']['completed']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600"><?php echo __('stats.featured_projects', 'Рекомендуемых'); ?></span>
                <span class="text-sm font-semibold"><?php echo $stats['portfolio']['featured']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600"><?php echo __('stats.avg_budget', 'Средний бюджет'); ?></span>
                <span class="text-sm font-semibold"><?php echo format_price($stats['portfolio']['avg_budget']); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Статистика отзывов -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('stats.reviews_stats', 'Статистика отзывов'); ?>
        </h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600"><?php echo __('stats.avg_rating', 'Средний рейтинг'); ?></span>
                <span class="text-sm font-semibold"><?php echo number_format($stats['reviews']['avg_rating'], 1); ?>/5</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600"><?php echo __('stats.verified_reviews', 'Проверенных отзывов'); ?></span>
                <span class="text-sm font-semibold"><?php echo $stats['reviews']['verified']; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600"><?php echo __('stats.pending_reviews', 'На модерации'); ?></span>
                <span class="text-sm font-semibold"><?php echo $stats['reviews']['pending']; ?></span>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Получение данных статистики
 */
function get_statistics_data($date_from, $date_to) {
    $db = get_database();
    
    // Статистика услуг
    $services_total = count($db->select('services'));
    $services_active = count($db->select('services', ['status' => 'active']));
    $services_featured = count($db->select('services', ['featured' => 1]));
    $services_prices = $db->select('services', ['status' => 'active'], ['columns' => 'price']);
    $avg_price = 0;
    if (!empty($services_prices)) {
        $total_price = 0;
        foreach ($services_prices as $service) {
            $total_price += floatval($service['price']);
        }
        $avg_price = $total_price / count($services_prices);
    }
    
    // Статистика портфолио
    $portfolio_total = count($db->select('portfolio'));
    $portfolio_completed = count($db->select('portfolio', ['status' => 'completed']));
    $portfolio_featured = count($db->select('portfolio', ['featured' => 1]));
    $portfolio_budgets = $db->select('portfolio', ['status' => 'completed'], ['columns' => 'budget']);
    $avg_budget = 0;
    if (!empty($portfolio_budgets)) {
        $total_budget = 0;
        foreach ($portfolio_budgets as $project) {
            $total_budget += floatval($project['budget']);
        }
        $avg_budget = $total_budget / count($portfolio_budgets);
    }
    
    // Статистика отзывов
    $reviews_total = count($db->select('reviews'));
    $reviews_verified = count($db->select('reviews', ['verified' => 1]));
    $reviews_pending = count($db->select('reviews', ['status' => 'pending']));
    $reviews_ratings = $db->select('reviews', ['status' => 'approved'], ['columns' => 'rating']);
    $avg_rating = 0;
    if (!empty($reviews_ratings)) {
        $total_rating = 0;
        foreach ($reviews_ratings as $review) {
            $total_rating += intval($review['rating']);
        }
        $avg_rating = $total_rating / count($reviews_ratings);
    }
    
    // Статистика блога
    $blog_total = count($db->select('blog_posts'));
    $blog_published = count($db->select('blog_posts', ['status' => 'published']));
    $blog_featured = count($db->select('blog_posts', ['featured' => 1]));
    
    // Топ контента (заглушка)
    $top_content = [
        ['title' => 'Ремонт ванной комнаты', 'type' => 'Услуга', 'views' => 156],
        ['title' => 'Современная кухня', 'type' => 'Проект', 'views' => 134],
        ['title' => 'Как выбрать материалы', 'type' => 'Статья', 'views' => 98],
        ['title' => 'Отзыв клиента', 'type' => 'Отзыв', 'views' => 87]
    ];
    
    return [
        'services' => [
            'total' => $services_total,
            'active' => $services_active,
            'featured' => $services_featured,
            'avg_price' => $avg_price,
            'change' => '+12%'
        ],
        'portfolio' => [
            'total' => $portfolio_total,
            'completed' => $portfolio_completed,
            'featured' => $portfolio_featured,
            'avg_budget' => $avg_budget,
            'change' => '+8%'
        ],
        'reviews' => [
            'total' => $reviews_total,
            'verified' => $reviews_verified,
            'pending' => $reviews_pending,
            'avg_rating' => $avg_rating,
            'change' => '+15%'
        ],
        'blog' => [
            'total' => $blog_total,
            'published' => $blog_published,
            'featured' => $blog_featured,
            'change' => '+5%'
        ],
        'top_content' => $top_content
    ];
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

