<?php
/**
 * Галерея изображений для админ-панели
 * Baumaster Admin Gallery
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';

// Проверка авторизации
if (!is_logged_in()) {
    json_response(['success' => false, 'error' => 'Не авторизован'], 401);
    exit;
}

// Обработка AJAX запросов
if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'get_images') {
    $images = get_gallery_images();
    json_response(['success' => true, 'images' => $images]);
    exit;
}

// Обработка POST запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        json_response(['success' => false, 'error' => 'Ошибка безопасности'], 403);
        exit;
    }
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'delete_image':
            $filename = sanitize_input($_POST['filename'] ?? '');
            if (delete_gallery_image($filename)) {
                json_response(['success' => true, 'message' => 'Изображение удалено']);
            } else {
                json_response(['success' => false, 'error' => 'Ошибка удаления изображения']);
            }
            break;
            
        default:
            json_response(['success' => false, 'error' => 'Недопустимое действие'], 400);
    }
    exit;
}

/**
 * Получить список изображений из галереи
 */
function get_gallery_images() {
    $images = [];
    $gallery_dir = UPLOADS_PATH . 'blog/';
    $thumbs_dir = $gallery_dir . 'thumbs/';
    
    if (!is_dir($gallery_dir)) {
        return $images;
    }
    
    $files = glob($gallery_dir . '*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
    
    foreach ($files as $file) {
        $filename = basename($file);
        $thumbnail = $thumbs_dir . $filename;
        
        // Пропускаем миниатюры
        if (strpos($filename, 'thumb_') === 0) {
            continue;
        }
        
        $images[] = [
            'filename' => $filename,
            'url' => UPLOADS_URL . 'blog/' . $filename,
            'thumbnail' => file_exists($thumbnail) ? UPLOADS_URL . 'blog/thumbs/' . $filename : null,
            'size' => filesize($file),
            'modified' => filemtime($file)
        ];
    }
    
    // Сортируем по дате изменения (новые сначала)
    usort($images, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
    
    return $images;
}

/**
 * Удалить изображение из галереи
 */
function delete_gallery_image($filename) {
    $filename = sanitize_input($filename);
    
    // Проверяем, что файл существует и находится в правильной директории
    $file_path = UPLOADS_PATH . 'blog/' . $filename;
    if (!file_exists($file_path) || strpos(realpath($file_path), realpath(UPLOADS_PATH . 'blog/')) !== 0) {
        return false;
    }
    
    // Удаляем основной файл
    if (unlink($file_path)) {
        // Удаляем миниатюру, если она существует
        $thumb_path = UPLOADS_PATH . 'blog/thumbs/' . $filename;
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }
        
        // Логируем удаление
        write_log("Gallery image deleted: {$filename} by user " . get_current_admin_user()['username'], 'WARNING');
        log_user_activity('gallery_delete', 'uploads', 0, [], ['filename' => $filename]);
        
        return true;
    }
    
    return false;
}
?>
