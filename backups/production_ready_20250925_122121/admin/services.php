<?php
/**
 * Страница управления услугами
 * Baumaster Admin Panel - Services Management
 */

// Подключение базовых файлов
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';

// Настройки страницы
$page_title = __('services.title', 'Управление услугами');
$page_description = __('services.description', 'Создание, редактирование и управление услугами компании');
$active_menu = 'services';

// Инициализация переменных
$error_message = '';
$success_message = '';
$services = [];
$current_service = null;
$action = $_GET['action'] ?? 'list';
$service_id = intval($_GET['id'] ?? 0);

// Обработка успешных сообщений
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success_message = __('services.create_success', 'Услуга успешно создана');
}

// Отладочная информация
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    write_log("POST request received: " . json_encode($_POST), 'INFO');
    write_log("FILES received: " . json_encode($_FILES), 'INFO');
}

// Получение базы данных
$db = get_database();

// Функции для работы с услугами
function create_service($data, $files = []) {
    global $db;
    
    // Валидация данных
    $errors = validate_service_data($data);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Обработка загрузки основного изображения
    $image_url = '';
    if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
        $image_result = handle_image_upload($files['image'], 'services');
        if ($image_result['success']) {
            $image_url = $image_result['url'];
        } else {
            $errors['image'] = $image_result['error'];
        }
    }
    // Если файл не выбран (UPLOAD_ERR_NO_FILE), это нормально - не добавляем ошибку
    
    // Обработка загрузки галереи
    $gallery_urls = [];
    if (isset($files['gallery']) && is_array($files['gallery']['name'])) {
        // Проверяем, есть ли хотя бы один файл с ошибкой не UPLOAD_ERR_NO_FILE
        $has_valid_files = false;
        foreach ($files['gallery']['error'] as $error) {
            if ($error !== UPLOAD_ERR_NO_FILE) {
                $has_valid_files = true;
                break;
            }
        }
        
        if ($has_valid_files) {
            $gallery_result = handle_multiple_image_upload($files['gallery'], 'services');
            if ($gallery_result['success']) {
                foreach ($gallery_result['results'] as $result) {
                    $gallery_urls[] = $result['url'];
                }
            }
            if (!empty($gallery_result['errors'])) {
                $errors['gallery'] = implode(', ', $gallery_result['errors']);
            }
        }
    }
    
    // Если есть ошибки с изображениями, возвращаем их
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Подготовка данных для сохранения
    $service_data = [
        'title' => sanitize_input($data['title']),
        'description' => sanitize_input($data['description']),
        'price' => floatval($data['price'] ?? 0),
        'price_type' => sanitize_input($data['price_type'] ?? 'fixed'),
        'image' => $image_url,
        'gallery' => json_encode($gallery_urls),
        'features' => json_encode($data['features'] ?? []),
        'meta_title' => sanitize_input($data['meta_title'] ?? ''),
        'meta_description' => sanitize_input($data['meta_description'] ?? ''),
        'keywords' => sanitize_input($data['keywords'] ?? ''),
        'status' => sanitize_input($data['status'] ?? 'active'),
        'priority' => intval($data['priority'] ?? 0),
        'category' => sanitize_input($data['category'] ?? 'general')
    ];
    
    // Сохранение в базе данных
    $service_id = $db->insert('services', $service_data);
    
    if ($service_id) {
        // Логирование
        write_log("New service created: {$service_data['title']} (ID: $service_id)", 'INFO');
        log_user_activity('service_create', 'services', $service_id);
        
        return [
            'success' => true, 
            'service_id' => $service_id,
            'message' => __('services.create_success', 'Услуга успешно создана')
        ];
    } else {
        return ['success' => false, 'errors' => ['general' => __('services.create_error', 'Ошибка при создании услуги')]];
    }
}

