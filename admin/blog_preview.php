<?php
/**
 * Страница предварительного просмотра статьи блога для админ-панели.
 * Симулирует отображение статьи на фронтенде.
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../ux/layout.php'; // Для render_frontend_layout
require_once __DIR__ . '/../ux/components.php'; // Для render_frontend_button

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo 'Method Not Allowed';
    exit;
}

// Получение данных из POST запроса
$postData = [
    'title' => $_POST['title'] ?? '',
    'excerpt' => $_POST['excerpt'] ?? '',
    'content' => $_POST['content'] ?? '',
    'category' => $_POST['category'] ?? 'tips',
    'tags' => $_POST['tags'] ?? '',
    'featured_image' => $_POST['featured_image'] ?? '',
    'meta_title' => $_POST['meta_title'] ?? '',
    'meta_description' => $_POST['meta_description'] ?? '',
    'keywords' => $_POST['keywords'] ?? '',
    'slug' => $_POST['slug'] ?? '',
    'post_type' => $_POST['post_type'] ?? 'article',
    'published_at' => date('Y-m-d H:i:s') // Для превью всегда текущая дата
];

// Обработка тегов (строка в массив)
$postData['tags'] = array_map('trim', explode(',', $postData['tags']));
$postData['tags'] = array_filter($postData['tags']);

// SEO данные для превью
$seo_title = htmlspecialchars($postData['meta_title'] ?: $postData['title'] . ' (Предпросмотр)');
$seo_description = htmlspecialchars($postData['meta_description'] ?: $postData['excerpt'] ?: '');
$seo_keywords = htmlspecialchars($postData['keywords'] ?: '');

// Начало буферизации вывода для контента страницы
ob_start();
?>

<!-- Hero Section (как на blog_post.php) -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="#" onclick="return false;" class="hover:text-accent-blue">Главная</a></li>
                <li>/</li>
                <li><a href="#" onclick="return false;" class="hover:text-accent-blue">Блог</a></li>
                <li>/</li>
                <li class="text-gray-900 font-medium"><?php echo $seo_title; ?></li>
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
                echo htmlspecialchars($categories[$postData['category']] ?? ucfirst($postData['category']));
                ?>
            </span>

            <h1 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
                <?php echo htmlspecialchars($postData['title']); ?>
            </h1>

            <?php if (!empty($postData['excerpt'])): ?>
                <p class="text-xl text-text-secondary max-w-2xl mx-auto mb-8">
                    <?php echo htmlspecialchars($postData['excerpt']); ?>
                </p>
            <?php endif; ?>

            <!-- Article Meta -->
            <div class="flex items-center justify-center space-x-6 text-sm text-gray-500">
                <span>📅 <?php echo format_date($postData['published_at'], 'd.m.Y'); ?> (Предпросмотр)</span>
                <span>👁 0 просмотров (Предпросмотр)</span>
                <?php if (!empty($postData['post_type']) && $postData['post_type'] !== 'article'): ?>
                    <span>📝 <?php echo ucfirst($postData['post_type']); ?></span>
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
            <?php if (!empty($postData['featured_image'])): ?>
                <div class="mb-8">
                    <img src="<?php echo htmlspecialchars($postData['featured_image']); ?>"
                         alt="<?php echo htmlspecialchars($postData['title']); ?>"
                         class="w-full h-64 lg:h-96 object-cover rounded-lg shadow-lg">
                </div>
            <?php endif; ?>

            <!-- Article Content -->
            <div class="text-gray-700 leading-relaxed">
                <?php echo $postData['content']; // HTML content ?>
            </div>

            <!-- Tags -->
            <?php if (!empty($postData['tags'])): ?>
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($postData['tags'] as $tag): ?>
                            <span class="inline-flex px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded-full">
                                #<?php echo htmlspecialchars($tag); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </article>

        <!-- Navigation (placeholder) -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="text-center text-gray-500">
                <p>Навигация между статьями будет доступна после публикации</p>
            </div>
        </div>
    </div>
</section>

<!-- Related Articles (placeholder) -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-montserrat font-semibold text-3xl text-text-primary mb-4">
                Похожие статьи
            </h2>
            <p class="text-xl text-text-secondary">
                Похожие статьи появятся после публикации
            </p>
        </div>
    </div>
</section>

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
    'canonical_url' => SITE_URL . '/blog_post.php?slug=' . urlencode($postData['slug'])
]);
?>
