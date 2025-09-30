<?php
/**
 * Страница FAQ/Блог
 * Baumaster Frontend - FAQ/Blog Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение данных
$seo = get_seo_data()['blog'];
$faq = get_faq_data();
$blog_posts = get_blog_posts(6);

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section id="hero" class="pt-16 bg-cover bg-center bg-no-repeat relative min-h-screen flex items-center" style="background-image: url('/assets/images/preview/FAQ.png'); background-size: cover; background-position: center center; background-attachment: scroll; -webkit-background-size: cover;">
    <!-- Overlay for better text readability -->
    <div class="hero-overlay absolute inset-0 bg-black bg-opacity-30" style="z-index: 1;"></div>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="text-center">
            <h1 id="hero-title" class="font-montserrat font-semibold text-3xl lg:text-6xl text-white mb-6 leading-tight hero-text-shadow">
                <?php 
                $title = 'Часто задаваемые вопросы';
                $words = explode(' ', $title);
                $directions = ['left', 'right', 'top', 'bottom'];
                foreach ($words as $index => $word) {
                    $direction = $directions[$index % count($directions)];
                    echo '<span class="hero-word hero-word-' . $direction . ' inline-block opacity-0" style="animation-delay: ' . ($index * 0.15) . 's;">' . htmlspecialchars($word) . '</span>';
                    if ($index < count($words) - 1) {
                        echo ' ';
                    }
                }
                ?>
            </h1>
            <p id="hero-subtitle" class="text-lg lg:text-2xl text-white mb-8 leading-relaxed max-w-4xl mx-auto hero-text-shadow hero-subtitle-animate">
                Ответы на самые популярные вопросы о ремонте, сроках, стоимости и процессе работы. 
                Не нашли ответ? Свяжитесь с нами напрямую.
            </p>
        </div>
    </div>
</section>

<style>
/* Hero word animations from different directions */
.hero-word {
    animation: fadeIn 0.8s ease-out forwards;
}

/* Word from left */
.hero-word-left {
    transform: translateX(-50px);
    animation: slideInFromLeft 0.8s ease-out forwards;
}

/* Word from right */
.hero-word-right {
    transform: translateX(50px);
    animation: slideInFromRight 0.8s ease-out forwards;
}

/* Word from top */
.hero-word-top {
    transform: translateY(-30px);
    animation: slideInFromTop 0.8s ease-out forwards;
}

/* Word from bottom */
.hero-word-bottom {
    transform: translateY(30px);
    animation: slideInFromBottom 0.8s ease-out forwards;
}