function update_service($service_id, $data, $files = []) {
    global $db;
    
    // Проверка существования услуги
    $existing_service = $db->select('services', ['id' => $service_id], ['limit' => 1]);
    if (!$existing_service) {
        return ['success' => false, 'errors' => ['general' => __('services.not_found', 'Услуга не найдена')]];
    }
    
    // Валидация данных
    $errors = validate_service_data($data, true);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Обработка загрузки основного изображения
    $image_url = $existing_service['image'];
    if (isset($files['image']) && $files['image']['error'] === UPLOAD_ERR_OK) {
        // Удаляем старое изображение
        if (!empty($existing_service['image'])) {
            delete_image($existing_service['image']);
        }
        
        $image_result = handle_image_upload($files['image'], 'services');
        if ($image_result['success']) {
            $image_url = $image_result['url'];
        } else {
            $errors['image'] = $image_result['error'];
        }
    } elseif (isset($data['current_image']) && empty($data['current_image'])) {
        // Пользователь удалил текущее изображение
        if (!empty($existing_service['image'])) {
            delete_image($existing_service['image']);
        }
        $image_url = '';
    }
    // Если файл не выбран (UPLOAD_ERR_NO_FILE), оставляем текущее изображение
    
    // Обработка загрузки галереи
    $gallery_urls = json_decode($existing_service['gallery'], true) ?: [];
    
    // Если пользователь удалил текущую галерею
    if (isset($data['current_gallery'])) {
        $current_gallery = json_decode($data['current_gallery'], true) ?: [];
        // Удаляем изображения, которые больше не в списке
        foreach ($gallery_urls as $url) {
            if (!in_array($url, $current_gallery)) {
                delete_image($url);
            }
        }
        $gallery_urls = $current_gallery;
    }
    
    // Добавляем новые изображения в галерею
    if (isset($files['gallery']) && is_array($files['gallery']['name'])) {
        // Проверяем, есть ли хотя бы один файл с ошибкой не UPLOAD_ERR_NO_FILE
        $has_valid_files = false;
        foreach ($files['gallery']['error'] as $error) {
            if ($error !== UPLOAD_ERR_NO_FILE) {
                $has_valid_files = true;
                break;
            }
        }
        
        if ($has_valid_files) {
            $gallery_result = handle_multiple_image_upload($files['gallery'], 'services');
            if ($gallery_result['success']) {
                foreach ($gallery_result['results'] as $result) {
                    $gallery_urls[] = $result['url'];
                }
            }
            if (!empty($gallery_result['errors'])) {
                $errors['gallery'] = implode(', ', $gallery_result['errors']);
            }
        }
    }
    
    // Если есть ошибки с изображениями, возвращаем их
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Подготовка данных для обновления
    $update_data = [
        'title' => sanitize_input($data['title']),
        'description' => sanitize_input($data['description']),
        'price' => floatval($data['price'] ?? 0),
        'price_type' => sanitize_input($data['price_type'] ?? 'fixed'),
        'image' => $image_url,
        'gallery' => json_encode($gallery_urls),
        'features' => json_encode($data['features'] ?? []),
        'meta_title' => sanitize_input($data['meta_title'] ?? ''),
        'meta_description' => sanitize_input($data['meta_description'] ?? ''),
        'keywords' => sanitize_input($data['keywords'] ?? ''),
        'status' => sanitize_input($data['status'] ?? 'active'),
        'priority' => intval($data['priority'] ?? 0),
        'category' => sanitize_input($data['category'] ?? 'general')
    ];
    
    // Обновление в базе данных
    if ($db->update('services', $update_data, ['id' => $service_id])) {
        // Логирование
        write_log("Service updated: {$existing_service['title']} (ID: $service_id)", 'INFO');
        log_user_activity('service_update', 'services', $service_id, $existing_service, $update_data);
        
        return ['success' => true, 'message' => __('services.update_success', 'Услуга успешно обновлена')];
    } else {
        return ['success' => false, 'errors' => ['general' => __('services.update_error', 'Ошибка при обновлении услуги')]];
    }
}

function delete_service($service_id) {
    global $db;
    
    // Проверка существования услуги
    $service = $db->select('services', ['id' => $service_id], ['limit' => 1]);
    if (!$service) {
        return ['success' => false, 'error' => __('services.not_found', 'Услуга не найдена')];
    }
    
    // Удаление изображений
    if (!empty($service['image'])) {
        delete_image($service['image']);
    }
    
    // Удаление галереи
    $gallery = json_decode($service['gallery'], true) ?: [];
    foreach ($gallery as $image_url) {
        delete_image($image_url);
    }
    
    // Удаление из базы данных
    if ($db->delete('services', ['id' => $service_id])) {
        // Логирование
        write_log("Service deleted: {$service['title']} (ID: $service_id)", 'WARNING');
        log_user_activity('service_delete', 'services', $service_id);
        
        return ['success' => true, 'message' => __('services.delete_success', 'Услуга успешно удалена')];
    } else {
        return ['success' => false, 'error' => __('services.delete_error', 'Ошибка при удалении услуги')];
    }
}

