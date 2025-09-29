<?php
/**
 * Страница настроек сайта
 * Baumaster Admin Panel
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';

// Проверка прав доступа (только для админов)
$current_user = get_current_admin_user();
if (!has_permission('settings.edit', $current_user)) {
    header('Location: index.php?error=access_denied');
    exit;
}

// Настройки страницы
$page_title = __('settings.title', 'О компании');
$page_description = __('settings.description', 'Управление информацией о компании');
$active_menu = 'settings';

// Инициализация переменных
$error_message = '';
$success_message = '';
$db = get_database();


// Обработка POST запросов
if ($_POST && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $category = $_POST['category'] ?? '';
    $settings_data = $_POST['settings'] ?? [];
    $working_days = $_POST['working_days'] ?? [];
    
    try {
        // Обработка обычных настроек
        foreach ($settings_data as $key => $value) {
            // Обработка обычных настроек
            if (true) {
                // Обычные настройки
            $existing = $db->select('settings', ['setting_key' => $key], ['limit' => 1]);
            
            if ($existing) {
                $db->update('settings', 
                    ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 
                    ['setting_key' => $key]
                );
            } else {
                $db->insert('settings', [
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'category' => $category
                ]);
                }
            }
        }
        
        // Обработка рабочих часов (упрощенная версия)
        if ($category === 'company') {
            // Сохраняем настройки для будней, субботы и воскресенья
            $working_hours_keys = [
                'working_hours_weekdays_from',
                'working_hours_weekdays_to', 
                'working_hours_saturday_from',
                'working_hours_saturday_to',
                'working_hours_sunday_from',
                'working_hours_sunday_to',
                'sunday_working'
            ];
            
            foreach ($working_hours_keys as $key) {
                $value = $settings_data[$key] ?? '';
                
                $existing = $db->select('settings', ['setting_key' => $key], ['limit' => 1]);
                if ($existing) {
                    $db->update('settings', 
                        ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 
                        ['setting_key' => $key]
                    );
                } else {
                    $db->insert('settings', [
                        'setting_key' => $key,
                        'setting_value' => $value,
                        'category' => 'company'
                    ]);
                }
            }
        }
        
        $success_message = __('settings.update_success', 'Настройки успешно обновлены');
        log_user_activity('settings_update', 'settings', 0);
        
        
    } catch (Exception $e) {
        $error_message = __('settings.update_error', 'Ошибка при обновлении настроек');
        write_log("Settings update error: " . $e->getMessage(), 'ERROR');
        
    }
}

// Получение текущих настроек
$settings = [];
$all_settings = $db->select('settings', [], ['order' => 'category, setting_key']);

foreach ($all_settings as $setting) {
    $settings[$setting['category']][$setting['setting_key']] = $setting;
}

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Сообщения -->
<?php render_error_message($error_message); ?>
<?php render_success_message($success_message); ?>


<!-- Вкладки настроек -->

<!-- Информация о компании -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('settings.company_info', 'Информация о компании'); ?>
        </h3>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="category" value="company">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php render_form_field([
                    'type' => 'text',
                    'name' => 'settings[company_name]',
                    'label' => __('settings.company_name', 'Название компании'),
                    'value' => $settings['company']['company_name']['setting_value'] ?? '',
                    'required' => true
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'email',
                    'name' => 'settings[company_email]',
                    'label' => __('settings.company_email', 'Email компании'),
                    'value' => $settings['company']['company_email']['setting_value'] ?? '',
                    'required' => true
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'text',
                    'name' => 'settings[company_phone]',
                    'label' => __('settings.company_phone', 'Телефон'),
                    'value' => $settings['company']['company_phone']['setting_value'] ?? ''
                ]); ?>
                
                <?php render_form_field([
                    'type' => 'text',
                    'name' => 'settings[company_address]',
                    'label' => __('settings.company_address', 'Адрес'),
                    'value' => $settings['company']['company_address']['setting_value'] ?? ''
                ]); ?>
            </div>
            
            <?php render_form_field([
                'type' => 'textarea',
                'name' => 'settings[company_description]',
                'label' => __('settings.company_description', 'Описание компании'),
                'value' => $settings['company']['company_description']['setting_value'] ?? '',
                'rows' => 3
            ]); ?>
            
            <!-- Рабочие часы -->
            <div class="space-y-4">
                <h4 class="text-md font-medium text-gray-900"><?php echo __('settings.working_hours', 'Рабочие часы'); ?></h4>
                
                <!-- Упрощенная форма -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Будни (ПН-ПТ)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">С</label>
                                <input type="time" 
                                       name="settings[working_hours_weekdays_from]" 
                                       value="<?php echo $settings['company']['working_hours_weekdays_from']['setting_value'] ?? '08:00'; ?>"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">До</label>
                                <input type="time" 
                                       name="settings[working_hours_weekdays_to]" 
                                       value="<?php echo $settings['company']['working_hours_weekdays_to']['setting_value'] ?? '20:00'; ?>"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Суббота (СБ)</label>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">С</label>
                                <input type="time" 
                                       name="settings[working_hours_saturday_from]" 
                                       value="<?php echo $settings['company']['working_hours_saturday_from']['setting_value'] ?? '09:00'; ?>"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">До</label>
                                <input type="time" 
                                       name="settings[working_hours_saturday_to]" 
                                       value="<?php echo $settings['company']['working_hours_saturday_to']['setting_value'] ?? '15:00'; ?>"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Воскресенье (ВС)</label>
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="sunday_working" 
                                   name="settings[sunday_working]" 
                                   value="1" 
                                   <?php echo ($settings['company']['sunday_working']['setting_value'] ?? '0') == '1' ? 'checked' : ''; ?>
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="sunday_working" class="ml-2 text-sm text-gray-600">Работаем в воскресенье</label>
                        </div>
                        <div id="sunday-times" class="mt-3 grid grid-cols-2 gap-3" style="<?php echo ($settings['company']['sunday_working']['setting_value'] ?? '0') == '1' ? '' : 'display: none;'; ?>">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">С</label>
                                <input type="time" 
                                       name="settings[working_hours_sunday_from]" 
                                       value="<?php echo $settings['company']['working_hours_sunday_from']['setting_value'] ?? '10:00'; ?>"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">До</label>
                                <input type="time" 
                                       name="settings[working_hours_sunday_to]" 
                                       value="<?php echo $settings['company']['working_hours_sunday_to']['setting_value'] ?? '16:00'; ?>"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Предварительный просмотр -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Предварительный просмотр:</h5>
                    <div id="working-hours-preview" class="text-sm text-gray-600">
                        <!-- Здесь будет отображаться предварительный просмотр -->
                    </div>
                </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функция показа уведомлений
    window.showNotification = function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Автоматически скрываем через 3 секунды
        setTimeout(() => {
            notification.remove();
        }, 3000);
    };
    
    // Управление рабочими часами (упрощенная версия)
    const previewElement = document.getElementById('working-hours-preview');
    const sundayCheckbox = document.getElementById('sunday_working');
    const sundayTimes = document.getElementById('sunday-times');
    
    function updateWorkingHoursPreview() {
        // Получаем значения из полей
        const weekdaysFrom = document.querySelector('input[name="settings[working_hours_weekdays_from]"]').value || '08:00';
        const weekdaysTo = document.querySelector('input[name="settings[working_hours_weekdays_to]"]').value || '20:00';
        const saturdayFrom = document.querySelector('input[name="settings[working_hours_saturday_from]"]').value || '09:00';
        const saturdayTo = document.querySelector('input[name="settings[working_hours_saturday_to]"]').value || '15:00';
        const sundayFrom = document.querySelector('input[name="settings[working_hours_sunday_from]"]').value || '10:00';
        const sundayTo = document.querySelector('input[name="settings[working_hours_sunday_to]"]').value || '16:00';
        const sundayWorking = sundayCheckbox.checked;
        
        // Формируем предварительный просмотр
        let previewParts = [];
        
        // Будни
        previewParts.push(`ПН-ПТ ${weekdaysFrom}-${weekdaysTo}`);
        
        // Суббота
        previewParts.push(`СБ ${saturdayFrom}-${saturdayTo}`);
        
        // Воскресенье
        if (sundayWorking) {
            previewParts.push(`ВС ${sundayFrom}-${sundayTo}`);
        } else {
            previewParts.push('ВС - X');
        }
        
        if (previewElement) {
            previewElement.innerHTML = previewParts.join('<br>');
        }
    }
    
    // Обработчик для чекбокса воскресенья
    if (sundayCheckbox) {
        sundayCheckbox.addEventListener('change', function() {
            if (this.checked) {
                sundayTimes.style.display = 'block';
            } else {
                sundayTimes.style.display = 'none';
            }
            updateWorkingHoursPreview();
        });
    }
    
    // Обработчики для полей времени
    const timeInputs = document.querySelectorAll('input[type="time"]');
    timeInputs.forEach(input => {
        input.addEventListener('change', updateWorkingHoursPreview);
        input.addEventListener('input', updateWorkingHoursPreview);
    });
    
    // Инициализация предварительного просмотра
    updateWorkingHoursPreview();
});
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

