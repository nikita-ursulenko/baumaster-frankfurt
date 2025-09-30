<?php
/**
 * Страница управления FAQ
 * Baumaster Admin Panel - FAQ Management
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';
require_once __DIR__ . '/../integrations/translation/TranslationManager.php';
require_once __DIR__ . '/../components/confirmation_modal.php';

// Настройки страницы
$page_title = __('faq.title', 'Управление FAQ');
$page_description = __('faq.description', 'Управление часто задаваемыми вопросами');
$active_menu = 'faq';

// Инициализация переменных
$error_message = '';
$success_message = '';
$faq_items = [];
$current_faq = null;
$action = $_GET['action'] ?? 'list';
$faq_id = intval($_GET['id'] ?? 0);

// Получение базы данных
$db = get_database();

// Функции для работы с FAQ
function create_faq($data) {
    global $db;
    
    $errors = validate_faq_data($data);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    $faq_data = [
        'question' => sanitize_input($data['question']),
        'answer' => sanitize_input($data['answer']),
        'category' => sanitize_input($data['category'] ?? 'general'),
        'status' => sanitize_input($data['status'] ?? 'active'),
        'sort_order' => intval($data['sort_order'] ?? 0)
    ];
    
    $faq_id = $db->insert('faq', $faq_data);
    
    if ($faq_id) {
        // Автоматический перевод на немецкий язык
        try {
            $translation_manager = new TranslationManager();
            $fields_to_translate = [
                'question' => $faq_data['question'],
                'answer' => $faq_data['answer']
            ];
            
            $translated_fields = $translation_manager->autoTranslateContent('faq', $faq_id, $fields_to_translate, 'ru', 'de');
            
            if (!empty($translated_fields)) {
                write_log("FAQ translations created for ID: $faq_id", 'INFO');
            }
        } catch (Exception $e) {
            write_log("FAQ translation error: " . $e->getMessage(), 'ERROR');
        }
        
        write_log("New FAQ created: {$faq_data['question']} (ID: $faq_id)", 'INFO');
        log_user_activity('faq_create', 'faq', $faq_id);
        return [
            'success' => true,
            'faq_id' => $faq_id,
            'message' => __('faq.create_success', 'FAQ успешно создан')
        ];
    } else {
        return ['success' => false, 'errors' => ['general' => __('faq.create_error', 'Ошибка при создании FAQ')]];
    }
}

function update_faq($faq_id, $data) {
    global $db;
    
    $existing_faq = $db->select('faq', ['id' => $faq_id], ['limit' => 1]);
    if (!$existing_faq) {
        return ['success' => false, 'errors' => ['general' => __('faq.not_found', 'FAQ не найден')]];
    }
    
    $errors = validate_faq_data($data, true);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    $update_data = [
        'question' => sanitize_input($data['question']),
        'answer' => sanitize_input($data['answer']),
        'category' => sanitize_input($data['category'] ?? 'general'),
        'status' => sanitize_input($data['status'] ?? 'active'),
        'sort_order' => intval($data['sort_order'] ?? 0)
    ];
    
    if ($db->update('faq', $update_data, ['id' => $faq_id])) {
        // Обновляем переводы на немецкий язык
        try {
            $translation_manager = new TranslationManager();
            $fields_to_translate = [
                'question' => $update_data['question'],
                'answer' => $update_data['answer']
            ];
            
            $translated_fields = $translation_manager->autoTranslateContent('faq', $faq_id, $fields_to_translate, 'ru', 'de');
            
            if (!empty($translated_fields)) {
                write_log("FAQ translations updated for ID: $faq_id", 'INFO');
            }
        } catch (Exception $e) {
            write_log("FAQ translation update error: " . $e->getMessage(), 'ERROR');
        }
        
        write_log("FAQ updated: {$existing_faq['question']} (ID: $faq_id)", 'INFO');
        log_user_activity('faq_update', 'faq', $faq_id, $existing_faq, $update_data);
        return ['success' => true, 'message' => __('faq.update_success', 'FAQ успешно обновлен')];
    } else {
        return ['success' => false, 'errors' => ['general' => __('faq.update_error', 'Ошибка при обновлении FAQ')]];
    }
}

function delete_faq($faq_id) {
    global $db;
    
    $faq = $db->select('faq', ['id' => $faq_id], ['limit' => 1]);
    if (!$faq) {
        return ['success' => false, 'error' => __('faq.not_found', 'FAQ не найден')];
    }
    
    if ($db->delete('faq', ['id' => $faq_id])) {
        write_log("FAQ deleted: {$faq['question']} (ID: $faq_id)", 'WARNING');
        log_user_activity('faq_delete', 'faq', $faq_id);
        return ['success' => true, 'message' => __('faq.delete_success', 'FAQ успешно удален')];
    } else {
        return ['success' => false, 'error' => __('faq.delete_error', 'Ошибка при удалении FAQ')];
    }
}

function validate_faq_data($data, $is_update = false) {
    $errors = [];
    
    $question = $data['question'] ?? '';
    if (empty($question)) {
        $errors['question'] = __('faq.question_required', 'Вопрос обязателен');
    } elseif (strlen($question) < 5) {
        $errors['question'] = __('faq.question_too_short', 'Вопрос должен содержать минимум 5 символов');
    }
    
    $answer = $data['answer'] ?? '';
    if (empty($answer)) {
        $errors['answer'] = __('faq.answer_required', 'Ответ обязателен');
    } elseif (strlen($answer) < 10) {
        $errors['answer'] = __('faq.answer_too_short', 'Ответ должен содержать минимум 10 символов');
    }
    
    return $errors;
}

// Обработка POST запросов
if ($_POST) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = __('common.csrf_error', 'Ошибка безопасности. Попробуйте снова.');
    } else {
        $post_action = $_POST['action'] ?? '';
        
        switch ($post_action) {
            case 'create':
                $result = create_faq($_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list';
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'update':
                $result = update_faq($faq_id, $_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'delete':
                $delete_id = intval($_POST['id'] ?? 0);
                $result = delete_faq($delete_id);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list';
                } else {
                    $error_message = $result['error'];
                }
                break;
                
            case 'toggle_status':
                $toggle_id = intval($_POST['id'] ?? 0);
                $faq = $db->select('faq', ['id' => $toggle_id], ['limit' => 1]);
                if ($faq) {
                    $new_status = $faq['status'] === 'active' ? 'inactive' : 'active';
                    $db->update('faq', ['status' => $new_status], ['id' => $toggle_id]);
                    $success_message = __('faq.status_updated', 'Статус FAQ обновлен');
                    write_log("FAQ status toggled: {$faq['question']} (ID: $toggle_id) -> $new_status", 'INFO');
                } else {
                    $error_message = __('faq.not_found', 'FAQ не найден');
                }
                break;
        }
    }
}

// Обработка действий
switch ($action) {
    case 'create':
    case 'edit':
        if ($action === 'edit') {
            $current_faq = $db->select('faq', ['id' => $faq_id], ['limit' => 1]);
            if (!$current_faq) {
                $error_message = __('faq.not_found', 'FAQ не найден');
                $action = 'list';
            }
        }
        break;
        
    case 'list':
    default:
        // Фильтрация и поиск
        $filters = [];
        $search = sanitize_input($_GET['search'] ?? '');
        $status_filter = $_GET['status'] ?? '';
        $category_filter = $_GET['category'] ?? '';
        
        if (!empty($search)) {
            $filters['question LIKE'] = "%{$search}%";
        }
        if (!empty($status_filter)) {
            $filters['status'] = $status_filter;
        }
        if (!empty($category_filter)) {
            $filters['category'] = $category_filter;
        }
        // Убираем фильтр по featured, так как поле не существует в таблице
        
        $faq_items = $db->select('faq', $filters, ['order' => 'sort_order DESC, created_at DESC']);
        break;
}

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<!-- Сообщения -->
<?php render_error_message($error_message); ?>
<?php render_success_message($success_message); ?>

<?php if ($action === 'list'): ?>
    <!-- Статистика и кнопки -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6 gap-4">
        <div class="flex items-center space-x-4" style="width: 100%;">
            <?php 
            // Подсчет статистики
            $total_questions = count($faq_items);
            $active_questions = count(array_filter($faq_items, function($item) {
                return $item['status'] === 'active';
            }));
            $inactive_questions = $total_questions - $active_questions;
            
            // Статистическая карточка для FAQ
            ?>
            <!-- Мобильная статистика -->
            <div class="lg:hidden grid grid-cols-2 gap-4 w-full">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                                <?php echo get_icon('question-mark-circle', 'w-4 h-4 text-white'); ?>
                            </div>
                        </div>
                        <div class="ml-2 flex-1">
                            
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo $total_questions; ?>
                            </p>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-gray-500">
                                <?php echo __('faq.total_count', 'Всего вопросов'); ?>
                            </p>
                </div>
                
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-2 flex-1">
                            
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo $active_questions; ?>
                            </p>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-gray-500">
                                <?php echo __('faq.active', 'Активные'); ?>
                            </p>
                </div>
            </div>

            <!-- Десктопная статистика -->
            <div class="hidden lg:block bg-white shadow-sm rounded-lg border border-gray-200 p-3 min-w-[180px]">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center">
                            <?php echo get_icon('question-mark-circle', 'w-4 h-4 text-white'); ?>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-xs font-medium text-gray-500">
                            <?php echo __('faq.total_count', 'Всего вопросов'); ?>
                        </p>
                        <p class="text-lg font-semibold text-gray-900">
                            <?php echo $total_questions; ?>
                        </p>
                        <?php if ($active_questions > 0): ?>
                        <p class="text-xs text-green-600 mt-1">
                            <?php echo $active_questions; ?> активных
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <?php render_button([
                'href' => '?action=create',
                'text' => __('faq.add_new', 'Добавить вопрос'),
                'variant' => 'primary',
                'icon' => get_icon('plus', 'w-4 h-4 mr-2')
            ]); ?>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
        <!-- Основной поиск - всегда видимый -->
        <div class="mb-4">
            <form method="GET" class="flex gap-2">
                <input type="hidden" name="action" value="list">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="<?php echo __('faq.search_placeholder', 'Поиск по вопросу...'); ?>"
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                </div>
                <button type="submit" 
                        class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-200 font-medium">
                    <?php echo __('common.search', 'Поиск'); ?>
                </button>
            </form>
        </div>

        <!-- Мобильные фильтры -->
        <div class="lg:hidden">
            <button type="button" 
                    onclick="toggleMobileFilters()" 
                    class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition duration-200">
                <span class="font-medium text-gray-700">
                    <?php echo __('common.filters', 'Фильтры'); ?>
                    <?php if (!empty($status_filter) || !empty($category_filter)): ?>
                        <span class="inline-flex items-center px-2 py-1 ml-2 text-xs font-medium bg-primary-100 text-primary-800 rounded-full">
        <?php 
                            $active_filters = 0;
                            if (!empty($status_filter)) $active_filters++;
                            if (!empty($category_filter)) $active_filters++;
                            echo $active_filters;
                            ?>
                        </span>
                    <?php endif; ?>
                </span>
                <svg id="filter-arrow" class="h-5 w-5 text-gray-500 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div id="mobile-filters" class="hidden mt-4 space-y-4">
                <form method="GET" class="space-y-4">
                    <input type="hidden" name="action" value="list">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    
                    <div class="grid grid-cols-1 gap-4">
                        <!-- Статус -->
                        <div>
                            <?php render_dropdown_field([
                                'name' => 'status',
                                'label' => __('faq.status', 'Статус'),
                                'value' => $status_filter,
                                'options' => [
                                    ['value' => '', 'text' => __('common.all', 'Все')],
                                    ['value' => 'active', 'text' => __('faq.status_active', 'Активные')],
                                    ['value' => 'inactive', 'text' => __('faq.status_inactive', 'Неактивные')]
                                ],
                                'placeholder' => __('common.all', 'Все'),
                                'class' => 'w-full'
                            ]); ?>
                        </div>
                        
                        <!-- Категория -->
                        <div>
                            <?php render_dropdown_field([
                                'name' => 'category',
                                'label' => __('faq.category', 'Категория'),
                                'value' => $category_filter,
                                'options' => [
                                    ['value' => '', 'text' => __('common.all', 'Все')],
                                    ['value' => 'general', 'text' => __('faq.category_general', 'Общие')],
                                    ['value' => 'services', 'text' => __('faq.category_services', 'Услуги')],
                                    ['value' => 'portfolio', 'text' => __('faq.category_portfolio', 'Портфолио')],
                                    ['value' => 'pricing', 'text' => __('faq.category_pricing', 'Цены')],
                                    ['value' => 'technical', 'text' => __('faq.category_technical', 'Технические')],
                                    ['value' => 'support', 'text' => __('faq.category_support', 'Поддержка')]
                                ],
                                'placeholder' => __('common.all', 'Все'),
                                'class' => 'w-full'
                            ]); ?>
                        </div>
                    </div>
                    
                    <div class="flex gap-2 pt-4">
                        <button type="submit" 
                                class="flex-1 px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-200 font-medium">
                            <?php echo __('common.apply_filters', 'Применить'); ?>
                        </button>
                        <a href="?action=list" 
                           class="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200 font-medium">
                            <?php echo __('common.clear', 'Очистить'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Десктопные фильтры -->
        <div class="hidden lg:block">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <input type="hidden" name="action" value="list">
                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                
                <!-- Статус -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo __('faq.status', 'Статус'); ?>
                    </label>
                    <select name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                        <option value=""><?php echo __('common.all', 'Все'); ?></option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>
                            <?php echo __('faq.status_active', 'Активные'); ?>
                        </option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>
                            <?php echo __('faq.status_inactive', 'Неактивные'); ?>
                        </option>
                    </select>
                </div>
                
                <!-- Категория -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo __('faq.category', 'Категория'); ?>
                    </label>
                    <select name="category" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                        <option value=""><?php echo __('common.all', 'Все'); ?></option>
                        <option value="general" <?php echo $category_filter === 'general' ? 'selected' : ''; ?>>
                            <?php echo __('faq.category_general', 'Общие'); ?>
                        </option>
                        <option value="services" <?php echo $category_filter === 'services' ? 'selected' : ''; ?>>
                            <?php echo __('faq.category_services', 'Услуги'); ?>
                        </option>
                        <option value="portfolio" <?php echo $category_filter === 'portfolio' ? 'selected' : ''; ?>>
                            <?php echo __('faq.category_portfolio', 'Портфолио'); ?>
                        </option>
                        <option value="pricing" <?php echo $category_filter === 'pricing' ? 'selected' : ''; ?>>
                            <?php echo __('faq.category_pricing', 'Цены'); ?>
                        </option>
                        <option value="technical" <?php echo $category_filter === 'technical' ? 'selected' : ''; ?>>
                            <?php echo __('faq.category_technical', 'Технические'); ?>
                        </option>
                        <option value="support" <?php echo $category_filter === 'support' ? 'selected' : ''; ?>>
                            <?php echo __('faq.category_support', 'Поддержка'); ?>
                        </option>
                    </select>
                </div>
                
                <!-- Кнопка фильтра -->
                <div>
                    <button type="submit" 
                            class="w-full px-4 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition duration-200 font-medium">
                        <?php echo __('common.filter', 'Фильтр'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Список FAQ -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <?php if (empty($faq_items)): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900"><?php echo __('faq.no_faq', 'FAQ не найдены'); ?></h3>
                <p class="mt-1 text-sm text-gray-500"><?php echo __('faq.no_faq_description', 'Начните с добавления первого вопроса'); ?></p>
                <div class="mt-6">
                    <?php render_button([
                        'href' => '?action=create',
                        'text' => __('faq.add_first', 'Добавить первый вопрос'),
                        'variant' => 'primary',
                        'icon' => get_icon('plus', 'w-4 h-4 mr-2')
                    ]); ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Мобильная версия - карточки -->
            <div class="block lg:hidden p-4 space-y-4">
                <?php foreach ($faq_items as $faq): ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <!-- Заголовок карточки -->
                        <div class="p-4 border-b border-gray-100">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                        <?php echo htmlspecialchars($faq['question']); ?>
                                    </h3>
                                    
                                    <!-- Бейджи -->
                                    <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo ucfirst($faq['category']); ?>
                                        </span>
                                        
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php echo $faq['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $faq['status'] === 'active' ? 'Активный' : 'Неактивный'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Содержимое FAQ -->
                        <div class="p-4">
                            <!-- Ответ -->
                            <p class="text-gray-700 text-sm mb-4 line-clamp-3 break-words">
                                <?php echo htmlspecialchars(substr($faq['answer'], 0, 200)) . (strlen($faq['answer']) > 200 ? '...' : ''); ?>
                            </p>
                            
                            <!-- Дополнительная информация -->
                            <div class="text-xs text-gray-500 space-y-1">
                                <div class="flex items-center">
                                    <svg class="h-3 w-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Создан: <?php echo format_date($faq['created_at']); ?>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-3 w-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    Обновлен: <?php echo format_date($faq['updated_at']); ?>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-3 w-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Приоритет: <?php echo $faq['sort_order']; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Действия -->
                        <div class="p-4 border-t border-gray-100 bg-gray-50">
                            <div class="flex flex-col gap-3">
                                <!-- Основная кнопка редактирования -->
                                <div class="flex justify-center">
                                    <?php render_button([
                                        'href' => '?action=edit&id=' . $faq['id'],
                                        'text' => __('common.edit', 'Редактировать'),
                                        'variant' => 'primary',
                                        'size' => 'md',
                                        'class' => 'w-full justify-center'
                                    ]); ?>
                                </div>
                                
                                <!-- Кнопки дополнительных действий -->
                                <div class="grid grid-cols-2 gap-2">
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="id" value="<?php echo $faq['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white rounded-lg focus:ring-2 focus:ring-offset-2 transition duration-200 <?php echo $faq['status'] === 'active' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500'; ?>">
                                            <?php echo $faq['status'] === 'active' ? 'Деактивировать' : 'Активировать'; ?>
                                        </button>
                                    </form>
                                    
                                    <button type="button" 
                                            class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition duration-200" 
                                            onclick="confirmDeleteFaq(<?php echo $faq['id']; ?>, '<?php echo htmlspecialchars($faq['question'], ENT_QUOTES); ?>')">
                                        <svg class="h-4 w-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        <?php echo __('common.delete', 'Удалить'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Десктопная версия -->
            <div class="hidden lg:block divide-y divide-gray-200">
                <?php foreach ($faq_items as $faq): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-3">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            <?php echo htmlspecialchars($faq['question']); ?>
                                        </h3>
                                        
                                        <!-- Бейджи -->
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo ucfirst($faq['category']); ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Статус -->
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo $faq['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $faq['status'] === 'active' ? 'Активный' : 'Неактивный'; ?>
                                    </span>
                                </div>
                                
                                <!-- Ответ -->
                                <p class="text-gray-700 text-sm mb-3 line-clamp-3">
                                    <?php echo htmlspecialchars(substr($faq['answer'], 0, 200)) . (strlen($faq['answer']) > 200 ? '...' : ''); ?>
                                </p>
                                
                                <!-- Дополнительная информация -->
                                <div class="flex items-center text-xs text-gray-500 space-x-4">
                                    <span>Создан: <?php echo format_date($faq['created_at']); ?></span>
                                    <span>Обновлен: <?php echo format_date($faq['updated_at']); ?></span>
                                    <span>Приоритет: <?php echo $faq['sort_order']; ?></span>
                                </div>
                            </div>
                            
                            <!-- Действия -->
                            <div class="flex items-center space-x-2 ml-4">
                                <?php render_button([
                                    'href' => '?action=edit&id=' . $faq['id'],
                                    'text' => __('common.edit', 'Редактировать'),
                                    'variant' => 'secondary',
                                    'size' => 'sm'
                                ]); ?>
                                
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="id" value="<?php echo $faq['id']; ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white <?php echo $faq['status'] === 'active' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'; ?> focus:outline-none focus:ring-2 focus:ring-offset-2 <?php echo $faq['status'] === 'active' ? 'focus:ring-red-500' : 'focus:ring-green-500'; ?>">
                                        <?php echo $faq['status'] === 'active' ? 'Деактивировать' : 'Активировать'; ?>
                                    </button>
                                </form>
                                
                                <button type="button" 
                                        class="text-red-400 hover:text-red-600 p-1" 
                                        title="<?php echo __('common.delete', 'Удалить'); ?>"
                                        onclick="confirmDeleteFaq(<?php echo $faq['id']; ?>, '<?php echo htmlspecialchars($faq['question'], ENT_QUOTES); ?>')">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

<!-- JavaScript функции для удаления FAQ -->
<script>
// Делаем функции глобальными сразу
window.confirmDeleteFaq = async function(faqId, question) {
    console.log('🚀 confirmDeleteFaq вызвана:', faqId, question);
    
    // Обрезаем вопрос если он слишком длинный
    const shortQuestion = question.length > 50 ? question.substring(0, 50) + '...' : question;
    const message = `Вы уверены, что хотите удалить FAQ "${shortQuestion}"? Это действие нельзя отменить.`;
    
    // Проверяем, доступна ли функция showConfirmationModal
    if (typeof showConfirmationModal === 'function') {
        console.log('✅ Используем модальное окно');
        const confirmed = await showConfirmationModal(message, 'Удаление FAQ');
        
        if (confirmed) {
            deleteFaq(faqId);
        }
    } else {
        console.log('⚠️ Используем fallback confirm');
        // Fallback к обычному confirm
        if (confirm(message)) {
            deleteFaq(faqId);
        }
    }
};

window.deleteFaq = function(faqId) {
    console.log('🗑️ deleteFaq вызвана для ID:', faqId);
    
    // Создаем форму для отправки
    const form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'delete';
    
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'id';
    idInput.value = faqId;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?php echo $csrf_token; ?>';
    
    form.appendChild(actionInput);
    form.appendChild(idInput);
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    console.log('📤 Отправляем форму удаления FAQ...');
    form.submit();
};

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Функции удаления FAQ инициализированы');
});

// Функция для переключения мобильных фильтров
function toggleMobileFilters() {
    const filters = document.getElementById('mobile-filters');
    const arrow = document.getElementById('filter-arrow');
    
    if (filters && arrow) {
        if (filters.classList.contains('hidden')) {
            filters.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            filters.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }
}
</script>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
    <!-- Форма создания/редактирования FAQ -->
    <div class="max-w-4xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                <?php echo $action === 'create' ? __('faq.create_title', 'Добавить вопрос') : __('faq.edit_title', 'Редактировать вопрос'); ?>
            </h2>
            
            <?php render_button([
                'href' => '?action=list',
                'text' => __('common.back_to_list', 'Назад к списку'),
                'variant' => 'secondary',
                'icon' => get_icon('arrow-left', 'w-4 h-4 mr-2')
            ]); ?>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <form method="POST" class="space-y-8">
                <input type="hidden" name="action" value="<?php echo $action === 'create' ? 'create' : 'update'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <!-- Основная информация -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('faq.basic_info', 'Основная информация'); ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <?php render_textarea_field([
                            'name' => 'question',
                            'label' => __('faq.question', 'Вопрос'),
                            'placeholder' => __('faq.question_placeholder', 'Введите вопрос'),
                            'required' => true,
                            'rows' => 3,
                            'value' => $current_faq['question'] ?? ''
                        ]); ?>
                        
                        <?php render_textarea_field([
                            'name' => 'answer',
                            'label' => __('faq.answer', 'Ответ'),
                            'placeholder' => __('faq.answer_placeholder', 'Введите подробный ответ'),
                            'required' => true,
                            'rows' => 6,
                            'value' => $current_faq['answer'] ?? ''
                        ]); ?>
                    </div>
                </div>
                
                <!-- Настройки -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('faq.settings', 'Настройки'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php 
                        $category_options = [
                            ['value' => 'general', 'text' => __('faq.category_general', 'Общие')],
                            ['value' => 'services', 'text' => __('faq.category_services', 'Услуги')],
                            ['value' => 'portfolio', 'text' => __('faq.category_portfolio', 'Портфолио')],
                            ['value' => 'pricing', 'text' => __('faq.category_pricing', 'Цены')],
                            ['value' => 'technical', 'text' => __('faq.category_technical', 'Технические')],
                            ['value' => 'support', 'text' => __('faq.category_support', 'Поддержка')]
                        ];
                        
                        render_dropdown_field([
                            'name' => 'category',
                            'id' => 'category',
                            'label' => __('faq.category', 'Категория'),
                            'value' => $current_faq['category'] ?? 'general',
                            'placeholder' => __('faq.select_category', 'Выберите категорию'),
                            'options' => $category_options
                        ]); ?>
                        
                        <?php render_dropdown_field([
                            'name' => 'status',
                            'id' => 'status',
                            'label' => __('faq.status', 'Статус'),
                            'value' => $current_faq['status'] ?? 'active',
                            'placeholder' => __('faq.select_status', 'Выберите статус'),
                            'options' => [
                                ['value' => 'active', 'text' => __('faq.status_active', 'Активный')],
                                ['value' => 'inactive', 'text' => __('faq.status_inactive', 'Неактивный')]
                            ]
                        ]); ?>
                        
                        <?php render_input_field([
                            'type' => 'number',
                            'name' => 'sort_order',
                            'label' => __('faq.sort_order', 'Приоритет сортировки'),
                            'placeholder' => '0',
                            'value' => $current_faq['sort_order'] ?? '0'
                        ]); ?>
                        
                    </div>
                </div>
                
                
                <!-- Кнопки -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <?php render_button([
                        'href' => '?action=list',
                        'text' => __('common.cancel', 'Отмена'),
                        'variant' => 'secondary'
                    ]); ?>
                    
                    <?php render_button([
                        'type' => 'submit',
                        'text' => $action === 'create' ? __('faq.create_button', 'Создать вопрос') : __('faq.update_button', 'Обновить вопрос'),
                        'variant' => 'primary'
                    ]); ?>
                </div>
            </form>
        </div>
    </div>

<?php endif; ?>

<?php
// Рендерим модальное окно подтверждения
render_confirmation_modal();
?>

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
