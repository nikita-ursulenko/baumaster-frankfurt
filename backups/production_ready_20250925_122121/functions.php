<?php
/**
 * Общие функции для проекта Baumaster
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * ФУНКЦИИ БЕЗОПАСНОСТИ
 */

/**
 * Санитизация данных
 */
function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Валидация email
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Генерация CSRF токена
 */
function generate_csrf_token() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Проверка CSRF токена
 */
function verify_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && 
           hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Хеширование пароля
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Проверка пароля
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * ФУНКЦИИ ЯЗЫКА И ЛОКАЛИЗАЦИИ
 */

/**
 * Получить текущий язык
 */
function get_current_language() {
    return $_SESSION['language'] ?? DEFAULT_LANG;
}

/**
 * Установить язык
 */
function set_language($lang) {
    if (in_array($lang, AVAILABLE_LANGS)) {
        $_SESSION['language'] = $lang;
        return true;
    }
    return false;
}

/**
 * Загрузить языковые данные
 */
function load_language($lang = null) {
    $lang = $lang ?? get_current_language();
    $file = LANG_PATH . $lang . '.json';
    
    if (file_exists($file)) {
        $content = file_get_contents($file);
        return json_decode($content, true);
    }
    
    return [];
}

/**
 * Получить переведенный текст
 */
function __($key, $fallback = null) {
    static $translations = null;
    
    if ($translations === null) {
        $translations = load_language();
    }
    
    $keys = explode('.', $key);
    $value = $translations;
    
    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $fallback ?? $key;
        }
    }
    
    return $value;
}

/**
 * ФУНКЦИИ РАБОТЫ С ФАЙЛАМИ
 */

/**
 * Безопасная загрузка файла
 */
function upload_file($file, $destination, $allowed_types = null) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Ошибка загрузки файла'];
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'error' => 'Файл не выбран'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'error' => 'Превышен максимальный размер файла'];
        default:
            return ['success' => false, 'error' => 'Неизвестная ошибка загрузки'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Файл слишком большой'];
    }
    
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension'] ?? '');
    
    if ($allowed_types && !in_array($extension, $allowed_types)) {
        return ['success' => false, 'error' => 'Недопустимый тип файла'];
    }
    
    $filename = uniqid() . '.' . $extension;
    $full_destination = ASSETS_PATH . '/uploads/' . $destination;
    
    // Создаем папку, если она не существует
    if (!is_dir($full_destination)) {
        mkdir($full_destination, 0777, true);
    }
    
    $filepath = $full_destination . '/' . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'error' => 'Не удалось сохранить файл'];
    }
    
    return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
}

/**
 * Удаление файла
 */
function delete_file($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return true;
}

/**
 * ФУНКЦИИ ФОРМАТИРОВАНИЯ
 */

/**
 * Форматирование даты
 */
function format_date($date, $format = 'd.m.Y H:i') {
    if ($date instanceof DateTime) {
        return $date->format($format);
    }
    
    // Проверяем, что дата не пустая
    if (empty($date) || $date === null) {
        return '';
    }
    
    $timestamp = is_numeric($date) ? $date : strtotime($date);
    
    // Проверяем, что strtotime вернул корректный timestamp
    if ($timestamp === false) {
        return '';
    }
    
    return date($format, $timestamp);
}

/**
 * Форматирование размера файла
 */
