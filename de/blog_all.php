<?php
/**
 * Страница всех статей блога с пагинацией - немецкая версия
 * Baumaster Frontend - All Blog Posts Page (German)
 */

// Устанавливаем язык
define('CURRENT_LANG', 'de');

// Подключение компонентов
require_once __DIR__ . '/../ux/layout.php';
require_once __DIR__ . '/../ux/components.php';
require_once __DIR__ . '/../ux/data.php';

// Параметры пагинации
$posts_per_page = 9;
$current_page = max(1, intval($_GET['page'] ?? 1));
$offset = ($current_page - 1) * $posts_per_page;

// Получение данных
$seo = get_seo_data()['blog'];
$seo['title'] = 'Alle Artikel - ' . $seo['title'];

// Получаем общее количество статей
$db = get_database();
$total_posts_result = $db->select('blog_posts', ['status' => 'published'], ['count' => true]);
$total_posts = is_array($total_posts_result) ? count($total_posts_result) : $total_posts_result;
$total_pages = ceil($total_posts / $posts_per_page);

// Получаем статьи для текущей страницы
$blog_posts = get_blog_posts_paginated($posts_per_page, $offset, 'de');

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                Alle Artikel
            </h1>
            <p class="text-xl text-text-secondary max-w-4xl mx-auto">
                Alle Artikel und Neuigkeiten über Renovierungen, neue Materialien und Technologien
            </p>
        </div>
    </div>
</section>

<!-- Blog Posts with Pagination -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Статистика -->
        <div class="text-center mb-12">
            <p class="text-lg text-text-secondary">
                Zeige <?php echo count($blog_posts); ?> von <?php echo $total_posts; ?> Artikeln
            </p>
        </div>
        
        <!-- Сетка статей -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <?php if (empty($blog_posts)): ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>Keine Artikel gefunden</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($blog_posts as $post): ?>
                    <article class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <?php if (!empty($post['featured_image'])): ?>
                            <div class="h-48 overflow-hidden">
                                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>"
                                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        <?php else: ?>
                            <div class="h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">Artikelbild</span>
                            </div>
                        <?php endif; ?>

                        <div class="p-6">
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm text-accent-blue font-medium">
                                    <?php echo format_date($post['published_at'], 'd.m.Y'); ?>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo htmlspecialchars(ucfirst($post['category'])); ?>
                                </span>
                            </div>
                            
                            <h3 class="font-semibold text-xl text-text-primary mb-3">
                                <a href="blog_post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>"
                                   class="hover:text-accent-blue transition-colors">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h3>
                            
                            <?php if (!empty($post['excerpt'])): ?>
                                <p class="text-text-secondary mb-4 line-clamp-3">
                                    <?php echo htmlspecialchars($post['excerpt']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <div class="flex items-center justify-between">
                                <a href="blog_post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>"
                                   class="text-accent-blue font-medium hover:underline">
                                    Weiterlesen →
                                </a>
                                <span class="text-xs text-gray-500">
                                    <?php echo $post['views']; ?> Aufrufe
                                </span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Пагинация -->
        <?php if ($total_pages > 1): ?>
            <div class="flex justify-center">
                <nav class="flex items-center space-x-2" aria-label="Pagination">
                    <!-- Предыдущая страница -->
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?php echo $current_page - 1; ?>" 
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Zurück
                        </a>
                    <?php endif; ?>
                    
                    <!-- Номера страниц -->
                    <?php
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    if ($start_page > 1): ?>
                        <a href="?page=1" 
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            1
                        </a>
                        <?php if ($start_page > 2): ?>
                            <span class="px-3 py-2 text-sm font-medium text-gray-500">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <?php if ($i == $current_page): ?>
                            <span class="px-3 py-2 text-sm font-medium text-white bg-accent-blue border border-accent-blue rounded-md">
                                <?php echo $i; ?>
                            </span>
                        <?php else: ?>
                            <a href="?page=<?php echo $i; ?>" 
                               class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                <?php echo $i; ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <span class="px-3 py-2 text-sm font-medium text-gray-500">...</span>
                        <?php endif; ?>
                        <a href="?page=<?php echo $total_pages; ?>" 
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            <?php echo $total_pages; ?>
                        </a>
                    <?php endif; ?>
                    
                    <!-- Следующая страница -->
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo $current_page + 1; ?>" 
                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Weiter
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
        
        <!-- Кнопка "Назад к FAQ" -->
        <div class="text-center mt-12">
            <a href="blog.php" 
               class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                <svg class="mr-2 -ml-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Zurück zu FAQ
            </a>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

// Рендеринг страницы
render_frontend_layout([
    'title' => $seo['title'],
    'meta_description' => $seo['description'],
    'active_page' => 'blog',
    'content' => $content
]);
?>
