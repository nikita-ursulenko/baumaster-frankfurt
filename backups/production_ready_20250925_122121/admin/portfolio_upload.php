<?php
/**
 * Обработчик загрузки изображений для портфолио
 * Baumaster Admin Panel - Portfolio Image Upload Handler
 */

require_once __DIR__ . '/../config.php';

// Проверка авторизации
require_once ADMIN_PATH . 'auth.php';
require_auth();

// Настройки загрузки
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB для портфолио изображений
define('ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('PORTFOLIO_UPLOAD_DIR', ASSETS_PATH . 'uploads/portfolio/');

// Создание директории если не существует
if (!is_dir(PORTFOLIO_UPLOAD_DIR)) {
    mkdir(PORTFOLIO_UPLOAD_DIR, 0755, true);
}

/**
 * Обработка загрузки изображения портфолио
 */
function handle_portfolio_image_upload($file, $prefix = 'portfolio_') {
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
    $filepath = PORTFOLIO_UPLOAD_DIR . $filename;
    
    // Перемещение загруженного файла
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Оптимизация изображения для портфолио
        optimize_portfolio_image($filepath, $extension);
        
        // Добавление водяного знака
        add_watermark($filepath, $extension);
        
        // Создание миниатюры
        create_thumbnail($filepath, $extension);
        
        // Возвращение относительного пути для базы данных
        $relative_path = '/assets/uploads/portfolio/' . $filename;
        
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $relative_path,
            'full_path' => $filepath,
            'thumbnail' => '/assets/uploads/portfolio/thumbs/thumb_' . $filename
        ];
    } else {
        return ['success' => false, 'error' => 'Ошибка при сохранении файла'];
    }
}

/**
 * Оптимизация изображения для портфолио
 */
function optimize_portfolio_image($filepath, $extension) {
    $max_width = 1920;
    $max_height = 1080;
    $quality = 90; // Высокое качество для портфолио
    
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
 * Создание миниатюры
 */
function create_thumbnail($filepath, $extension) {
    $thumb_width = 400;
    $thumb_height = 300;
    $quality = 85;
    
    // Создание директории для миниатюр
    $thumb_dir = PORTFOLIO_UPLOAD_DIR . 'thumbs/';
    if (!is_dir($thumb_dir)) {
        mkdir($thumb_dir, 0755, true);
    }
    
    $filename = basename($filepath);
    $thumb_path = $thumb_dir . 'thumb_' . $filename;
    
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
    
    $width = imagesx($image);
    $height = imagesy($image);
    
    // Вычисление размеров с обрезкой (crop)
    $ratio = max($thumb_width / $width, $thumb_height / $height);
    $new_width = $width * $ratio;
    $new_height = $height * $ratio;
    
    // Создание миниатюры
    $thumbnail = imagecreatetruecolor($thumb_width, $thumb_height);
    
    // Сохранение прозрачности
    if ($extension === 'png' || $extension === 'gif') {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $thumb_width, $thumb_height, $transparent);
    }
    
    // Центрирование и обрезка
    $src_x = ($new_width - $thumb_width) / 2 / $ratio;
    $src_y = ($new_height - $thumb_height) / 2 / $ratio;
    
    imagecopyresampled(
        $thumbnail, $image,
        0, 0, $src_x, $src_y,
        $thumb_width, $thumb_height,
        $thumb_width / $ratio, $thumb_height / $ratio
    );
    
    // Сохранение миниатюры
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($thumbnail, $thumb_path, $quality);
            break;
        case 'png':
            imagepng($thumbnail, $thumb_path);
            break;
        case 'gif':
            imagegif($thumbnail, $thumb_path);
            break;
        case 'webp':
            imagewebp($thumbnail, $thumb_path, $quality);
            break;
    }
    
    imagedestroy($image);
    imagedestroy($thumbnail);
}

/**
 * Добавление водяного знака на изображение
 */
