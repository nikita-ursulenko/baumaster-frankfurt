<?php
/**
 * Страница управления блогом/FAQ
 * Baumaster Admin Panel - Blog Management
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once UI_PATH . 'base.php';
require_once COMPONENTS_PATH . 'admin_layout.php';
require_once __DIR__ . '/../integrations/translation/TranslationManager.php';
require_once __DIR__ . '/../components/confirmation_modal.php';

// Настройки страницы
$page_title = __('blog.title', 'Управление блогом');
$page_description = __('blog.description', 'Создание и управление статьями блога и FAQ');
$active_menu = 'blog';

// Инициализация переменных
$error_message = '';
$success_message = '';
$posts = [];
$current_post = null;
$action = $_GET['action'] ?? 'list';
$post_id = intval($_GET['id'] ?? 0);

// Получение базы данных
$db = get_database();

// Функции для работы с блогом
function create_post($data) {
    global $db;
    
    $errors = validate_post_data($data);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Генерация slug из заголовка
    $slug = generate_slug($data['title']);
    
    // Обработка тегов
    $tags = array_filter(array_map('trim', explode(',', $data['tags'] ?? '')));
    
    // Обработка загрузки изображения
    $featured_image = '';
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = handle_image_upload($_FILES['featured_image'], 'blog');
        if ($upload_result['success']) {
            $featured_image = basename($upload_result['filepath']);
        } else {
            return ['success' => false, 'error' => $upload_result['error']];
        }
    } elseif (!empty($data['current_image'])) {
        // Сохраняем текущее изображение, если новое не загружено
        $featured_image = basename($data['current_image']);
    }
    
    $post_data = [
        'title' => sanitize_input($data['title']),
        'slug' => $slug,
        'excerpt' => sanitize_input($data['excerpt'] ?? ''),
        'content' => $data['content'], // HTML контент
        'category' => sanitize_input($data['category']),
        'tags' => json_encode($tags),
        'featured_image' => $featured_image,
        'meta_title' => sanitize_input($data['meta_title'] ?? ''),
        'meta_description' => sanitize_input($data['meta_description'] ?? ''),
        'keywords' => sanitize_input($data['keywords'] ?? ''),
        'status' => sanitize_input($data['status'] ?? 'draft'),
        'post_type' => sanitize_input($data['post_type'] ?? 'article'),
        'author_id' => get_current_admin_user()['id'] ?? 1,
        'featured' => intval($data['featured'] ?? 0),
        'sort_order' => intval($data['sort_order'] ?? 0),
        'published_at' => !empty($data['published_at']) ? $data['published_at'] : null
    ];
    
    $post_id = $db->insert('blog_posts', $post_data);
    
    if ($post_id) {
        // АВТОМАТИЧЕСКИЙ ПЕРЕВОД
        try {
            $translation_manager = new TranslationManager();
            $translation_manager->autoTranslateContent('blog_posts', $post_id, [
                'title' => $post_data['title'],
                'excerpt' => $post_data['excerpt'],
                'content' => $post_data['content'],
                'meta_title' => $post_data['meta_title'],
                'meta_description' => $post_data['meta_description'],
                'keywords' => $post_data['keywords']
            ]);
            write_log("Auto-translation completed for blog post ID: $post_id", 'INFO');
        } catch (Exception $e) {
            write_log("Auto-translation failed for blog post ID: $post_id - " . $e->getMessage(), 'WARNING');
        }
        
        write_log("New blog post created: {$post_data['title']} (ID: $post_id)", 'INFO');
        log_user_activity('blog_create', 'blog_posts', $post_id);
        return [
            'success' => true,
            'post_id' => $post_id,
            'message' => __('blog.create_success', 'Статья успешно создана')
        ];
    } else {
        return ['success' => false, 'errors' => ['general' => __('blog.create_error', 'Ошибка при создании статьи')]];
    }
}

function update_post($post_id, $data) {
    global $db;
    
    $existing_post = $db->select('blog_posts', ['id' => $post_id], ['limit' => 1]);
    if (!$existing_post) {
        return ['success' => false, 'errors' => ['general' => __('blog.not_found', 'Статья не найдена')]];
    }
    
    $errors = validate_post_data($data, true);
    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }
    
    // Генерация slug из заголовка
    $slug = generate_slug($data['title']);
    
    // Обработка тегов
    $tags = array_filter(array_map('trim', explode(',', $data['tags'] ?? '')));
    
    // Обработка загрузки изображения
    $featured_image = $current_post['featured_image'] ?? ''; // Сохраняем текущее изображение по умолчанию
    
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        // Удаляем старое изображение, если есть
        if (!empty($current_post['featured_image'])) {
            delete_image('/assets/uploads/blog/' . $current_post['featured_image']);
        }
        
        $upload_result = handle_image_upload($_FILES['featured_image'], 'blog');
        if ($upload_result['success']) {
            $featured_image = basename($upload_result['filepath']);
        } else {
            return ['success' => false, 'error' => $upload_result['error']];
        }
    } elseif (isset($data['remove_current_image']) && $data['remove_current_image']) {
        // Удаляем текущее изображение
        if (!empty($current_post['featured_image'])) {
            delete_image('/assets/uploads/blog/' . $current_post['featured_image']);
        }
        $featured_image = '';
    }
    
    $update_data = [
        'title' => sanitize_input($data['title']),
        'slug' => $slug,
        'excerpt' => sanitize_input($data['excerpt'] ?? ''),
        'content' => $data['content'],
        'category' => sanitize_input($data['category']),
        'tags' => json_encode($tags),
        'featured_image' => $featured_image,
        'meta_title' => sanitize_input($data['meta_title'] ?? ''),
        'meta_description' => sanitize_input($data['meta_description'] ?? ''),
        'keywords' => sanitize_input($data['keywords'] ?? ''),
        'status' => sanitize_input($data['status'] ?? 'draft'),
        'post_type' => sanitize_input($data['post_type'] ?? 'article'),
        'featured' => intval($data['featured'] ?? 0),
        'sort_order' => intval($data['sort_order'] ?? 0),
        'published_at' => !empty($data['published_at']) ? $data['published_at'] : null
    ];
    
    if ($db->update('blog_posts', $update_data, ['id' => $post_id])) {
        write_log("Blog post updated: {$existing_post['title']} (ID: $post_id)", 'INFO');
        log_user_activity('blog_update', 'blog_posts', $post_id, $existing_post, $update_data);
        return ['success' => true, 'message' => __('blog.update_success', 'Статья успешно обновлена')];
    } else {
        return ['success' => false, 'errors' => ['general' => __('blog.update_error', 'Ошибка при обновлении статьи')]];
    }
}

function delete_post($post_id) {
    global $db;
    
    $post = $db->select('blog_posts', ['id' => $post_id], ['limit' => 1]);
    if (!$post) {
        return ['success' => false, 'error' => __('blog.not_found', 'Статья не найдена')];
    }
    
    // Удаляем связанные переводы
    $db->delete('translations', ['source_table' => 'blog_posts', 'source_id' => $post_id]);
    
    // Удаляем изображение, если есть
    if (!empty($post['featured_image'])) {
        $image_path = UPLOADS_PATH . 'blog/' . $post['featured_image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
        
        // Удаляем миниатюру, если есть
        $thumb_path = UPLOADS_PATH . 'blog/thumbs/' . $post['featured_image'];
        if (file_exists($thumb_path)) {
            unlink($thumb_path);
        }
    }
    
    if ($db->delete('blog_posts', ['id' => $post_id])) {
        write_log("Blog post deleted: {$post['title']} (ID: $post_id)", 'WARNING');
        log_user_activity('blog_delete', 'blog_posts', $post_id);
        return ['success' => true, 'message' => __('blog.delete_success', 'Статья успешно удалена')];
    } else {
        return ['success' => false, 'error' => __('blog.delete_error', 'Ошибка при удалении статьи')];
    }
}

function validate_post_data($data, $is_update = false) {
    $errors = [];
    
    $title = $data['title'] ?? '';
    if (empty($title)) {
        $errors['title'] = __('blog.title_required', 'Заголовок статьи обязателен');
    } elseif (strlen($title) < 5) {
        $errors['title'] = __('blog.title_too_short', 'Заголовок должен содержать минимум 5 символов');
    }
    
    $content = $data['content'] ?? '';
    if (empty($content)) {
        $errors['content'] = __('blog.content_required', 'Содержание статьи обязательно');
    } elseif (strlen(strip_tags($content)) < 50) {
        $errors['content'] = __('blog.content_too_short', 'Содержание должно содержать минимум 50 символов');
    }
    
    $category = $data['category'] ?? '';
    if (empty($category)) {
        $errors['category'] = __('blog.category_required', 'Категория статьи обязательна');
    }
    
    return $errors;
}

function generate_slug($title) {
    $slug = transliterate($title);
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Транслитерация кириллицы в латиницу
 */
