<?php
/**
 * Страница выхода из админ-панели
 * Baumaster Admin Panel
 */

// Подключение базовых файлов
require_once __DIR__ . '/../config.php';

// Логирование выхода пользователя
if (is_logged_in()) {
    $user = get_current_admin_user();
    write_log("User {$user['username']} logged out", 'INFO');
}

// Выход пользователя
logout_user();

// Перенаправление на страницу логина с сообщением
header('Location: ' . get_admin_url('login.php?message=logged_out'));
exit;
