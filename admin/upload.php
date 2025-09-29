<?php
/**
 * Обработчик загрузки файлов для админ-панели
 * Baumaster Admin Upload Handler
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Проверка авторизации
if (!is_logged_in()) {
    json_response(['success' => false, 'error' => 'Не авторизован'], 401);
    exit;
}

// Проверка CSRF токена
if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    json_response(['success' => false, 'error' => 'Ошибка безопасности'], 403);
    exit;
}

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Недопустимый метод запроса'], 405);
    exit;
}

// Проверка действия
$action = $_POST['action'] ?? '';
if ($action !== 'upload_image') {
    json_response(['success' => false, 'error' => 'Недопустимое действие'], 400);
    exit;
}

// Проверка наличия файла
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    json_response(['success' => false, 'error' => 'Файл не был загружен'], 400);
    exit;
}

$file = $_FILES['image'];

// Проверка типа файла
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($file['type'], $allowed_types)) {
    json_response(['success' => false, 'error' => 'Недопустимый тип файла. Разрешены: JPEG, PNG, GIF, WebP'], 400);
    exit;
}

// Проверка размера файла (максимум 5MB)
$max_size = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $max_size) {
    json_response(['success' => false, 'error' => 'Файл слишком большой. Максимальный размер: 5MB'], 400);
    exit;
}

// Создание уникального имени файла
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = uniqid() . '_' . time() . '.' . $extension;

// Путь для сохранения
$upload_dir = UPLOADS_PATH . 'blog/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$file_path = $upload_dir . $filename;

// Перемещение файла
if (move_uploaded_file($file['tmp_name'], $file_path)) {
    // Создание миниатюры
    $thumbnail_path = $upload_dir . 'thumbs/' . $filename;
    if (!is_dir($upload_dir . 'thumbs/')) {
        mkdir($upload_dir . 'thumbs/', 0755, true);
    }
    
    create_thumbnail($file_path, $thumbnail_path, 300, 200);
    
    // URL файла
    $file_url = UPLOADS_URL . 'blog/' . $filename;
    
    // Логирование
    write_log("Image uploaded: {$filename} by user " . get_current_admin_user()['username'], 'INFO');
    log_user_activity('image_upload', 'uploads', 0, [], ['filename' => $filename, 'url' => $file_url]);
    
    json_response([
        'success' => true,
        'url' => $file_url,
        'filename' => $filename,
        'size' => $file['size'],
        'type' => $file['type']
    ]);
} else {
    json_response(['success' => false, 'error' => 'Ошибка при сохранении файла'], 500);
}

/**
 * Создание миниатюры изображения
 */
function create_thumbnail($source_path, $thumbnail_path, $max_width, $max_height) {
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
    $thumbnail = imagecreatetruecolor($thumb_width, $thumb_height);
    
    // Сохранение прозрачности для PNG и GIF
    if ($mime_type === 'image/png' || $mime_type === 'image/gif') {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $thumb_width, $thumb_height, $transparent);
    }
    
    // Изменение размера
    imagecopyresampled($thumbnail, $source_image, 0, 0, 0, 0, $thumb_width, $thumb_height, $source_width, $source_height);
    
    // Сохранение миниатюры
    $result = false;
    switch ($mime_type) {
        case 'image/jpeg':
            $result = imagejpeg($thumbnail, $thumbnail_path, 85);
            break;
        case 'image/png':
            $result = imagepng($thumbnail, $thumbnail_path, 8);
            break;
        case 'image/gif':
            $result = imagegif($thumbnail, $thumbnail_path);
            break;
        case 'image/webp':
            $result = imagewebp($thumbnail, $thumbnail_path, 85);
            break;
    }
    
    // Освобождение памяти
    imagedestroy($source_image);
    imagedestroy($thumbnail);
    
    return $result;
}
?>