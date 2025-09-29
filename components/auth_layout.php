<?php
/**
 * Layout для страниц авторизации
 * Baumaster Admin Panel
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

require_once UI_PATH . 'base.php';

/**
 * Рендеринг layout для страниц авторизации
 */
function render_auth_layout($options = []) {
    $defaults = [
        'title' => __('auth.login', 'Вход в админ-панель'),
        'description' => 'Админ-панель сайта ' . SITE_NAME,
        'show_back_link' => true,
        'content' => '',
        'additional_js' => ''
    ];
    
    $opts = array_merge($defaults, $options);
    ?>
    <!DOCTYPE html>
    <html lang="<?php echo get_current_language(); ?>">
    <head>
        <?php render_admin_head($opts['title'], $opts['description']); ?>
    </head>
    <body class="bg-gradient-to-br from-slate-100 to-slate-200 min-h-screen flex items-center justify-center p-4">
        
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-8 space-y-6">
            <!-- Логотип и заголовок -->
            <div class="text-center">
                <div class="mx-auto w-20 h-20 bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-4">
                    <?php echo get_icon('building', 'w-10 h-10'); ?>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    <?php echo __('auth.admin_panel', 'Админ-панель'); ?>
                </h1>
                <p class="text-gray-600">
                    <?php echo __('auth.login_description', 'Войдите в свой аккаунт'); ?>
                </p>
            </div>

            <!-- Основной контент -->
            <?php echo $opts['content']; ?>

            <!-- Дополнительная информация -->
            <div class="text-center">
                <p class="text-xs text-gray-500">
                    <?php echo __('auth.login_footer', 'Для получения доступа обратитесь к администратору'); ?>
                </p>
                
                <?php if ($opts['show_back_link']): ?>
                    <!-- Ссылка на главную страницу -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a 
                            href="<?php echo get_site_url(); ?>" 
                            class="text-primary-600 hover:text-primary-700 text-sm font-medium inline-flex items-center"
                        >
                            <?php echo get_icon('arrow-left', 'w-4 h-4 mr-1'); ?>
                            <?php echo __('common.back_to_site', 'Вернуться на сайт'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Дополнительный JavaScript -->
        <?php if ($opts['additional_js']): ?>
            <?php echo $opts['additional_js']; ?>
        <?php endif; ?>

        <?php if (is_debug()): ?>
        <!-- Отладочная информация -->
        <div class="fixed bottom-4 left-4 bg-black text-white text-xs p-2 rounded opacity-50">
            Debug: <?php echo get_current_language(); ?> | 
            Default admin: root/root
        </div>
        <?php endif; ?>

    </body>
    </html>
    <?php
}

/**
 * Рендеринг формы логина
 */
function render_login_form($csrf_token, $username_value = '') {
    ob_start();
    ?>
    <form method="POST" class="space-y-6" id="login-form">
        <input type="hidden" name="action" value="login">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
        
        <!-- Поле логина -->
        <?php render_input_field([
            'name' => 'username',
            'label' => __('auth.username', 'Имя пользователя'),
            'placeholder' => __('auth.username_placeholder', 'Введите логин'),
            'required' => true,
            'autocomplete' => 'username',
            'value' => $username_value
        ]); ?>

        <!-- Поле пароля -->
        <?php render_password_field([
            'name' => 'password',
            'label' => __('auth.password', 'Пароль'),
            'placeholder' => __('auth.password_placeholder', 'Введите пароль'),
            'required' => true,
            'autocomplete' => 'current-password'
        ]); ?>

        <!-- Кнопка входа -->
        <?php render_button([
            'type' => 'submit',
            'text' => __('auth.login', 'Войти'),
            'variant' => 'primary',
            'size' => 'lg',
            'class' => 'w-full transform hover:-translate-y-0.5 hover:shadow-lg'
        ]); ?>
    </form>

    <script>
        // Автофокус на поле логина
        document.getElementById('username').focus();

        // Дополнительная валидация формы
        document.getElementById('login-form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            
            if (username.length < 3) {
                e.preventDefault();
                alert('<?php echo __('auth.username_too_short', 'Логин должен содержать минимум 3 символа'); ?>');
                return;
            }
            
            if (password.length < 4) {
                e.preventDefault();
                alert('<?php echo __('auth.password_too_short', 'Пароль должен содержать минимум 4 символа'); ?>');
                return;
            }
        });
    </script>
    <?php
    return ob_get_clean();
}
?>

