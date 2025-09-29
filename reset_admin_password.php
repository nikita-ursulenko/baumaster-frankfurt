<?php
/**
 * Скрипт для сброса пароля администратора
 * ВНИМАНИЕ: Удалите этот файл после использования!
 */

// Подключение конфигурации
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';

// Простая защита - только для локального использования
if (!in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1', '5.61.34.176'])) {
    die('Доступ запрещен');
}

echo "<h1>Сброс пароля администратора</h1>";

try {
    $db = get_database();
    
    // Новый пароль
    $new_password = 'admin123';
    
    // Хеширование пароля
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Обновление пароля
    $result = $db->update('users', 
        ['password' => $hashed_password], 
        ['username' => 'root']
    );
    
    if ($result) {
        echo "<p style='color: green;'>✅ Пароль успешно сброшен!</p>";
        echo "<p><strong>Логин:</strong> root</p>";
        echo "<p><strong>Новый пароль:</strong> {$new_password}</p>";
        echo "<p><strong>URL админки:</strong> <a href='/admin/login.php'>http://5.61.34.176/admin/login.php</a></p>";
        echo "<p style='color: red;'><strong>ВАЖНО:</strong> Удалите этот файл после использования!</p>";
    } else {
        echo "<p style='color: red;'>❌ Ошибка при сбросе пароля</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Ошибка: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
