<?php
/**
 * Layout компонент для админ-панели
 * Baumaster Admin Panel
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

require_once UI_PATH . 'base.php';
require_once COMPONENTS_PATH . 'admin_js.php';
require_once ADMIN_PATH . 'auth.php';

/**
 * Рендеринг основного layout админки
 */
function render_admin_layout($options = []) {
    // Проверка авторизации
    require_auth();
    
    // Проверка времени сессии
    if (!check_session_timeout()) {
        header('Location: ' . get_admin_url('login.php?error=session_expired'));
        exit;
    }
    
    // Настройки по умолчанию
    $defaults = [
        'page_title' => __('menu.dashboard', 'Панель управления'),
        'page_description' => '',
        'active_menu' => 'dashboard',
        'content' => '',
        'additional_css' => '',
        'additional_js' => ''
    ];
    
    $opts = array_merge($defaults, $options);
    
    // Получение информации о текущем пользователе
    $current_user = get_current_user_info();
    
    // Список меню
    $menu_items = get_admin_menu_items($opts['active_menu'], $current_user);
    ?>
    <!DOCTYPE html>
    <html lang="<?php echo get_current_language(); ?>" class="h-full bg-gray-50">
    <head>
        <?php render_admin_head($opts['page_title'], $opts['page_description'], admin_css_styles(), ''); ?>
        
        <?php if ($opts['additional_css']): ?>
            <?php echo $opts['additional_css']; ?>
        <?php endif; ?>
    </head>
    <body class="h-full">
        <div class="min-h-screen bg-gray-50">
            <!-- Sidebar -->
            <?php render_admin_sidebar($menu_items, $current_user); ?>

            <!-- Main Content -->
            <div class="pl-64">
                <!-- Header -->
                <?php render_admin_header($opts['page_title'], $opts['page_description']); ?>

                <!-- Page Content -->
                <main class="p-6">
                    <div class="fade-in">
                        <?php echo $opts['content']; ?>
                    </div>
                </main>
            </div>
        </div>

        <!-- JavaScript -->
        <?php render_admin_javascript(); ?>
        
        <?php if ($opts['additional_js']): ?>
            <?php echo $opts['additional_js']; ?>
        <?php endif; ?>

        <?php if (is_debug()): ?>
        <!-- Отладочная информация -->
        <div class="fixed bottom-4 left-4 bg-black text-white text-xs p-2 rounded opacity-50 z-50">
            Debug: <?php echo get_current_language(); ?> | 
            User: <?php echo $current_user['username']; ?> (<?php echo $current_user['role']; ?>) |
            Memory: <?php echo round(memory_get_usage() / 1024 / 1024, 2); ?>MB
        </div>
        <?php endif; ?>
    </body>
    </html>
    <?php
}

/**
 * Генерация меню админки
 */
function get_admin_menu_items($active_menu = 'dashboard', $current_user = null) {
    $menu_items = [
        'dashboard' => [
            'title' => __('menu.dashboard', 'Панель управления'),
            'url' => 'index.php',
            'icon' => 'dashboard',
            'active' => $active_menu === 'dashboard'
        ],
        'services' => [
            'title' => __('menu.services', 'Услуги'),
            'url' => 'services.php',
            'icon' => 'services',
            'active' => $active_menu === 'services'
        ],
        'portfolio' => [
            'title' => __('menu.portfolio', 'Портфолио'),
            'url' => 'portfolio.php',
            'icon' => 'portfolio',
            'active' => $active_menu === 'portfolio'
        ],
        'reviews' => [
            'title' => __('menu.reviews', 'Отзывы'),
            'url' => 'reviews.php',
            'icon' => 'reviews',
            'active' => $active_menu === 'reviews'
        ],
        'blog' => [
            'title' => __('menu.blog', 'Блог'),
            'url' => 'blog.php',
            'icon' => 'blog',
            'active' => $active_menu === 'blog'
        ],
        'settings' => [
            'title' => __('menu.settings', 'Настройки'),
            'url' => 'settings.php',
            'icon' => 'cog',
            'active' => $active_menu === 'settings'
        ],
        'stats' => [
            'title' => __('menu.stats', 'Статистика'),
            'url' => 'stats.php',
            'icon' => 'statistics',
            'active' => $active_menu === 'stats'
        ],
        'seo' => [
            'title' => __('menu.seo', 'SEO'),
            'url' => 'seo_analysis.php',
            'icon' => 'search',
            'active' => $active_menu === 'seo'
        ],
        'integrations' => [
            'title' => __('menu.integrations', 'Интеграции'),
            'url' => 'integrations.php',
            'icon' => 'cog',
            'active' => $active_menu === 'integrations'
        ],
        'testing' => [
            'title' => __('menu.testing', 'Тестирование'),
            'url' => 'testing.php',
            'icon' => 'wrench',
            'active' => $active_menu === 'testing'
        ],
        'users' => [
            'title' => __('menu.users', 'Пользователи'),
            'url' => 'users.php',
            'icon' => 'users',
            'active' => $active_menu === 'users',
            'role_required' => 'admin'
        ]
    ];
    
    // Фильтрация меню по ролям
    return array_filter($menu_items, function($item) use ($current_user) {
        if (isset($item['role_required'])) {
            return user_has_role($item['role_required']);
        }
        return true;
    });
}

