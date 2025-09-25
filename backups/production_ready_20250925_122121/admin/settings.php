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
$page_title = __('settings.title', 'Настройки сайта');
$page_description = __('settings.description', 'Управление основными настройками сайта и компании');
$active_menu = 'settings';

// Инициализация переменных
$error_message = '';
$success_message = '';
$db = get_database();

// Обработка POST запросов
if ($_POST && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $category = $_POST['category'] ?? '';
    $settings_data = $_POST['settings'] ?? [];
    
    try {
        foreach ($settings_data as $key => $value) {
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

<!-- Заголовок -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">
            <?php echo __('settings.title', 'Настройки сайта'); ?>
        </h2>
        <p class="text-sm text-gray-600 mt-1">
            <?php echo __('settings.description', 'Управление основными настройками сайта и компании'); ?>
        </p>
    </div>
</div>

<!-- Вкладки настроек -->
<div class="mb-6">
    <nav class="flex space-x-8" aria-label="Tabs">
        <button class="settings-tab-btn active" data-tab="company">
            <?php echo get_icon('building', 'w-5 h-5 mr-2'); ?>
            <?php echo __('settings.tab_company', 'Компания'); ?>
        </button>
        <button class="settings-tab-btn" data-tab="seo">
            <?php echo get_icon('search', 'w-5 h-5 mr-2'); ?>
            <?php echo __('settings.tab_seo', 'SEO'); ?>
        </button>
        <button class="settings-tab-btn" data-tab="social">
            <?php echo get_icon('share', 'w-5 h-5 mr-2'); ?>
            <?php echo __('settings.tab_social', 'Соц. сети'); ?>
        </button>
        <button class="settings-tab-btn" data-tab="site">
            <?php echo get_icon('cog', 'w-5 h-5 mr-2'); ?>
            <?php echo __('settings.tab_site', 'Сайт'); ?>
        </button>
    </nav>
</div>

<!-- Информация о компании -->
<div class="settings-tab-content" data-tab="company">
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
            
            <?php render_form_field([
                'type' => 'text',
                'name' => 'settings[working_hours]',
                'label' => __('settings.working_hours', 'Рабочие часы'),
                'value' => $settings['site']['working_hours']['setting_value'] ?? ''
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

<!-- SEO настройки -->
<div class="settings-tab-content hidden" data-tab="seo">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('settings.seo_settings', 'SEO настройки'); ?>
        </h3>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="category" value="seo">
            
            <?php render_form_field([
                'type' => 'text',
                'name' => 'settings[site_title]',
                'label' => __('settings.site_title', 'Заголовок сайта'),
                'value' => $settings['seo']['site_title']['setting_value'] ?? '',
                'required' => true,
                'help' => __('settings.site_title_help', 'Отображается в заголовке браузера и поисковых системах')
            ]); ?>
            
            <?php render_form_field([
                'type' => 'textarea',
                'name' => 'settings[site_description]',
                'label' => __('settings.site_description', 'Описание сайта'),
                'value' => $settings['seo']['site_description']['setting_value'] ?? '',
                'rows' => 3,
                'help' => __('settings.site_description_help', 'Краткое описание для поисковых систем (до 160 символов)')
            ]); ?>
            
            <?php render_form_field([
                'type' => 'textarea',
                'name' => 'settings[site_keywords]',
                'label' => __('settings.site_keywords', 'Ключевые слова'),
                'value' => $settings['seo']['site_keywords']['setting_value'] ?? '',
                'rows' => 2,
                'help' => __('settings.site_keywords_help', 'Ключевые слова через запятую')
            ]); ?>
            
            <?php render_form_field([
                'type' => 'textarea',
                'name' => 'settings[google_analytics]',
                'label' => __('settings.google_analytics', 'Google Analytics'),
                'value' => $settings['seo']['google_analytics']['setting_value'] ?? '',
                'rows' => 3,
                'help' => __('settings.google_analytics_help', 'Код отслеживания Google Analytics')
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

<!-- Социальные сети -->
<div class="settings-tab-content hidden" data-tab="social">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('settings.social_networks', 'Социальные сети'); ?>
        </h3>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="category" value="social">
            
            <?php render_form_field([
                'type' => 'url',
                'name' => 'settings[facebook_url]',
                'label' => __('settings.facebook_url', 'Facebook'),
                'value' => $settings['social']['facebook_url']['setting_value'] ?? '',
                'placeholder' => 'https://www.facebook.com/yourpage'
            ]); ?>
            
            <?php render_form_field([
                'type' => 'url',
                'name' => 'settings[instagram_url]',
                'label' => __('settings.instagram_url', 'Instagram'),
                'value' => $settings['social']['instagram_url']['setting_value'] ?? '',
                'placeholder' => 'https://www.instagram.com/yourpage'
            ]); ?>
            
            <?php render_form_field([
                'type' => 'url',
                'name' => 'settings[linkedin_url]',
                'label' => __('settings.linkedin_url', 'LinkedIn'),
                'value' => $settings['social']['linkedin_url']['setting_value'] ?? '',
                'placeholder' => 'https://www.linkedin.com/company/yourcompany'
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

<!-- Настройки сайта -->
<div class="settings-tab-content hidden" data-tab="site">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('settings.site_settings', 'Настройки сайта'); ?>
        </h3>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="category" value="site">
            
            <div>
                <?php render_dropdown_field([
                    'name' => 'settings[default_language]',
                    'label' => __('settings.default_language', 'Язык по умолчанию'),
                    'value' => $settings['site']['default_language']['setting_value'] ?? 'ru',
                    'options' => [
                        ['value' => 'ru', 'text' => 'Русский'],
                        ['value' => 'de', 'text' => 'Deutsch'],
                        ['value' => 'en', 'text' => 'English']
                    ],
                    'placeholder' => 'Выберите язык'
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Управление вкладками
    const tabButtons = document.querySelectorAll('.settings-tab-btn');
    const tabContents = document.querySelectorAll('.settings-tab-content');
    
    console.log('Tab buttons found:', tabButtons.length);
    console.log('Tab contents found:', tabContents.length);
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const targetTab = this.getAttribute('data-tab');
            
            console.log('Switching to tab:', targetTab);
            
            // Убрать активный класс со всех кнопок
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Добавить активный класс к текущей кнопке
            this.classList.add('active');
            
            // Скрыть все контенты
            tabContents.forEach(content => {
                content.classList.add('hidden');
                console.log('Hiding content:', content.getAttribute('data-tab'));
            });
            
            // Показать целевой контент
            const targetContent = document.querySelector(`.settings-tab-content[data-tab="${targetTab}"]`);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                console.log('Showing content:', targetTab);
            } else {
                console.error('Target content not found for tab:', targetTab);
            }
        });
    });
});
</script>

<style>
.settings-tab-btn {
    @apply px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 flex items-center;
}

.settings-tab-btn.active {
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