function add_watermark($filepath, $extension) {
    // Загружаем основное изображение
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            $image = imagecreatefromjpeg($filepath);
            break;
        case 'png':
            $image = imagecreatefrompng($filepath);
            break;
        case 'gif':
            return; // Не добавляем водяные знаки на GIF
        case 'webp':
            $image = imagecreatefromwebp($filepath);
            break;
        default:
            return;
    }
    
    if (!$image) return;
    
    $image_width = imagesx($image);
    $image_height = imagesy($image);
    
    // Создаем водяной знак из текста
    $watermark_text = SITE_NAME;
    $font_size = max(12, min(24, $image_width / 30)); // Адаптивный размер шрифта
    
    // Цвет водяного знака (белый с прозрачностью)
    $white = imagecolorallocatealpha($image, 255, 255, 255, 50);
    $black = imagecolorallocatealpha($image, 0, 0, 0, 80);
    
    // Позиционирование водяного знака в правом нижнем углу
    $padding = 20;
    $text_box = imagettfbbox($font_size, 0, __DIR__ . '/../assets/fonts/roboto.ttf', $watermark_text);
    
    // Если шрифт не найден, используем встроенный шрифт
    if (!$text_box || !file_exists(__DIR__ . '/../assets/fonts/roboto.ttf')) {
        $text_width = strlen($watermark_text) * ($font_size * 0.6);
        $text_height = $font_size;
        
        $x = $image_width - $text_width - $padding;
        $y = $image_height - $padding;
        
        // Тень
        imagestring($image, 3, $x + 1, $y + 1, $watermark_text, $black);
        // Основной текст
        imagestring($image, 3, $x, $y, $watermark_text, $white);
    } else {
        $text_width = $text_box[4] - $text_box[0];
        $text_height = $text_box[1] - $text_box[5];
        
        $x = $image_width - $text_width - $padding;
        $y = $image_height - $padding;
        
        // Тень
        imagettftext($image, $font_size, 0, $x + 1, $y + 1, $black, __DIR__ . '/../assets/fonts/roboto.ttf', $watermark_text);
        // Основной текст
        imagettftext($image, $font_size, 0, $x, $y, $white, __DIR__ . '/../assets/fonts/roboto.ttf', $watermark_text);
    }
    
    // Сохраняем изображение с водяным знаком
    switch ($extension) {
        case 'jpg':
        case 'jpeg':
            imagejpeg($image, $filepath, 90);
            break;
        case 'png':
            imagepng($image, $filepath);
            break;
        case 'webp':
            imagewebp($image, $filepath, 90);
            break;
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
        case 'upload_portfolio_image':
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $result = handle_portfolio_image_upload($_FILES['image']);
                json_response($result);
            } else {
                json_response(['success' => false, 'error' => 'Файл не выбран']);
            }
            break;
            
        case 'upload_multiple_images':
            $results = [];
            if (isset($_FILES['images']) && is_array($_FILES['images']['name'])) {
                $file_count = count($_FILES['images']['name']);
                
                for ($i = 0; $i < $file_count; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    
                    $file = [
                        'name' => $_FILES['images']['name'][$i],
                        'type' => $_FILES['images']['type'][$i],
                        'tmp_name' => $_FILES['images']['tmp_name'][$i],
                        'error' => $_FILES['images']['error'][$i],
                        'size' => $_FILES['images']['size'][$i]
                    ];
                    
                    $result = handle_portfolio_image_upload($file, 'gallery_');
                    $results[] = $result;
                }
                
                json_response([
                    'success' => true,
                    'results' => $results,
                    'message' => 'Загружено ' . count(array_filter($results, fn($r) => $r['success'])) . ' из ' . count($results) . ' изображений'
                ]);
            } else {
                json_response(['success' => false, 'error' => 'Файлы не выбраны']);
            }
            break;
            
        case 'delete_image':
            $filepath = $_POST['filepath'] ?? '';
            if (!empty($filepath)) {
                $full_path = PORTFOLIO_UPLOAD_DIR . basename($filepath);
                $thumb_path = PORTFOLIO_UPLOAD_DIR . 'thumbs/thumb_' . basename($filepath);
                
                $deleted = false;
                if (file_exists($full_path) && unlink($full_path)) {
                    $deleted = true;
                }
                
                // Удаление миниатюры
                if (file_exists($thumb_path)) {
                    unlink($thumb_path);
                }
                
                if ($deleted) {
                    json_response(['success' => true, 'message' => 'Изображение удалено']);
                } else {
                    json_response(['success' => false, 'error' => 'Ошибка при удалении изображения']);
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
