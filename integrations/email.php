<?php
/**
 * Email Integration
 * Baumaster Integrations - Email Notifications
 */

/**
 * Отправка email уведомлений
 */
function send_email($to, $subject, $message, $is_html = true) {
    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-Type: ' . ($is_html ? 'text/html' : 'text/plain') . '; charset=UTF-8';
    $headers[] = 'From: ' . get_setting('company_email', 'noreply@baumaster-frankfurt.de');
    $headers[] = 'Reply-To: ' . get_setting('company_email', 'info@baumaster-frankfurt.de');
    $headers[] = 'X-Mailer: PHP/' . phpversion();
    
    $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Отправка уведомления о новой заявке
 */
function send_contact_form_notification($form_data) {
    $admin_email = get_setting('admin_email', get_setting('company_email', 'admin@baumaster-frankfurt.de'));
    
    $subject = 'Новая заявка с сайта - ' . $form_data['name'];
    
    $message = '<html><body>';
    $message .= '<h2>Новая заявка с сайта</h2>';
    $message .= '<p><strong>Имя:</strong> ' . htmlspecialchars($form_data['name']) . '</p>';
    $message .= '<p><strong>Email:</strong> ' . htmlspecialchars($form_data['email']) . '</p>';
    $message .= '<p><strong>Телефон:</strong> ' . htmlspecialchars($form_data['phone']) . '</p>';
    $message .= '<p><strong>Услуга:</strong> ' . htmlspecialchars($form_data['service']) . '</p>';
    $message .= '<p><strong>Сообщение:</strong></p>';
    $message .= '<p>' . nl2br(htmlspecialchars($form_data['message'])) . '</p>';
    $message .= '<p><strong>Дата:</strong> ' . date('d.m.Y H:i:s') . '</p>';
    $message .= '</body></html>';
    
    return send_email($admin_email, $subject, $message);
}

/**
 * Отправка подтверждения клиенту
 */
function send_contact_form_confirmation($form_data) {
    $subject = 'Спасибо за обращение - ' . get_setting('company_name', 'Baumaster Frankfurt');
    
    $message = '<html><body>';
    $message .= '<h2>Спасибо за обращение!</h2>';
    $message .= '<p>Уважаемый(ая) ' . htmlspecialchars($form_data['name']) . ',</p>';
    $message .= '<p>Мы получили вашу заявку и свяжемся с вами в ближайшее время.</p>';
    $message .= '<p><strong>Детали заявки:</strong></p>';
    $message .= '<ul>';
    $message .= '<li>Услуга: ' . htmlspecialchars($form_data['service']) . '</li>';
    $message .= '<li>Дата: ' . date('d.m.Y H:i:s') . '</li>';
    $message .= '</ul>';
    $message .= '<p>С уважением,<br>' . get_setting('company_name', 'Baumaster Frankfurt') . '</p>';
    $message .= '</body></html>';
    
    return send_email($form_data['email'], $subject, $message);
}

/**
 * Отправка уведомления о новом отзыве
 */
function send_review_notification($review_data) {
    $admin_email = get_setting('admin_email', get_setting('company_email', 'admin@baumaster-frankfurt.de'));
    
    $subject = 'Новый отзыв от ' . $review_data['client_name'];
    
    $message = '<html><body>';
    $message .= '<h2>Новый отзыв на сайте</h2>';
    $message .= '<p><strong>Клиент:</strong> ' . htmlspecialchars($review_data['client_name']) . '</p>';
    $message .= '<p><strong>Email:</strong> ' . htmlspecialchars($review_data['client_email']) . '</p>';
    $message .= '<p><strong>Рейтинг:</strong> ' . $review_data['rating'] . '/5</p>';
    $message .= '<p><strong>Отзыв:</strong></p>';
    $message .= '<p>' . nl2br(htmlspecialchars($review_data['review_text'])) . '</p>';
    $message .= '<p><strong>Дата:</strong> ' . date('d.m.Y H:i:s') . '</p>';
    $message .= '<p><a href="' . get_setting('admin_url', 'https://baumaster-frankfurt.de/admin') . '/reviews.php">Перейти к модерации</a></p>';
    $message .= '</body></html>';
    
    return send_email($admin_email, $subject, $message);
}

/**
 * Отправка уведомления о новом пользователе
 */
function send_user_registration_notification($user_data) {
    $admin_email = get_setting('admin_email', get_setting('company_email', 'admin@baumaster-frankfurt.de'));
    
    $subject = 'Новый пользователь в админ-панели';
    
    $message = '<html><body>';
    $message .= '<h2>Новый пользователь</h2>';
    $message .= '<p><strong>Логин:</strong> ' . htmlspecialchars($user_data['username']) . '</p>';
    $message .= '<p><strong>Email:</strong> ' . htmlspecialchars($user_data['email']) . '</p>';
    $message .= '<p><strong>Роль:</strong> ' . htmlspecialchars($user_data['role']) . '</p>';
    $message .= '<p><strong>Дата регистрации:</strong> ' . date('d.m.Y H:i:s') . '</p>';
    $message .= '</body></html>';
    
    return send_email($admin_email, $subject, $message);
}

/**
 * Отправка системных уведомлений
 */
function send_system_notification($type, $data) {
    $admin_email = get_setting('admin_email', get_setting('company_email', 'admin@baumaster-frankfurt.de'));
    
    $subjects = [
        'backup_created' => 'Резервная копия создана',
        'backup_failed' => 'Ошибка создания резервной копии',
        'security_alert' => 'Предупреждение безопасности',
        'maintenance' => 'Техническое обслуживание'
    ];
    
    $subject = $subjects[$type] ?? 'Системное уведомление';
    
    $message = '<html><body>';
    $message .= '<h2>' . $subject . '</h2>';
    $message .= '<p><strong>Время:</strong> ' . date('d.m.Y H:i:s') . '</p>';
    $message .= '<p><strong>Детали:</strong></p>';
    $message .= '<pre>' . print_r($data, true) . '</pre>';
    $message .= '</body></html>';
    
    return send_email($admin_email, $subject, $message);
}
?>