function transliterate($text) {
    $cyr = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я'];
    $lat = ['a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya', 'A', 'B', 'V', 'G', 'D', 'E', 'Yo', 'Zh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'Ts', 'Ch', 'Sh', 'Sch', '', 'Y', '', 'E', 'Yu', 'Ya'];

    return str_replace($cyr, $lat, $text);
}

// Обработка POST запросов
if ($_POST) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = __('common.csrf_error', 'Ошибка безопасности. Попробуйте снова.');
    } else {
        $post_action = $_POST['action'] ?? '';
        
        switch ($post_action) {
            case 'create':
                $result = create_post($_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list';
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'update':
                $result = update_post($post_id, $_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = implode('<br>', $result['errors']);
                }
                break;
                
            case 'delete':
                $delete_id = intval($_POST['id'] ?? 0);
                $result = delete_post($delete_id);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list';
                } else {
                    $error_message = $result['error'];
                }
                break;
                
            case 'publish':
                $publish_id = intval($_POST['id'] ?? 0);
                $post = $db->select('blog_posts', ['id' => $publish_id], ['limit' => 1]);
                if ($post) {
                    $db->update('blog_posts', [
                        'status' => 'published',
                        'published_at' => date('Y-m-d H:i:s')
                    ], ['id' => $publish_id]);
                    $success_message = __('blog.published', 'Статья опубликована');
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
            $current_post = $db->select('blog_posts', ['id' => $post_id], ['limit' => 1]);
            if (!$current_post) {
                $error_message = __('blog.not_found', 'Статья не найдена');
                $action = 'list';
            } else {
                $current_post['tags'] = json_decode($current_post['tags'], true) ?? [];
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
        $type_filter = $_GET['post_type'] ?? '';
        
        if (!empty($search)) {
            $filters['title LIKE'] = "%{$search}%";
        }
        if (!empty($status_filter)) {
            $filters['status'] = $status_filter;
        }
        if (!empty($category_filter)) {
            $filters['category'] = $category_filter;
        }
        if (!empty($type_filter)) {
            $filters['post_type'] = $type_filter;
        }
        
        $posts = $db->select('blog_posts', $filters, ['order' => 'sort_order DESC, created_at DESC']);
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
            $total_posts = count($posts);
            $published_posts = count(array_filter($posts, function($post) {
                return $post['status'] === 'published';
            }));
            $featured_posts = count(array_filter($posts, function($post) {
                return $post['featured'] == 1;
            }));
            
            // Статистическая карточка для блога
            ?>
            <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 min-w-[200px]">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center">
                            <?php echo get_icon('document-text', 'w-5 h-5 text-white'); ?>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-500">
                            <?php echo __('blog.total_count', 'Всего статей'); ?>
                        </p>
                        <p class="text-2xl font-semibold text-gray-900">
                            <?php echo $total_posts; ?>
                        </p>
                        <?php if ($featured_posts > 0): ?>
                        <p class="text-xs text-yellow-600 mt-1">
                            <?php echo $featured_posts; ?> рекомендуемых
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-2">
            <?php render_button([
                'href' => '?action=create',
                'text' => __('blog.add_new', 'Добавить статью'),
                'variant' => 'primary',
                'icon' => get_icon('plus', 'w-4 h-4 mr-2')
            ]); ?>
            
            <?php render_button([
                'href' => 'blog_export.php',
                'text' => __('blog.export', 'Экспорт в CSV'),
                'variant' => 'secondary',
                'icon' => get_icon('download', 'w-4 h-4 mr-2')
            ]); ?>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
        <?php 
        render_filter_form([
            'fields' => [
                [
                    'type' => 'search',
                    'name' => 'search',
                    'placeholder' => __('blog.search_placeholder', 'Название статьи...'),
                    'value' => $search
                ],
                [
                    'type' => 'dropdown',
                    'name' => 'category',
                    'label' => __('blog.category', 'Категория'),
                    'value' => $category_filter,
                    'options' => [
                        ['value' => '', 'text' => __('common.all', 'Все')],
                        ['value' => 'tips', 'text' => __('blog.category_tips', 'Советы')],
                        ['value' => 'faq', 'text' => __('blog.category_faq', 'FAQ')],
                        ['value' => 'news', 'text' => __('blog.category_news', 'Новости')],
                        ['value' => 'guides', 'text' => __('blog.category_guides', 'Руководства')]
                    ],
                    'placeholder' => __('common.all', 'Все')
                ],
                [
                    'type' => 'dropdown',
                    'name' => 'post_type',
                    'label' => __('blog.type', 'Тип'),
                    'value' => $type_filter,
                    'options' => [
                        ['value' => '', 'text' => __('common.all', 'Все')],
                        ['value' => 'article', 'text' => __('blog.type_article', 'Статья')],
                        ['value' => 'faq', 'text' => __('blog.type_faq', 'FAQ')],
                        ['value' => 'news', 'text' => __('blog.type_news', 'Новость')],
                        ['value' => 'tips', 'text' => __('blog.type_tips', 'Совет')]
                    ],
                    'placeholder' => __('common.all', 'Все')
                ],
                [
                    'type' => 'dropdown',
                    'name' => 'status',
                    'label' => __('blog.status', 'Статус'),
                    'value' => $status_filter,
                    'options' => [
                        ['value' => '', 'text' => __('common.all', 'Все')],
                        ['value' => 'draft', 'text' => __('blog.status_draft', 'Черновик')],
                        ['value' => 'published', 'text' => __('blog.status_published', 'Опубликовано')]
                    ],
                    'placeholder' => __('common.all', 'Все')
                ]
            ],
            'button_text' => __('common.filter', 'Фильтр')
        ]);
        ?>
    </div>

    <!-- Список статей -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
        <?php if (empty($posts)): ?>
            <div class="text-center py-12">
                
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900"><?php echo __('blog.no_posts', 'Статьи не найдены'); ?></h3>
                <p class="mt-1 text-sm text-gray-500"><?php echo __('blog.no_posts_description', 'Начните с создания первой статьи'); ?></p>
                <div class="mt-6">
                    <?php render_button([
                        'href' => '?action=create',
                        'text' => __('blog.add_first', 'Создать первую статью'),
                        'variant' => 'primary',
                        'icon' => get_icon('plus', 'w-4 h-4 mr-2')
                    ]); ?>
                </div>
            </div>
        <?php else: ?>
            <div class="divide-y divide-gray-200">
                <?php foreach ($posts as $post): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Изображение статьи -->
                                <div class="flex-shrink-0">
                                    <?php if (!empty($post['featured_image'])): ?>
                                        <img class="h-16 w-24 rounded-lg object-cover" src="/assets/uploads/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                                    <?php else: ?>
                                        <div class="h-16 w-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Информация о статье -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                <?php echo htmlspecialchars($post['title']); ?>
                                            </h3>
                                            
                                            <!-- Бейджи -->
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <?php echo htmlspecialchars(ucfirst($post['category'])); ?>
                                            </span>
                                            
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <?php echo htmlspecialchars(ucfirst($post['post_type'])); ?>
                                            </span>
                                            
                                            <?php if ($post['featured']): ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    ⭐ Рекомендуемая
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Статус -->
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php echo $post['status'] === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                            <?php echo $post['status'] === 'published' ? 'Опубликовано' : 'Черновик'; ?>
                                        </span>
                                    </div>
                                    
                                    <!-- Краткое описание -->
                                    <?php if (!empty($post['excerpt'])): ?>
                                        <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                                            <?php echo htmlspecialchars($post['excerpt']); ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <!-- Дополнительная информация -->
                                    <div class="flex items-center text-xs text-gray-500 space-x-4">
                                        <span>Просмотры: <?php echo $post['views']; ?></span>
                                        <?php if ($post['published_at']): ?>
                                            <span>Опубликовано: <?php echo format_date($post['published_at']); ?></span>
                                        <?php endif; ?>
                                        <span>Создано: <?php echo format_date($post['created_at']); ?></span>
                                    </div>
                                    
                                    <!-- Теги -->
                                    <?php 
                                    $tags = json_decode($post['tags'], true);
                                    if (!empty($tags) && is_array($tags)): 
                                    ?>
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            <?php foreach (array_slice($tags, 0, 3) as $tag): ?>
                                                <span class="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">
                                                    #<?php echo htmlspecialchars($tag); ?>
                                                </span>
                                            <?php endforeach; ?>
                                            <?php if (count($tags) > 3): ?>
                                                <span class="text-xs text-gray-500">+<?php echo count($tags) - 3; ?> еще</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Действия -->
                            <div class="flex items-center space-x-2 ml-4">
                                <?php render_button([
                                    'href' => '?action=edit&id=' . $post['id'],
                                    'text' => __('common.edit', 'Редактировать'),
                                    'variant' => 'secondary',
                                    'size' => 'sm'
                                ]); ?>
                                
                                <?php if ($post['status'] === 'draft'): ?>
                                    <form method="POST" class="inline-block">
                                        <input type="hidden" name="action" value="publish">
                                        <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                                        <?php render_button([
                                            'type' => 'submit',
                                            'text' => __('blog.publish', 'Опубликовать'),
                                            'variant' => 'primary',
                                            'size' => 'sm'
                                        ]); ?>
                                    </form>
                                <?php endif; ?>
                                
                                <button type="button" 
                                        class="text-red-400 hover:text-red-600 p-1" 
                                        title="<?php echo __('common.delete', 'Удалить'); ?>"
                                        onclick="confirmDeleteBlogPost(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['title'], ENT_QUOTES); ?>')">
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

<!-- JavaScript функции для удаления статей блога -->
<script>
// Делаем функции глобальными сразу
window.confirmDeleteBlogPost = async function(postId, title) {
    console.log('🚀 confirmDeleteBlogPost вызвана:', postId, title);
    
    // Обрезаем заголовок если он слишком длинный
    const shortTitle = title.length > 50 ? title.substring(0, 50) + '...' : title;
    const message = `Вы уверены, что хотите удалить статью "${shortTitle}"? Это действие нельзя отменить. Все связанные файлы и переводы также будут удалены.`;
    
    // Проверяем, доступна ли функция showConfirmationModal
    if (typeof showConfirmationModal === 'function') {
        console.log('✅ Используем модальное окно');
        const confirmed = await showConfirmationModal(message, 'Удаление статьи');
        
        if (confirmed) {
            deleteBlogPost(postId);
        }
    } else {
        console.log('⚠️ Используем fallback confirm');
        // Fallback к обычному confirm
        if (confirm(message)) {
            deleteBlogPost(postId);
        }
    }
};

window.deleteBlogPost = function(postId) {
    console.log('🗑️ deleteBlogPost вызвана для ID:', postId);
    
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
    idInput.value = postId;
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = 'csrf_token';
    csrfInput.value = '<?php echo $csrf_token; ?>';
    
    form.appendChild(actionInput);
    form.appendChild(idInput);
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    console.log('📤 Отправляем форму удаления статьи блога...');
    form.submit();
};

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ Функции удаления статей блога инициализированы');
});
</script>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
    <!-- Форма создания/редактирования статьи -->
    <div class="max-w-6xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">
                <?php echo $action === 'create' ? __('blog.create_title', 'Создать статью') : __('blog.edit_title', 'Редактировать статью'); ?>
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
                
                <!-- Основная информация -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('blog.basic_info', 'Основная информация'); ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Заголовок -->
                        <?php render_input_field([
                            'name' => 'title',
                            'label' => __('blog.title', 'Заголовок статьи'),
                            'placeholder' => __('blog.title_placeholder', 'Введите заголовок статьи'),
                            'required' => true,
                            'value' => $current_post['title'] ?? ''
                        ]); ?>
                        
                        <!-- Краткое описание -->
                        <?php render_textarea_field([
                            'name' => 'excerpt',
                            'label' => __('blog.excerpt', 'Краткое описание'),
                            'placeholder' => __('blog.excerpt_placeholder', 'Краткое описание статьи для превью'),
                            'rows' => 3,
                            'value' => $current_post['excerpt'] ?? ''
                        ]); ?>

                        <!-- Slug (URL) -->
                        <?php render_input_field([
                            'name' => 'slug',
                            'label' => __('blog.slug', 'URL статьи (slug)'),
                            'placeholder' => __('blog.slug_placeholder', 'url-statii'),
                            'help' => __('blog.slug_help', 'Автоматически генерируется из заголовка. Только латиница, цифры и дефисы.'),
                            'value' => $current_post['slug'] ?? ''
                        ]); ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Категория -->
                            <?php render_dropdown_field([
                                'name' => 'category',
                                'id' => 'category',
                                'label' => __('blog.category', 'Категория'),
                                'placeholder' => __('blog.category_placeholder', 'Выберите категорию'),
                                'value' => $current_post['category'] ?? '',
                                'options' => [
                                    ['value' => 'tips', 'text' => __('blog.category_tips', 'Советы')],
                                    ['value' => 'faq', 'text' => __('blog.category_faq', 'FAQ')],
                                    ['value' => 'news', 'text' => __('blog.category_news', 'Новости')],
                                    ['value' => 'guides', 'text' => __('blog.category_guides', 'Руководства')]
                                ],
                                'required' => true
                            ]); ?>
                            
                            <!-- Тип статьи -->
                            <?php render_dropdown_field([
                                'name' => 'post_type',
                                'id' => 'post_type',
                                'label' => __('blog.type', 'Тип статьи'),
                                'placeholder' => __('blog.type_placeholder', 'Выберите тип статьи'),
                                'value' => $current_post['post_type'] ?? 'article',
                                'options' => [
                                    ['value' => 'article', 'text' => __('blog.type_article', 'Статья')],
                                    ['value' => 'faq', 'text' => __('blog.type_faq', 'FAQ')],
                                    ['value' => 'news', 'text' => __('blog.type_news', 'Новость')],
                                    ['value' => 'tips', 'text' => __('blog.type_tips', 'Совет')]
                                ]
                            ]); ?>
                        </div>
                    </div>
                </div>
                
                <!-- Содержание -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('blog.content', 'Содержание статьи'); ?>
                    </h3>
                    
                    <!-- WYSIWYG редактор -->
                    <div class="space-y-2">
                        <label for="wysiwyg-editor" class="block text-sm font-medium text-gray-700">
                            <?php echo __('blog.content_label', 'Полный текст статьи'); ?> *
                        </label>
                        <div id="wysiwyg-editor" 
                             class="min-h-[400px] border border-gray-300 rounded-md focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500"
                             data-value="<?php echo htmlspecialchars($current_post['content'] ?? ''); ?>">
                        </div>
                        <input type="hidden" id="wysiwyg-editor_hidden" name="content" value="<?php echo htmlspecialchars($current_post['content'] ?? ''); ?>">
                    </div>
                    
                    <p class="text-sm text-gray-500 mt-2">
                        <?php echo __('blog.content_help', 'Поддерживается HTML разметка. Используйте теги h2, h3, p, ul, li для структурирования контента.'); ?>
                    </p>
                </div>
                
                <!-- Изображения и SEO -->
                <div class="border-b border-gray-200 pb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('blog.media_seo', 'Медиа и SEO'); ?>
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Главное изображение -->
                        <?php render_image_upload_field([
                            'name' => 'featured_image',
                            'id' => 'featured_image',
                            'label' => __('blog.featured_image', 'Главное изображение'),
                            'current_image' => !empty($current_post['featured_image']) ? '/assets/uploads/blog/' . $current_post['featured_image'] : '',
                            'accept' => 'image/*',
                            'required' => false
                        ]); ?>
                        
                        <!-- Теги -->
                        <?php render_input_field([
                            'name' => 'tags',
                            'label' => __('blog.tags', 'Теги'),
                            'placeholder' => __('blog.tags_placeholder', 'Через запятую: ремонт, советы, материалы'),
                            'value' => (isset($current_post['tags']) && is_array($current_post['tags'])) ? implode(', ', $current_post['tags']) : ''
                        ]); ?>
                        
                        <!-- Meta Title -->
                        <?php render_input_field([
                            'name' => 'meta_title',
                            'label' => __('blog.meta_title', 'Meta Title'),
                            'placeholder' => __('blog.meta_title_placeholder', 'SEO заголовок для поисковых систем'),
                            'value' => $current_post['meta_title'] ?? ''
                        ]); ?>
                        
                        <!-- Meta Description -->
                        <?php render_textarea_field([
                            'name' => 'meta_description',
                            'label' => __('blog.meta_description', 'Meta Description'),
                            'placeholder' => __('blog.meta_description_placeholder', 'SEO описание для поисковых систем (до 160 символов)'),
                            'rows' => 3,
                            'value' => $current_post['meta_description'] ?? ''
                        ]); ?>
                        
                        <!-- Keywords -->
                        <?php render_input_field([
                            'name' => 'keywords',
                            'label' => __('blog.keywords', 'Ключевые слова'),
                            'placeholder' => __('blog.keywords_placeholder', 'ключевое слово, другое слово, третье слово'),
                            'value' => $current_post['keywords'] ?? ''
                        ]); ?>
                    </div>
                </div>
                
                <!-- Настройки публикации -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <?php echo __('blog.publish_settings', 'Настройки публикации'); ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Статус -->
                        <?php render_dropdown_field([
                            'name' => 'status',
                            'id' => 'status',
                            'label' => __('blog.status', 'Статус'),
                            'placeholder' => __('blog.status_placeholder', 'Выберите статус'),
                            'value' => $current_post['status'] ?? 'draft',
                            'options' => [
                                ['value' => 'draft', 'text' => __('blog.status_draft', 'Черновик')],
                                ['value' => 'published', 'text' => __('blog.status_published', 'Опубликовано')]
                            ]
                        ]); ?>
                        
                        <!-- Дата публикации -->
                        <?php render_input_field([
                            'type' => 'datetime-local',
                            'name' => 'published_at',
                            'label' => __('blog.publish_date', 'Дата публикации'),
                            'value' => (isset($current_post['published_at']) && $current_post['published_at']) ? date('Y-m-d\TH:i', strtotime($current_post['published_at'])) : date('Y-m-d\TH:i')
                        ]); ?>
                        
                        <!-- Рекомендуемая статья -->
                        <div class="flex items-center">
                            <input id="featured" name="featured" type="checkbox" value="1" <?php echo ($current_post['featured'] ?? 0) ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <label for="featured" class="ml-2 block text-sm text-gray-900">
                                <?php echo __('blog.featured', 'Рекомендуемая статья'); ?>
                            </label>
                        </div>
                        
                        <!-- Приоритет сортировки -->
                        <?php render_input_field([
                            'type' => 'number',
                            'name' => 'sort_order',
                            'label' => __('blog.sort_order', 'Приоритет сортировки'),
                            'placeholder' => '0',
                            'value' => $current_post['sort_order'] ?? '0'
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
                        'type' => 'button',
                        'text' => __('blog.preview', 'Предварительный просмотр'),
                        'variant' => 'secondary',
                        'onclick' => 'previewPost()'
                    ]); ?>

                    <?php render_button([
                        'type' => 'submit',
                        'text' => $action === 'create' ? __('blog.create_button', 'Создать статью') : __('blog.update_button', 'Обновить статью'),
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
    'content' => $page_content,
    'additional_js' => '
        <!-- Quill.js CSS -->
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        
        <!-- Quill.js JS -->
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        
        <!-- WYSIWYG Editor -->
        <script src="' . ASSETS_URL . 'js/wysiwyg-editor.js"></script>
        
        <!-- Admin Blog JS -->
        <script src="' . ASSETS_URL . 'js/admin_blog.js"></script>
        
        <script>
        // Инициализация WYSIWYG редактора
        document.addEventListener("DOMContentLoaded", function() {
            if (document.getElementById("wysiwyg-editor")) {
                window.blogEditor = new WysiwygEditor("wysiwyg-editor", {
                    value: document.getElementById("wysiwyg-editor").dataset.value || "",
                    placeholder: "Введите содержание статьи. Поддерживается HTML разметка."
                });
            }
        });
        </script>'
]);
?>

