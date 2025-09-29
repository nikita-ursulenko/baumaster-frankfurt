<?php
/**
 * Image Optimizer
 * Baumaster SEO Tools - Image Optimization
 */

/**
 * Оптимизация изображения
 */
function optimize_image($source_path, $destination_path = null, $quality = 85, $max_width = 1920, $max_height = 1080) {
    if (!file_exists($source_path)) {
        return false;
    }
    
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    $source_width = $image_info[0];
    $source_height = $image_info[1];
    $mime_type = $image_info['mime'];
    
    // Создание изображения из файла
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
    
    // Вычисление новых размеров
    $ratio = min($max_width / $source_width, $max_height / $source_height);
    $new_width = intval($source_width * $ratio);
    $new_height = intval($source_height * $ratio);
    
    // Создание нового изображения
    $optimized_image = imagecreatetruecolor($new_width, $new_height);
    
    // Сохранение прозрачности для PNG
    if ($mime_type === 'image/png') {
        imagealphablending($optimized_image, false);
        imagesavealpha($optimized_image, true);
        $transparent = imagecolorallocatealpha($optimized_image, 255, 255, 255, 127);
        imagefilledrectangle($optimized_image, 0, 0, $new_width, $new_height, $transparent);
    }
    
    // Изменение размера
    imagecopyresampled($optimized_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $source_width, $source_height);
    
    // Определение пути назначения
    if (!$destination_path) {
        $path_info = pathinfo($source_path);
        $destination_path = $path_info['dirname'] . '/' . $path_info['filename'] . '_optimized.' . $path_info['extension'];
    }
    
    // Сохранение оптимизированного изображения
    $result = false;
    switch ($mime_type) {
        case 'image/jpeg':
            $result = imagejpeg($optimized_image, $destination_path, $quality);
            break;
        case 'image/png':
            $result = imagepng($optimized_image, $destination_path, 9);
            break;
        case 'image/gif':
            $result = imagegif($optimized_image, $destination_path);
            break;
        case 'image/webp':
            $result = imagewebp($optimized_image, $destination_path, $quality);
            break;
    }
    
    // Очистка памяти
    imagedestroy($source_image);
    imagedestroy($optimized_image);
    
    return $result ? $destination_path : false;
}

/**
 * Создание WebP версии изображения
 */
function create_webp_image($source_path, $destination_path = null, $quality = 85) {
    if (!file_exists($source_path)) {
        return false;
    }
    
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    $mime_type = $image_info['mime'];
    
    // Создание изображения из файла
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
        default:
            return false;
    }
    
    if (!$source_image) {
        return false;
    }
    
    // Определение пути назначения
    if (!$destination_path) {
        $path_info = pathinfo($source_path);
        $destination_path = $path_info['dirname'] . '/' . $path_info['filename'] . '.webp';
    }
    
    // Создание WebP изображения
    $result = imagewebp($source_image, $destination_path, $quality);
    
    // Очистка памяти
    imagedestroy($source_image);
    
    return $result ? $destination_path : false;
}

/**
 * Создание миниатюры для SEO оптимизатора
 */
if (!function_exists('create_seo_thumbnail')) {
function create_seo_thumbnail($source_path, $destination_path, $width = 300, $height = 200, $crop = true) {
    if (!file_exists($source_path)) {
        return false;
    }
    
    $image_info = getimagesize($source_path);
    if (!$image_info) {
        return false;
    }
    
    $source_width = $image_info[0];
    $source_height = $image_info[1];
    $mime_type = $image_info['mime'];
    
    // Создание изображения из файла
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
    
    if ($crop) {
        // Обрезка по центру
        $ratio = max($width / $source_width, $height / $source_height);
        $crop_width = intval($width / $ratio);
        $crop_height = intval($height / $ratio);
        $crop_x = intval(($source_width - $crop_width) / 2);
        $crop_y = intval(($source_height - $crop_height) / 2);
        
        $thumbnail = imagecreatetruecolor($width, $height);
        imagecopyresampled($thumbnail, $source_image, 0, 0, $crop_x, $crop_y, $width, $height, $crop_width, $crop_height);
    } else {
        // Пропорциональное изменение размера
        $ratio = min($width / $source_width, $height / $source_height);
        $new_width = intval($source_width * $ratio);
        $new_height = intval($source_height * $ratio);
        
        $thumbnail = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($thumbnail, $source_image, 0, 0, 0, 0, $new_width, $new_height, $source_width, $source_height);
    }
    
    // Сохранение миниатюры
    $result = imagejpeg($thumbnail, $destination_path, 90);
    
    // Очистка памяти
    imagedestroy($source_image);
    imagedestroy($thumbnail);
    
    return $result ? $destination_path : false;
}
} // Закрытие if (!function_exists('create_seo_thumbnail'))

/**
 * Пакетная оптимизация изображений
 */
function batch_optimize_images($directory, $recursive = true) {
    $optimized_count = 0;
    $errors = [];
    
    $iterator = $recursive ? 
        new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) : 
        new DirectoryIterator($directory);
    
    foreach ($iterator as $file) {
        if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif'])) {
            $source_path = $file->getPathname();
            $optimized_path = optimize_image($source_path);
            
            if ($optimized_path) {
                $optimized_count++;
                
                // Создание WebP версии
                $webp_path = create_webp_image($source_path);
                if ($webp_path) {
                    $optimized_count++;
                }
            } else {
                $errors[] = "Failed to optimize: " . $source_path;
            }
        }
    }
    
    return [
        'optimized_count' => $optimized_count,
        'errors' => $errors
    ];
}

/**
 * Получение информации об изображении для SEO
 */
function get_image_seo_info($image_path) {
    if (!file_exists($image_path)) {
        return false;
    }
    
    $image_info = getimagesize($image_path);
    if (!$image_info) {
        return false;
    }
    
    $file_size = filesize($image_path);
    $path_info = pathinfo($image_path);
    
    return [
        'width' => $image_info[0],
        'height' => $image_info[1],
        'mime_type' => $image_info['mime'],
        'file_size' => $file_size,
        'file_size_formatted' => format_file_size($file_size),
        'filename' => $path_info['filename'],
        'extension' => $path_info['extension'],
        'alt_text' => generate_alt_text($path_info['filename']),
        'is_optimized' => strpos($path_info['filename'], '_optimized') !== false
    ];
}

/**
 * Генерация alt текста для изображения
 */
function generate_alt_text($filename) {
    // Удаление расширения
    $name = pathinfo($filename, PATHINFO_FILENAME);
    
    // Удаление суффиксов оптимизации
    $name = str_replace(['_optimized', '_thumb', '_webp'], '', $name);
    
    // Замена подчеркиваний на пробелы
    $name = str_replace('_', ' ', $name);
    
    // Заглавная буква
    $name = ucfirst($name);
    
    return $name;
}
?>

