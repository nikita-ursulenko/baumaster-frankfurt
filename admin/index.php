<?php
/**
 * Главная страница админ-панели (Dashboard)
 * Baumaster Admin Panel
 */

// Подключение базовых файлов
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';

// Настройки страницы
$page_title = __('dashboard.title', 'Панель управления');
$page_description = __('dashboard.description', 'Обзор системы и основная статистика');
$active_menu = 'dashboard';

// Обработка смены языка
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'change_language') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $new_lang = $_POST['language'] ?? '';
        if (set_language($new_lang)) {
            json_response(['success' => true]);
        }
    }
    json_response(['success' => false]);
}

// Получение статистических данных
function get_dashboard_stats() {
    $db = get_database();
    
    // Количество услуг
    $services_count = count($db->select('services', ['status' => 'active']));
    
    // Количество проектов портфолио
    $portfolio_count = count($db->select('portfolio', ['status' => 'active']));
    
    // Количество отзывов
    $reviews_count = count($db->select('reviews', ['status' => 'published']));
    
    // Количество статей блога
    $blog_count = count($db->select('blog_posts', ['status' => 'published']));
    
    // Количество пользователей
    $users_count = count($db->select('users', ['status' => 'active']));
    
    return [
        'services' => $services_count,
        'portfolio' => $portfolio_count,
        'reviews' => $reviews_count,
        'blog' => $blog_count,
        'users' => $users_count
    ];
}

// Получение последней активности с пагинацией
function get_recent_activity($page = 1, $per_page = 10) {
    $db = get_database();
    
    // Подсчитываем общее количество записей
    $pdo = $db->get_pdo();
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM activity_log");
    $stmt->execute();
    $result = $stmt->fetch();
    $total_count = $result ? $result['count'] : 0;
    
    // Вычисляем offset
    $offset = ($page - 1) * $per_page;
    
    // Получаем записи для текущей страницы
    $logs = $db->select('activity_log', [], [
        'order' => 'created_at DESC',
        'limit' => $per_page,
        'offset' => $offset
    ]);
    
    return [
        'data' => $logs,
        'total' => $total_count,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => ceil($total_count / $per_page)
    ];
}

$stats = get_dashboard_stats();
$activity_page = intval($_GET['activity_page'] ?? 1);
$recent_activity = get_recent_activity($activity_page, 10);

// Начало вывода контента
ob_start();
?>

<!-- Карточки со статистикой -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php 
    // Услуги
    render_stat_card([
        'title' => __('dashboard.total_services', 'Услуги'),
        'value' => $stats['services'],
        'icon' => get_icon('services', 'w-5 h-5 text-white'),
        'color' => 'blue',
        'link' => 'services.php'
    ]);
    
    // Портфолио
    render_stat_card([
        'title' => __('dashboard.total_projects', 'Проекты'),
        'value' => $stats['portfolio'],
        'icon' => get_icon('portfolio', 'w-5 h-5 text-white'),
        'color' => 'green',
        'link' => 'portfolio.php'
    ]);
    
    // Отзывы
    render_stat_card([
        'title' => __('dashboard.total_reviews', 'Отзывы'),
        'value' => $stats['reviews'],
        'icon' => get_icon('reviews', 'w-5 h-5 text-white'),
        'color' => 'yellow',
        'link' => 'reviews.php'
    ]);
    
    // Пользователи (только для админов)
    render_stat_card([
        'title' => __('dashboard.total_users', 'Пользователи'),
        'value' => $stats['users'],
        'icon' => get_icon('users', 'w-5 h-5 text-white'),
        'color' => 'purple',
        'link' => user_has_role('admin') ? 'users.php' : ''
    ]);
    ?>
</div>

<!-- Активность -->
<div class="grid grid-cols-1 gap-6 mb-8">
    <!-- Последняя активность -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('dashboard.recent_activity', 'Последняя активность'); ?>
        </h3>
        <div class="flow-root">
            <ul class="-mb-8">
                <?php if (empty($recent_activity['data'])): ?>
                    <li class="text-center py-8 text-gray-500">
                        <?php echo __('dashboard.no_activity', 'Нет записей активности'); ?>
                    </li>
                <?php else: ?>
                    <?php foreach ($recent_activity['data'] as $index => $activity): ?>
                        <li>
                            <div class="relative pb-8">
                                <?php if ($index !== count($recent_activity['data']) - 1): ?>
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                <?php endif; ?>
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-900">
                                                <?php echo htmlspecialchars($activity['action'] ?? 'Неизвестное действие'); ?>
                                            </p>
                                        </div>
                                        <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                            <?php echo format_date($activity['created_at'] ?? time()); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
        
        <!-- Пагинация для активности -->
        <?php if ($recent_activity['total_pages'] > 1): ?>
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Показано <?php echo count($recent_activity['data']); ?> из <?php echo $recent_activity['total']; ?> записей
                </div>
                <div class="flex space-x-2">
                    <?php if ($recent_activity['page'] > 1): ?>
                        <a href="?activity_page=<?php echo $recent_activity['page'] - 1; ?>" 
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Назад
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $recent_activity['total_pages']; $i++): ?>
                        <?php if ($i == $recent_activity['page']): ?>
                            <span class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                                <?php echo $i; ?>
                            </span>
                        <?php else: ?>
                            <a href="?activity_page=<?php echo $i; ?>" 
                               class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <?php echo $i; ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($recent_activity['page'] < $recent_activity['total_pages']): ?>
                        <a href="?activity_page=<?php echo $recent_activity['page'] + 1; ?>" 
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Вперед
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Быстрые действия -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        <?php echo __('dashboard.quick_actions', 'Быстрые действия'); ?>
    </h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php 
        // Быстрые кнопки действий
        $quick_actions = [
            [
                'href' => 'services.php?action=create',
                'text' => __('dashboard.add_service', 'Добавить услугу'),
                'icon' => get_icon('plus', 'w-4 h-4 mr-2')
            ],
            [
                'href' => 'portfolio.php?action=create',
                'text' => __('dashboard.add_project', 'Добавить проект'),
                'icon' => get_icon('plus', 'w-4 h-4 mr-2')
            ],
            [
                'href' => 'blog.php?action=create',
                'text' => __('dashboard.add_article', 'Добавить статью'),
                'icon' => get_icon('plus', 'w-4 h-4 mr-2')
            ],
            [
                'href' => 'settings.php',
                'text' => __('dashboard.settings', 'Настройки'),
                'icon' => get_icon('settings', 'w-4 h-4 mr-2')
            ]
        ];
        
        foreach ($quick_actions as $action) {
            render_button([
                'href' => $action['href'],
                'text' => $action['text'],
                'variant' => 'secondary',
                'size' => 'md',
                'icon' => $action['icon']
            ]);
        }
        ?>
    </div>
</div>


<?php
$page_content = ob_get_clean();

// Рендеринг страницы
render_admin_layout([
    'page_title' => $page_title,
    'page_description' => $page_description,
    'active_menu' => $active_menu,
    'content' => $page_content
]);
?>
