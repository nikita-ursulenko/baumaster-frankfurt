<?php
/**
 * Страница отдельной статьи блога
 * Baumaster Frontend - Blog Post Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение slug из URL
$slug = $_GET['slug'] ?? '';

// Получение данных статьи
$post = get_blog_post($slug);

if (!$post) {
    // Статья не найдена - перенаправляем на 404 или блог
    header('HTTP/1.0 404 Not Found');
    $content = '
        <section class="pt-16 py-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="font-montserrat font-semibold text-4xl text-text-primary mb-4">
                    Статья не найдена
                </h1>
                <p class="text-xl text-text-secondary mb-8">
                    К сожалению, запрашиваемая статья не существует или была удалена.
                </p>
                <a href="blog.php" class="inline-flex items-center px-6 py-3 bg-accent-blue text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Вернуться к блогу
                </a>
            </div>
        </section>
    ';

    render_frontend_layout([
        'title' => 'Статья не найдена | ' . SITE_NAME,
        'meta_description' => 'Запрашиваемая статья не найдена',
        'active_page' => 'blog',
        'content' => $content
    ]);
    exit;
}

// SEO данные
$seo_title = $post['meta_title'];
$seo_description = $post['meta_description'];
$seo_keywords = $post['keywords'];

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="index.php" class="hover:text-accent-blue">Главная</a></li>
                <li>/</li>
                <li><a href="blog.php" class="hover:text-accent-blue">Блог</a></li>
                <li>/</li>
                <li class="text-gray-900 font-medium"><?php echo htmlspecialchars($post['title']); ?></li>
            </ol>
        </nav>

        <!-- Article Header -->
        <div class="text-center">
            <!-- Category Badge -->
            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-accent-blue/10 text-accent-blue mb-4">
                <?php
                $categories = [
                    'tips' => 'Советы',
                    'faq' => 'FAQ',
                    'news' => 'Новости',
                    'guides' => 'Руководства'
                ];
                echo htmlspecialchars($categories[$post['category']] ?? ucfirst($post['category']));
                ?>
            </span>

            <h1 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
                <?php echo htmlspecialchars($post['title']); ?>
            </h1>

            <?php if (!empty($post['excerpt'])): ?>
                <p class="text-xl text-text-secondary max-w-2xl mx-auto mb-8">
                    <?php echo htmlspecialchars($post['excerpt']); ?>
                </p>
            <?php endif; ?>

            <!-- Article Meta -->
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-500">
                <span>📅 <?php echo format_date($post['published_at'], 'd.m.Y'); ?></span>
                <span>👁 <?php echo $post['views']; ?> просмотров</span>
                <?php if (!empty($post['post_type']) && $post['post_type'] !== 'article'): ?>
                    <span>📝 <?php echo ucfirst($post['post_type']); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Article Content -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <article class="prose prose-lg max-w-none">

            <!-- Featured Image -->
            <?php if (!empty($post['featured_image'])): ?>
                <div class="mb-8">
                    <img src="<?php echo htmlspecialchars($post['featured_image']); ?>"
                         alt="<?php echo htmlspecialchars($post['title']); ?>"
                         class="w-full h-64 lg:h-96 object-cover rounded-lg shadow-lg">
                </div>
            <?php endif; ?>

            <!-- Article Content -->
            <div class="text-gray-700 leading-relaxed">
                <?php echo html_entity_decode($post['content'], ENT_QUOTES, 'UTF-8'); ?>
            </div>

            <!-- Tags -->
            <?php if (!empty($post['tags']) && is_array($post['tags'])): ?>
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($post['tags'] as $tag): ?>
                            <span class="inline-flex px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full">
                                #<?php echo htmlspecialchars($tag); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </article>

        <!-- Navigation -->
        <?php if ($post['navigation']['prev'] || $post['navigation']['next']): ?>
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <?php if ($post['navigation']['prev']): ?>
                        <a href="blog_post.php?slug=<?php echo htmlspecialchars($post['navigation']['prev']['slug']); ?>"
                           class="flex items-center text-accent-blue hover:text-blue-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <div>
                                <div class="text-sm text-gray-500">Предыдущая статья</div>
                                <div class="font-medium"><?php echo htmlspecialchars($post['navigation']['prev']['title']); ?></div>
                            </div>
                        </a>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>

                    <?php if ($post['navigation']['next']): ?>
                        <a href="blog_post.php?slug=<?php echo htmlspecialchars($post['navigation']['next']['slug']); ?>"
                           class="flex items-center text-accent-blue hover:text-blue-700 transition-colors text-right">
                            <div>
                                <div class="text-sm text-gray-500">Следующая статья</div>
                                <div class="font-medium"><?php echo htmlspecialchars($post['navigation']['next']['title']); ?></div>
                            </div>
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    <?php else: ?>
                        <div></div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Articles -->
<?php if (!empty($post['related_posts'])): ?>
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-montserrat font-semibold text-3xl text-text-primary mb-4">
                Похожие статьи
            </h2>
            <p class="text-xl text-text-secondary">
                Читайте также эти материалы
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($post['related_posts'] as $related): ?>
                <article class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <?php if (!empty($related['featured_image'])): ?>
                        <div class="h-48 overflow-hidden">
                            <img src="<?php echo htmlspecialchars($related['featured_image']); ?>"
                                 alt="<?php echo htmlspecialchars($related['title']); ?>"
                                 class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                    <?php else: ?>
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500">Изображение статьи</span>
                        </div>
                    <?php endif; ?>

                    <div class="p-6">
                        <div class="text-sm text-accent-blue font-medium mb-2">
                            <?php echo format_date($related['published_at'], 'd.m.Y'); ?>
                        </div>
                        <h3 class="font-semibold text-xl text-text-primary mb-3">
                            <a href="blog_post.php?slug=<?php echo htmlspecialchars($related['slug']); ?>"
                               class="hover:text-accent-blue transition-colors">
                                <?php echo htmlspecialchars($related['title']); ?>
                            </a>
                        </h3>
                        <?php if (!empty($related['excerpt'])): ?>
                            <p class="text-text-secondary mb-4 line-clamp-3">
                                <?php echo htmlspecialchars($related['excerpt']); ?>
                            </p>
                        <?php endif; ?>
                        <a href="blog_post.php?slug=<?php echo htmlspecialchars($related['slug']); ?>"
                           class="text-accent-blue font-medium hover:underline">
                            Читать далее →
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Contact CTA -->
<section class="py-16 bg-premium-gray">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-montserrat font-semibold text-3xl text-text-primary mb-6">
            Есть вопросы по ремонту?
        </h2>
        <p class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto">
            Свяжитесь с нами, и мы ответим на все ваши вопросы о ремонте и отделке.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <?php render_frontend_button([
                'text' => 'Задать вопрос',
                'variant' => 'primary',
                'size' => 'lg',
                'href' => 'contact.php'
            ]); ?>
            <?php render_frontend_button([
                'text' => 'Позвонить сейчас',
                'variant' => 'outline',
                'size' => 'lg',
                'onclick' => 'window.open("tel:+4969123456789")'
            ]); ?>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

// Рендеринг страницы с SEO данными
render_frontend_layout([
    'title' => $seo_title,
    'meta_description' => $seo_description,
    'meta_keywords' => $seo_keywords,
    'active_page' => 'blog',
    'content' => $content,
    'canonical_url' => SITE_URL . '/blog_post.php?slug=' . urlencode($slug)
]);
?>
