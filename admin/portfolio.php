<?php
/**
 * Страница управления портфолио
 * Baumaster Admin Panel - Portfolio Management
 */

// Подключение базовых файлов
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';
require_once __DIR__ . '/../components/confirmation_modal.php';

// Настройки страницы
$page_title = __('portfolio.title', 'Управление портфолио');
$page_description = __('portfolio.description', 'Создание, редактирование и управление проектами портфолио');
$active_menu = 'portfolio';

// Инициализация переменных
$error_message = '';
$success_message = '';
$projects = [];
$current_project = null;
$action = $_GET['action'] ?? 'list';
$project_id = intval($_GET['id'] ?? 0);

// Получение базы данных
$db = get_database();

// Функции для работы с портфолио
function create_project($data) {
    global $db;
    
    // Валидация данных
    $errors = validate_project_data($data);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Проверка на дублирование по названию
    $existing_project = $db->select('portfolio', ['title' => $data['title']], ['limit' => 1]);
    if ($existing_project) {
        return ['success' => false, 'errors' => ['title' => 'Проект с таким названием уже существует']];
    }
    
    // Обработка загрузки главного изображения
    $featured_image = '';
    if (!empty($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handle_image_upload($_FILES['featured_image'], 'portfolio');
        if ($upload_result['success']) {
            $featured_image = $upload_result['filename'];
        } else {
            return ['success' => false, 'errors' => ['featured_image' => $upload_result['error']]];
        }
    }
    
    // Обработка загрузки галереи изображений
    $gallery = [];
    if (!empty($_FILES['gallery_images']) && $_FILES['gallery_images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handle_multiple_image_upload($_FILES['gallery_images'], 'portfolio');
        if ($upload_result['success'] && !empty($upload_result['results'])) {
            $gallery = array_column($upload_result['results'], 'filename');
        } else {
            return ['success' => false, 'errors' => ['gallery_images' => implode(', ', $upload_result['errors'])]];
        }
    }
    
    // Обработка загрузки изображений "До" и "После"
    $before_image = '';
    $after_image = '';
    if (!empty($_FILES['before_image']) && $_FILES['before_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handle_image_upload($_FILES['before_image'], 'portfolio');
        if ($upload_result['success']) {
            $before_image = $upload_result['filename'];
        } else {
            return ['success' => false, 'errors' => ['before_image' => $upload_result['error']]];
        }
    }
    
    if (!empty($_FILES['after_image']) && $_FILES['after_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handle_image_upload($_FILES['after_image'], 'portfolio');
        if ($upload_result['success']) {
            $after_image = $upload_result['filename'];
        } else {
            return ['success' => false, 'errors' => ['after_image' => $upload_result['error']]];
        }
    }
    
    // Обработка технической информации
    $technical_info = [
        'rooms' => intval($data['rooms'] ?? 0),
        'bathrooms' => intval($data['bathrooms'] ?? 0),
        'year' => intval($data['project_year'] ?? date('Y')),
        'style' => sanitize_input($data['project_style'] ?? ''),
        'features' => array_filter(explode(',', $data['features'] ?? ''))
    ];
    
    // Обработка тегов
    $tags = array_filter(array_map('trim', explode(',', $data['tags'] ?? '')));
    
    // Подготовка данных для сохранения
    $project_data = [
        'title' => sanitize_input($data['title']),
        'description' => sanitize_input($data['description']),
        'category' => sanitize_input($data['category']),
        'completion_date' => !empty($data['completion_date']) ? $data['completion_date'] : null,
        'area' => sanitize_input($data['area'] ?? ''),
        'duration' => sanitize_input($data['duration'] ?? ''),
        'budget' => !empty($data['budget']) ? floatval($data['budget']) : null,
        'client_name' => sanitize_input($data['client_name'] ?? ''),
        'location' => sanitize_input($data['location'] ?? ''),
        'featured_image' => $featured_image,
        'gallery' => json_encode($gallery),
        'technical_info' => json_encode($technical_info),
        'before_after' => json_encode([
            'before' => $before_image,
            'after' => $after_image
        ]),
        'tags' => json_encode($tags),
        'status' => sanitize_input($data['status'] ?? 'active'),
        'featured' => intval($data['featured'] ?? 0),
        'sort_order' => intval($data['sort_order'] ?? 0),
        'meta_title' => sanitize_input($data['meta_title'] ?? ''),
        'meta_description' => sanitize_input($data['meta_description'] ?? '')
    ];
    
    // Сохранение в базе данных
    $project_id = $db->insert('portfolio', $project_data);
    
    if ($project_id) {
        // АВТОМАТИЧЕСКИЙ ПЕРЕВОД
        try {
            require_once __DIR__ . '/../integrations/translation/TranslationManager.php';
            $translation_manager = new TranslationManager();
            $translation_manager->autoTranslateContent('portfolio', $project_id, [
                'title' => $project_data['title'],
                'description' => $project_data['description'],
                'area' => $project_data['area'],
                'duration' => $project_data['duration'],
                'client_name' => $project_data['client_name'] ?? '',
                'meta_title' => $project_data['meta_title'],
                'meta_description' => $project_data['meta_description']
            ]);
            write_log("Auto-translation completed for portfolio project ID: $project_id", 'INFO');
        } catch (Exception $e) {
            write_log("Auto-translation failed for portfolio project ID: $project_id - " . $e->getMessage(), 'WARNING');
        }
        
        // Логирование
        write_log("New portfolio project created: {$project_data['title']} (ID: $project_id)", 'INFO');
        log_user_activity('portfolio_create', 'portfolio', $project_id);
        
        return [
            'success' => true,
            'project_id' => $project_id,
            'message' => __('portfolio.create_success', 'Проект успешно создан')
        ];
    } else {
        return ['success' => false, 'errors' => ['general' => __('portfolio.create_error', 'Ошибка при создании проекта')]];
    }
}

function update_project($project_id, $data) {
    global $db;
    
    // Проверка существования проекта
    $existing_project = $db->select('portfolio', ['id' => $project_id], ['limit' => 1]);
    if (!$existing_project) {
        return ['success' => false, 'errors' => ['general' => __('portfolio.not_found', 'Проект не найден')]];
    }
    
    // Валидация данных
    $errors = validate_project_data($data, true);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Обработка загрузки главного изображения
    $featured_image = $existing_project['featured_image'];
    if (!empty($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handle_image_upload($_FILES['featured_image'], 'portfolio');
        if ($upload_result['success']) {
            $featured_image = $upload_result['filename'];
        } else {
            return ['success' => false, 'errors' => ['featured_image' => $upload_result['error']]];
        }
    }
    
    // Обработка загрузки галереи изображений
    $gallery = json_decode($existing_project['gallery'], true) ?? [];
    if (!empty($_FILES['gallery_images']) && $_FILES['gallery_images']['error'][0] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handle_multiple_image_upload($_FILES['gallery_images'], 'portfolio');
        if ($upload_result['success'] && !empty($upload_result['results'])) {
            $new_gallery = array_column($upload_result['results'], 'filename');
            $gallery = array_merge($gallery, $new_gallery);
        } else {
            return ['success' => false, 'errors' => ['gallery_images' => implode(', ', $upload_result['errors'])]];
        }
    }
    
    // Обработка удаления изображений из галереи
    if (!empty($data['current_gallery'])) {
        $current_gallery = json_decode($data['current_gallery'], true) ?? [];
        $gallery = array_intersect($gallery, $current_gallery);
    }
    
    // Обработка загрузки изображений "До" и "После"
    $before_after = json_decode($existing_project['before_after'], true) ?? ['before' => '', 'after' => ''];
    $before_image = $before_after['before'];
    $after_image = $before_after['after'];
    
    if (!empty($_FILES['before_image']) && $_FILES['before_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handle_image_upload($_FILES['before_image'], 'portfolio');
        if ($upload_result['success']) {
            $before_image = $upload_result['filename'];
        } else {
            return ['success' => false, 'errors' => ['before_image' => $upload_result['error']]];
        }
    }
    
    if (!empty($_FILES['after_image']) && $_FILES['after_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = handle_image_upload($_FILES['after_image'], 'portfolio');
        if ($upload_result['success']) {
            $after_image = $upload_result['filename'];
        } else {
            return ['success' => false, 'errors' => ['after_image' => $upload_result['error']]];
        }
    }
    
    // Обработка технической информации
    $technical_info = [
        'rooms' => intval($data['rooms'] ?? 0),
        'bathrooms' => intval($data['bathrooms'] ?? 0),
        'year' => intval($data['project_year'] ?? date('Y')),
        'style' => sanitize_input($data['project_style'] ?? ''),
        'features' => array_filter(explode(',', $data['features'] ?? ''))
    ];
    
    // Обработка тегов
    $tags = array_filter(array_map('trim', explode(',', $data['tags'] ?? '')));
    
    // Подготовка данных для обновления
    $update_data = [
        'title' => sanitize_input($data['title']),
        'description' => sanitize_input($data['description']),
        'category' => sanitize_input($data['category']),
        'completion_date' => !empty($data['completion_date']) ? $data['completion_date'] : null,
        'area' => sanitize_input($data['area'] ?? ''),
        'duration' => sanitize_input($data['duration'] ?? ''),
        'budget' => !empty($data['budget']) ? floatval($data['budget']) : null,
        'client_name' => sanitize_input($data['client_name'] ?? ''),
        'location' => sanitize_input($data['location'] ?? ''),
        'featured_image' => $featured_image,
        'gallery' => json_encode($gallery),
        'technical_info' => json_encode($technical_info),
        'before_after' => json_encode([
            'before' => $before_image,
            'after' => $after_image
        ]),
        'tags' => json_encode($tags),
        'status' => sanitize_input($data['status'] ?? 'active'),
        'featured' => intval($data['featured'] ?? 0),
        'sort_order' => intval($data['sort_order'] ?? 0),
        'meta_title' => sanitize_input($data['meta_title'] ?? ''),
        'meta_description' => sanitize_input($data['meta_description'] ?? '')
    ];
    
    // Обновление в базе данных
    if ($db->update('portfolio', $update_data, ['id' => $project_id])) {
        // Логирование
        write_log("Portfolio project updated: {$existing_project['title']} (ID: $project_id)", 'INFO');
        log_user_activity('portfolio_update', 'portfolio', $project_id, $existing_project, $update_data);
        
        return ['success' => true, 'message' => __('portfolio.update_success', 'Проект успешно обновлен')];
    } else {
        return ['success' => false, 'errors' => ['general' => __('portfolio.update_error', 'Ошибка при обновлении проекта')]];
    }
}

function delete_project($project_id) {
    global $db;
    
    // Проверка существования проекта
    $project = $db->select('portfolio', ['id' => $project_id], ['limit' => 1]);
    if (!$project) {
        return ['success' => false, 'error' => __('portfolio.not_found', 'Проект не найден')];
    }
    
    // Удаление из базы данных
    if ($db->delete('portfolio', ['id' => $project_id])) {
        // Логирование
        write_log("Portfolio project deleted: {$project['title']} (ID: $project_id)", 'WARNING');
        log_user_activity('portfolio_delete', 'portfolio', $project_id);
        
        return ['success' => true, 'message' => __('portfolio.delete_success', 'Проект успешно удален')];
    } else {
        return ['success' => false, 'error' => __('portfolio.delete_error', 'Ошибка при удалении проекта')];
    }
}

function validate_project_data($data, $is_update = false) {
    $errors = [];
    
    // Валидация названия
    $title = $data['title'] ?? '';
    if (empty($title)) {
        $errors['title'] = __('portfolio.title_required', 'Название проекта обязательно');
    } elseif (strlen($title) < 5) {
        $errors['title'] = __('portfolio.title_too_short', 'Название должно содержать минимум 5 символов');
    } elseif (strlen($title) > 255) {
        $errors['title'] = __('portfolio.title_too_long', 'Название должно содержать максимум 255 символов');
    }
    
    // Валидация описания
    $description = $data['description'] ?? '';
    if (empty($description)) {
        $errors['description'] = __('portfolio.description_required', 'Описание проекта обязательно');
    } elseif (strlen($description) < 20) {
        $errors['description'] = __('portfolio.description_too_short', 'Описание должно содержать минимум 20 символов');
    }
    
    // Валидация категории
    $category = $data['category'] ?? '';
    if (empty($category)) {
        $errors['category'] = __('portfolio.category_required', 'Категория проекта обязательна');
    }
    
    // Валидация бюджета
    $budget = $data['budget'] ?? '';
    if (!empty($budget) && !is_numeric($budget)) {
        $errors['budget'] = __('portfolio.budget_invalid', 'Бюджет должен быть числом');
    } elseif (!empty($budget) && floatval($budget) < 0) {
        $errors['budget'] = __('portfolio.budget_negative', 'Бюджет не может быть отрицательным');
    }
    
    // Валидация даты завершения
    $completion_date = $data['completion_date'] ?? '';
    if (!empty($completion_date) && !strtotime($completion_date)) {
        $errors['completion_date'] = __('portfolio.date_invalid', 'Неверный формат даты');
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
                $result = create_project($_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list'; // Возвращаемся к списку
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'update':
                $result = update_project($project_id, $_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'delete':
                $delete_id = intval($_POST['id'] ?? 0);
                $result = delete_project($delete_id);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list'; // Возвращаемся к списку
                } else {
                    $error_message = $result['error'];
                }
                break;
                
            case 'toggle_status':
                $project = $db->select('portfolio', ['id' => $project_id], ['limit' => 1]);
                if ($project) {
                    $new_status = $project['status'] === 'active' ? 'inactive' : 'active';
                    $db->update('portfolio', ['status' => $new_status], ['id' => $project_id]);
                    $success_message = __('portfolio.status_updated', 'Статус проекта обновлен');
                }
                break;
                
            case 'toggle_featured':
                $project = $db->select('portfolio', ['id' => $project_id], ['limit' => 1]);
                if ($project) {
                    $new_featured = $project['featured'] ? 0 : 1;
                    $db->update('portfolio', ['featured' => $new_featured], ['id' => $project_id]);
                    $success_message = __('portfolio.featured_updated', 'Статус "Рекомендуемый" обновлен');
                }
                break;
                
            case 'change_language':
                if (set_language($_POST['language'] ?? '')) {
                    json_response(['success' => true]);
                }
                json_response(['success' => false]);
                break;
        }
    }
}

// Обработка действий
switch ($action) {
    case 'create':
    case 'edit':
        if ($action === 'edit') {
            $current_project = $db->select('portfolio', ['id' => $project_id], ['limit' => 1]);
            if (!$current_project) {
                $error_message = __('portfolio.not_found', 'Проект не найден');
                $action = 'list';
            } else {
                // Декодирование JSON данных для формы
                $current_project['gallery'] = json_decode($current_project['gallery'], true) ?? [];
                $current_project['technical_info'] = json_decode($current_project['technical_info'], true) ?? [];
                $current_project['before_after'] = json_decode($current_project['before_after'], true) ?? [];
                $current_project['tags'] = json_decode($current_project['tags'], true) ?? [];
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
        $featured_filter = $_GET['featured'] ?? '';
        
        if (!empty($search)) {
            $filters['_search'] = [
                'field' => 'title',
                'value' => $search
            ];
        }
        if (!empty($status_filter)) {
            $filters['status'] = $status_filter;
        }
        if (!empty($category_filter)) {
            $filters['category'] = $category_filter;
        }
        if (!empty($featured_filter)) {
            $filters['featured'] = intval($featured_filter);
        }
        
        // Получение списка проектов
        $projects = $db->select('portfolio', $filters, ['order' => 'sort_order DESC, featured DESC, created_at DESC']);
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
        <div class="flex items-center space-x-4" style="
    width: 100%;
">
            <?php 
            // Подсчет статистики
            $total_projects = count($projects);
            $active_projects = count(array_filter($projects, function($project) {
                return $project['status'] === 'active';
            }));
            $featured_projects = count(array_filter($projects, function($project) {
                return $project['featured'] == 1;
            }));
            
            // Статистическая карточка для портфолио
            ?>
            <!-- Мобильная статистика -->
            <div class="lg:hidden grid grid-cols-2 gap-4 w-full">
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-3" >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                <?php echo get_icon('portfolio', 'w-4 h-4 text-white'); ?>
                            </div>
                        </div>
                        <div class="ml-2 flex-1">
                            
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo $total_projects; ?>
                            </p>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-gray-500">
                                <?php echo __('portfolio.total_count', 'Всего проектов'); ?>
                            </p>
                </div>
                
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-3">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-2 flex-1">
                           
                            <p class="text-lg font-semibold text-gray-900">
                                <?php echo $featured_projects; ?>
                            </p>
                        </div>
                    </div>
                    <p class="text-xs font-medium text-gray-500">
                                <?php echo __('portfolio.featured', 'Рекомендуемые'); ?>
                            </p>
                </div>
            </div>

            <!-- Десктопная статистика -->
            <div class="hidden lg:block bg-white shadow-sm rounded-lg border border-gray-200 p-3 min-w-[180px]">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <?php echo get_icon('portfolio', 'w-4 h-4 text-white'); ?>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-xs font-medium text-gray-500">
                            <?php echo __('portfolio.total_count', 'Всего проектов'); ?>
                        </p>
                        <p class="text-lg font-semibold text-gray-900">
                            <?php echo $total_projects; ?>
                        </p>
                        <?php if ($featured_projects > 0): ?>
                        <p class="text-xs text-yellow-600 mt-1">
                            <?php echo $featured_projects; ?> рекомендуемых
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <?php render_button([
                'href' => '?action=create',
                'text' => __('portfolio.add_new', 'Добавить проект'),
                'variant' => 'primary',
                'icon' => get_icon('plus', 'w-4 h-4 mr-2')
            ]); ?>
            
            <?php render_button([
                'href' => 'portfolio_export.php',
                'text' => __('portfolio.export', 'Экспорт в CSV'),
                'variant' => 'secondary',
                'icon' => get_icon('download', 'w-4 h-4 mr-2')
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
                           placeholder="<?php echo __('portfolio.search_placeholder', 'Название проекта...'); ?>"
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
                    <?php if (!empty($category_filter) || !empty($status_filter) || !empty($featured_filter)): ?>
                        <span class="inline-flex items-center px-2 py-1 ml-2 text-xs font-medium bg-primary-100 text-primary-800 rounded-full">
        <?php 
                            $active_filters = 0;
                            if (!empty($category_filter)) $active_filters++;
                            if (!empty($status_filter)) $active_filters++;
                            if (!empty($featured_filter)) $active_filters++;
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
                        <!-- Категория -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <?php echo __('portfolio.category', 'Категория'); ?>
                            </label>
                            <select name="category" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                                <option value=""><?php echo __('common.all', 'Все'); ?></option>
                                <option value="apartment" <?php echo $category_filter === 'apartment' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.category_apartment', 'Квартиры'); ?>
                                </option>
                                <option value="house" <?php echo $category_filter === 'house' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.category_house', 'Дома'); ?>
                                </option>
                                <option value="office" <?php echo $category_filter === 'office' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.category_office', 'Офисы'); ?>
                                </option>
                                <option value="commercial" <?php echo $category_filter === 'commercial' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.category_commercial', 'Коммерческие'); ?>
                                </option>
                                <option value="bathroom" <?php echo $category_filter === 'bathroom' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.category_bathroom', 'Ванные комнаты'); ?>
                                </option>
                                <option value="kitchen" <?php echo $category_filter === 'kitchen' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.category_kitchen', 'Кухни'); ?>
                                </option>
                            </select>
                        </div>
                        
                        <!-- Статус -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <?php echo __('portfolio.status', 'Статус'); ?>
                            </label>
                            <select name="status" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                                <option value=""><?php echo __('common.all', 'Все'); ?></option>
                                <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.status_active', 'Активные'); ?>
                                </option>
                                <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.status_inactive', 'Скрытые'); ?>
                                </option>
                            </select>
                        </div>
                        
                        <!-- Рекомендуемые -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <?php echo __('portfolio.featured', 'Рекомендуемые'); ?>
                            </label>
                            <select name="featured" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                                <option value=""><?php echo __('common.all', 'Все'); ?></option>
                                <option value="1" <?php echo $featured_filter === '1' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.featured_yes', 'Рекомендуемые'); ?>
                                </option>
                                <option value="0" <?php echo $featured_filter === '0' ? 'selected' : ''; ?>>
                                    <?php echo __('portfolio.featured_no', 'Обычные'); ?>
                                </option>
                            </select>
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
                
                <!-- Категория -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo __('portfolio.category', 'Категория'); ?>
                    </label>
                    <select name="category" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                        <option value=""><?php echo __('common.all', 'Все'); ?></option>
                        <option value="apartment" <?php echo $category_filter === 'apartment' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.category_apartment', 'Квартиры'); ?>
                        </option>
                        <option value="house" <?php echo $category_filter === 'house' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.category_house', 'Дома'); ?>
                        </option>
                        <option value="office" <?php echo $category_filter === 'office' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.category_office', 'Офисы'); ?>
                        </option>
                        <option value="commercial" <?php echo $category_filter === 'commercial' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.category_commercial', 'Коммерческие'); ?>
                        </option>
                        <option value="bathroom" <?php echo $category_filter === 'bathroom' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.category_bathroom', 'Ванные комнаты'); ?>
                        </option>
                        <option value="kitchen" <?php echo $category_filter === 'kitchen' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.category_kitchen', 'Кухни'); ?>
                        </option>
                    </select>
                </div>
                
                <!-- Статус -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo __('portfolio.status', 'Статус'); ?>
                    </label>
                    <select name="status" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                        <option value=""><?php echo __('common.all', 'Все'); ?></option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.status_active', 'Активные'); ?>
                        </option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.status_inactive', 'Скрытые'); ?>
                        </option>
                    </select>
                </div>
                
                <!-- Рекомендуемые -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <?php echo __('portfolio.featured', 'Рекомендуемые'); ?>
                    </label>
                    <select name="featured" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200">
                        <option value=""><?php echo __('common.all', 'Все'); ?></option>
                        <option value="1" <?php echo $featured_filter === '1' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.featured_yes', 'Рекомендуемые'); ?>
                        </option>
                        <option value="0" <?php echo $featured_filter === '0' ? 'selected' : ''; ?>>
                            <?php echo __('portfolio.featured_no', 'Обычные'); ?>
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

    <!-- Список проектов -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <?php if (empty($projects)): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900"><?php echo __('portfolio.no_projects', 'Проекты не найдены'); ?></h3>
                <p class="mt-1 text-sm text-gray-500"><?php echo __('portfolio.no_projects_description', 'Начните с создания первого проекта'); ?></p>
                <div class="mt-6">
                    <?php render_button([
                        'href' => '?action=create',
                        'text' => __('portfolio.add_first', 'Создать первый проект'),
                        'variant' => 'primary',
                        'icon' => get_icon('plus', 'w-4 h-4 mr-2')
                    ]); ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Мобильная версия - карточки -->
            <div class="block lg:hidden p-4 space-y-4">
                <?php foreach ($projects as $project): ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <!-- Изображение проекта -->
                        <div class="relative h-48 bg-gray-200">
                            <?php if (!empty($project['featured_image'])): ?>
                                <img src="/assets/uploads/portfolio/<?php echo htmlspecialchars($project['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($project['title']); ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Бейджи -->
                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                <?php if ($project['featured']): ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⭐ <?php echo __('portfolio.featured', 'Рекомендуемый'); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo htmlspecialchars(ucfirst($project['category'])); ?>
                                </span>
                            </div>
                            
                            <!-- Статус -->
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php echo $project['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $project['status'] === 'active' ? __('portfolio.status_active', 'Активный') : __('portfolio.status_inactive', 'Скрытый'); ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Информация о проекте -->
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-gray-900 mb-2">
                                <?php echo htmlspecialchars($project['title']); ?>
                            </h3>
                            
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                <?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?><?php echo strlen($project['description']) > 100 ? '...' : ''; ?>
                            </p>
                            
                            <!-- Детали проекта -->
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mb-4">
                                <?php if (!empty($project['area'])): ?>
                                    <div class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        </svg>
                                        <?php echo htmlspecialchars($project['area']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($project['duration'])): ?>
                                    <div class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php echo htmlspecialchars($project['duration']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($project['budget'])): ?>
                                    <div class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        €<?php echo number_format($project['budget'], 0, ',', ' '); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($project['completion_date'])): ?>
                                    <div class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <?php echo format_date($project['completion_date']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Действия -->
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <div class="flex space-x-2">
                                    <?php render_button([
                                        'href' => '?action=edit&id=' . $project['id'],
                                        'text' => __('common.edit', 'Редактировать'),
                                        'variant' => 'secondary',
                                        'size' => 'sm'
                                    ]); ?>
                                </div>
                                
                                <div class="flex space-x-1">
                                    <!-- Переключение статуса -->
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="text-gray-400 hover:text-gray-600" 
                                                title="<?php echo $project['status'] === 'active' ? __('portfolio.hide', 'Скрыть') : __('portfolio.show', 'Показать'); ?>">
                                            <?php if ($project['status'] === 'active'): ?>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            <?php else: ?>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                                </svg>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                    
                                    <!-- Переключение рекомендуемого -->
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="action" value="toggle_featured">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="<?php echo $project['featured'] ? 'text-yellow-500' : 'text-gray-400'; ?> hover:text-yellow-600"
                                                title="<?php echo __('portfolio.toggle_featured', 'Переключить рекомендуемый'); ?>">
                                            <svg class="h-4 w-4" fill="<?php echo $project['featured'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    
                                    <!-- Удаление -->
                                    <button type="button" 
                                            class="text-red-400 hover:text-red-600" 
                                            title="<?php echo __('common.delete', 'Удалить'); ?>"
                                            onclick="confirmDeleteProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title'], ENT_QUOTES); ?>')">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Десктопная версия - сетка -->
            <div class="hidden lg:grid lg:grid-cols-2 xl:grid-cols-3 gap-6 p-6">
                <?php foreach ($projects as $project): ?>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 overflow-hidden">
                        <!-- Изображение проекта -->
                        <div class="relative h-48 bg-gray-200">
                            <?php if (!empty($project['featured_image'])): ?>
                                <img src="/assets/uploads/portfolio/<?php echo htmlspecialchars($project['featured_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($project['title']); ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Бейджи -->
                            <div class="absolute top-2 left-2 flex flex-col gap-1">
                                <?php if ($project['featured']): ?>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        ⭐ <?php echo __('portfolio.featured', 'Рекомендуемый'); ?>
                                    </span>
                                <?php endif; ?>
                                
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo htmlspecialchars(ucfirst($project['category'])); ?>
                                </span>
                            </div>
                            
                            <!-- Статус -->
                            <div class="absolute top-2 right-2">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    <?php echo $project['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo $project['status'] === 'active' ? __('portfolio.status_active', 'Активный') : __('portfolio.status_inactive', 'Скрытый'); ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Информация о проекте -->
                        <div class="p-4">
                            <h3 class="font-semibold text-lg text-gray-900 mb-2">
                                <?php echo htmlspecialchars($project['title']); ?>
                            </h3>
                            
                            <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                <?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?><?php echo strlen($project['description']) > 100 ? '...' : ''; ?>
                            </p>
                            
                            <!-- Детали проекта -->
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mb-4">
                                <?php if (!empty($project['area'])): ?>
                                    <div class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                        </svg>
                                        <?php echo htmlspecialchars($project['area']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($project['duration'])): ?>
                                    <div class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <?php echo htmlspecialchars($project['duration']); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($project['budget'])): ?>
                                    <div class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        €<?php echo number_format($project['budget'], 0, ',', ' '); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($project['completion_date'])): ?>
                                    <div class="flex items-center">
                                        <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <?php echo format_date($project['completion_date']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Действия -->
                            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                                <div class="flex space-x-2">
                                    <?php render_button([
                                        'href' => '?action=edit&id=' . $project['id'],
                                        'text' => __('common.edit', 'Редактировать'),
                                        'variant' => 'secondary',
                                        'size' => 'sm'
                                    ]); ?>
                                </div>
                                
                                <div class="flex space-x-1">
                                    <!-- Переключение статуса -->
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="text-gray-400 hover:text-gray-600" 
                                                title="<?php echo $project['status'] === 'active' ? __('portfolio.hide', 'Скрыть') : __('portfolio.show', 'Показать'); ?>">
                                            <?php if ($project['status'] === 'active'): ?>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            <?php else: ?>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                                                </svg>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                    
                                    <!-- Переключение рекомендуемого -->
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="action" value="toggle_featured">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="<?php echo $project['featured'] ? 'text-yellow-500' : 'text-gray-400'; ?> hover:text-yellow-600"
                                                title="<?php echo __('portfolio.toggle_featured', 'Переключить рекомендуемый'); ?>">
                                            <svg class="h-4 w-4" fill="<?php echo $project['featured'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    
                                    <!-- Удаление -->
                                    <button type="button" 
                                            class="text-red-400 hover:text-red-600" 
                                            title="<?php echo __('common.delete', 'Удалить'); ?>"
                                            onclick="confirmDeleteProject(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars($project['title'], ENT_QUOTES); ?>')">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

<!-- JavaScript функции для удаления проектов -->
<script>
// Делаем функции глобальными сразу
window.confirmDeleteProject = async function(projectId, projectTitle) {
    console.log('🚀 confirmDeleteProject вызвана:', projectId, projectTitle);
    
    const message = `Вы уверены, что хотите удалить проект "${projectTitle}"? Это действие нельзя отменить.`;
    
    // Проверяем, доступна ли функция showConfirmationModal
    if (typeof showConfirmationModal === 'function') {
        console.log('✅ Используем модальное окно');
        const confirmed = await showConfirmationModal(message, 'Удаление проекта');
        
        if (confirmed) {
            deleteProject(projectId);
        }
    } else {
        console.log('⚠️ Используем fallback confirm');
        // Fallback к обычному confirm
        if (confirm(message)) {
            deleteProject(projectId);
        }
    }
};

window.deleteProject = function(projectId) {
    console.log('🗑️ deleteProject вызвана для ID:', projectId);
    
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
    idInput.value = projectId;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?php echo $csrf_token; ?>';
    
    form.appendChild(actionInput);
    form.appendChild(idInput);
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    console.log('📤 Отправляем форму удаления проекта...');
    form.submit();
};

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Функции удаления проектов инициализированы');
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
    <!-- Форма создания/редактирования проекта -->
    <div class="max-w-4xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                <?php echo $action === 'create' ? __('portfolio.create_title', 'Создать проект') : __('portfolio.edit_title', 'Редактировать проект'); ?>
            </h2>
            
            <?php render_button([
                'href' => '?action=list',
                'text' => __('common.back_to_list', 'Назад к списку'),
                'variant' => 'secondary',
                'icon' => get_icon('arrow-left', 'w-4 h-4 mr-2')
            ]); ?>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <form method="POST" class="space-y-8" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $action === 'create' ? 'create' : 'update'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <!-- Основная информация -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('portfolio.basic_info', 'Основная информация'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Название -->
                        <?php render_input_field([
                            'name' => 'title',
                            'label' => __('portfolio.title', 'Название проекта'),
                            'placeholder' => __('portfolio.title_placeholder', 'Введите название проекта'),
                            'required' => true,
                            'value' => $current_project['title'] ?? ''
                        ]); ?>
                        
                        <!-- Категория -->
                        <?php render_dropdown_field([
                            'name' => 'category',
                            'id' => 'category',
                            'label' => __('portfolio.category', 'Категория'),
                            'required' => true,
                            'value' => $current_project['category'] ?? '',
                            'placeholder' => __('portfolio.select_category', 'Выберите категорию'),
                            'options' => [
                                ['value' => 'apartment', 'text' => __('portfolio.category_apartment', 'Квартиры')],
                                ['value' => 'house', 'text' => __('portfolio.category_house', 'Дома')],
                                ['value' => 'office', 'text' => __('portfolio.category_office', 'Офисы')],
                                ['value' => 'commercial', 'text' => __('portfolio.category_commercial', 'Коммерческие')],
                                ['value' => 'bathroom', 'text' => __('portfolio.category_bathroom', 'Ванные комнаты')],
                                ['value' => 'kitchen', 'text' => __('portfolio.category_kitchen', 'Кухни')]
                            ]
                        ]); ?>
                    </div>
                    
                    <!-- Описание -->
                    <?php render_textarea_field([
                        'name' => 'description',
                        'label' => __('portfolio.description', 'Описание проекта'),
                        'placeholder' => __('portfolio.description_placeholder', 'Подробное описание проекта, выполненных работ и использованных материалов'),
                        'required' => true,
                        'rows' => 6,
                        'value' => $current_project['description'] ?? ''
                    ]); ?>
                </div>
                
                <!-- Детали проекта -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('portfolio.project_details', 'Детали проекта'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Клиент -->
                        <?php render_input_field([
                            'name' => 'client_name',
                            'label' => __('portfolio.client_name', 'Имя клиента'),
                            'placeholder' => __('portfolio.client_placeholder', 'Имя клиента или компании'),
                            'value' => $current_project['client_name'] ?? ''
                        ]); ?>
                        
                        <!-- Локация -->
                        <?php render_input_field([
                            'name' => 'location',
                            'label' => __('portfolio.location', 'Местоположение'),
                            'placeholder' => __('portfolio.location_placeholder', 'Район или адрес'),
                            'value' => $current_project['location'] ?? ''
                        ]); ?>
                        
                        <!-- Площадь -->
                        <?php render_input_field([
                            'name' => 'area',
                            'label' => __('portfolio.area', 'Площадь'),
                            'placeholder' => __('portfolio.area_placeholder', 'например: 85 м²'),
                            'value' => $current_project['area'] ?? ''
                        ]); ?>
                        
                        <!-- Продолжительность -->
                        <?php render_input_field([
                            'name' => 'duration',
                            'label' => __('portfolio.duration', 'Продолжительность'),
                            'placeholder' => __('portfolio.duration_placeholder', 'например: 6 недель'),
                            'value' => $current_project['duration'] ?? ''
                        ]); ?>
                        
                        <!-- Бюджет -->
                        <?php render_input_field([
                            'type' => 'number',
                            'name' => 'budget',
                            'label' => __('portfolio.budget', 'Бюджет (€)'),
                            'placeholder' => '0',
                            'value' => $current_project['budget'] ?? '',
                            'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200'
                        ]); ?>
                        
                        <!-- Дата завершения -->
                        <?php render_input_field([
                            'type' => 'date',
                            'name' => 'completion_date',
                            'label' => __('portfolio.completion_date', 'Дата завершения'),
                            'value' => $current_project['completion_date'] ?? ''
                        ]); ?>
                    </div>
                </div>
                
                <!-- Техническая информация -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('portfolio.technical_info', 'Техническая информация'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Количество комнат -->
                        <?php render_input_field([
                            'type' => 'number',
                            'name' => 'rooms',
                            'label' => __('portfolio.rooms', 'Количество комнат'),
                            'placeholder' => '0',
                            'value' => $current_project['technical_info']['rooms'] ?? ''
                        ]); ?>
                        
                        <!-- Количество ванных -->
                        <?php render_input_field([
                            'type' => 'number',
                            'name' => 'bathrooms',
                            'label' => __('portfolio.bathrooms', 'Количество ванных'),
                            'placeholder' => '0',
                            'value' => $current_project['technical_info']['bathrooms'] ?? ''
                        ]); ?>
                        
                        <!-- Год проекта -->
                        <?php render_input_field([
                            'type' => 'number',
                            'name' => 'project_year',
                            'label' => __('portfolio.year', 'Год проекта'),
                            'placeholder' => date('Y'),
                            'value' => $current_project['technical_info']['year'] ?? date('Y')
                        ]); ?>
                        
                        <!-- Стиль -->
                        <?php render_input_field([
                            'name' => 'project_style',
                            'label' => __('portfolio.style', 'Стиль'),
                            'placeholder' => __('portfolio.style_placeholder', 'например: современный, классический, минимализм'),
                            'value' => $current_project['technical_info']['style'] ?? ''
                        ]); ?>
                    </div>
                    
                    <!-- Особенности проекта -->
                    <?php render_textarea_field([
                        'name' => 'features',
                        'label' => __('portfolio.features', 'Особенности проекта'),
                        'placeholder' => __('portfolio.features_placeholder', 'Через запятую: теплые полы, LED освещение, эко-материалы'),
                        'rows' => 3,
                        'value' => (isset($current_project['technical_info']['features']) && is_array($current_project['technical_info']['features'])) ? implode(', ', $current_project['technical_info']['features']) : ''
                    ]); ?>
                    
                    <!-- Теги -->
                    <?php render_input_field([
                        'name' => 'tags',
                        'label' => __('portfolio.tags', 'Теги'),
                        'placeholder' => __('portfolio.tags_placeholder', 'Через запятую: ремонт, квартира, современный стиль'),
                        'value' => (isset($current_project['tags']) && is_array($current_project['tags'])) ? implode(', ', $current_project['tags']) : ''
                    ]); ?>
                </div>
                
                <!-- Изображения -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('portfolio.images', 'Изображения'); ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Главное изображение -->
                        <?php render_image_upload_field([
                            'name' => 'featured_image',
                            'id' => 'featured_image',
                            'label' => __('portfolio.featured_image', 'Главное изображение'),
                            'current_image' => !empty($current_project['featured_image']) ? '/assets/uploads/portfolio/' . $current_project['featured_image'] : '',
                            'accept' => 'image/*',
                            'required' => false
                        ]); ?>
                        
                        <!-- Галерея изображений -->
                        <?php render_image_gallery_field([
                            'name' => 'gallery_images',
                            'id' => 'gallery_images',
                            'label' => __('portfolio.gallery', 'Галерея изображений'),
                            'current_images' => !empty($current_project['gallery']) ? array_map(function($img) { return '/assets/uploads/portfolio/' . $img; }, $current_project['gallery']) : [],
                            'accept' => 'image/*',
                            'required' => false
                        ]); ?>
                        
                        <!-- До/После -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php render_image_upload_field([
                                'name' => 'before_image',
                                'id' => 'before_image',
                                'label' => __('portfolio.before_image', 'Изображение "До"'),
                                'current_image' => !empty($current_project['before_after']['before']) ? '/assets/uploads/portfolio/' . $current_project['before_after']['before'] : '',
                                'accept' => 'image/*',
                                'required' => false
                            ]); ?>
                            
                            <?php render_image_upload_field([
                                'name' => 'after_image',
                                'id' => 'after_image',
                                'label' => __('portfolio.after_image', 'Изображение "После"'),
                                'current_image' => !empty($current_project['before_after']['after']) ? '/assets/uploads/portfolio/' . $current_project['before_after']['after'] : '',
                                'accept' => 'image/*',
                                'required' => false
                            ]); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Настройки -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('portfolio.settings', 'Настройки'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Статус -->
                        <?php render_dropdown_field([
                            'name' => 'status',
                            'id' => 'status',
                            'label' => __('portfolio.status', 'Статус'),
                            'value' => $current_project['status'] ?? 'active',
                            'placeholder' => __('portfolio.select_status', 'Выберите статус'),
                            'options' => [
                                ['value' => 'active', 'text' => __('portfolio.status_active', 'Активный')],
                                ['value' => 'inactive', 'text' => __('portfolio.status_inactive', 'Скрытый')]
                            ]
                        ]); ?>
                        
                        <!-- Рекомендуемый -->
                        <?php render_dropdown_field([
                            'name' => 'featured',
                            'id' => 'featured',
                            'label' => __('portfolio.featured', 'Рекомендуемый'),
                            'value' => $current_project['featured'] ?? 0,
                            'placeholder' => __('portfolio.select_featured', 'Выберите статус'),
                            'options' => [
                                ['value' => '0', 'text' => __('portfolio.featured_no', 'Обычный')],
                                ['value' => '1', 'text' => __('portfolio.featured_yes', 'Рекомендуемый')]
                            ]
                        ]); ?>
                        
                        <!-- Приоритет сортировки -->
                        <?php render_input_field([
                            'type' => 'number',
                            'name' => 'sort_order',
                            'label' => __('portfolio.sort_order', 'Приоритет сортировки'),
                            'placeholder' => '0',
                            'value' => $current_project['sort_order'] ?? '0',
                            'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200'
                        ]); ?>
                    </div>
                </div>
                
                <!-- SEO настройки -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('portfolio.seo', 'SEO настройки'); ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Meta Title -->
                        <?php render_input_field([
                            'name' => 'meta_title',
                            'label' => __('portfolio.meta_title', 'Meta Title'),
                            'placeholder' => __('portfolio.meta_title_placeholder', 'SEO заголовок для поисковых систем'),
                            'value' => $current_project['meta_title'] ?? ''
                        ]); ?>
                        
                        <!-- Meta Description -->
                        <?php render_textarea_field([
                            'name' => 'meta_description',
                            'label' => __('portfolio.meta_description', 'Meta Description'),
                            'placeholder' => __('portfolio.meta_description_placeholder', 'SEO описание для поисковых систем (до 160 символов)'),
                            'rows' => 3,
                            'value' => $current_project['meta_description'] ?? ''
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
                        'text' => $action === 'create' ? __('portfolio.create_button', 'Создать проект') : __('portfolio.update_button', 'Обновить проект'),
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

