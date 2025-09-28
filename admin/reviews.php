<?php
/**
 * Страница управления отзывами
 * Baumaster Admin Panel - Reviews Management
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once COMPONENTS_PATH . 'admin_layout.php';
require_once __DIR__ . '/../components/confirmation_modal.php';

// Настройки страницы
$page_title = __('reviews.title', 'Управление отзывами');
$page_description = __('reviews.description', 'Модерация и управление отзывами клиентов');
$active_menu = 'reviews';

// Инициализация переменных
$error_message = '';
$success_message = '';
$reviews = [];
$current_review = null;
$action = $_GET['action'] ?? 'list';
$review_id = intval($_GET['id'] ?? 0);

// Получение базы данных
$db = get_database();

// Функции для работы с отзывами
function translate_review_on_approval($review_id) {
    global $db;
    
    // Проверяем, включен ли автоматический перевод
    if (!defined('AUTO_TRANSLATE_REVIEWS') || !AUTO_TRANSLATE_REVIEWS) {
        return false;
    }
    
    try {
        require_once __DIR__ . '/../integrations/translation/TranslationManager.php';
        $translation_manager = new TranslationManager();
        
        // Получаем данные отзыва
        $review = $db->select('reviews', ['id' => $review_id], ['limit' => 1]);
        if (!$review) {
            return false;
        }
        
        $review_text = $review['review_text'];
        
        // Определяем язык отзыва
        $is_russian = preg_match('/[а-яё]/iu', $review_text);
        $is_german = preg_match('/[äöüß]/iu', $review_text);
        
        $from_lang = 'ru';
        $to_lang = 'de';
        
        // Если отзыв на немецком, переводим на русский
        if ($is_german && !$is_russian) {
            $from_lang = 'de';
            $to_lang = 'ru';
        }
        // Если отзыв на русском, переводим на немецкий
        elseif ($is_russian && !$is_german) {
            $from_lang = 'ru';
            $to_lang = 'de';
        }
        // Если язык не определен, считаем русским и переводим на немецкий
        else {
            $from_lang = 'ru';
            $to_lang = 'de';
        }
        
        // Поля для перевода
        $fields_to_translate = [
            'review_text' => $review_text
        ];
        
        // Добавляем перевод для услуги, если она выбрана
        if (!empty($review['service_id'])) {
            $service_data = $db->select('services', ['id' => intval($review['service_id'])], ['limit' => 1]);
            if (!empty($service_data)) {
                $service_name = $service_data['title'] ?? '';
                if (!empty($service_name)) {
                    $fields_to_translate['service_name'] = $service_name;
                }
            }
        }
        
        // Выполняем автоматический перевод
        $translated_fields = $translation_manager->autoTranslateContent('reviews', $review_id, $fields_to_translate, $from_lang, $to_lang);
        
        if (!empty($translated_fields)) {
            write_log("Отзыв ID {$review_id} переведен при одобрении с {$from_lang} на {$to_lang}", 'INFO');
            return true;
        }
        
        return false;
        
    } catch (Exception $e) {
        write_log("Ошибка перевода отзыва ID {$review_id} при одобрении: " . $e->getMessage(), 'ERROR');
        return false;
    }
}

function create_review($data) {
    global $db;
    
    $errors = validate_review_data($data);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Обработка загрузки фото клиента
    $client_photo = '';
    if (isset($_FILES['client_photo']) && $_FILES['client_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['client_photo'], 'clients', ['jpg', 'jpeg', 'png', 'gif']);
        if ($upload_result['success']) {
            $client_photo = $upload_result['filename'];
        } else {
            $errors['client_photo'] = $upload_result['error'];
            return ['success' => false, 'errors' => $errors];
        }
    } elseif (!empty($data['client_photo_url'])) {
        $client_photo = sanitize_input($data['client_photo_url']);
    } elseif (!empty($data['client_photo'])) {
        $client_photo = sanitize_input($data['client_photo']);
    }
    
    $review_data = [
        'client_name' => sanitize_input($data['client_name']),
        'client_email' => sanitize_input($data['client_email'] ?? ''),
        'client_phone' => sanitize_input($data['client_phone'] ?? ''),
        'client_photo' => $client_photo,
        'review_text' => sanitize_input($data['review_text']),
        'rating' => intval($data['rating'] ?? 5),
        'project_id' => !empty($data['project_id']) ? intval($data['project_id']) : null,
        'service_id' => !empty($data['service_id']) ? intval($data['service_id']) : null,
        'status' => sanitize_input($data['status'] ?? 'pending'),
        'review_date' => !empty($data['review_date']) ? $data['review_date'] : date('Y-m-d'),
        'verified' => intval($data['verified'] ?? 0),
        'featured' => intval($data['featured'] ?? 0),
        'sort_order' => intval($data['sort_order'] ?? 0),
        'admin_notes' => sanitize_input($data['admin_notes'] ?? ''),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];
    
    $review_id = $db->insert('reviews', $review_data);
    
    if ($review_id) {
        // Автоматический перевод при создании отзыва со статусом "опубликован"
        if ($review_data['status'] === 'published') {
            translate_review_on_approval($review_id);
        }
        
        write_log("New review created: {$review_data['client_name']} (ID: $review_id)", 'INFO');
        log_user_activity('review_create', 'reviews', $review_id);
        return [
            'success' => true,
            'review_id' => $review_id,
            'message' => __('reviews.create_success', 'Отзыв успешно создан')
        ];
    } else {
        return ['success' => false, 'errors' => ['general' => __('reviews.create_error', 'Ошибка при создании отзыва')]];
    }
}

function update_review($review_id, $data) {
    global $db;
    
    $existing_review = $db->select('reviews', ['id' => $review_id], ['limit' => 1]);
    if (!$existing_review) {
        return ['success' => false, 'errors' => ['general' => __('reviews.not_found', 'Отзыв не найден')]];
    }
    
    $errors = validate_review_data($data, true);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Обработка загрузки фото клиента
    $client_photo = $existing_review['client_photo'] ?? '';
    if (isset($_FILES['client_photo']) && $_FILES['client_photo']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['client_photo'], 'clients', ['jpg', 'jpeg', 'png', 'gif']);
        if ($upload_result['success']) {
            // Удаляем старое фото, если оно есть
            if (!empty($client_photo) && file_exists(ASSETS_PATH . '/uploads/clients/' . $client_photo)) {
                unlink(ASSETS_PATH . '/uploads/clients/' . $client_photo);
            }
            $client_photo = $upload_result['filename'];
        } else {
            $errors['client_photo'] = $upload_result['error'];
            return ['success' => false, 'errors' => $errors];
        }
    } elseif (!empty($data['client_photo_url'])) {
        $client_photo = sanitize_input($data['client_photo_url']);
    } elseif (!empty($data['client_photo'])) {
        $client_photo = sanitize_input($data['client_photo']);
    }
    
    $update_data = [
        'client_name' => sanitize_input($data['client_name']),
        'client_email' => sanitize_input($data['client_email'] ?? ''),
        'client_phone' => sanitize_input($data['client_phone'] ?? ''),
        'client_photo' => $client_photo,
        'review_text' => sanitize_input($data['review_text']),
        'rating' => intval($data['rating'] ?? 5),
        'project_id' => !empty($data['project_id']) ? intval($data['project_id']) : null,
        'service_id' => !empty($data['service_id']) ? intval($data['service_id']) : null,
        'status' => sanitize_input($data['status'] ?? 'pending'),
        'review_date' => !empty($data['review_date']) ? $data['review_date'] : date('Y-m-d'),
        'verified' => intval($data['verified'] ?? 0),
        'featured' => intval($data['featured'] ?? 0),
        'sort_order' => intval($data['sort_order'] ?? 0),
        'admin_notes' => sanitize_input($data['admin_notes'] ?? '')
    ];
    
    if ($db->update('reviews', $update_data, ['id' => $review_id])) {
        // Автоматический перевод при изменении статуса на "опубликован"
        if (isset($update_data['status']) && $update_data['status'] === 'published' && $existing_review['status'] !== 'published') {
            translate_review_on_approval($review_id);
        }
        
        write_log("Review updated: {$existing_review['client_name']} (ID: $review_id)", 'INFO');
        log_user_activity('review_update', 'reviews', $review_id, $existing_review, $update_data);
        return ['success' => true, 'message' => __('reviews.update_success', 'Отзыв успешно обновлен')];
    } else {
        return ['success' => false, 'errors' => ['general' => __('reviews.update_error', 'Ошибка при обновлении отзыва')]];
    }
}

function delete_review($review_id) {
    global $db;
    
    $review = $db->select('reviews', ['id' => $review_id], ['limit' => 1]);
    if (!$review) {
        return ['success' => false, 'error' => __('reviews.not_found', 'Отзыв не найден')];
    }
    
    // Удаляем фото клиента, если оно есть
    if (!empty($review['client_photo']) && file_exists(ASSETS_PATH . '/uploads/clients/' . $review['client_photo'])) {
        unlink(ASSETS_PATH . '/uploads/clients/' . $review['client_photo']);
    }
    
    if ($db->delete('reviews', ['id' => $review_id])) {
        write_log("Review deleted: {$review['client_name']} (ID: $review_id)", 'WARNING');
        log_user_activity('review_delete', 'reviews', $review_id);
        return ['success' => true, 'message' => __('reviews.delete_success', 'Отзыв успешно удален')];
    } else {
        return ['success' => false, 'error' => __('reviews.delete_error', 'Ошибка при удалении отзыва')];
    }
}

function validate_review_data($data, $is_update = false) {
    $errors = [];
    
    $client_name = $data['client_name'] ?? '';
    if (empty($client_name)) {
        $errors['client_name'] = __('reviews.client_name_required', 'Имя клиента обязательно');
    } elseif (strlen($client_name) < 2) {
        $errors['client_name'] = __('reviews.client_name_too_short', 'Имя должно содержать минимум 2 символа');
    }
    
    $review_text = $data['review_text'] ?? '';
    if (empty($review_text)) {
        $errors['review_text'] = __('reviews.text_required', 'Текст отзыва обязателен');
    } elseif (strlen($review_text) < 10) {
        $errors['review_text'] = __('reviews.text_too_short', 'Отзыв должен содержать минимум 10 символов');
    }
    
    $rating = intval($data['rating'] ?? 0);
    if ($rating < 1 || $rating > 5) {
        $errors['rating'] = __('reviews.rating_invalid', 'Рейтинг должен быть от 1 до 5');
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
                $result = create_review($_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list';
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'update':
                $result = update_review($review_id, $_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'delete':
                $delete_id = intval($_POST['id'] ?? 0);
                $result = delete_review($delete_id);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list';
                } else {
                    $error_message = $result['error'];
                }
                break;
                
            case 'moderate':
                $moderate_id = intval($_POST['id'] ?? 0);
                $review = $db->select('reviews', ['id' => $moderate_id], ['limit' => 1]);
                if ($review) {
                    $new_status = $_POST['new_status'] ?? 'pending';
                    $db->update('reviews', ['status' => $new_status], ['id' => $moderate_id]);
                    
                    // Автоматический перевод при одобрении
                    if ($new_status === 'published') {
                        translate_review_on_approval($moderate_id);
                    }
                    
                    $success_message = __('reviews.status_updated', 'Статус отзыва обновлен');
                    write_log("Review moderated: {$review['client_name']} (ID: $moderate_id) -> $new_status", 'INFO');
                } else {
                    $error_message = __('reviews.not_found', 'Отзыв не найден');
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
            $current_review = $db->select('reviews', ['id' => $review_id], ['limit' => 1]);
            if (!$current_review) {
                $error_message = __('reviews.not_found', 'Отзыв не найден');
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
        $rating_filter = $_GET['rating'] ?? '';
        
        if (!empty($search)) {
            $filters['client_name LIKE'] = "%{$search}%";
        }
        if (!empty($status_filter)) {
            $filters['status'] = $status_filter;
        }
        if (!empty($rating_filter)) {
            $filters['rating'] = intval($rating_filter);
        }
        
        $reviews = $db->select('reviews', $filters, ['order' => 'sort_order DESC, created_at DESC']);
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
        <div class="flex items-center space-x-4">
            <?php 
            // Подсчет статистики
            $total_reviews = count($reviews);
            $published_reviews = count(array_filter($reviews, function($review) {
                return $review['status'] === 'published';
            }));
            $pending_reviews = count(array_filter($reviews, function($review) {
                return $review['status'] === 'pending';
            }));
            
            // Статистическая карточка для отзывов
            ?>
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 min-w-[200px]">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                            <?php echo get_icon('star', 'w-5 h-5 text-white'); ?>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">
                            <?php echo __('reviews.total_count', 'Всего отзывов'); ?>
                        </p>
                        <p class="text-2xl font-semibold text-gray-900">
                            <?php echo $total_reviews; ?>
                        </p>
                        <?php if ($published_reviews > 0): ?>
                        <p class="text-xs text-green-600 mt-1">
                            <?php echo $published_reviews; ?> опубликованных
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <?php render_button([
                'href' => '?action=create',
                'text' => __('reviews.add_new', 'Добавить отзыв'),
                'variant' => 'primary',
                'icon' => get_icon('plus', 'w-4 h-4 mr-2')
            ]); ?>
            
            <?php render_button([
                'href' => 'reviews_export.php',
                'text' => __('reviews.export', 'Экспорт в CSV'),
                'variant' => 'secondary',
                'icon' => get_icon('download', 'w-4 h-4 mr-2')
            ]); ?>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
        <?php 
        // Подготовка опций рейтинга
        $rating_options = [['value' => '', 'text' => __('common.all', 'Все')]];
        for ($i = 5; $i >= 1; $i--) {
            $rating_options[] = [
                'value' => $i,
                'text' => $i . ' ' . ($i == 1 ? 'звезда' : ($i < 5 ? 'звезды' : 'звезд'))
            ];
        }
        
        render_filter_form([
            'fields' => [
                [
                    'type' => 'search',
                    'name' => 'search',
                    'placeholder' => __('reviews.search_placeholder', 'Имя клиента...'),
                    'value' => $search
                ],
                [
                    'type' => 'dropdown',
                    'name' => 'status',
                    'label' => __('reviews.status', 'Статус'),
                    'value' => $status_filter,
                    'options' => [
                        ['value' => '', 'text' => __('common.all', 'Все')],
                        ['value' => 'pending', 'text' => __('reviews.status_pending', 'На модерации')],
                        ['value' => 'published', 'text' => __('reviews.status_published', 'Опубликованы')],
                        ['value' => 'rejected', 'text' => __('reviews.status_rejected', 'Отклонены')]
                    ],
                    'placeholder' => __('common.all', 'Все')
                ],
                [
                    'type' => 'dropdown',
                    'name' => 'rating',
                    'label' => __('reviews.rating', 'Рейтинг'),
                    'value' => $rating_filter,
                    'options' => $rating_options,
                    'placeholder' => __('common.all', 'Все')
                ]
            ],
            'button_text' => __('common.filter', 'Фильтр')
        ]);
        ?>
    </div>

    <!-- Список отзывов -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <?php if (empty($reviews)): ?>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900"><?php echo __('reviews.no_reviews', 'Отзывы не найдены'); ?></h3>
                <p class="mt-1 text-sm text-gray-500"><?php echo __('reviews.no_reviews_description', 'Начните с добавления первого отзыва'); ?></p>
                <div class="mt-6">
                    <?php render_button([
                        'href' => '?action=create',
                        'text' => __('reviews.add_first', 'Добавить первый отзыв'),
                        'variant' => 'primary',
                        'icon' => get_icon('plus', 'w-4 h-4 mr-2')
                    ]); ?>
                </div>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-200">
                <?php foreach ($reviews as $review): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Фото клиента -->
                                <div class="flex-shrink-0">
                                    <?php if (!empty($review['client_photo'])): ?>
                                        <?php 
                                        $photo_src = $review['client_photo'];
                                        // Если это не URL (не начинается с http), добавляем путь к папке
                                        if (!preg_match('/^https?:\/\//', $photo_src)) {
                                            $photo_src = '/assets/uploads/clients/' . $photo_src;
                                        }
                                        ?>
                                        <img class="h-12 w-12 rounded-full object-cover" src="<?php echo htmlspecialchars($photo_src); ?>" alt="<?php echo htmlspecialchars($review['client_name']); ?>">
                                    <?php else: ?>
                                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Информация об отзыве -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                <?php echo htmlspecialchars($review['client_name']); ?>
                                            </h3>
                                            
                                            <!-- Рейтинг -->
                                            <div class="flex items-center">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <svg class="h-4 w-4 <?php echo $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                <?php endfor; ?>
                                                <span class="ml-1 text-sm text-gray-600">(<?php echo $review['rating']; ?>)</span>
                                            </div>
                                            
                                            <!-- Бейджи -->
                                            <?php if ($review['verified']): ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    ✓ Проверен
                                                </span>
                                            <?php endif; ?>
                                            
                                            <?php if ($review['featured']): ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    ⭐ Рекомендуемый
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Статус -->
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php 
                                                switch($review['status']) {
                                                    case 'published': echo 'bg-green-100 text-green-800'; break;
                                                    case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                    case 'rejected': echo 'bg-red-100 text-red-800'; break;
                                                    default: echo 'bg-gray-100 text-gray-800';
                                                }
                                            ?>">
                                            <?php 
                                                switch($review['status']) {
                                                    case 'published': echo 'Опубликован'; break;
                                                    case 'pending': echo 'На модерации'; break;
                                                    case 'rejected': echo 'Отклонен'; break;
                                                    default: echo ucfirst($review['status']);
                                                }
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Текст отзыва -->
                                    <p class="text-gray-700 text-sm mb-3 line-clamp-3">
                                        "<?php echo htmlspecialchars($review['review_text']); ?>"
                                    </p>
                                    
                                    <!-- Дополнительная информация -->
                                    <div class="flex items-center text-xs text-gray-500 space-x-4">
                                        <span><?php echo format_date($review['review_date']); ?></span>
                                        <?php if (!empty($review['client_email'])): ?>
                                            <span><?php echo htmlspecialchars($review['client_email']); ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($review['admin_notes'])): ?>
                                            <span class="text-blue-600">📝 Есть заметки</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Действия -->
                            <div class="flex items-center space-x-2 ml-4">
                                <?php render_button([
                                    'href' => '?action=edit&id=' . $review['id'],
                                    'text' => __('common.edit', 'Редактировать'),
                                    'variant' => 'secondary',
                                    'size' => 'sm'
                                ]); ?>
                                
                                <?php if ($review['status'] === 'pending'): ?>
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="action" value="moderate">
                                        <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                        <input type="hidden" name="new_status" value="published">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <?php echo __('reviews.approve', 'Одобрить'); ?>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" class="inline-block ml-2">
                                        <input type="hidden" name="action" value="moderate">
                                        <input type="hidden" name="id" value="<?php echo $review['id']; ?>">
                                        <input type="hidden" name="new_status" value="rejected">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <?php echo __('reviews.reject', 'Отклонить'); ?>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <button type="button" 
                                        class="text-red-400 hover:text-red-600 p-1" 
                                        title="<?php echo __('common.delete', 'Удалить'); ?>"
                                        onclick="confirmDeleteReview(<?php echo $review['id']; ?>, '<?php echo htmlspecialchars($review['client_name'], ENT_QUOTES); ?>')">
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

<!-- JavaScript функции для удаления отзывов -->
<script>
// Делаем функции глобальными сразу
window.confirmDeleteReview = async function(reviewId, clientName) {
    console.log('🚀 confirmDeleteReview вызвана:', reviewId, clientName);
    
    const message = `Вы уверены, что хотите удалить отзыв от "${clientName}"? Это действие нельзя отменить.`;
    
    // Проверяем, доступна ли функция showConfirmationModal
    if (typeof showConfirmationModal === 'function') {
        console.log('✅ Используем модальное окно');
        const confirmed = await showConfirmationModal(message, 'Удаление отзыва');
        
        if (confirmed) {
            deleteReview(reviewId);
        }
    } else {
        console.log('⚠️ Используем fallback confirm');
        // Fallback к обычному confirm
        if (confirm(message)) {
            deleteReview(reviewId);
        }
    }
};

window.deleteReview = function(reviewId) {
    console.log('🗑️ deleteReview вызвана для ID:', reviewId);
    
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
    idInput.value = reviewId;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?php echo $csrf_token; ?>';
    
    form.appendChild(actionInput);
    form.appendChild(idInput);
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    console.log('📤 Отправляем форму удаления отзыва...');
    form.submit();
};

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Функции удаления отзывов инициализированы');
});
</script>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
    <!-- Форма создания/редактирования отзыва -->
    <div class="max-w-4xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                <?php echo $action === 'create' ? __('reviews.create_title', 'Добавить отзыв') : __('reviews.edit_title', 'Редактировать отзыв'); ?>
            </h2>
            
            <?php render_button([
                'href' => '?action=list',
                'text' => __('common.back_to_list', 'Назад к списку'),
                'variant' => 'secondary',
                'icon' => get_icon('arrow-left', 'w-4 h-4 mr-2')
            ]); ?>
        </div>

        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
            <form method="POST" enctype="multipart/form-data" class="space-y-8">
                <input type="hidden" name="action" value="<?php echo $action === 'create' ? 'create' : 'update'; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                
                <!-- Информация о клиенте -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('reviews.client_info', 'Информация о клиенте'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php render_input_field([
                            'name' => 'client_name',
                            'label' => __('reviews.client_name', 'Имя клиента'),
                            'placeholder' => __('reviews.client_name_placeholder', 'Введите имя клиента'),
                            'required' => true,
                            'value' => $current_review['client_name'] ?? ''
                        ]); ?>
                        
                        <?php render_input_field([
                            'type' => 'email',
                            'name' => 'client_email',
                            'label' => __('reviews.client_email', 'Email клиента'),
                            'placeholder' => __('reviews.client_email_placeholder', 'email@example.com'),
                            'value' => $current_review['client_email'] ?? ''
                        ]); ?>
                        
                        <?php render_input_field([
                            'type' => 'tel',
                            'name' => 'client_phone',
                            'label' => __('reviews.client_phone', 'Телефон клиента'),
                            'placeholder' => __('reviews.client_phone_placeholder', '+49 176 12345678'),
                            'value' => $current_review['client_phone'] ?? ''
                        ]); ?>
                        
                        <div class="space-y-2">
                            <label for="client_photo" class="block text-sm font-medium text-gray-700">
                                <?php echo __('reviews.client_photo', 'Фото клиента'); ?>
                            </label>
                            
                            <!-- Текущее фото -->
                            <?php if (!empty($current_review['client_photo'])): ?>
                                <div class="mb-4">
                                    <p class="text-sm text-gray-600 mb-2">Текущее фото:</p>
                                    <img src="/assets/uploads/clients/<?php echo htmlspecialchars($current_review['client_photo']); ?>" 
                                         alt="Фото клиента" 
                                         class="h-20 w-20 rounded-full object-cover border border-gray-300">
                                </div>
                            <?php endif; ?>
                            
                            <!-- Поле загрузки файла -->
                            <input type="file" 
                                   id="client_photo" 
                                   name="client_photo" 
                                   accept="image/jpeg,image/jpg,image/png,image/gif"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            
                            <p class="text-xs text-gray-500">
                                Поддерживаемые форматы: JPG, PNG, GIF. Максимальный размер: 5MB
                            </p>
                            
                            <!-- Поле для URL (альтернатива) -->
                            <div class="mt-2">
                                <label for="client_photo_url" class="block text-sm font-medium text-gray-600">
                                    Или введите URL фотографии:
                                </label>
                                <input type="url" 
                                       id="client_photo_url" 
                                       name="client_photo_url" 
                                       placeholder="https://example.com/photo.jpg"
                                       value="<?php echo htmlspecialchars($current_review['client_photo'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Отзыв -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('reviews.review_content', 'Содержание отзыва'); ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <?php render_textarea_field([
                            'name' => 'review_text',
                            'label' => __('reviews.review_text', 'Текст отзыва'),
                            'placeholder' => __('reviews.review_text_placeholder', 'Введите текст отзыва клиента'),
                            'required' => true,
                            'rows' => 6,
                            'value' => $current_review['review_text'] ?? ''
                        ]); ?>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php 
                            // Подготовка опций рейтинга
                            $rating_options = [];
                            for ($i = 1; $i <= 5; $i++) {
                                $rating_options[] = [
                                    'value' => $i,
                                    'text' => $i . ' ' . ($i == 1 ? 'звезда' : ($i < 5 ? 'звезды' : 'звезд'))
                                ];
                            }
                            
                            render_dropdown_field([
                                'name' => 'rating',
                                'id' => 'rating',
                                'label' => __('reviews.rating', 'Рейтинг'),
                                'required' => true,
                                'value' => $current_review['rating'] ?? 5,
                                'placeholder' => __('reviews.select_rating', 'Выберите рейтинг'),
                                'options' => $rating_options
                            ]); 
                            ?>
                            
                            <?php render_input_field([
                                'type' => 'date',
                                'name' => 'review_date',
                                'label' => __('reviews.review_date', 'Дата отзыва'),
                                'value' => $current_review['review_date'] ?? date('Y-m-d')
                            ]); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Настройки -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('reviews.settings', 'Настройки'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php render_dropdown_field([
                            'name' => 'status',
                            'id' => 'status',
                            'label' => __('reviews.status', 'Статус'),
                            'value' => $current_review['status'] ?? 'pending',
                            'placeholder' => __('reviews.select_status', 'Выберите статус'),
                            'options' => [
                                ['value' => 'pending', 'text' => __('reviews.status_pending', 'На модерации')],
                                ['value' => 'published', 'text' => __('reviews.status_published', 'Опубликован')],
                                ['value' => 'rejected', 'text' => __('reviews.status_rejected', 'Отклонен')]
                            ]
                        ]); ?>
                        
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="verified" name="verified" type="checkbox" value="1" <?php echo ($current_review['verified'] ?? 0) ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="verified" class="ml-2 block text-sm text-gray-900">
                                    <?php echo __('reviews.verified', 'Проверенный клиент'); ?>
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input id="featured" name="featured" type="checkbox" value="1" <?php echo ($current_review['featured'] ?? 0) ? 'checked' : ''; ?>
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="featured" class="ml-2 block text-sm text-gray-900">
                                    <?php echo __('reviews.featured', 'Рекомендуемый отзыв'); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <?php render_input_field([
                        'type' => 'number',
                        'name' => 'sort_order',
                        'label' => __('reviews.sort_order', 'Приоритет сортировки'),
                        'placeholder' => '0',
                        'value' => $current_review['sort_order'] ?? '0'
                    ]); ?>
                </div>
                
                <!-- Заметки администратора -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('reviews.admin_notes', 'Заметки администратора'); ?>
                    </h3>
                    
                    <?php render_textarea_field([
                        'name' => 'admin_notes',
                        'label' => __('reviews.admin_notes_label', 'Внутренние заметки'),
                        'placeholder' => __('reviews.admin_notes_placeholder', 'Заметки для внутреннего использования (не отображаются публично)'),
                        'rows' => 3,
                        'value' => $current_review['admin_notes'] ?? ''
                    ]); ?>
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
                        'text' => $action === 'create' ? __('reviews.create_button', 'Создать отзыв') : __('reviews.update_button', 'Обновить отзыв'),
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

