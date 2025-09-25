<?php
/**
 * Экспорт пользователей в CSV
 * Baumaster Admin Panel - Users CSV Export
 */

require_once __DIR__ . '/../config.php';
require_once ADMIN_PATH . 'auth.php';

// Проверка авторизации и прав доступа (только админы)
$current_user = get_current_admin_user();
if ($current_user['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Получение данных
$db = get_database();
$users = $db->select('users', [], ['order' => 'created_at DESC']);

// Настройка заголовков для скачивания файла
$filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');

// Открытие потока вывода
$output = fopen('php://output', 'w');

// BOM для корректного отображения UTF-8 в Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Заголовки столбцов
$headers = [
    'ID',
    'Логин',
    'Email',
    'Роль',
    'Статус',
    'Дата создания',
    'Дата обновления'
];

fputcsv($output, $headers, ';');

// Экспорт данных
foreach ($users as $user) {
    $row = [
        $user['id'],
        $user['username'],
        $user['email'],
        translate_user_role($user['role']),
        translate_user_status($user['status']),
        format_date($user['created_at']),
        format_date($user['updated_at'])
    ];
    
    fputcsv($output, $row, ';');
}

// Закрытие потока
fclose($output);

// Логирование экспорта
write_log("Users exported to CSV by user: " . $current_user['username'], 'INFO');
log_user_activity('users_export', 'users', 0);

/**
 * Перевод роли пользователя
 */
function translate_user_role($role) {
    switch ($role) {
        case 'admin': return 'Администратор';
        case 'editor': return 'Редактор';
        case 'moderator': return 'Модератор';
        default: return ucfirst($role);
    }
}

/**
 * Перевод статуса пользователя
 */
function translate_user_status($status) {
    switch ($status) {
        case 'active': return 'Активен';
        case 'inactive': return 'Неактивен';
        case 'banned': return 'Заблокирован';
        default: return ucfirst($status);
    }
}

exit;
?>

