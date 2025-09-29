<?php
/**
 * Страница авторизации администратора
 * Baumaster Admin Panel
 */

// Подключение базовых файлов
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'auth_layout.php';

// Если уже авторизован, перенаправляем в админку
if (is_logged_in()) {
    header('Location: ' . get_admin_url('index.php'));
    exit;
}

$error_message = '';
$success_message = '';

// Обработка формы логина
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'login') {
    // Проверка CSRF токена
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = __('auth.csrf_error', 'Ошибка безопасности. Попробуйте снова.');
    } else {
        $username = sanitize_input($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error_message = __('auth.empty_fields', 'Заполните все поля');
        } else {
            // Попытка авторизации
            $db = get_database();
            $user = $db->select('users', ['username' => $username, 'status' => 'active'], ['limit' => 1]);
            
            if ($user && verify_password($password, $user['password'])) {
                // Успешная авторизация
                login_user($user);
                
                // Логирование
                write_log("User {$user['username']} logged in", 'INFO');
                
                // Перенаправление в админку
                header('Location: ' . get_admin_url('index.php'));
                exit;
            } else {
                $error_message = __('auth.login_error', 'Неверный логин или пароль');
                
                // Логирование неудачной попытки
                write_log("Failed login attempt for username: $username", 'WARNING');
            }
        }
    }
}

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Подготовка контента
ob_start();
render_error_message($error_message);
render_success_message($success_message);
echo render_login_form($csrf_token, $_POST['username'] ?? '');
$form_content = ob_get_clean();

// Рендеринг страницы
render_auth_layout([
    'title' => __('auth.login', 'Вход в админ-панель'),
    'content' => $form_content
]);
