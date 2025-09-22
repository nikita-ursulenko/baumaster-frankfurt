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

// Получение последней активности
function get_recent_activity() {
    $db = get_database();
    
    // Получаем последние 10 записей логов
    $logs = $db->select('activity_log', [], [
        'order' => 'created_at DESC',
        'limit' => 10
    ]);
    
    return $logs;
}

// Получение данных для графиков
function get_chart_data() {
    // Здесь будут реальные данные, пока используем моковые данные
    return [
        'monthly_stats' => [
            'labels' => ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн'],
            'services' => [12, 15, 18, 22, 19, 25],
            'portfolio' => [8, 10, 12, 15, 14, 18],
            'reviews' => [20, 25, 30, 28, 35, 40]
        ]
    ];
}

$stats = get_dashboard_stats();
$recent_activity = get_recent_activity();
$chart_data = get_chart_data();

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

<!-- Графики и активность -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- График статистики -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('dashboard.monthly_stats', 'Статистика по месяцам'); ?>
        </h3>
        <div class="h-64">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Последняя активность -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('dashboard.recent_activity', 'Последняя активность'); ?>
        </h3>
        <div class="flow-root">
            <ul class="-mb-8">
                <?php if (empty($recent_activity)): ?>
                    <li class="text-center py-8 text-gray-500">
                        <?php echo __('dashboard.no_activity', 'Нет записей активности'); ?>
                    </li>
                <?php else: ?>
                    <?php foreach ($recent_activity as $index => $activity): ?>
                        <li>
                            <div class="relative pb-8">
                                <?php if ($index !== count($recent_activity) - 1): ?>
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

<script>
// Инициализация графика
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const chartData = <?php echo json_encode($chart_data['monthly_stats']); ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [
                {
                    label: '<?php echo __('dashboard.services', 'Услуги'); ?>',
                    data: chartData.services,
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4
                },
                {
                    label: '<?php echo __('dashboard.projects', 'Проекты'); ?>',
                    data: chartData.portfolio,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                },
                {
                    label: '<?php echo __('dashboard.reviews', 'Отзывы'); ?>',
                    data: chartData.reviews,
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

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
