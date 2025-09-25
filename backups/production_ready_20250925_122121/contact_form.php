<?php
/**
 * Contact Form Handler
 * Baumaster - Contact Form Processing
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/integrations/email.php';
require_once __DIR__ . '/integrations/analytics.php';

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// Инициализация переменных
$errors = [];
$success = false;

// Валидация данных
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? '');
$message = trim($_POST['message'] ?? '');

// Валидация имени
if (empty($name)) {
    $errors['name'] = 'Имя обязательно для заполнения';
} elseif (strlen($name) < 2) {
    $errors['name'] = 'Имя должно содержать минимум 2 символа';
}

// Валидация email
if (empty($email)) {
    $errors['email'] = 'Email обязателен для заполнения';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Некорректный email адрес';
}

// Валидация телефона
if (empty($phone)) {
    $errors['phone'] = 'Телефон обязателен для заполнения';
} elseif (!preg_match('/^[\+]?[0-9\s\-\(\)]{10,}$/', $phone)) {
    $errors['phone'] = 'Некорректный номер телефона';
}

// Валидация сообщения
if (empty($message)) {
    $errors['message'] = 'Сообщение обязательно для заполнения';
} elseif (strlen($message) < 10) {
    $errors['message'] = 'Сообщение должно содержать минимум 10 символов';
}

// Если нет ошибок, отправляем email
if (empty($errors)) {
    $form_data = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'service' => $service,
        'message' => $message,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    // Отправка уведомления администратору
    $admin_notification = send_contact_form_notification($form_data);
    
    // Отправка подтверждения клиенту
    $client_confirmation = send_contact_form_confirmation($form_data);
    
    if ($admin_notification && $client_confirmation) {
        $success = true;
        
        // Отслеживание конверсии
        echo track_conversion('contact_form');
        
        // Логирование
        write_log("Contact form submitted by: {$name} ({$email})", 'INFO');
        
        // Сохранение в базу данных (если нужно)
        save_contact_form_data($form_data);
    } else {
        $errors['general'] = 'Ошибка при отправке сообщения. Попробуйте позже.';
    }
}

// Сохранение данных формы в базу
function save_contact_form_data($data) {
    $db = get_database();
    
    $contact_data = [
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'service' => $data['service'],
        'message' => $data['message'],
        'ip_address' => $data['ip'],
        'user_agent' => $data['user_agent'],
        'status' => 'new',
        'created_at' => $data['timestamp']
    ];
    
    return $db->insert('contact_forms', $contact_data);
}

// Перенаправление обратно на страницу контактов
$redirect_url = 'contact.php';
if ($success) {
    $redirect_url .= '?success=1';
} else {
    $redirect_url .= '?error=1&' . http_build_query($errors);
}

header('Location: ' . $redirect_url);
exit;
?>

