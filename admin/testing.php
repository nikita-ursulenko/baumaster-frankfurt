<?php
/**
 * Testing and Optimization Page
 * Baumaster Admin Panel - Testing and Bug Fixing
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';

// Проверка прав доступа (только для админов)
$current_user = get_current_admin_user();
if ($current_user['role'] !== 'admin') {
    header('Location: index.php?error=access_denied');
    exit;
}

// Настройки страницы
$page_title = __('testing.title', 'Тестирование и оптимизация');
$page_description = __('testing.description', 'Тестирование системы и исправление ошибок');
$active_menu = 'testing';

// Инициализация переменных
$test_results = '';
$optimization_results = '';
$bug_fix_results = '';

// Обработка POST запросов
if ($_POST && verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $action = $_POST['action'] ?? '';
    
    ob_start();
    
    switch ($action) {
        case 'run_tests':
            require_once __DIR__ . '/../tests/test_suite.php';
            $test_suite = new TestSuite();
            $test_suite->runAllTests();
            break;
            
        case 'run_security_tests':
            require_once __DIR__ . '/../tests/security_test.php';
            $security_test = new SecurityTest();
            $security_test->runSecurityTests();
            break;
            
        case 'run_optimization':
            require_once __DIR__ . '/../tools/optimizer.php';
            $optimizer = new SystemOptimizer();
            $optimizer->runOptimization();
            break;
            
        case 'run_bug_fixer':
            require_once __DIR__ . '/../tools/bug_fixer.php';
            $bug_fixer = new BugFixer();
            $bug_fixer->runBugFixer();
            break;
    }
    
    $test_results = ob_get_clean();
}

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Заголовок -->

<!-- Инструменты тестирования -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Основные тесты -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('testing.basic_tests', 'Основные тесты'); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <?php echo __('testing.basic_tests_description', 'Тестирование основных функций системы: база данных, авторизация, CRUD операции'); ?>
        </p>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="run_tests">
            
            <?php render_button([
                'type' => 'submit',
                'text' => __('testing.run_basic_tests', 'Запустить основные тесты'),
                'variant' => 'primary',
                'icon' => get_icon('play', 'w-4 h-4 mr-2')
            ]); ?>
        </form>
    </div>
    
    <!-- Тесты безопасности -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('testing.security_tests', 'Тесты безопасности'); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <?php echo __('testing.security_tests_description', 'Проверка защиты от SQL инъекций, XSS атак, CSRF и других уязвимостей'); ?>
        </p>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="run_security_tests">
            
            <?php render_button([
                'type' => 'submit',
                'text' => __('testing.run_security_tests', 'Запустить тесты безопасности'),
                'variant' => 'secondary',
                'icon' => get_icon('shield', 'w-4 h-4 mr-2')
            ]); ?>
        </form>
    </div>
    
    <!-- Оптимизация системы -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('testing.optimization', 'Оптимизация системы'); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <?php echo __('testing.optimization_description', 'Анализ и оптимизация производительности, базы данных, изображений и кэша'); ?>
        </p>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="run_optimization">
            
            <?php render_button([
                'type' => 'submit',
                'text' => __('testing.run_optimization', 'Запустить оптимизацию'),
                'variant' => 'secondary',
                'icon' => get_icon('cog', 'w-4 h-4 mr-2')
            ]); ?>
        </form>
    </div>
    
    <!-- Исправление багов -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('testing.bug_fixing', 'Исправление багов'); ?>
        </h3>
        <p class="text-sm text-gray-600 mb-4">
            <?php echo __('testing.bug_fixing_description', 'Автоматическое обнаружение и исправление ошибок в коде и конфигурации'); ?>
        </p>
        
        <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            <input type="hidden" name="action" value="run_bug_fixer">
            
            <?php render_button([
                'type' => 'submit',
                'text' => __('testing.run_bug_fixer', 'Запустить исправление багов'),
                'variant' => 'secondary',
                'icon' => get_icon('wrench', 'w-4 h-4 mr-2')
            ]); ?>
        </form>
    </div>
</div>

<!-- Результаты тестирования -->
<?php if (!empty($test_results)): ?>
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('testing.results', 'Результаты тестирования'); ?>
        </h3>
        
        <div class="bg-gray-50 rounded-lg p-4 overflow-auto max-h-96">
            <?php echo $test_results; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Статистика системы -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('testing.system_info', 'Информация о системе'); ?>
        </h3>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">PHP Version:</span>
                <span class="font-medium"><?php echo PHP_VERSION; ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Memory Limit:</span>
                <span class="font-medium"><?php echo ini_get('memory_limit'); ?></span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Max Execution Time:</span>
                <span class="font-medium"><?php echo ini_get('max_execution_time'); ?>s</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Upload Max Size:</span>
                <span class="font-medium"><?php echo ini_get('upload_max_filesize'); ?></span>
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('testing.database_info', 'Информация о базе данных'); ?>
        </h3>
        <div class="space-y-2 text-sm">
            <?php
            try {
                $db = get_database();
                $db_file = DATA_PATH . 'database.db';
                if (file_exists($db_file)) {
                    $db_size = filesize($db_file);
                    echo "<div class='flex justify-between'><span class='text-gray-600'>Size:</span><span class='font-medium'>" . format_file_size($db_size) . "</span></div>";
                }
                
                $tables = ['users', 'services', 'portfolio', 'reviews', 'blog_posts', 'settings'];
                foreach ($tables as $table) {
                    $count = count($db->select($table));
                    echo "<div class='flex justify-between'><span class='text-gray-600'>" . ucfirst($table) . ":</span><span class='font-medium'>{$count}</span></div>";
                }
            } catch (Exception $e) {
                echo "<div class='text-red-600'>Database error: " . $e->getMessage() . "</div>";
            }
            ?>
        </div>
    </div>
    
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            <?php echo __('testing.file_info', 'Информация о файлах'); ?>
        </h3>
        <div class="space-y-2 text-sm">
            <?php
            $directories = [
                'PHP Files' => glob(__DIR__ . '/../**/*.php', GLOB_BRACE),
                'CSS Files' => glob(ASSETS_PATH . 'css/*.css'),
                'JS Files' => glob(ASSETS_PATH . 'js/*.js'),
                'Images' => glob(ASSETS_PATH . 'images/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE)
            ];
            
            foreach ($directories as $type => $files) {
                $count = count($files);
                echo "<div class='flex justify-between'><span class='text-gray-600'>{$type}:</span><span class='font-medium'>{$count}</span></div>";
            }
            ?>
        </div>
    </div>
</div>

<!-- Рекомендации по улучшению -->
<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-8">
    <h3 class="text-lg font-medium text-blue-900 mb-4">
        <?php echo __('testing.recommendations', 'Рекомендации по улучшению'); ?>
    </h3>
    <div class="space-y-3 text-sm text-blue-800">
        <div class="flex items-start space-x-2">
            <span class="text-blue-600">•</span>
            <span><?php echo __('testing.rec_1', 'Регулярно запускайте тесты безопасности для проверки уязвимостей'); ?></span>
        </div>
        <div class="flex items-start space-x-2">
            <span class="text-blue-600">•</span>
            <span><?php echo __('testing.rec_2', 'Используйте оптимизатор для улучшения производительности'); ?></span>
        </div>
        <div class="flex items-start space-x-2">
            <span class="text-blue-600">•</span>
            <span><?php echo __('testing.rec_3', 'Проверяйте логи на наличие ошибок и предупреждений'); ?></span>
        </div>
        <div class="flex items-start space-x-2">
            <span class="text-blue-600">•</span>
            <span><?php echo __('testing.rec_4', 'Создавайте резервные копии перед внесением изменений'); ?></span>
        </div>
    </div>
</div>

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