/* Keyframes for different directions */
@keyframes slideInFromLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInFromRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInFromTop {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInFromBottom {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Ensure proper spacing between words */
.hero-word + .hero-word {
    margin-left: 0.1em;
}

/* Subtitle animation */
.hero-subtitle-animate {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease-out forwards;
    animation-delay: 0.8s;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Fade in up animations */
.fade-in-up {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-in-up.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Smooth transitions for all interactive elements */
.card, .blog-card, .faq-item, .stat-item, .feature-item {
    transition: all 0.3s ease;
}

.card:hover, .blog-card:hover, .faq-item:hover, .stat-item:hover, .feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.card img, .blog-card img, .faq-item img, .stat-item img, .feature-item img {
    transition: transform 0.3s ease;
}

.card:hover img, .blog-card:hover img, .faq-item:hover img, .stat-item:hover img, .feature-item:hover img {
    transform: scale(1.05);
}

/* Button smooth transitions */
button, .btn {
    transition: all 0.3s ease;
}

button:hover, .btn:hover {
    transform: translateY(-2px);
    /* box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); */
}

/* Link smooth transitions */
a {
    transition: all 0.3s ease;
}

a:hover {
    transform: translateY(-1px);
}

/* Form elements smooth transitions */
input, textarea, select {
    transition: all 0.3s ease;
}

input:focus, textarea:focus, select:focus {
    transform: scale(1.02);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Modal smooth transitions */
.modal {
    transition: all 0.3s ease;
}

.modal-backdrop {
    transition: opacity 0.3s ease;
}

.modal-content {
    transition: all 0.3s ease;
    transform: scale(0.9);
}

.modal.show .modal-content {
    transform: scale(1);
}

/* Smooth scroll behavior */
html {
    scroll-behavior: smooth;
}

/* Section transitions */
section {
    transition: all 0.3s ease;
}

/* Card content smooth transitions */
.card h3, .blog-card h3, .faq-item h3, .stat-item h3, .feature-item h3 {
    transition: color 0.3s ease;
}

.card:hover h3, .blog-card:hover h3, .faq-item:hover h3, .stat-item:hover h3, .feature-item:hover h3 {
    color: #3b82f6;
}

/* Price and button smooth transitions */
.card .font-semibold, .blog-card .font-semibold, .faq-item .font-semibold, .stat-item .font-semibold, .feature-item .font-semibold {
    transition: all 0.3s ease;
}

.card:hover .font-semibold, .blog-card:hover .font-semibold, .faq-item:hover .font-semibold, .stat-item:hover .font-semibold, .feature-item:hover .font-semibold {
    transform: scale(1.05);
}

/* Smooth loading states */
.loading {
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

/* Smooth hover effects for text */
.text-accent-blue {
    transition: all 0.3s ease;
}

.text-accent-blue:hover {
    transform: scale(1.05);
}

/* Enhanced smooth transitions for better UX */
* {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth hover effects for all cards */
.card, .blog-card, .faq-item, .stat-item, .feature-item {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Tips card smooth upscale on hover */
.tips-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.tips-card:hover {
    transform: scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* Smooth image transitions */
img {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth text transitions */
h1, h2, h3, h4, h5, h6, p, span, div {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth background transitions */
.bg-white, .bg-gray-50, .bg-gray-100 {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth shadow transitions */
.shadow-sm, .shadow, .shadow-lg, .shadow-xl {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth border transitions */
.border, .border-2, .border-gray-200, .border-accent-blue {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth color transitions */
.text-text-primary, .text-text-secondary, .text-accent-blue {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth opacity transitions */
.opacity-0, .opacity-50, .opacity-75, .opacity-100 {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Smooth transform transitions */
.transform, .translateY, .scale, .rotate {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .py-20 > div:first-child {
        margin: 0 5%;
    }
    
    footer > div:first-child {
        margin: 0 5%;
    }
}
</style>

<!-- FAQ Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-4">
            <?php foreach ($faq as $index => $item): ?>
                <?php render_faq_item($item, $index); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Tips Section -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="tips-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 fade-in-up">
                Полезные советы
            </h2>
            <p id="tips-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto fade-in-up">
                Практические рекомендации от наших мастеров
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg tips-card">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Подготовка к ремонту</h3>
                <p class="text-text-secondary mb-4">
                    Как правильно подготовить помещение к началу работ и что нужно предусмотреть заранее.
                </p>
                <ul class="text-sm text-text-secondary space-y-1">
                    <li>• Освободите помещение от мебели</li>
                    <li>• Уберите ценные вещи</li>
                    <li>• Обеспечьте доступ к помещению</li>
                    <li>• Согласуйте время работ с соседями</li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg tips-card">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Экономия на ремонте</h3>
                <p class="text-text-secondary mb-4">
                    Легальные способы сэкономить на ремонте без ущерба для качества работ.
                </p>
                <ul class="text-sm text-text-secondary space-y-1">
                    <li>• Покупайте материалы сами</li>
                    <li>• Выбирайте сезон для ремонта</li>
                    <li>• Делайте ремонт поэтапно</li>
                    <li>• Используйте акции и скидки</li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg tips-card">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Контроль качества</h3>
                <p class="text-text-secondary mb-4">
                    На что обратить внимание при приёмке работ и как проверить качество выполнения.
                </p>
                <ul class="text-sm text-text-secondary space-y-1">
                    <li>• Проверьте ровность поверхностей</li>
                    <li>• Осмотрите углы и стыки</li>
                    <li>• Проверьте работу всех систем</li>
                    <li>• Сделайте фото для гарантии</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Blog Posts -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="blog-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 fade-in-up">
                Статьи и новости
            </h2>
            <p id="blog-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto fade-in-up">
                Актуальная информация о ремонте, новых материалах и технологиях
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($blog_posts)): ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>Статьи блога скоро появятся</p>
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
                                <span class="text-gray-500">Изображение статьи</span>
                            </div>
                        <?php endif; ?>

                        <div class="p-6">
                            <div class="text-sm text-accent-blue font-medium mb-2">
                                <?php echo format_date($post['published_at'], 'd.m.Y'); ?>
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
                            <a href="blog_post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>"
                               class="text-accent-blue font-medium hover:underline">
                                Читать далее →
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Кнопка "Все статьи" -->
        <div class="text-center mt-12">
            <a href="blog_all.php" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-accent-blue hover:bg-accent-blue-dark transition-colors duration-200">
                Все статьи
                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 text-center">
        <h2 id="contact-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6 fade-in-up">
            Не нашли ответ на свой вопрос?
        </h2>
        <p id="contact-subtitle" class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto fade-in-up">
            Свяжитесь с нами напрямую, и мы ответим на все ваши вопросы о ремонте и отделке.
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

<script>
// Animation functions
function isElementPartiallyInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top < window.innerHeight &&
        rect.bottom > 0
    );
}

function animateElement(element, delay = 0) {
    if (element && isElementPartiallyInViewport(element) && !element.classList.contains('animate')) {
        setTimeout(() => {
            element.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            element.classList.add('animate');
        }, delay);
    }
}

function animateOnScroll() {
    // Animate tips section
    const tipsTitle = document.getElementById('tips-title');
    const tipsSubtitle = document.getElementById('tips-subtitle');
    
    if (tipsTitle) animateElement(tipsTitle, 0);
    if (tipsSubtitle) animateElement(tipsSubtitle, 200);
    
    // Animate blog section
    const blogTitle = document.getElementById('blog-title');
    const blogSubtitle = document.getElementById('blog-subtitle');
    
    if (blogTitle) animateElement(blogTitle, 0);
    if (blogSubtitle) animateElement(blogSubtitle, 200);
    
    // Animate contact section
    const contactTitle = document.getElementById('contact-title');
    const contactSubtitle = document.getElementById('contact-subtitle');
    
    if (contactTitle) animateElement(contactTitle, 0);
    if (contactSubtitle) animateElement(contactSubtitle, 200);
}

// Throttled scroll event listener
let scrollTimeout;
window.addEventListener('scroll', function() {
    if (scrollTimeout) {
        clearTimeout(scrollTimeout);
    }
    scrollTimeout = setTimeout(animateOnScroll, 10);
});

// Initial check on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(animateOnScroll, 100);
});
</script>

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

