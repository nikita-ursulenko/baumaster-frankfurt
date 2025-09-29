<?php
/**
 * Страница создания бэкапов данных
 * Baumaster Admin Panel - Data Backup
 */

require_once __DIR__ . '/../config.php';
require_once COMPONENTS_PATH . 'admin_layout.php';

// Проверка прав доступа (только для админов)
$current_user = get_current_admin_user();
if ($current_user['role'] !== 'admin') {
    header('Location: index.php?error=access_denied');
    exit;
}

// Настройки страницы
$page_title = __('backup.title', 'Бэкап данных');
$page_description = __('backup.description', 'Создание резервных копий данных системы');
$active_menu = 'backup';

// Инициализация переменных
$error_message = '';
$success_message = '';
$db = get_database();

// Обработка POST запросов
if ($_POST && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'create_backup':
                $backup_file = create_database_backup();
                if ($backup_file) {
                    $success_message = __('backup.created_success', 'Бэкап успешно создан: ') . $backup_file;
                    log_user_activity('backup_create', 'backup', 0);
                } else {
                    $error_message = __('backup.create_error', 'Ошибка при создании бэкапа');
                }
                break;
                
            case 'download_backup':
                $backup_file = $_POST['backup_file'] ?? '';
                if ($backup_file && file_exists($backup_file)) {
                    download_backup_file($backup_file);
                } else {
                    $error_message = __('backup.file_not_found', 'Файл бэкапа не найден');
                }
                break;
                
            case 'delete_backup':
                $backup_file = $_POST['backup_file'] ?? '';
                if ($backup_file && file_exists($backup_file)) {
                    if (unlink($backup_file)) {
                        $success_message = __('backup.deleted_success', 'Бэкап успешно удален');
                        log_user_activity('backup_delete', 'backup', 0);
                    } else {
                        $error_message = __('backup.delete_error', 'Ошибка при удалении бэкапа');
                    }
                } else {
                    $error_message = __('backup.file_not_found', 'Файл бэкапа не найден');
                }
                break;
        }
    } catch (Exception $e) {
        $error_message = __('backup.error', 'Ошибка: ') . $e->getMessage();
        write_log("Backup error: " . $e->getMessage(), 'ERROR');
    }
}

// Получение списка существующих бэкапов
$backup_dir = DATA_PATH . 'backups/';
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0755, true);
}

$backups = [];
if (is_dir($backup_dir)) {
    $files = scandir($backup_dir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'sql') {
            $file_path = $backup_dir . $file;
            $backups[] = [
                'file' => $file,
                'path' => $file_path,
                'size' => filesize($file_path),
                'date' => date('Y-m-d H:i:s', filemtime($file_path))
            ];
        }
    }
    
    // Сортировка по дате (новые сверху)
    usort($backups, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
}

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Сообщения -->
<?php render_error_message($error_message); ?>
<?php render_success_message($success_message); ?>

<!-- Заголовок -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">
            <?php echo __('backup.title', 'Бэкап данных'); ?>
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            <?php echo __('backup.description', 'Создание резервных копий данных системы'); ?>
        </p>
    </div>
    
    <div class="flex gap-2">
        <?php render_button([
            'href' => 'stats.php',
            'text' => __('common.back', 'Назад к статистике'),
            'variant' => 'secondary',
            'icon' => get_icon('arrow-left', 'w-4 h-4 mr-2')
        ]); ?>
    </div>
</div>

<!-- Создание нового бэкапа -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-medium text-gray-900 mb-4">
        <?php echo __('backup.create_new', 'Создать новый бэкап'); ?>
    </h3>
    
    <form method="POST" class="flex items-center gap-4">
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="action" value="create_backup">
        
        <div class="flex-1">
            <p class="text-sm text-gray-600">
                <?php echo __('backup.create_description', 'Создать полную резервную копию всех данных системы включая услуги, портфолио, отзывы, статьи блога и настройки.'); ?>
            </p>
        </div>
        
        <?php render_button([
            'type' => 'submit',
            'text' => __('backup.create_now', 'Создать бэкап'),
            'variant' => 'primary',
            'icon' => get_icon('download', 'w-4 h-4 mr-2')
        ]); ?>
    </form>
</div>

<!-- Список существующих бэкапов -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">
            <?php echo __('backup.existing_backups', 'Существующие бэкапы'); ?>
        </h3>
    </div>
    
    <?php if (empty($backups)): ?>
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900"><?php echo __('backup.no_backups', 'Бэкапы не найдены'); ?></h3>
            <p class="mt-1 text-sm text-gray-500"><?php echo __('backup.no_backups_description', 'Создайте первый бэкап данных системы'); ?></p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo __('backup.file_name', 'Имя файла'); ?>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo __('backup.file_size', 'Размер'); ?>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo __('backup.created_date', 'Дата создания'); ?>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <?php echo __('common.actions', 'Действия'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($backups as $backup): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($backup['file']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo format_file_size($backup['size']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo format_date($backup['date']); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="action" value="download_backup">
                                        <input type="hidden" name="backup_file" value="<?php echo htmlspecialchars($backup['path']); ?>">
                                        <button type="submit" class="text-primary-600 hover:text-primary-900">
                                            <?php echo __('backup.download', 'Скачать'); ?>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" class="inline" onsubmit="return confirm('<?php echo __('backup.delete_confirm', 'Вы уверены, что хотите удалить этот бэкап?'); ?>')">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="action" value="delete_backup">
                                        <input type="hidden" name="backup_file" value="<?php echo htmlspecialchars($backup['path']); ?>">
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <?php echo __('common.delete', 'Удалить'); ?>
                                        </button>
                                    </form>
                                </div>
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
 * Создание бэкапа базы данных
 */
function create_database_backup() {
    $backup_dir = DATA_PATH . 'backups/';
    if (!is_dir($backup_dir)) {
        mkdir($backup_dir, 0755, true);
    }
    
    $backup_file = $backup_dir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';
    
    // Получение всех таблиц
    $db = get_database();
    $tables = ['users', 'services', 'portfolio', 'reviews', 'blog_posts', 'settings', 'user_activity'];
    
    $sql_content = "-- Baumaster Database Backup\n";
    $sql_content .= "-- Created: " . date('Y-m-d H:i:s') . "\n";
    $sql_content .= "-- Version: 1.0\n\n";
    
    foreach ($tables as $table) {
        $data = $db->select($table);
        if (!empty($data)) {
            $sql_content .= "-- Table: {$table}\n";
            $sql_content .= "DELETE FROM {$table};\n";
            
            foreach ($data as $row) {
                $columns = array_keys($row);
                $values = array_map(function($value) {
                    return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                }, array_values($row));
                
                $sql_content .= "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql_content .= "\n";
        }
    }
    
    if (file_put_contents($backup_file, $sql_content)) {
        return $backup_file;
    }
    
    return false;
}

/**
 * Скачивание файла бэкапа
 */
function download_backup_file($file_path) {
    if (!file_exists($file_path)) {
        return false;
    }
    
    $filename = basename($file_path);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . filesize($file_path));
    header('Cache-Control: no-cache, must-revalidate');
    
    readfile($file_path);
    exit;
}

/**
 * Форматирование размера файла
 */
function format_file_size($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
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