function format_filesize($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Обрезка текста
 */
function truncate_text($text, $length = 150, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * ФУНКЦИИ АВТОРИЗАЦИИ
 */

/**
 * Проверить, авторизован ли пользователь
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
}

/**
 * Получить данные текущего пользователя
 */
function get_current_admin_user() {
    if (!is_logged_in()) {
        return null;
    }
    
    // Здесь будет загрузка из БД
    return $_SESSION['user_data'] ?? null;
}

/**
 * Проверка роли пользователя
 */
function require_role($required_role) {
    $user = get_current_admin_user();
    
    if (!$user) {
        header('Location: login.php');
        exit;
    }
    
    $role_hierarchy = [
        'admin' => 3,
        'editor' => 2,
        'moderator' => 1
    ];
    
    $user_level = $role_hierarchy[$user['role']] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 0;
    
    if ($user_level < $required_level) {
        header('Location: index.php?error=access_denied');
        exit;
    }
    
    return true;
}

/**
 * Проверка прав доступа к конкретному действию
 */
function has_permission($action, $user = null) {
    if (!$user) {
        $user = get_current_admin_user();
    }
    
    if (!$user) {
        return false;
    }
    
    $permissions = [
        'admin' => [
            'users.create', 'users.edit', 'users.delete', 'users.view',
            'services.create', 'services.edit', 'services.delete', 'services.view',
            'portfolio.create', 'portfolio.edit', 'portfolio.delete', 'portfolio.view',
            'reviews.create', 'reviews.edit', 'reviews.delete', 'reviews.view', 'reviews.moderate',
            'blog.create', 'blog.edit', 'blog.delete', 'blog.view', 'blog.publish',
            'settings.edit', 'stats.view', 'export.data'
        ],
        'editor' => [
            'services.create', 'services.edit', 'services.view',
            'portfolio.create', 'portfolio.edit', 'portfolio.view',
            'reviews.view', 'reviews.moderate',
            'blog.create', 'blog.edit', 'blog.view', 'blog.publish'
        ],
        'moderator' => [
            'reviews.view', 'reviews.moderate',
            'blog.view'
        ]
    ];
    
    $user_permissions = $permissions[$user['role']] ?? [];
    return in_array($action, $user_permissions);
}

/**
 * Получение списка доступных ролей
 */
function get_available_roles() {
    return [
        'admin' => __('users.role_admin', 'Администратор'),
        'editor' => __('users.role_editor', 'Редактор'),
        'moderator' => __('users.role_moderator', 'Модератор')
    ];
}

/**
 * Получение списка статусов пользователей
 */
function get_user_statuses() {
    return [
        'active' => __('users.status_active', 'Активен'),
        'inactive' => __('users.status_inactive', 'Неактивен'),
        'banned' => __('users.status_banned', 'Заблокирован')
    ];
}

/**
 * Получение настройки по ключу
 */
function get_setting($key, $default = '') {
    $db = get_database();
    $setting = $db->select('settings', ['setting_key' => $key], ['limit' => 1]);
    
    if ($setting && !empty($setting)) {
        return $setting[0]['setting_value'];
    }
    
    return $default;
}

/**
 * Установка настройки
 */
function set_setting($key, $value, $category = 'general', $description = '') {
    $db = get_database();
    
    $existing = $db->select('settings', ['setting_key' => $key], ['limit' => 1]);
    
    if ($existing && !empty($existing)) {
        return $db->update('settings', 
            ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 
            ['setting_key' => $key]
        );
    } else {
        return $db->insert('settings', [
            'setting_key' => $key,
            'setting_value' => $value,
            'category' => $category,
            'description' => $description
        ]);
    }
}

/**
 * Получение всех настроек по категории
 */
function get_settings_by_category($category) {
    $db = get_database();
    $settings = $db->select('settings', ['category' => $category], ['order' => 'setting_key']);
    
    $result = [];
    foreach ($settings as $setting) {
        $result[$setting['setting_key']] = $setting['setting_value'];
    }
    
    return $result;
}

/**
 * Форматирование цены
 */
function format_price($price, $currency = '€') {
    if (empty($price) || $price == 0) {
        return 'По запросу';
    }
    
    return number_format($price, 0, ',', ' ') . ' ' . $currency;
}


/**
 * Получение активности пользователей за период
 */
function get_user_activity_stats($days = 30) {
    $db = get_database();
    $date_from = date('Y-m-d', strtotime("-{$days} days"));
    
    $activities = $db->select('user_activity', 
        ['created_at >=' => $date_from], 
        ['order' => 'created_at DESC']
    );
    
    $stats = [
        'total_activities' => count($activities),
        'unique_users' => count(array_unique(array_column($activities, 'user_id'))),
        'activities_by_day' => [],
        'top_actions' => []
    ];
    
    // Группировка по дням
    foreach ($activities as $activity) {
        $date = date('Y-m-d', strtotime($activity['created_at']));
        if (!isset($stats['activities_by_day'][$date])) {
            $stats['activities_by_day'][$date] = 0;
        }
        $stats['activities_by_day'][$date]++;
    }
    
    // Топ действий
    $actions = array_count_values(array_column($activities, 'action'));
    arsort($actions);
    $stats['top_actions'] = array_slice($actions, 0, 5, true);
    
    return $stats;
}

/**
 * Проверить роль пользователя
 */
function user_has_role($role) {
    $user = get_current_admin_user();
    return $user && ($user['role'] === $role || $user['role'] === 'admin');
}

/**
 * Логирование пользователя
 */
function login_user($user_data) {
    $_SESSION['user_id'] = $user_data['id'];
    $_SESSION['user_data'] = $user_data;
    $_SESSION['login_time'] = time();
}

/**
 * Разлогинивание пользователя
 */
function logout_user() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_data']);
    unset($_SESSION['login_time']);
    session_regenerate_id(true);
}

/**
 * ФУНКЦИИ ДЛЯ РАБОТЫ С ИЗОБРАЖЕНИЯМИ
 */

/**
 * Загрузка и обработка изображения
 */
function handle_image_upload($file, $destination_folder = 'services') {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Ошибка загрузки файла'];
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'error' => 'Файл не выбран'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'error' => 'Превышен максимальный размер файла'];
        default:
            return ['success' => false, 'error' => 'Неизвестная ошибка загрузки'];
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Файл слишком большой'];
    }
    
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension'] ?? '');
    
    if (!in_array($extension, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Недопустимый тип файла. Разрешены: ' . implode(', ', ALLOWED_IMAGE_TYPES)];
    }
    
    // Создание папки назначения
    $upload_path = UPLOADS_PATH . $destination_folder . '/';
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    
    // Генерация уникального имени файла
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $upload_path . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'error' => 'Не удалось сохранить файл'];
    }
    
    // Создание миниатюры
    $thumbs_dir = $upload_path . 'thumbs/';
    if (!is_dir($thumbs_dir)) {
        mkdir($thumbs_dir, 0755, true);
    }
    create_thumbnail($filepath, $thumbs_dir . $filename, 300, 300);
    
    return [
        'success' => true, 
        'filename' => $filename, 
        'filepath' => $filepath,
        'url' => '/assets/uploads/' . $destination_folder . '/' . $filename
    ];
}