/**
 * Рендеринг sidebar админки
 */
function render_admin_sidebar($menu_items, $current_user) {
    ?>
    <div class="fixed inset-y-0 left-0 z-50 w-64 bg-admin-sidebar">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 px-4 bg-black bg-opacity-20">
            <div class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-primary-500 rounded-lg flex items-center justify-center">
                    <?php echo get_icon('building', 'w-5 h-5'); ?>
                </div>
                <span class="text-white font-semibold text-lg">Baumaster</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="mt-8 px-4">
            <div class="space-y-1">
                <?php foreach ($menu_items as $key => $item): ?>
                    <a 
                        href="<?php echo htmlspecialchars($item['url']); ?>" 
                        class="sidebar-item flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200 <?php echo $item['active'] ? 'active' : 'text-gray-300 hover:text-white'; ?>"
                    >
                        <?php echo get_icon($item['icon']); ?>
                        <span class="ml-3"><?php echo htmlspecialchars($item['title']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </nav>

        <!-- User Info -->
        <div class="absolute bottom-0 w-full p-4">
            <div class="bg-black bg-opacity-20 rounded-lg p-3">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">
                                <?php echo strtoupper(substr($current_user['username'], 0, 1)); ?>
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">
                            <?php echo htmlspecialchars($current_user['username']); ?>
                        </p>
                        <p class="text-xs text-gray-300 truncate">
                            <?php echo htmlspecialchars($current_user['role']); ?>
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a 
                            href="logout.php" 
                            class="text-gray-300 hover:text-white transition-colors duration-200"
                            title="<?php echo __('auth.logout', 'Выйти'); ?>"
                        >
                            <?php echo get_icon('logout'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Рендеринг header админки
 */
function render_admin_header($page_title, $page_description = '') {
    ?>
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        <?php echo htmlspecialchars($page_title); ?>
                    </h1>
                    <?php if ($page_description): ?>
                        <p class="mt-1 text-sm text-gray-500">
                            <?php echo htmlspecialchars($page_description); ?>
                        </p>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Ссылка на сайт -->
                    <?php render_button([
                        'href' => get_site_url(),
                        'text' => __('common.view_site', 'Смотреть сайт'),
                        'variant' => 'secondary',
                        'size' => 'sm',
                        'icon' => get_icon('external-link', 'w-4 h-4 mr-2'),
                        'class' => 'target="_blank"'
                    ]); ?>
                    
                    <!-- Языки -->
                    <div class="relative">
                        <select 
                            onchange="changeLanguage(this.value)"
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md"
                        >
                            <?php foreach (AVAILABLE_LANGS as $lang): ?>
                                <option value="<?php echo $lang; ?>" <?php echo $lang === get_current_language() ? 'selected' : ''; ?>>
                                    <?php echo strtoupper($lang); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <?php
}

/**
 * Кастомные CSS стили для админки
 */
function admin_css_styles() {
    return '
    <style>
        .sidebar-item:hover {
            background-color: rgba(59, 130, 246, 0.1);
        }
        
        .sidebar-item.active {
            background-color: #3b82f6;
            color: white;
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
        }
    </style>
    ';
}
?>
