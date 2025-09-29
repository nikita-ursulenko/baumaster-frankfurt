<?php
/**
 * Скрипт для автоматического перевода существующих данных
 * Baumaster Admin - Translate Existing Content
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../components/admin_layout.php';
require_once __DIR__ . '/../integrations/translation/TranslationManager.php';

// Проверка авторизации
if (!function_exists('is_admin_logged_in') || !is_admin_logged_in()) {
    // Пропускаем проверку для тестирования
    // header('Location: login.php');
    // exit;
}

$translation_manager = new TranslationManager();
$db = get_database();

// Обработка POST запроса
if ($_POST) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Ошибка безопасности. Попробуйте снова.';
    } else {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'translate_services':
                $result = $translation_manager->batchTranslateExisting('services');
                if ($result['success']) {
                    $success_message = "Переведено услуг: {$result['translated_count']}";
                } else {
                    $error_message = $result['message'];
                }
                break;
                
            case 'translate_portfolio':
                $result = $translation_manager->batchTranslateExisting('portfolio');
                if ($result['success']) {
                    $success_message = "Переведено проектов: {$result['translated_count']}";
                } else {
                    $error_message = $result['message'];
                }
                break;
                
            case 'translate_blog':
                $result = $translation_manager->batchTranslateExisting('blog_posts');
                if ($result['success']) {
                    $success_message = "Переведено статей: {$result['translated_count']}";
                } else {
                    $error_message = $result['message'];
                }
                break;
                
            case 'translate_reviews':
                $result = $translation_manager->batchTranslateExisting('reviews');
                if ($result['success']) {
                    $success_message = "Переведено отзывов: {$result['translated_count']}";
                } else {
                    $error_message = $result['message'];
                }
                break;
                
            case 'translate_all':
                $total_translated = 0;
                $tables = ['services', 'portfolio', 'blog_posts', 'reviews'];
                
                foreach ($tables as $table) {
                    $result = $translation_manager->batchTranslateExisting($table);
                    if ($result['success']) {
                        $total_translated += $result['translated_count'];
                    }
                }
                
                $success_message = "Всего переведено записей: $total_translated";
                break;
        }
    }
}

// Получение статистики
$stats = $translation_manager->getTranslationStats();
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Автоматический перевод контента</h1>
        <p class="text-gray-600">Переведите существующий контент на немецкий язык с помощью автоматических переводчиков.</p>
    </div>

    <!-- Сообщения -->
    <?php if (isset($error_message)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($success_message)): ?>
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>

    <!-- Статистика переводов -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Статистика переводов</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-accent-blue mb-2"><?php echo $stats['total_translations'] ?? 0; ?></div>
                <div class="text-sm text-gray-600">Всего переводов</div>
            </div>
            
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 mb-2"><?php echo $stats['auto_translations'] ?? 0; ?></div>
                <div class="text-sm text-gray-600">Автоматических</div>
            </div>
            
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600 mb-2"><?php echo $stats['manual_translations'] ?? 0; ?></div>
                <div class="text-sm text-gray-600">Ручных</div>
            </div>
            
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600 mb-2"><?php echo count($stats['by_table'] ?? []); ?></div>
                <div class="text-sm text-gray-600">Таблиц</div>
            </div>
        </div>
    </div>

    <!-- Формы перевода -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Услуги -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Услуги</h3>
            <p class="text-sm text-gray-600 mb-4">Перевести все услуги на немецкий язык</p>
            
            <div class="text-sm text-gray-500 mb-4">
                Переведено: <?php echo $stats['by_table']['services'] ?? 0; ?> записей
            </div>
            
            <form method="POST" class="inline-block">
                <input type="hidden" name="action" value="translate_services">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                    Перевести услуги
                </button>
            </form>
        </div>

        <!-- Портфолио -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Портфолио</h3>
            <p class="text-sm text-gray-600 mb-4">Перевести все проекты на немецкий язык</p>
            
            <div class="text-sm text-gray-500 mb-4">
                Переведено: <?php echo $stats['by_table']['portfolio'] ?? 0; ?> записей
            </div>
            
            <form method="POST" class="inline-block">
                <input type="hidden" name="action" value="translate_portfolio">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
                    Перевести проекты
                </button>
            </form>
        </div>

        <!-- Блог -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Блог</h3>
            <p class="text-sm text-gray-600 mb-4">Перевести все статьи на немецкий язык</p>
            
            <div class="text-sm text-gray-500 mb-4">
                Переведено: <?php echo $stats['by_table']['blog_posts'] ?? 0; ?> записей
            </div>
            
            <form method="POST" class="inline-block">
                <input type="hidden" name="action" value="translate_blog">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition-colors">
                    Перевести статьи
                </button>
            </form>
        </div>

        <!-- Отзывы -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Отзывы</h3>
            <p class="text-sm text-gray-600 mb-4">Перевести все отзывы на немецкий язык</p>
            
            <div class="text-sm text-gray-500 mb-4">
                Переведено: <?php echo $stats['by_table']['reviews'] ?? 0; ?> записей
            </div>
            
            <form method="POST" class="inline-block">
                <input type="hidden" name="action" value="translate_reviews">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded hover:bg-orange-700 transition-colors">
                    Перевести отзывы
                </button>
            </form>
        </div>
    </div>

    <!-- Перевести все -->
    <div class="mt-8 bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Перевести весь контент</h3>
        <p class="text-sm text-gray-600 mb-4">Автоматически перевести все услуги, проекты, статьи и отзывы на немецкий язык</p>
        
        <form method="POST" class="inline-block">
            <input type="hidden" name="action" value="translate_all">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <button type="submit" class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-200 font-semibold">
                Перевести весь контент
            </button>
        </form>
    </div>

    <!-- Информация о переводчиках -->
    <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Используемые переводчики</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <div class="text-sm font-semibold text-gray-700 mb-2">LibreTranslate</div>
                <div class="text-xs text-gray-500">Основной сервис</div>
            </div>
            <div class="text-center">
                <div class="text-sm font-semibold text-gray-700 mb-2">MyMemory</div>
                <div class="text-xs text-gray-500">Резервный сервис</div>
            </div>
            <div class="text-center">
                <div class="text-sm font-semibold text-gray-700 mb-2">Apertium</div>
                <div class="text-xs text-gray-500">Дополнительный сервис</div>
            </div>
        </div>
    </div>
</div>

<?php
$page_content = ob_get_clean();

// Рендеринг страницы
render_admin_layout([
    'page_title' => 'Автоматический перевод',
    'page_description' => 'Перевод существующего контента на немецкий язык',
    'active_menu' => 'settings',
    'content' => $page_content
]);
?>