/**
 * Создание миниатюры изображения
 */
function create_thumbnail($source_path, $dest_path, $max_width = 300, $max_height = 300) {
    if (!file_exists($source_path)) {
        return false;
    }
    
    // Создание папки для миниатюр
    $thumb_dir = dirname($dest_path);
    if (!is_dir($thumb_dir)) {
        mkdir($thumb_dir, 0755, true);
    }
    
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    $source_width = $image_info[0];
    $source_height = $image_info[1];
    $mime_type = $image_info['mime'];
    
    // Вычисление размеров миниатюры
    $ratio = min($max_width / $source_width, $max_height / $source_height);
    $thumb_width = intval($source_width * $ratio);
    $thumb_height = intval($source_height * $ratio);
    
    // Создание изображения в зависимости от типа
    switch ($mime_type) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;
        case 'image/webp':
            $source_image = imagecreatefromwebp($source_path);
            break;
        default:
            return false;
    }
    
    if (!$source_image) {
        return false;
    }
    
    // Создание миниатюры
    $thumb_image = imagecreatetruecolor($thumb_width, $thumb_height);
    
    // Сохранение прозрачности для PNG и GIF
    if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
        imagealphablending($thumb_image, false);
        imagesavealpha($thumb_image, true);
        $transparent = imagecolorallocatealpha($thumb_image, 255, 255, 255, 127);
        imagefilledrectangle($thumb_image, 0, 0, $thumb_width, $thumb_height, $transparent);
    }
    
    imagecopyresampled($thumb_image, $source_image, 0, 0, 0, 0, $thumb_width, $thumb_height, $source_width, $source_height);
    
    // Сохранение миниатюры
    $result = false;
    switch ($mime_type) {
        case 'image/jpeg':
            $result = imagejpeg($thumb_image, $dest_path, 90);
            break;
        case 'image/png':
            $result = imagepng($thumb_image, $dest_path, 9);
            break;
        case 'image/gif':
            $result = imagegif($thumb_image, $dest_path);
            break;
        case 'image/webp':
            $result = imagewebp($thumb_image, $dest_path, 90);
            break;
    }
    
    // Освобождение памяти
    imagedestroy($source_image);
    imagedestroy($thumb_image);
    
    return $result;
}

/**
 * Удаление изображения и его миниатюры
 */
function delete_image($image_path) {
    if (empty($image_path)) {
        return true;
    }
    
    $full_path = ABSPATH . ltrim($image_path, '/');
    $thumb_path = str_replace('/uploads/', '/uploads/thumbs/', $full_path);
    
    $result = true;
    
    if (file_exists($full_path)) {
        $result = unlink($full_path) && $result;
    }
    
    if (file_exists($thumb_path)) {
        $result = unlink($thumb_path) && $result;
    }
    
    return $result;
}

/**
 * Обработка множественной загрузки изображений
 */
