<?php
/**
 * Страница активности пользователей
 * Baumaster Admin Panel - User Activity Log
 */

require_once __DIR__ . '/../config.php';
require_once COMPONENTS_PATH . 'admin_layout.php';

// Проверка прав доступа (только для админов)
$current_user = get_current_admin_user();
if ($current_user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Настройки страницы
$page_title = __('users.activity_title', 'Активность пользователей');
$page_description = __('users.activity_description', 'Журнал действий пользователей системы');
$active_menu = 'users';

// Инициализация переменных
$activities = [];
$filters = [];

// Получение базы данных
$db = get_database();

// Фильтрация
$user_filter = $_GET['user_id'] ?? '';
$action_filter = $_GET['action'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

if (!empty($user_filter)) {
    $filters['user_id'] = intval($user_filter);
}
if (!empty($action_filter)) {
    $filters['action'] = $action_filter;
}
if (!empty($date_from)) {
    $filters['created_at >='] = $date_from . ' 00:00:00';
}
if (!empty($date_to)) {
    $filters['created_at <='] = $date_to . ' 23:59:59';
}

// Получение активности
$activities = $db->select('user_activity', $filters, ['order' => 'created_at DESC', 'limit' => 100]);

// Получение списка пользователей для фильтра
$users = $db->select('users', [], ['order' => 'username ASC']);

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Заголовок -->
<div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">
            <?php echo __('users.activity_title', 'Активность пользователей'); ?>
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            <?php echo __('users.activity_count', 'Записей'); ?>: <?php echo count($activities); ?>
        </p>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-2">
        <?php render_button([
            'href' => 'users.php',
            'text' => __('users.back_to_users', 'Назад к пользователям'),
            'variant' => 'secondary',
            'icon' => get_icon('arrow-left', 'w-4 h-4 mr-2')
        ]); ?>
    </div>
</div>

<!-- Фильтры -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
        <div>
            <?php 
            $user_options = [['value' => '', 'text' => __('common.all', 'Все')]];
            foreach ($users as $user) {
                $user_options[] = [
                    'value' => $user['id'],
                    'text' => htmlspecialchars($user['username']) . ' (' . htmlspecialchars($user['email']) . ')'
                ];
            }
            render_dropdown_field([
                'name' => 'user_id',
                'label' => __('users.filter_user', 'Пользователь'),
                'value' => $user_filter,
                'options' => $user_options,
                'placeholder' => __('common.all', 'Все'),
                'searchable' => true
            ]); 
            ?>
        </div>
        
        <div>
            <?php render_dropdown_field([
                'name' => 'action',
                'label' => __('users.filter_action', 'Действие'),
                'value' => $action_filter,
                'options' => [
                    ['value' => '', 'text' => __('common.all', 'Все')],
                    ['value' => 'login', 'text' => __('users.action_login', 'Вход в систему')],
                    ['value' => 'logout', 'text' => __('users.action_logout', 'Выход из системы')],
                    ['value' => 'create', 'text' => __('users.action_create', 'Создание')],
                    ['value' => 'update', 'text' => __('users.action_update', 'Обновление')],
                    ['value' => 'delete', 'text' => __('users.action_delete', 'Удаление')]
                ],
                'placeholder' => __('common.all', 'Все')
            ]); ?>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <?php echo __('users.filter_date_from', 'Дата с'); ?>
            </label>
            <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                <?php echo __('users.filter_date_to', 'Дата по'); ?>
            </label>
            <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
        </div>
        
        <div class="flex items-end">
            <?php render_button([
                'type' => 'submit',
                'text' => __('common.filter', 'Фильтр'),
                'variant' => 'secondary',
                'size' => 'md'
            ]); ?>
        </div>
    </form>
</div>

<!-- Список активности -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <?php if (empty($activities)): ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900"><?php echo __('users.no_activity', 'Активность не найдена'); ?></h3>
            <p class="mt-1 text-sm text-gray-500"><?php echo __('users.no_activity_description', 'За выбранный период активности не зафиксировано'); ?></p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo __('users.activity_user', 'Пользователь'); ?>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo __('users.activity_action', 'Действие'); ?>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo __('users.activity_table', 'Таблица'); ?>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo __('users.activity_record', 'Запись'); ?>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo __('users.activity_time', 'Время'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($activities as $activity): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                <?php echo strtoupper(substr($activity['user_id'], 0, 1)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            ID: <?php echo $activity['user_id']; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php 
                                        switch($activity['action']) {
                                            case 'create': echo 'bg-green-100 text-green-800'; break;
                                            case 'update': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'delete': echo 'bg-red-100 text-red-800'; break;
                                            case 'login': echo 'bg-green-100 text-green-800'; break;
                                            case 'logout': echo 'bg-gray-100 text-gray-800'; break;
                                            default: echo 'bg-gray-100 text-gray-800';
                                        }
                                    ?>">
                                    <?php echo translate_activity_action($activity['action']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($activity['table_name'] ?? '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $activity['record_id'] ?? '-'; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo format_date($activity['created_at']); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
/**
 * Перевод действия активности
 */
function translate_activity_action($action) {
    switch ($action) {
        case 'login': return 'Вход в систему';
        case 'logout': return 'Выход из системы';
        case 'create': return 'Создание';
        case 'update': return 'Обновление';
        case 'delete': return 'Удаление';
        case 'export': return 'Экспорт';
        default: return ucfirst($action);
    }
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

