<?php
/**
 * SMS Integration Management Page
 * Baumaster Admin Panel - SMS Integration
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';
require_once __DIR__ . '/../integrations/email.php';

// Проверка прав доступа
$current_user = get_current_admin_user();
if (!has_permission('settings.edit', $current_user)) {
    header('Location: index.php?error=access_denied');
    exit;
}

// Настройки страницы
$page_title = __('sms_integration.title', 'SMS Интеграция');
$page_description = __('sms_integration.description', 'Управление SMS и Email уведомлениями');
$active_menu = 'sms_integration';

// Инициализация переменных
$error_message = '';
$success_message = '';
$db = get_database();

// Обработка POST запросов
if ($_POST && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'update_email_settings':
                $email_settings = [
                    'smtp_host' => $_POST['smtp_host'] ?? '',
                    'smtp_port' => $_POST['smtp_port'] ?? '587',
                    'smtp_username' => $_POST['smtp_username'] ?? '',
                    'smtp_password' => $_POST['smtp_password'] ?? '',
                    'smtp_encryption' => $_POST['smtp_encryption'] ?? 'tls',
                    'admin_email' => $_POST['admin_email'] ?? ''
                ];
                
                foreach ($email_settings as $key => $value) {
                    set_setting($key, $value, 'email');
                }
                
                $success_message = __('sms_integration.email_updated', 'Настройки email обновлены');
                break;
                
                
            case 'test_email':
                $test_email = $_POST['test_email'] ?? '';
                if (filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
                    $test_result = send_system_notification('test', ['message' => 'Тестовое сообщение']);
                    if ($test_result) {
                        $success_message = __('sms_integration.email_test_success', 'Тестовое email отправлено');
                    } else {
                        $error_message = __('sms_integration.email_test_failed', 'Ошибка отправки тестового email');
                    }
                } else {
                    $error_message = __('sms_integration.invalid_email', 'Некорректный email адрес');
                }
                break;
        }
    } catch (Exception $e) {
        $error_message = __('sms_integration.update_error', 'Ошибка при обновлении настроек: ') . $e->getMessage();
        write_log("SMS Integration update error: " . $e->getMessage(), 'ERROR');
    }
}

// Получение текущих настроек
$email_settings = get_settings_by_category('email');

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Сообщения -->
<?php render_error_message($error_message); ?>
<?php render_success_message($success_message); ?>


<!-- Email настройки -->
<div class="mb-6">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <div class="flex items-center mb-4">
            <?php echo get_icon('mail', 'w-6 h-6 mr-3 text-primary-600'); ?>
            <h3 class="text-lg font-medium text-gray-900">
                <?php echo __('sms_integration.email_settings', 'Email Настройки'); ?>
            </h3>
        </div>

        
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="update_email_settings">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php render_form_field([
                    'type' => 'text',
                    'name' => 'smtp_host',
                    'label' => __('sms_integration.smtp_host', 'SMTP сервер'),
                    'value' => $email_settings['smtp_host'] ?? '',
                    'placeholder' => 'smtp.gmail.com'
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'number',
                    'name' => 'smtp_port',
                    'label' => __('sms_integration.smtp_port', 'SMTP порт'),
                    'value' => $email_settings['smtp_port'] ?? '587'
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'text',
                    'name' => 'smtp_username',
                    'label' => __('sms_integration.smtp_username', 'SMTP логин'),
                    'value' => $email_settings['smtp_username'] ?? ''
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'password',
                    'name' => 'smtp_password',
                    'label' => __('sms_integration.smtp_password', 'SMTP пароль'),
                    'value' => $email_settings['smtp_password'] ?? ''
                ]); ?>
                
                <div>
                    <?php render_dropdown_field([
                        'name' => 'smtp_encryption',
                        'label' => __('sms_integration.smtp_encryption', 'Шифрование'),
                        'value' => $email_settings['smtp_encryption'] ?? 'tls',
                        'options' => [
                            ['value' => 'tls', 'text' => 'TLS'],
                            ['value' => 'ssl', 'text' => 'SSL'],
                            ['value' => 'none', 'text' => 'None']
                        ],
                        'placeholder' => 'Выберите тип шифрования'
                    ]); ?>
                </div>
                
                <?php render_form_field([
                    'type' => 'email',
                    'name' => 'admin_email',
                    'label' => __('sms_integration.admin_email', 'Email администратора'),
                    'value' => $email_settings['admin_email'] ?? '',
                    'help' => __('sms_integration.admin_email_help', 'Email для получения уведомлений')
                ]); ?>
            </div>
            
            <div class="flex justify-between">
                <?php render_button([
                    'type' => 'submit',
                    'text' => __('common.save', 'Сохранить'),
                    'variant' => 'primary'
                ]); ?>
                
                <div class="flex gap-2">
                    <?php render_form_field([
                        'type' => 'email',
                        'name' => 'test_email',
                        'placeholder' => __('sms_integration.test_email_placeholder', 'Email для теста'),
                        'class' => 'px-3 py-2 border border-gray-300 rounded-md'
                    ]); ?>
                    <button type="button" onclick="testEmail()" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        <?php echo __('sms_integration.test_email', 'Тест'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
function testEmail() {
    const testEmail = document.querySelector('input[name="test_email"]').value;
    if (!testEmail) {
        alert('<?php echo __('sms_integration.enter_test_email', 'Введите email для теста'); ?>');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
        <input type="hidden" name="action" value="test_email">
        <input type="hidden" name="test_email" value="${testEmail}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>


<?php
$page_content = ob_get_clean();

// Рендеринг страницы
render_admin_layout([
    'page_title' => $page_title,
    'page_description' => $page_description,
    'active_menu' => $active_menu,
    'content' => $page_content
]);
?>