function handle_multiple_image_upload($files, $destination_folder = 'services') {
    $results = [];
    $errors = [];
    
    if (!is_array($files['name'])) {
        return handle_image_upload($files, $destination_folder);
    }
    
    $file_count = count($files['name']);
    
    for ($i = 0; $i < $file_count; $i++) {
        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];
        
        $result = handle_image_upload($file, $destination_folder);
        
        if ($result['success']) {
            $results[] = $result;
        } else {
            $errors[] = $result['error'];
        }
    }
    
    return [
        'success' => !empty($results),
        'results' => $results,
        'errors' => $errors
    ];
}

/**
 * ФУНКЦИИ ЛОГИРОВАНИЯ АКТИВНОСТИ
 */

/**
 * Логирование активности пользователя
 */
function log_user_activity($action, $table_name = '', $record_id = 0, $old_values = null, $new_values = null) {
    if (!is_logged_in()) {
        return false;
    }
    
    $user = get_current_admin_user();
    if (!$user) {
        return false;
    }
    
    $db = get_database();
    
    $activity_data = [
        'user_id' => $user['id'],
        'action' => $action,
        'table_name' => $table_name,
        'record_id' => $record_id,
        'old_values' => $old_values ? json_encode($old_values) : null,
        'new_values' => $new_values ? json_encode($new_values) : null,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    return $db->insert('activity_log', $activity_data);
}

/**
 * ФУНКЦИИ ОТЛАДКИ И ЛОГИРОВАНИЯ
 */

/**
 * Записать в лог
 */
function write_log($message, $level = 'INFO') {
    if (!LOG_ERRORS) {
        return;
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[$timestamp] [$level] $message" . PHP_EOL;
    
    file_put_contents(ERROR_LOG_PATH, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Дамп переменной для отладки
 */
function debug_dump($var, $die = false) {
    if (!is_debug()) {
        return;
    }
    
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    
    if ($die) {
        die();
    }
}

/**
 * ФУНКЦИИ JSON
 */

/**
 * Безопасный JSON ответ
 */
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

/**
 * Чтение JSON файла
 */
function read_json_file($filepath) {
    if (!file_exists($filepath)) {
        return [];
    }
    
    $content = file_get_contents($filepath);
    $data = json_decode($content, true);
    
    return $data ?? [];
}

/**
 * Запись JSON файла
 */
function write_json_file($filepath, $data) {
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    $dir = dirname($filepath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    return file_put_contents($filepath, $json, LOCK_EX) !== false;
}

/**
 * ФУНКЦИИ ПАГИНАЦИИ
 */

/**
 * Создать пагинацию
 */
function create_pagination($total_items, $current_page = 1, $per_page = null) {
    $per_page = $per_page ?? ITEMS_PER_PAGE;
    $total_pages = ceil($total_items / $per_page);
    $current_page = max(1, min($current_page, $total_pages));
    $offset = ($current_page - 1) * $per_page;
    
    return [
        'total_items' => $total_items,
        'total_pages' => $total_pages,
        'current_page' => $current_page,
        'per_page' => $per_page,
        'offset' => $offset,
        'has_prev' => $current_page > 1,
        'has_next' => $current_page < $total_pages,
        'prev_page' => $current_page - 1,
        'next_page' => $current_page + 1
    ];
}

/**
 * Рендеринг поля формы
 */
function render_form_field($config) {
    $type = $config['type'] ?? 'text';
    $name = $config['name'] ?? '';
    $label = $config['label'] ?? '';
    $value = $config['value'] ?? '';
    $placeholder = $config['placeholder'] ?? '';
    $required = $config['required'] ?? false;
    $rows = $config['rows'] ?? 3;
    
    echo '<div class="space-y-1">';
    echo '<label for="' . htmlspecialchars($name) . '" class="block text-sm font-medium text-gray-700">';
    echo htmlspecialchars($label);
    if ($required) {
        echo ' <span class="text-red-500">*</span>';
    }
    echo '</label>';
    
    switch ($type) {
        case 'textarea':
            echo '<textarea id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '"';
            echo ' rows="' . $rows . '"';
            if ($placeholder) echo ' placeholder="' . htmlspecialchars($placeholder) . '"';
            if ($required) echo ' required';
            echo ' class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">';
            echo htmlspecialchars($value);
            echo '</textarea>';
            break;
        case 'email':
        case 'url':
        case 'text':
        default:
            echo '<input type="' . htmlspecialchars($type) . '" id="' . htmlspecialchars($name) . '" name="' . htmlspecialchars($name) . '"';
            echo ' value="' . htmlspecialchars($value) . '"';
            if ($placeholder) echo ' placeholder="' . htmlspecialchars($placeholder) . '"';
            if ($required) echo ' required';
            echo ' class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">';
            break;
    }
    
    echo '</div>';
}

