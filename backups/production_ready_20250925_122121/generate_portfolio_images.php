<?php
/**
 * Скрипт для генерации изображений портфолио
 * Создает placeholder изображения для демонстрации
 */

// Создаем папку для портфолио
$portfolio_dir = '/Applications/XAMPP/xamppfiles/htdocs/assets/uploads/portfolio/';
if (!is_dir($portfolio_dir)) {
    mkdir($portfolio_dir, 0755, true);
}

// Функция для создания placeholder изображения
function create_placeholder_image($width, $height, $text, $filename, $bg_color = '#f0f0f0', $text_color = '#333') {
    $image = imagecreate($width, $height);
    
    // Парсим цвет фона
    $bg_r = hexdec(substr($bg_color, 1, 2));
    $bg_g = hexdec(substr($bg_color, 3, 2));
    $bg_b = hexdec(substr($bg_color, 5, 2));
    $bg_color = imagecolorallocate($image, $bg_r, $bg_g, $bg_b);
    
    // Парсим цвет текста
    $text_r = hexdec(substr($text_color, 1, 2));
    $text_g = hexdec(substr($text_color, 3, 2));
    $text_b = hexdec(substr($text_color, 5, 2));
    $text_color = imagecolorallocate($image, $text_r, $text_g, $text_b);
    
    // Заполняем фон
    imagefill($image, 0, 0, $bg_color);
    
    // Добавляем текст
    $font_size = 5;
    $text_width = imagefontwidth($font_size) * strlen($text);
    $text_height = imagefontheight($font_size);
    $x = ($width - $text_width) / 2;
    $y = ($height - $text_height) / 2;
    
    imagestring($image, $font_size, $x, $y, $text, $text_color);
    
    // Сохраняем изображение
    imagejpeg($image, $filename, 90);
    imagedestroy($image);
}

// Создаем изображения для разных проектов
$projects = [
    'apartment_renovation' => [
        'title' => 'Ремонт квартиры',
        'color' => '#e8f4fd',
        'images' => ['main.jpg', 'gallery1.jpg', 'gallery2.jpg', 'gallery3.jpg', 'before.jpg', 'after.jpg']
    ],
    'bathroom_renovation' => [
        'title' => 'Ремонт ванной',
        'color' => '#f0f8ff',
        'images' => ['main.jpg', 'gallery1.jpg', 'gallery2.jpg', 'before.jpg', 'after.jpg']
    ],
    'kitchen_renovation' => [
        'title' => 'Ремонт кухни',
        'color' => '#fff8e1',
        'images' => ['main.jpg', 'gallery1.jpg', 'gallery2.jpg', 'gallery3.jpg', 'before.jpg', 'after.jpg']
    ],
    'office_renovation' => [
        'title' => 'Ремонт офиса',
        'color' => '#f3e5f5',
        'images' => ['main.jpg', 'gallery1.jpg', 'gallery2.jpg', 'before.jpg', 'after.jpg']
    ],
    'house_renovation' => [
        'title' => 'Ремонт дома',
        'color' => '#e8f5e8',
        'images' => ['main.jpg', 'gallery1.jpg', 'gallery2.jpg', 'gallery3.jpg', 'gallery4.jpg', 'before.jpg', 'after.jpg']
    ]
];

foreach ($projects as $project_key => $project) {
    echo "Создаем изображения для проекта: {$project['title']}\n";
    
    foreach ($project['images'] as $image_name) {
        $filename = $portfolio_dir . $project_key . '_' . $image_name;
        $text = $project['title'] . "\n" . str_replace('.jpg', '', $image_name);
        
        // Разные размеры для разных типов изображений
        if (strpos($image_name, 'main') !== false) {
            create_placeholder_image(800, 600, $text, $filename, $project['color']);
        } elseif (strpos($image_name, 'gallery') !== false) {
            create_placeholder_image(600, 400, $text, $filename, $project['color']);
        } else {
            create_placeholder_image(500, 400, $text, $filename, $project['color']);
        }
        
        echo "  Создано: {$image_name}\n";
    }
}

echo "Все изображения портфолио созданы!\n";
?>
