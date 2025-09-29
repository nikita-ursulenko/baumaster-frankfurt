<?php
/**
 * Функции авторизации для админ-панели
 * Baumaster Admin Panel
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * ОСНОВНЫЕ ФУНКЦИИ АВТОРИЗАЦИИ
 */

/**
 * Проверка авторизации пользователя
 */
function require_auth($redirect_to_login = true) {
    if (!is_logged_in()) {
        if ($redirect_to_login) {
            header('Location: ' . get_admin_url('login.php'));
            exit;
        }
        return false;
    }
    
    return true;
}


/**
 * Получить информацию о текущем пользователе
 */
function get_current_user_info() {
    if (!is_logged_in()) {
        return null;
    }
    
    $user_data = get_current_admin_user();
    if (!$user_data) {
        return null;
    }
    
    return [
        'id' => $user_data['id'],
        'username' => $user_data['username'],
        'email' => $user_data['email'],
        'role' => $user_data['role'],
        'status' => $user_data['status'],
        'created_at' => $user_data['created_at'],
        'login_time' => $_SESSION['login_time'] ?? null
    ];
}

/**
 * Проверка времени сессии
 */
function check_session_timeout() {
    if (!is_logged_in()) {
        return false;
    }
    
    $login_time = $_SESSION['login_time'] ?? 0;
    $current_time = time();
    
    // Проверка времени жизни сессии
    if (($current_time - $login_time) > SESSION_LIFETIME) {
        logout_user();
        return false;
    }
    
    // Обновление времени последней активности
    $_SESSION['last_activity'] = $current_time;
    
    return true;
}

/**
 * ФУНКЦИИ УПРАВЛЕНИЯ ПОЛЬЗОВАТЕЛЯМИ
 */

/**
 * Создание нового пользователя
 */
function create_user($data) {
    $db = get_database();
    
    // Валидация данных
    $errors = validate_user_data($data);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Проверка уникальности username и email
    if ($db->select('users', ['username' => $data['username']], ['limit' => 1])) {
        return ['success' => false, 'errors' => ['username' => 'Пользователь с таким логином уже существует']];
    }
    
    if ($db->select('users', ['email' => $data['email']], ['limit' => 1])) {
        return ['success' => false, 'errors' => ['email' => 'Пользователь с таким email уже существует']];
    }
    
    // Подготовка данных для сохранения
    $user_data = [
        'username' => sanitize_input($data['username']),
        'email' => sanitize_input($data['email']),
        'password' => hash_password($data['password']),
        'role' => sanitize_input($data['role'] ?? 'editor'),
        'status' => sanitize_input($data['status'] ?? 'active')
    ];
    
    // Сохранение в базе данных
    $user_id = $db->insert('users', $user_data);
    
    if ($user_id) {
        // Логирование
        write_log("New user created: {$user_data['username']} (ID: $user_id)", 'INFO');
        
        return [
            'success' => true, 
            'user_id' => $user_id,
            'message' => 'Пользователь успешно создан'
        ];
    } else {
        return ['success' => false, 'errors' => ['general' => 'Ошибка при создании пользователя']];
    }
}

/**
 * Обновление пользователя
 */
function update_user($user_id, $data) {
    $db = get_database();
    
    // Проверка существования пользователя
    $existing_user = $db->select('users', ['id' => $user_id], ['limit' => 1]);
    if (!$existing_user) {
        return ['success' => false, 'errors' => ['general' => 'Пользователь не найден']];
    }
    
    // Подготовка данных для обновления
    $update_data = [];
    
    if (isset($data['username']) && $data['username'] !== $existing_user['username']) {
        // Проверка уникальности username
        if ($db->select('users', ['username' => $data['username']], ['limit' => 1])) {
            return ['success' => false, 'errors' => ['username' => 'Пользователь с таким логином уже существует']];
        }
        $update_data['username'] = sanitize_input($data['username']);
    }
    
    if (isset($data['email']) && $data['email'] !== $existing_user['email']) {
        // Проверка уникальности email
        if ($db->select('users', ['email' => $data['email']], ['limit' => 1])) {
            return ['success' => false, 'errors' => ['email' => 'Пользователь с таким email уже существует']];
        }
        $update_data['email'] = sanitize_input($data['email']);
    }
    
    if (isset($data['password']) && !empty($data['password'])) {
        $update_data['password'] = hash_password($data['password']);
    }
    
    if (isset($data['role'])) {
        $update_data['role'] = sanitize_input($data['role']);
    }
    
    if (isset($data['status'])) {
        $update_data['status'] = sanitize_input($data['status']);
    }
    
    // Если нет данных для обновления
    if (empty($update_data)) {
        return ['success' => true, 'message' => 'Нет изменений для сохранения'];
    }
    
    // Обновление в базе данных
    if ($db->update('users', $update_data, ['id' => $user_id])) {
        // Логирование
        write_log("User updated: {$existing_user['username']} (ID: $user_id)", 'INFO');
        
        return ['success' => true, 'message' => 'Пользователь успешно обновлен'];
    } else {
        return ['success' => false, 'errors' => ['general' => 'Ошибка при обновлении пользователя']];
    }
}

