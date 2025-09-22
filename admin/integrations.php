<?php
/**
 * Integrations Management Page
 * Baumaster Admin Panel - Integrations
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';
require_once __DIR__ . '/../integrations/email.php';
require_once __DIR__ . '/../integrations/analytics.php';
require_once __DIR__ . '/../integrations/maps.php';
require_once __DIR__ . '/../integrations/i18n.php';

// Проверка прав доступа
$current_user = get_current_admin_user();
if (!has_permission('settings.edit', $current_user)) {
    header('Location: index.php?error=access_denied');
    exit;
}

// Настройки страницы
$page_title = __('integrations.title', 'Интеграции');
$page_description = __('integrations.description', 'Управление внешними сервисами и интеграциями');
$active_menu = 'integrations';

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
                
                $success_message = __('integrations.email_updated', 'Настройки email обновлены');
                break;
                
            case 'update_analytics_settings':
                $analytics_settings = [
                    'google_analytics' => $_POST['google_analytics'] ?? '',
                    'google_tag_manager' => $_POST['google_tag_manager'] ?? '',
                    'facebook_pixel' => $_POST['facebook_pixel'] ?? ''
                ];
                
                foreach ($analytics_settings as $key => $value) {
                    set_setting($key, $value, 'analytics');
                }
                
                $success_message = __('integrations.analytics_updated', 'Настройки аналитики обновлены');
                break;
                
            case 'update_maps_settings':
                $maps_settings = [
                    'google_maps_api_key' => $_POST['google_maps_api_key'] ?? '',
                    'company_latitude' => $_POST['company_latitude'] ?? '50.1109',
                    'company_longitude' => $_POST['company_longitude'] ?? '8.6821'
                ];
                
                foreach ($maps_settings as $key => $value) {
                    set_setting($key, $value, 'maps');
                }
                
                $success_message = __('integrations.maps_updated', 'Настройки карт обновлены');
                break;
                
            case 'test_email':
                $test_email = $_POST['test_email'] ?? '';
                if (filter_var($test_email, FILTER_VALIDATE_EMAIL)) {
                    $test_result = send_system_notification('test', ['message' => 'Тестовое сообщение']);
                    if ($test_result) {
                        $success_message = __('integrations.email_test_success', 'Тестовое email отправлено');
                    } else {
                        $error_message = __('integrations.email_test_failed', 'Ошибка отправки тестового email');
                    }
                } else {
                    $error_message = __('integrations.invalid_email', 'Некорректный email адрес');
                }
                break;
        }
    } catch (Exception $e) {
        $error_message = __('integrations.update_error', 'Ошибка при обновлении настроек: ') . $e->getMessage();
        write_log("Integrations update error: " . $e->getMessage(), 'ERROR');
    }
}

// Получение текущих настроек
$email_settings = get_settings_by_category('email');
$analytics_settings = get_settings_by_category('analytics');
$maps_settings = get_settings_by_category('maps');

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Сообщения -->
<?php render_error_message($error_message); ?>
<?php render_success_message($success_message); ?>

<!-- Заголовок -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">
            <?php echo __('integrations.title', 'Интеграции'); ?>
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            <?php echo __('integrations.description', 'Управление внешними сервисами и интеграциями'); ?>
        </p>
    </div>
</div>

<!-- Вкладки интеграций -->
<div class="mb-6">
    <nav class="flex space-x-8" aria-label="Tabs">
        <button class="integration-tab-btn active" data-tab="email">
            <?php echo get_icon('mail', 'w-5 h-5 mr-2'); ?>
            <?php echo __('integrations.tab_email', 'Email'); ?>
        </button>
        <button class="integration-tab-btn" data-tab="analytics">
            <?php echo get_icon('chart', 'w-5 h-5 mr-2'); ?>
            <?php echo __('integrations.tab_analytics', 'Аналитика'); ?>
        </button>
        <button class="integration-tab-btn" data-tab="maps">
            <?php echo get_icon('map', 'w-5 h-5 mr-2'); ?>
            <?php echo __('integrations.tab_maps', 'Карты'); ?>
        </button>
        <button class="integration-tab-btn" data-tab="i18n">
            <?php echo get_icon('globe', 'w-5 h-5 mr-2'); ?>
            <?php echo __('integrations.tab_i18n', 'Языки'); ?>
        </button>
    </nav>
</div>

<!-- Email настройки -->
<div class="integration-tab-content" data-tab="email">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('integrations.email_settings', 'Настройки Email'); ?>
        </h3>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="update_email_settings">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php render_form_field([
                    'type' => 'text',
                    'name' => 'smtp_host',
                    'label' => __('integrations.smtp_host', 'SMTP сервер'),
                    'value' => $email_settings['smtp_host'] ?? '',
                    'placeholder' => 'smtp.gmail.com'
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'number',
                    'name' => 'smtp_port',
                    'label' => __('integrations.smtp_port', 'SMTP порт'),
                    'value' => $email_settings['smtp_port'] ?? '587'
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'text',
                    'name' => 'smtp_username',
                    'label' => __('integrations.smtp_username', 'SMTP логин'),
                    'value' => $email_settings['smtp_username'] ?? ''
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'password',
                    'name' => 'smtp_password',
                    'label' => __('integrations.smtp_password', 'SMTP пароль'),
                    'value' => $email_settings['smtp_password'] ?? ''
                ]); ?>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        <?php echo __('integrations.smtp_encryption', 'Шифрование'); ?>
                    </label>
                    <select name="smtp_encryption" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                        <option value="tls" <?php echo ($email_settings['smtp_encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                        <option value="ssl" <?php echo ($email_settings['smtp_encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                        <option value="none" <?php echo ($email_settings['smtp_encryption'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                    </select>
                </div>
                
                <?php render_form_field([
                    'type' => 'email',
                    'name' => 'admin_email',
                    'label' => __('integrations.admin_email', 'Email администратора'),
                    'value' => $email_settings['admin_email'] ?? '',
                    'help' => __('integrations.admin_email_help', 'Email для получения уведомлений')
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
                        'placeholder' => __('integrations.test_email_placeholder', 'Email для теста'),
                        'class' => 'px-3 py-2 border border-gray-300 rounded-md'
                    ]); ?>
                    <button type="button" onclick="testEmail()" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                        <?php echo __('integrations.test_email', 'Тест'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Analytics настройки -->
<div class="integration-tab-content hidden" data-tab="analytics">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('integrations.analytics_settings', 'Настройки Аналитики'); ?>
        </h3>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="update_analytics_settings">
            
            <?php render_form_field([
                'type' => 'text',
                'name' => 'google_analytics',
                'label' => __('integrations.google_analytics', 'Google Analytics ID'),
                'value' => $analytics_settings['google_analytics'] ?? '',
                'placeholder' => 'G-XXXXXXXXXX',
                'help' => __('integrations.google_analytics_help', 'ID отслеживания Google Analytics')
            ]); ?>
            
            <?php render_form_field([
                'type' => 'text',
                'name' => 'google_tag_manager',
                'label' => __('integrations.google_tag_manager', 'Google Tag Manager ID'),
                'value' => $analytics_settings['google_tag_manager'] ?? '',
                'placeholder' => 'GTM-XXXXXXX',
                'help' => __('integrations.google_tag_manager_help', 'ID контейнера Google Tag Manager')
            ]); ?>
            
            <?php render_form_field([
                'type' => 'text',
                'name' => 'facebook_pixel',
                'label' => __('integrations.facebook_pixel', 'Facebook Pixel ID'),
                'value' => $analytics_settings['facebook_pixel'] ?? '',
                'placeholder' => '123456789012345',
                'help' => __('integrations.facebook_pixel_help', 'ID пикселя Facebook для отслеживания')
            ]); ?>
            
            <div class="flex justify-end">
                <?php render_button([
                    'type' => 'submit',
                    'text' => __('common.save', 'Сохранить'),
                    'variant' => 'primary'
                ]); ?>
            </div>
        </form>
    </div>
</div>

<!-- Maps настройки -->
<div class="integration-tab-content hidden" data-tab="maps">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('integrations.maps_settings', 'Настройки Карт'); ?>
        </h3>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="update_maps_settings">
            
            <?php render_form_field([
                'type' => 'text',
                'name' => 'google_maps_api_key',
                'label' => __('integrations.google_maps_api_key', 'Google Maps API ключ'),
                'value' => $maps_settings['google_maps_api_key'] ?? '',
                'placeholder' => 'AIzaSyB...',
                'help' => __('integrations.google_maps_api_key_help', 'API ключ для Google Maps')
            ]); ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php render_form_field([
                    'type' => 'text',
                    'name' => 'company_latitude',
                    'label' => __('integrations.company_latitude', 'Широта'),
                    'value' => $maps_settings['company_latitude'] ?? '50.1109',
                    'help' => __('integrations.coordinates_help', 'Координаты офиса компании')
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'text',
                    'name' => 'company_longitude',
                    'label' => __('integrations.company_longitude', 'Долгота'),
                    'value' => $maps_settings['company_longitude'] ?? '8.6821'
                ]); ?>
            </div>
            
            <div class="flex justify-end">
                <?php render_button([
                    'type' => 'submit',
                    'text' => __('common.save', 'Сохранить'),
                    'variant' => 'primary'
                ]); ?>
            </div>
        </form>
    </div>
</div>

<!-- i18n настройки -->
<div class="integration-tab-content hidden" data-tab="i18n">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('integrations.i18n_settings', 'Настройки Языков'); ?>
        </h3>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo __('integrations.current_language', 'Текущий язык'); ?>
                </label>
                <?php echo generate_language_selector(); ?>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">
                    <?php echo __('integrations.available_languages', 'Доступные языки'); ?>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php foreach (get_available_languages() as $code => $lang): ?>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-2xl"><?php echo $lang['flag']; ?></span>
                                <span class="font-medium"><?php echo $lang['native_name']; ?></span>
                            </div>
                            <p class="text-sm text-gray-600"><?php echo $lang['name']; ?></p>
                            <p class="text-xs text-gray-500">Код: <?php echo $code; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Управление вкладками
    const tabButtons = document.querySelectorAll('.integration-tab-btn');
    const tabContents = document.querySelectorAll('.integration-tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Убрать активный класс со всех кнопок
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Добавить активный класс к текущей кнопке
            this.classList.add('active');
            
            // Скрыть все контенты
            tabContents.forEach(content => content.classList.add('hidden'));
            // Показать целевой контент
            document.querySelector(`[data-tab="${targetTab}"]`).classList.remove('hidden');
        });
    });
});

function testEmail() {
    const testEmail = document.querySelector('input[name="test_email"]').value;
    if (!testEmail) {
        alert('<?php echo __('integrations.enter_test_email', 'Введите email для теста'); ?>');
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

<style>
.integration-tab-btn {
    @apply px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 flex items-center;
}

.integration-tab-btn.active {
    @apply text-primary-600 border-primary-500;
}
</style>

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

