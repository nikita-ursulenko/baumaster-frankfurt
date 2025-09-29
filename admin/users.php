<?php
/**
 * Страница управления пользователями
 * Baumaster Admin Panel
 */

// Подключение базовых файлов
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';

// Проверка прав доступа (только для админов)
$current_user = get_current_admin_user();
if ($current_user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Настройки страницы
$page_title = __('users.title', 'Управление пользователями');
$page_description = __('users.description', 'Создание, редактирование и управление пользователями системы');
$active_menu = 'users';

// Инициализация переменных
$error_message = '';
$success_message = '';
$users = [];
$current_user_data = null;
$action = $_GET['action'] ?? 'list';
$user_id = intval($_GET['id'] ?? 0);

// Получение базы данных
$db = get_database();

// Обработка POST запросов
if ($_POST) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = __('common.csrf_error', 'Ошибка безопасности. Попробуйте снова.');
    } else {
        $post_action = $_POST['action'] ?? '';
        
        switch ($post_action) {
            case 'create':
                $result = create_user($_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                    log_user_activity('user_create', 'users', $result['user_id']);
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'update':
                $result = update_user($user_id, $_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                    log_user_activity('user_update', 'users', $user_id);
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'delete':
                $result = delete_user($user_id);
                if ($result['success']) {
                    $success_message = $result['message'];
                    log_user_activity('user_delete', 'users', $user_id);
                    $action = 'list'; // Возвращаемся к списку
                } else {
                    $error_message = $result['error'];
                }
                break;
                
            case 'change_language':
                if (set_language($_POST['language'] ?? '')) {
                    json_response(['success' => true]);
                }
                json_response(['success' => false]);
                break;
        }
    }
}

// Обработка действий
switch ($action) {
    case 'create':
    case 'edit':
        if ($action === 'edit') {
            $current_user_data = $db->select('users', ['id' => $user_id], ['limit' => 1]);
            if (!$current_user_data) {
                $error_message = __('users.not_found', 'Пользователь не найден');
                $action = 'list';
            }
        }
        break;
        
    case 'list':
    default:
        // Получение списка пользователей
        $users = $db->select('users', [], ['order' => 'created_at DESC']);
        break;
}

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Сообщения -->
<?php render_error_message($error_message); ?>
<?php render_success_message($success_message); ?>

<?php if ($action === 'list'): ?>
    <!-- Заголовок и кнопка создания -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">
                <?php echo __('users.list_title', 'Список пользователей'); ?>
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                <?php echo __('users.total_count', 'Всего пользователей'); ?>: <?php echo count($users); ?>
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <?php render_button([
                'href' => '?action=create',
                'text' => __('users.add_new', 'Добавить пользователя'),
                'variant' => 'primary',
                'icon' => get_icon('plus', 'w-4 h-4 mr-2')
            ]); ?>
            
            <?php render_button([
                'href' => 'users_export.php',
                'text' => __('users.export', 'Экспорт в CSV'),
                'variant' => 'secondary',
                'icon' => get_icon('download', 'w-4 h-4 mr-2')
            ]); ?>
            
            <?php render_button([
                'href' => 'user_activity.php',
                'text' => __('users.activity', 'Активность'),
                'variant' => 'secondary',
                'icon' => get_icon('clock', 'w-4 h-4 mr-2')
            ]); ?>
        </div>
    </div>

    <!-- Таблица пользователей -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?php echo __('users.username', 'Пользователь'); ?>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?php echo __('users.email', 'Email'); ?>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?php echo __('users.role', 'Роль'); ?>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?php echo __('users.status', 'Статус'); ?>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?php echo __('users.created', 'Создан'); ?>
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <?php echo __('common.actions', 'Действия'); ?>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <?php echo __('users.no_users', 'Пользователей не найдено'); ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-primary-500 flex items-center justify-center">
                                            <span class="text-sm font-medium text-white">
                                                <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($user['username']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php echo $user['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo htmlspecialchars(ucfirst($user['status'])); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo format_date($user['created_at']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                <?php render_button([
                                    'href' => '?action=edit&id=' . $user['id'],
                                    'text' => __('common.edit', 'Редактировать'),
                                    'variant' => 'secondary',
                                    'size' => 'sm'
                                ]); ?>
                                
                                <?php if ($user['id'] != get_current_admin_user()['id']): ?>
                                    <form method="POST" class="inline-block" onsubmit="return confirmDelete('<?php echo __('users.confirm_delete', 'Вы уверены, что хотите удалить этого пользователя?'); ?>');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <?php render_button([
                                            'type' => 'submit',
                                            'text' => __('common.delete', 'Удалить'),
                                            'variant' => 'danger',
                                            'size' => 'sm'
                                        ]); ?>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
    <!-- Форма создания/редактирования пользователя -->
    <div class="max-w-2xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                <?php echo $action === 'create' ? __('users.create_title', 'Создать пользователя') : __('users.edit_title', 'Редактировать пользователя'); ?>
            </h2>
            
            <?php render_button([
                'href' => '?action=list',
                'text' => __('common.back_to_list', 'Назад к списку'),
                'variant' => 'secondary',
                'icon' => get_icon('arrow-left', 'w-4 h-4 mr-2')
            ]); ?>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="<?php echo $action === 'create' ? 'create' : 'update'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Имя пользователя -->
                    <?php render_input_field([
                        'name' => 'username',
                        'label' => __('users.username', 'Имя пользователя'),
                        'placeholder' => __('users.username_placeholder', 'Введите имя пользователя'),
                        'required' => true,
                        'value' => $current_user_data['username'] ?? ''
                    ]); ?>
                    
                    <!-- Email -->
                    <?php render_input_field([
                        'type' => 'email',
                        'name' => 'email',
                        'label' => __('users.email', 'Email адрес'),
                        'placeholder' => __('users.email_placeholder', 'Введите email'),
                        'required' => true,
                        'value' => $current_user_data['email'] ?? ''
                    ]); ?>
                </div>
                
                <!-- Пароль -->
                <?php if ($action === 'create'): ?>
                    <?php render_password_field([
                        'name' => 'password',
                        'label' => __('users.password', 'Пароль'),
                        'placeholder' => __('users.password_placeholder', 'Введите пароль'),
                        'required' => true
                    ]); ?>
                <?php else: ?>
                    <?php render_password_field([
                        'name' => 'password',
                        'label' => __('users.new_password', 'Новый пароль'),
                        'placeholder' => __('users.new_password_placeholder', 'Оставьте пустым, если не хотите менять'),
                        'required' => false
                    ]); ?>
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Роль -->
                    <?php render_dropdown_field([
                        'name' => 'role',
                        'id' => 'role',
                        'label' => __('users.role', 'Роль'),
                        'required' => true,
                        'value' => $current_user_data['role'] ?? '',
                        'placeholder' => __('users.select_role', 'Выберите роль'),
                        'options' => [
                            ['value' => 'editor', 'text' => __('users.role_editor', 'Редактор')],
                            ['value' => 'moderator', 'text' => __('users.role_moderator', 'Модератор')],
                            ['value' => 'admin', 'text' => __('users.role_admin', 'Администратор')]
                        ]
                    ]); ?>
                    
                    <!-- Статус -->
                    <?php render_dropdown_field([
                        'name' => 'status',
                        'id' => 'status',
                        'label' => __('users.status', 'Статус'),
                        'required' => true,
                        'value' => $current_user_data['status'] ?? 'active',
                        'placeholder' => __('users.select_status', 'Выберите статус'),
                        'options' => [
                            ['value' => 'active', 'text' => __('users.status_active', 'Активен')],
                            ['value' => 'inactive', 'text' => __('users.status_inactive', 'Неактивен')],
                            ['value' => 'blocked', 'text' => __('users.status_blocked', 'Заблокирован')]
                        ]
                    ]); ?>
                </div>
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <?php render_button([
                        'href' => '?action=list',
                        'text' => __('common.cancel', 'Отмена'),
                        'variant' => 'secondary'
                    ]); ?>
                    
                    <?php render_button([
                        'type' => 'submit',
                        'text' => $action === 'create' ? __('users.create_button', 'Создать пользователя') : __('users.update_button', 'Обновить пользователя'),
                        'variant' => 'primary'
                    ]); ?>
                </div>
            </form>
        </div>
    </div>

<?php endif; ?>

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