function validate_service_data($data, $is_update = false) {
    $errors = [];
    
    // Валидация названия
    $title = $data['title'] ?? '';
    if (empty($title)) {
        $errors['title'] = __('services.title_required', 'Название услуги обязательно');
    } elseif (strlen($title) < 3) {
        $errors['title'] = __('services.title_too_short', 'Название должно содержать минимум 3 символа');
    } elseif (strlen($title) > 200) {
        $errors['title'] = __('services.title_too_long', 'Название должно содержать максимум 200 символов');
    }
    
    // Валидация описания
    $description = $data['description'] ?? '';
    if (empty($description)) {
        $errors['description'] = __('services.description_required', 'Описание услуги обязательно');
    } elseif (strlen($description) < 10) {
        $errors['description'] = __('services.description_too_short', 'Описание должно содержать минимум 10 символов');
    }
    
    // Валидация цены
    $price = $data['price'] ?? '';
    if (!empty($price) && !is_numeric($price)) {
        $errors['price'] = __('services.price_invalid', 'Цена должна быть числом');
    } elseif (!empty($price) && floatval($price) < 0) {
        $errors['price'] = __('services.price_negative', 'Цена не может быть отрицательной');
    }
    
    // Валидация приоритета
    $priority = $data['priority'] ?? '';
    if (!empty($priority) && !is_numeric($priority)) {
        $errors['priority'] = __('services.priority_invalid', 'Приоритет должен быть числом');
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
                write_log("Creating service with data: " . json_encode($_POST), 'INFO');
                $result = create_service($_POST, $_FILES);
                write_log("Create service result: " . json_encode($result), 'INFO');
                if ($result['success']) {
                    $success_message = $result['message'];
                    // Редирект на список услуг
                    header('Location: ?action=list&success=1');
                    exit;
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'update':
                $result = update_service($service_id, $_POST, $_FILES);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'delete':
                $delete_id = intval($_POST['id'] ?? $service_id);
                $result = delete_service($delete_id);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list'; // Возвращаемся к списку
                } else {
                    $error_message = $result['error'];
                }
                break;
                
            case 'toggle_status':
                $service = $db->select('services', ['id' => $service_id], ['limit' => 1]);
                if ($service) {
                    $new_status = $service['status'] === 'active' ? 'inactive' : 'active';
                    $db->update('services', ['status' => $new_status], ['id' => $service_id]);
                    $success_message = __('services.status_updated', 'Статус услуги обновлен');
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
            $current_service = $db->select('services', ['id' => $service_id], ['limit' => 1]);
            if (!$current_service) {
                $error_message = __('services.not_found', 'Услуга не найдена');
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
            // Используем специальный ключ для LIKE поиска
            $filters['_search'] = ['field' => 'title', 'value' => $search];
        }
        if (!empty($status_filter)) {
            $filters['status'] = $status_filter;
        }
        if (!empty($category_filter)) {
            $filters['category'] = $category_filter;
        }
        
        // Получение списка услуг
        $services = $db->select('services', $filters, ['order' => 'priority DESC, created_at DESC']);
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
    <!-- Заголовок и кнопки -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">
                <?php echo __('services.title', 'Название услуги'); ?>
            </h2>
            <p class="text-sm text-gray-600 mt-1">
                <?php echo __('services.total_count', 'Всего услуг'); ?>: <?php echo count($services); ?>
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <?php render_button([
                'href' => '?action=create',
                'text' => __('services.add_new', 'Добавить услугу'),
                'variant' => 'primary',
                'icon' => get_icon('plus', 'w-4 h-4 mr-2')
            ]); ?>
            
            <?php render_button([
                'href' => 'services_export.php',
                'text' => __('services.export', 'Экспорт в CSV'),
                'variant' => 'secondary',
                'icon' => get_icon('download', 'w-4 h-4 mr-2')
            ]); ?>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
        <?php 
        render_filter_form([
            'class' => 'grid grid-cols-1 md:grid-cols-4 gap-4 items-end',
            'fields' => [
                [
                    'type' => 'search',
                    'name' => 'search',
                    'placeholder' => __('services.search_placeholder', 'Название услуги...'),
                    'value' => $search
                ],
                [
                    'type' => 'dropdown',
                    'name' => 'status',
                    'label' => __('services.status', 'Статус'),
                    'value' => $status_filter,
                    'options' => [
                        ['value' => '', 'text' => __('common.all', 'Все')],
                        ['value' => 'active', 'text' => __('services.status_active', 'Активные')],
                        ['value' => 'inactive', 'text' => __('services.status_inactive', 'Неактивные')]
                    ],
                    'placeholder' => __('common.all', 'Все')
                ],
                [
                    'type' => 'dropdown',
                    'name' => 'category',
                    'label' => __('services.category', 'Категория'),
                    'value' => $category_filter,
                    'options' => [
                        ['value' => '', 'text' => __('common.all', 'Все')],
                        ['value' => 'painting', 'text' => __('services.category_painting', 'Малярные работы')],
                        ['value' => 'flooring', 'text' => __('services.category_flooring', 'Укладка полов')],
                        ['value' => 'bathroom', 'text' => __('services.category_bathroom', 'Ремонт ванных')],
                        ['value' => 'drywall', 'text' => __('services.category_drywall', 'Гипсокартон')],
                        ['value' => 'tiling', 'text' => __('services.category_tiling', 'Плитка')],
                        ['value' => 'renovation', 'text' => __('services.category_renovation', 'Комплексный ремонт')]
                    ],
                    'placeholder' => __('common.all', 'Все')
                ]
            ],
            'button_text' => __('common.filter', 'Фильтр')
        ]);
        ?>
    </div>

    <!-- Таблица услуг -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <?php if (empty($services)): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H7m5 0v-5a2 2 0 00-2-2H8a2 2 0 00-2 2v5m10 0V9a2 2 0 00-2-2h-4a2 2 0 00-2 2v10"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900"><?php echo __('services.no_services', 'Услуги не найдены'); ?></h3>
                <p class="mt-1 text-sm text-gray-500"><?php echo __('services.no_services_description', 'Начните с создания первой услуги'); ?></p>
                <div class="mt-6">
                    <?php render_button([
                        'href' => '?action=create',
                        'text' => __('services.add_first', 'Создать первую услугу'),
                        'variant' => 'primary',
                        'icon' => get_icon('plus', 'w-4 h-4 mr-2')
                    ]); ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Адаптивная таблица с горизонтальной прокруткой -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 admin-table-responsive">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">
                                <?php echo __('services.service', 'Услуга'); ?>
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">
                                <?php echo __('services.category', 'Категория'); ?>
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">
                                <?php echo __('services.price', 'Цена'); ?>
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[100px]">
                                <?php echo __('services.status', 'Статус'); ?>
                            </th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[80px]">
                                <?php echo __('services.priority', 'Приоритет'); ?>
                            </th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[300px]">
                                <?php echo __('common.actions', 'Действия'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($services as $service): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-4">
                                    <div class="flex items-center">
                                        <?php if (!empty($service['image'])): ?>
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" src="<?php echo htmlspecialchars($service['image']); ?>" alt="">
                                            </div>
                                            <div class="ml-4">
                                        <?php else: ?>
                                            <div>
                                        <?php endif; ?>
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($service['title']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars(substr($service['description'], 0, 60)); ?><?php echo strlen($service['description']) > 60 ? '...' : ''; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars(ucfirst($service['category'])); ?>
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if ($service['price'] > 0): ?>
                                        <?php echo number_format($service['price'], 0, ',', ' '); ?> €
                                        <?php if ($service['price_type'] === 'per_m2'): ?>
                                            /м²
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-gray-500">По договорённости</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?php echo $service['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo $service['status'] === 'active' ? __('services.status_active', 'Активна') : __('services.status_inactive', 'Неактивна'); ?>
                                    </span>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php echo intval($service['priority']); ?>
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex flex-wrap gap-2 justify-end">
                                        <?php render_button([
                                            'href' => '?action=edit&id=' . $service['id'],
                                            'text' => __('common.edit', 'Редактировать'),
                                            'variant' => 'secondary',
                                            'size' => 'sm'
                                        ]); ?>
                                        
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                            <?php render_button([
                                                'type' => 'submit',
                                                'text' => $service['status'] === 'active' ? __('services.deactivate', 'Скрыть') : __('services.activate', 'Показать'),
                                                'variant' => $service['status'] === 'active' ? 'secondary' : 'primary',
                                                'size' => 'sm'
                                            ]); ?>
                                        </form>
                                        
                                        <form method="POST" class="inline-block" onsubmit="return confirmDelete('<?php echo __('services.confirm_delete', 'Вы уверены, что хотите удалить эту услугу?'); ?>');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                            <?php render_button([
                                                'type' => 'submit',
                                                'text' => __('common.delete', 'Удалить'),
                                                'variant' => 'danger',
                                                'size' => 'sm'
                                            ]); ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
    <!-- Форма создания/редактирования услуги -->
    <div class="max-w-4xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                <?php echo $action === 'create' ? __('services.create_title', 'Создать услугу') : __('services.edit_title', 'Редактировать услугу'); ?>
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
                        <?php echo __('services.basic_info', 'Основная информация'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Название -->
                        <?php render_input_field([
                            'name' => 'title',
                            'label' => __('services.title', 'Название услуги'),
                            'placeholder' => __('services.title_placeholder', 'Введите название услуги'),
                            'required' => true,
                            'value' => $current_service['title'] ?? ''
                        ]); ?>
                        
                        <!-- Категория -->
                        <?php render_dropdown_field([
                            'name' => 'category',
                            'id' => 'category',
                            'label' => __('services.category', 'Категория'),
                            'required' => true,
                            'value' => $current_service['category'] ?? '',
                            'placeholder' => __('services.select_category', 'Выберите категорию'),
                            'options' => [
                                ['value' => 'painting', 'text' => __('services.category_painting', 'Малярные работы')],
                                ['value' => 'flooring', 'text' => __('services.category_flooring', 'Укладка полов')],
                                ['value' => 'bathroom', 'text' => __('services.category_bathroom', 'Ремонт ванных')],
                                ['value' => 'drywall', 'text' => __('services.category_drywall', 'Гипсокартон')],
                                ['value' => 'tiling', 'text' => __('services.category_tiling', 'Плитка')],
                                ['value' => 'renovation', 'text' => __('services.category_renovation', 'Комплексный ремонт')]
                            ]
                        ]); ?>
                    </div>
                    
                    <!-- Описание -->
                    <?php render_textarea_field([
                        'name' => 'description',
                        'label' => __('services.description', 'Описание услуги'),
                        'placeholder' => __('services.description_placeholder', 'Подробное описание услуги, процесса работы и преимуществ'),
                        'required' => true,
                        'rows' => 6,
                        'value' => $current_service['description'] ?? ''
                    ]); ?>
                </div>
                
                <!-- Изображения -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('services.images', 'Изображения'); ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Основное изображение -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <?php echo __('services.main_image', 'Основное изображение'); ?>
                            </label>
                            
                            <!-- Текущее изображение -->
                            <?php if (!empty($current_service['image'])): ?>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2"><?php echo __('common.current_image', 'Текущее изображение'); ?>:</p>
                                    <div class="relative inline-block">
                                        <img src="<?php echo htmlspecialchars($current_service['image']); ?>" 
                                             alt="<?php echo __('common.current_image', 'Текущее изображение'); ?>" 
                                             class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Поле загрузки -->
                            <div class="relative">
                                <input 
                                    type="file" 
                                    name="image"
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                                    onchange="previewImage(this)"
                                >
                                <p class="text-xs text-gray-500 mt-1">
                                    <?php echo __('common.max_file_size', 'Максимальный размер файла'); ?>: 10MB
                                </p>
                            </div>
                            
                            <!-- Превью нового изображения -->
                            <div id="image-preview" class="hidden mt-4">
                                <p class="text-sm text-gray-600 mb-2"><?php echo __('common.new_image_preview', 'Превью нового изображения'); ?>:</p>
                                <img id="preview-img" 
                                     class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                            </div>
                        </div>
                        
                        <!-- Галерея изображений -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <?php echo __('services.gallery', 'Галерея изображений'); ?>
                            </label>
                            
                            <!-- Поле загрузки множественных файлов -->
                            <div class="relative">
                                <input 
                                    type="file" 
                                    name="gallery[]"
                                    multiple
                                    accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                                >
                                <p class="text-xs text-gray-500 mt-1">
                                    <?php echo __('common.max_files', 'Максимум файлов'); ?>: 10
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ценообразование -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('services.pricing', 'Ценообразование'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Цена -->
                        <?php render_input_field([
                            'type' => 'number',
                            'name' => 'price',
                            'label' => __('services.price', 'Цена (€)'),
                            'placeholder' => '0',
                            'value' => $current_service['price'] ?? '',
                            'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200'
                        ]); ?>
                        
                        <!-- Тип ценообразования -->
                        <?php render_dropdown_field([
                            'name' => 'price_type',
                            'id' => 'price_type',
                            'label' => __('services.price_type', 'Тип ценообразования'),
                            'value' => $current_service['price_type'] ?? 'fixed',
                            'placeholder' => __('services.select_price_type', 'Выберите тип ценообразования'),
                            'options' => [
                                ['value' => 'fixed', 'text' => __('services.price_fixed', 'Фиксированная')],
                                ['value' => 'per_m2', 'text' => __('services.price_per_m2', 'За м²')],
                                ['value' => 'per_hour', 'text' => __('services.price_per_hour', 'За час')]
                            ]
                        ]); ?>
                    </div>
                </div>
                
                <!-- Настройки -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('services.settings', 'Настройки'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Статус -->
                        <?php render_dropdown_field([
                            'name' => 'status',
                            'id' => 'status',
                            'label' => __('services.status', 'Статус'),
                            'value' => $current_service['status'] ?? 'active',
                            'placeholder' => __('services.select_status', 'Выберите статус'),
                            'options' => [
                                ['value' => 'active', 'text' => __('services.status_active', 'Активна')],
                                ['value' => 'inactive', 'text' => __('services.status_inactive', 'Неактивна')]
                            ]
                        ]); ?>
                        
                        <!-- Приоритет -->
                        <?php render_input_field([
                            'type' => 'number',
                            'name' => 'priority',
                            'label' => __('services.priority', 'Приоритет сортировки'),
                            'placeholder' => '0',
                            'value' => $current_service['priority'] ?? '0',
                            'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200'
                        ]); ?>
                    </div>
                    
                    <div class="mt-4">
                        <p class="text-sm text-gray-500">
                            <?php echo __('services.priority_help', 'Услуги с большим приоритетом отображаются выше в списке (0 = самый низкий приоритет)'); ?>
                        </p>
                    </div>
                </div>
                
                <!-- SEO настройки -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('services.seo', 'SEO настройки'); ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Meta Title -->
                        <?php render_input_field([
                            'name' => 'meta_title',
                            'label' => __('services.meta_title', 'Meta Title'),
                            'placeholder' => __('services.meta_title_placeholder', 'SEO заголовок для поисковых систем'),
                            'value' => $current_service['meta_title'] ?? ''
                        ]); ?>
                        
                        <!-- Meta Description -->
                        <?php render_textarea_field([
                            'name' => 'meta_description',
                            'label' => __('services.meta_description', 'Meta Description'),
                            'placeholder' => __('services.meta_description_placeholder', 'SEO описание для поисковых систем (до 160 символов)'),
                            'rows' => 3,
                            'value' => $current_service['meta_description'] ?? ''
                        ]); ?>
                        
                        <!-- Keywords -->
                        <?php render_input_field([
                            'name' => 'keywords',
                            'label' => __('services.keywords', 'Ключевые слова'),
                            'placeholder' => __('services.keywords_placeholder', 'ключевое слово, другое слово, третье слово'),
                            'value' => $current_service['keywords'] ?? ''
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
                        'text' => $action === 'create' ? __('services.create_button', 'Создать услугу') : __('services.update_button', 'Обновить услугу'),
                        'variant' => 'primary'
                    ]); ?>
                </div>
            </form>
        </div>
    </div>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.classList.add('hidden');
    }
}

function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php endif; ?>

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