/**
 * Удаление пользователя
 */
function delete_user($user_id) {
    $db = get_database();
    
    // Проверка существования пользователя
    $user = $db->select('users', ['id' => $user_id], ['limit' => 1]);
    if (!$user) {
        return ['success' => false, 'error' => 'Пользователь не найден'];
    }
    
    // Нельзя удалить себя
    $current_user = get_current_admin_user();
    if ($current_user && $current_user['id'] == $user_id) {
        return ['success' => false, 'error' => 'Нельзя удалить свой собственный аккаунт'];
    }
    
    // Удаление из базы данных
    if ($db->delete('users', ['id' => $user_id])) {
        // Логирование
        write_log("User deleted: {$user['username']} (ID: $user_id)", 'WARNING');
        
        return ['success' => true, 'message' => 'Пользователь успешно удален'];
    } else {
        return ['success' => false, 'error' => 'Ошибка при удалении пользователя'];
    }
}

/**
 * ВАЛИДАЦИЯ
 */

/**
 * Валидация данных пользователя
 */
function validate_user_data($data, $is_update = false) {
    $errors = [];
    
    // Валидация username
    if (!$is_update || isset($data['username'])) {
        $username = $data['username'] ?? '';
        if (empty($username)) {
            $errors['username'] = 'Логин обязателен';
        } elseif (strlen($username) < 3) {
            $errors['username'] = 'Логин должен содержать минимум 3 символа';
        } elseif (strlen($username) > 50) {
            $errors['username'] = 'Логин должен содержать максимум 50 символов';
        } elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors['username'] = 'Логин может содержать только буквы, цифры, дефис и подчеркивание';
        }
    }
    
    // Валидация email
    if (!$is_update || isset($data['email'])) {
        $email = $data['email'] ?? '';
        if (empty($email)) {
            $errors['email'] = 'Email обязателен';
        } elseif (!validate_email($email)) {
            $errors['email'] = 'Неверный формат email';
        }
    }
    
    // Валидация password
    if (!$is_update || (isset($data['password']) && !empty($data['password']))) {
        $password = $data['password'] ?? '';
        if (empty($password)) {
            $errors['password'] = 'Пароль обязателен';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Пароль должен содержать минимум 6 символов';
        }
    }
    
    // Валидация роли
    if (isset($data['role'])) {
        $allowed_roles = ['admin', 'editor', 'moderator'];
        if (!in_array($data['role'], $allowed_roles)) {
            $errors['role'] = 'Недопустимая роль';
        }
    }
    
    // Валидация статуса
    if (isset($data['status'])) {
        $allowed_statuses = ['active', 'inactive', 'blocked'];
        if (!in_array($data['status'], $allowed_statuses)) {
            $errors['status'] = 'Недопустимый статус';
        }
    }
    
    return $errors;
}

/**
 * ЛОГИРОВАНИЕ АКТИВНОСТИ
 */

/**
 * Получить логи активности
 */
function get_activity_logs($limit = 50, $offset = 0, $filters = []) {
    $db = get_database();
    
    // TODO: Реализовать фильтрацию и JOIN с таблицей users
    $logs = $db->select('activity_log', [], [
        'order' => 'created_at DESC',
        'limit' => $limit,
        'offset' => $offset
    ]);
    
    return $logs;
}
