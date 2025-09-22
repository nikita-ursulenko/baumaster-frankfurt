<?php
/**
 * Обработчик загрузки файлов
 * Baumaster Admin Panel - File Upload Handler
 */

require_once __DIR__ . '/../config.php';

// Проверка авторизации
require_once ADMIN_PATH . 'auth.php';
require_auth();

// Настройки загрузки
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('UPLOAD_DIR', ASSETS_PATH . 'uploads/services/');

// Создание директории если не существует
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

/**
 * Обработка загрузки изображения
 */
function handle_image_upload($file, $prefix = '') {
    // Проверка ошибок загрузки
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => get_upload_error_message($file['error'])];
    }
    
    // Проверка размера файла
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Файл слишком большой. Максимальный размер: ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB'];
    }
    
    // Проверка типа файла
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension']);
    
    if (!in_array($extension, ALLOWED_TYPES)) {
        return ['success' => false, 'error' => 'Неподдерживаемый тип файла. Разрешены: ' . implode(', ', ALLOWED_TYPES)];
    }
    
    // Проверка MIME типа
    $mime_type = mime_content_type($file['tmp_name']);
    $allowed_mime = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    if (!in_array($mime_type, $allowed_mime)) {
        return ['success' => false, 'error' => 'Недопустимый MIME тип файла'];
    }
    
    // Генерация уникального имени файла
    $filename = $prefix . time() . '_' . uniqid() . '.' . $extension;
    $filepath = UPLOAD_DIR . $filename;
    
    // Перемещение загруженного файла
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Оптимизация изображения
        optimize_image($filepath, $extension);
        
        // Возвращение относительного пути для базы данных
        $relative_path = '/assets/uploads/services/' . $filename;
        
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $relative_path,
            'full_path' => $filepath
        ];
    } else {
        return ['success' => false, 'error' => 'Ошибка при сохранении файла'];
    }
}

/**
 * Оптимизация изображения
 */
function optimize_image($filepath, $extension) {
    $max_width = 1200;
    $max_height = 800;
    $quality = 85;
    
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($filepath);
            break;
        case 'png':
            $image = imagecreatefrompng($filepath);
            break;
        case 'gif':
            $image = imagecreatefromgif($filepath);
            break;
        case 'webp':
            $image = imagecreatefromwebp($filepath);
            break;
        default:
            return;
    }
    
    if (!$image) return;
    
    // Получение размеров изображения
    $width = imagesx($image);
    $height = imagesy($image);
    
    // Вычисление новых размеров с сохранением пропорций
    if ($width > $max_width || $height > $max_height) {
        $ratio = min($max_width / $width, $max_height / $height);
        $new_width = round($width * $ratio);
        $new_height = round($height * $ratio);
        
        // Создание нового изображения
        $new_image = imagecreatetruecolor($new_width, $new_height);
        
        // Сохранение прозрачности для PNG и GIF
        if ($extension === 'png' || $extension === 'gif') {
            imagealphablending($new_image, false);
            imagesavealpha($new_image, true);
            $transparent = imagecolorallocatealpha($new_image, 255, 255, 255, 127);
            imagefilledrectangle($new_image, 0, 0, $new_width, $new_height, $transparent);
        }
        
        // Изменение размера
        imagecopyresampled($new_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        
        // Сохранение оптимизированного изображения
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($new_image, $filepath, $quality);
                break;
            case 'png':
                imagepng($new_image, $filepath, round(9 * (100 - $quality) / 100));
                break;
            case 'gif':
                imagegif($new_image, $filepath);
                break;
            case 'webp':
                imagewebp($new_image, $filepath, $quality);
                break;
        }
        
        imagedestroy($new_image);
    }
    
    imagedestroy($image);
}

/**
 * Получение сообщения об ошибке загрузки
 */
function get_upload_error_message($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'Файл слишком большой';
        case UPLOAD_ERR_PARTIAL:
            return 'Файл был загружен частично';
        case UPLOAD_ERR_NO_FILE:
            return 'Файл не был загружен';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'Временная папка недоступна';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Ошибка записи файла на диск';
        case UPLOAD_ERR_EXTENSION:
            return 'Загрузка файла остановлена расширением';
        default:
            return 'Неизвестная ошибка загрузки';
    }
}

// Обработка AJAX запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Проверка CSRF токена
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        json_response(['success' => false, 'error' => 'Ошибка безопасности']);
    }
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'upload_service_image':
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $result = handle_image_upload($_FILES['image'], 'service_');
                json_response($result);
            } else {
                json_response(['success' => false, 'error' => 'Файл не выбран']);
            }
            break;
            
        case 'delete_image':
            $filepath = $_POST['filepath'] ?? '';
            if (!empty($filepath)) {
                $full_path = ASSETS_PATH . 'uploads/services/' . basename($filepath);
                if (file_exists($full_path) && unlink($full_path)) {
                    json_response(['success' => true, 'message' => 'Файл удален']);
                } else {
                    json_response(['success' => false, 'error' => 'Ошибка при удалении файла']);
                }
            } else {
                json_response(['success' => false, 'error' => 'Не указан путь к файлу']);
            }
            break;
            
        default:
            json_response(['success' => false, 'error' => 'Неизвестное действие']);
    }
} else {
    // Показать страницу загрузки (если нужно)
    http_response_code(404);
    echo 'Not Found';
}
?>

