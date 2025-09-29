<?php
/**
 * Главная страница сайта
 * Baumaster Frontend - Home Page
 */

// Подключение компонентов
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Установка языка
define('CURRENT_LANG', 'ru');

// Получение данных
$seo = get_seo_data()['home'];
$services = get_services_data();
$portfolio = array_slice(get_portfolio_data(), 0, 3); // Показываем только первые 3
$reviews = array_slice(get_reviews_data(), 0, 4); // Показываем только первые 4
$statistics = get_statistics();

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section id="hero" class="pt-16 bg-cover bg-center bg-no-repeat relative min-h-screen flex items-center" style="background-image: url('/assets/images/preview/home.png');">
    <!-- Overlay for better text readability -->
    <div class="hero-overlay absolute inset-0 bg-black bg-opacity-30"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="text-center">
            <h1 id="hero-title" class="font-montserrat font-semibold text-4xl lg:text-6xl text-white mb-6 leading-tight hero-text-shadow">
                <?php 
                $title = $seo['h1'] ?? 'Профессиональные внутренние работы во Франкфурте';
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
            <p id="hero-subtitle" class="text-xl lg:text-2xl text-white mb-8 leading-relaxed max-w-4xl mx-auto hero-text-shadow hero-subtitle-animate">
                Полный спектр внутренних работ — от малярки до укладки полов. 
                Премиальное качество и надёжность для вашего дома.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <div id="hero-button-1" class="hero-animate">
                    <?php render_frontend_button([
                        'text' => 'Бесплатный расчёт',
                        'variant' => 'primary',
                        'size' => 'lg',
                        'href' => 'contact.php',
                        'class' => 'hero-btn-primary btn-animated btn-ripple'
                    ]); ?>
                </div>
                <div id="hero-button-2" class="hero-animate">
                    <?php render_frontend_button([
                        'text' => 'Наши услуги',
                        'variant' => 'outline',
                        'size' => 'lg',
                        'onclick' => "document.getElementById('services').scrollIntoView({behavior: 'smooth'})",
                        'class' => 'hero-btn-outline btn-animated btn-ripple'
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Hero section animations with optimized delays */

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

/* Subtitle animation - appears after title with smooth transition */
.hero-subtitle-animate {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    animation-delay: 0.8s; /* Faster appearance after title */
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Buttons animation - appears after subtitle with smooth transition */
.hero-animate {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* First button appears after subtitle with smooth transition */
#hero-button-1 {
    animation-delay: 1.2s; /* Faster appearance */
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Second button appears slightly after first with smooth transition */
#hero-button-2 {
    animation-delay: 1.4s; /* 0.2s after first button */
    transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Fade in up animation */
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

/* Responsive button styling */
.hero-animate {
    flex: 1;
    max-width: 250px;
    min-width: 200px;
}

.hero-animate a,
.hero-animate button {
    width: 100%;
    min-width: 180px;
    text-align: center;
    display: flex;
    justify-content: center;
    align-items: center;
    white-space: nowrap;
    height: 56px;
}

/* Mobile responsive - 65% width and centered */
@media (max-width: 640px) {
    .hero-animate {
        flex: 1;
        max-width: 65%;
        width: 65%;
        margin: 0 auto;
    }
    
    .hero-animate a,
    .hero-animate button {
        min-width: 140px;
        padding: 12px 24px;
        font-size: 16px;
        width: 100%;
        height: 48px;
    }
}

@media (max-width: 480px) {
    .hero-animate {
        width: 65%;
        max-width: 65%;
        margin: 0 auto;
    }
    
    .hero-animate a,
    .hero-animate button {
        width: 100%;
        min-width: 120px;
        padding: 14px 20px;
        height: 48px;
    }
}

/* Services Section Animations */
.services-title-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease-out;
}

.services-title-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

.services-subtitle-animate {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease-out;
    transition-delay: 0.3s;
}

.services-subtitle-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

.service-card-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.service-card-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Service card hover effects */
.service-card-animate .bg-white {
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.service-card-animate:hover .bg-white {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.service-card-animate:hover .bg-white img {
    transform: scale(1.05);
}

.service-card-animate .bg-white img {
    transition: transform 0.3s ease;
}

/* Service card content layout */
.service-card-animate .bg-white > div:last-child {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: 1.5rem;
}

.service-card-animate .bg-white > div:last-child > p {
    flex-grow: 1;
    margin-bottom: 1rem;
}

.service-card-animate .bg-white > div:last-child > ul {
    margin-bottom: 1rem;
}

.service-card-animate .bg-white > div:last-child > div:last-child {
    margin-top: auto;
    padding-top: 1rem;
}

/* Portfolio Section Animations */
.portfolio-title-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease-out;
}

.portfolio-title-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

.portfolio-subtitle-animate {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease-out;
    transition-delay: 0.3s;
}

.portfolio-subtitle-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

.portfolio-card-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.portfolio-card-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Portfolio card hover effects */
.portfolio-card-animate .bg-white {
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.portfolio-card-animate:hover .bg-white {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.portfolio-card-animate:hover .bg-white img {
    transform: scale(1.05);
}

.portfolio-card-animate .bg-white img {
    transition: transform 0.3s ease;
}

/* Portfolio card content layout */
.portfolio-card-animate .bg-white > div:last-child {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    padding: 1.5rem;
}

.portfolio-card-animate .bg-white > div:last-child > p {
    flex-grow: 1;
    margin-bottom: 1rem;
}

.portfolio-card-animate .bg-white > div:last-child > div:last-child {
    margin-top: auto;
    padding-top: 1rem;
}

/* Grid alignment for equal height cards */
.grid {
    align-items: stretch;
}

/* Ensure all service cards have equal height */
.service-card-animate {
    height: 100%;
}

/* Ensure all portfolio cards have equal height */
.portfolio-card-animate {
    height: 100%;
}

/* Ensure all review cards have equal height */
.review-card-animate {
    height: 100%;
}

/* Smooth transitions for all interactive elements */
.service-card-animate .bg-white {
    transition: all 0.3s ease;
}

.service-card-animate .bg-white:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.service-card-animate .bg-white img {
    transition: transform 0.3s ease;
}

.service-card-animate .bg-white:hover img {
    transform: scale(1.05);
}

/* Portfolio card smooth transitions */
.portfolio-card-animate .portfolio-item {
    transition: all 0.3s ease;
}

.portfolio-card-animate .portfolio-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.portfolio-card-animate .portfolio-item img {
    transition: transform 0.3s ease;
}

.portfolio-card-animate .portfolio-item:hover img {
    transform: scale(1.05);
}

/* Review card smooth transitions */
.review-card-animate .bg-white {
    transition: all 0.3s ease;
}

.review-card-animate .bg-white:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.review-card-animate .bg-white img {
    transition: transform 0.3s ease;
}

.review-card-animate .bg-white:hover img {
    transform: scale(1.05);
}

/* Button smooth transitions */
button, .btn {
    transition: all 0.3s ease;
}

button:hover, .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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
.service-card-animate .bg-white h3,
.portfolio-card-animate .portfolio-item h3,
.review-card-animate .bg-white h3 {
    transition: color 0.3s ease;
}

.service-card-animate .bg-white:hover h3,
.portfolio-card-animate .portfolio-item:hover h3,
.review-card-animate .bg-white:hover h3 {
    color: #3b82f6;
}

/* Price and button smooth transitions */
.service-card-animate .bg-white .font-semibold,
.portfolio-card-animate .portfolio-item .font-semibold {
    transition: all 0.3s ease;
}

.service-card-animate .bg-white:hover .font-semibold,
.portfolio-card-animate .portfolio-item:hover .font-semibold {
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
.service-card-animate,
.portfolio-card-animate,
.review-card-animate {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
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

/* About Section Animations */
.about-title-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease-out;
}

.about-title-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

.about-stat-animate {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease-out;
}

.about-stat-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Reviews Section Animations */
.reviews-title-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease-out;
}

.reviews-title-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

.reviews-subtitle-animate {
    opacity: 0;
    transform: translateY(20px);
    transition: all 0.6s ease-out;
    transition-delay: 0.3s;
}

.reviews-subtitle-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

.review-card-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.review-card-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Review card hover effects */
.review-card-animate .bg-white {
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.review-card-animate:hover .bg-white {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

/* Review card content layout */
.review-card-animate .bg-white {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.review-card-animate .bg-white > div:last-child {
    margin-top: auto;
    padding-top: 1rem;
}

/* Grid layout for equal height cards */
.grid.md\\:grid-cols-2.lg\\:grid-cols-3 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    align-items: stretch;
}

.grid.md\\:grid-cols-2.lg\\:grid-cols-4 {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    align-items: stretch;
}

/* Ensure all card containers have equal height */
.service-card-animate,
.portfolio-card-animate,
.review-card-animate {
    display: flex;
    flex-direction: column;
    height: 100%;
}
</style>

<!-- Services Section -->
<section id="services" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="services-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 services-title-animate">
                Наши услуги
            </h2>
            <p id="services-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto services-subtitle-animate">
                Выполняем все виды внутренних работ с гарантией качества и в договорные сроки
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 align-items-stretch">
            <?php foreach (array_slice($services, 0, 6) as $index => $service): ?>
                <div class="service-card-animate" data-delay="<?php echo $index * 0.2; ?>">
                    <?php render_service_card($service); ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <?php render_frontend_button([
                'text' => 'Все услуги',
                'variant' => 'outline',
                'size' => 'lg',
                'href' => 'services.php'
            ]); ?>
        </div>
    </div>
</section>

<!-- Portfolio Section -->
<section id="portfolio" class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="portfolio-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 portfolio-title-animate">
                Наши работы
            </h2>
            <p id="portfolio-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto portfolio-subtitle-animate">
                Посмотрите примеры наших проектов — от небольших ремонтов до комплексной реконструкции
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($portfolio as $index => $project): ?>
                <div class="portfolio-card-animate" data-delay="<?php echo $index * 0.15; ?>">
                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden group h-full flex flex-col">
                    
                    <!-- Featured Image -->
                    <div class="relative h-64 bg-gray-200 overflow-hidden">
                        <img src="<?php echo htmlspecialchars($project['image']); ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                            <button class="opacity-0 group-hover:opacity-100 bg-white text-accent-blue px-4 py-2 rounded font-medium transition-opacity" 
                                    onclick="openProjectModal(<?php echo $project['id']; ?>)">
                                Подробнее
                            </button>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="inline-block px-3 py-1 bg-accent-blue text-white text-sm rounded-full">
                                <?php echo htmlspecialchars($project['category']); ?>
                            </span>
                        </div>
                        <?php if ($project['featured']): ?>
                        <div class="absolute top-4 right-4">
                            <span class="inline-block px-2 py-1 bg-yellow-500 text-white text-xs rounded-full">
                                ⭐ Рекомендуемый
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Project Info -->
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="font-semibold text-xl text-text-primary mb-3">
                            <?php echo htmlspecialchars($project['title']); ?>
                        </h3>
                        <p class="text-text-secondary mb-4 leading-relaxed line-clamp-3 flex-grow">
                            <?php 
                            $description = $project['description'];
                            if (strlen($description) > 200) {
                                $description = substr($description, 0, 200);
                                $lastSpace = strrpos($description, ' ');
                                if ($lastSpace !== false) {
                                    $description = substr($description, 0, $lastSpace);
                                }
                                $description .= '...';
                            }
                            echo htmlspecialchars($description);
                            ?>
                        </p>
                        
                        <!-- Project Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm text-text-secondary">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                <?php 
                                $area = $project['area'];
                                // Оставляем русские единицы измерения
                                echo htmlspecialchars($area); 
                                ?>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php 
                                $duration = $project['duration'];
                                // Оставляем русские единицы времени
                                echo htmlspecialchars($duration); 
                                ?>
                            </div>
                            <?php if ($project['budget']): ?>
                            <div class="flex items-center col-span-2">
                                <svg class="h-4 w-4 mr-2 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                €<?php echo number_format($project['budget'], 0, ',', ' '); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Tags -->
                        <?php if (!empty($project['tags'])): ?>
                        <div class="flex flex-wrap gap-2 mb-4">
                            <?php foreach (array_slice($project['tags'], 0, 3) as $tag): ?>
                                <span class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                                    <?php echo htmlspecialchars($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2 mt-auto">
                            <button onclick="openProjectModal(<?php echo $project['id']; ?>)" 
                                    class="flex-1 bg-accent-blue text-white px-4 py-2 rounded font-medium hover:bg-blue-600 transition-colors">
                                Подробнее
                            </button>
                            <?php if (!empty($project['gallery'])): ?>
                            <button onclick="openGallery(<?php echo $project['id']; ?>)" 
                                    class="px-4 py-2 border border-accent-blue text-accent-blue rounded font-medium hover:bg-accent-blue hover:text-white transition-colors">
                                Галерея
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <?php render_frontend_button([
                'text' => 'Смотреть все проекты',
                'variant' => 'primary',
                'size' => 'lg',
                'href' => 'portfolio.php'
            ]); ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 id="about-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6 about-title-animate">
                    О компании Frankfurt Innenausbau
                </h2>
                <p class="text-lg text-text-secondary mb-6 leading-relaxed">
                    Мы команда опытных мастеров, работающих во Франкфурте более 10 лет. 
                    Специализируемся на внутренних работах и знаем все тонкости качественного ремонта.
                </p>
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <?php if (!empty($statistics)): ?>
                        <?php foreach (array_slice($statistics, 0, 4) as $index => $stat): ?>
                            <div class="about-stat-animate" data-delay="<?php echo $index * 0.2; ?>">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-accent-blue mb-2"><?php echo htmlspecialchars($stat['number']); ?></div>
                                    <div class="text-text-secondary"><?php echo htmlspecialchars($stat['label']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-accent-blue mb-2">500+</div>
                            <div class="text-text-secondary">Довольных клиентов</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-accent-blue mb-2">10+</div>
                            <div class="text-text-secondary">Лет опыта</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-accent-blue mb-2">100%</div>
                            <div class="text-text-secondary">Качество работ</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-accent-blue mb-2">24/7</div>
                            <div class="text-text-secondary">Поддержка клиентов</div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php render_frontend_button([
                    'text' => 'Подробнее о нас',
                    'variant' => 'outline',
                    'size' => 'lg',
                    'href' => 'about.php'
                ]); ?>
            </div>
            <div class="relative">
                <div class="bg-gradient-to-br from-accent-blue to-gray-700 rounded-lg p-8 text-white">
                    <h3 class="font-semibold text-2xl mb-4">Почему выбирают нас?</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Гарантия качества на все работы
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Соблюдение договорных сроков
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Качественные материалы
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Профессиональная команда
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section id="reviews" class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="reviews-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 reviews-title-animate">
                Отзывы наших клиентов
            </h2>
            <p id="reviews-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto reviews-subtitle-animate">
                Читайте, что говорят о нашей работе те, кто уже доверил нам свой ремонт
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($reviews as $index => $review): ?>
                <div class="review-card-animate" data-delay="<?php echo $index * 0.15; ?>">
                    <?php render_review_card($review); ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <?php render_frontend_button([
                'text' => 'Все отзывы',
                'variant' => 'outline',
                'size' => 'lg',
                'href' => 'review.php'
            ]); ?>
        </div>
    </div>
</section>


<!-- Service Modal -->
<div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div id="serviceModalContent" class="bg-white rounded-lg max-w-4xl mx-auto max-h-[90vh] overflow-y-auto w-full">
        <!-- Modal content will be loaded here -->
    </div>
</div>

<!-- Project Modal -->
<div id="projectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-semibold" id="modalTitle">Проект</h3>
                    <button onclick="closeProjectModal()" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div id="modalContent">
                    <!-- Контент будет загружен динамически -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="max-w-6xl w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-semibold text-white">Галерея проекта</h3>
                <button onclick="closeGallery()" class="text-white hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="galleryContent" class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <!-- Изображения будут загружены динамически -->
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-[99999] hidden items-center justify-center p-4">
    <div class="relative max-w-7xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
    </div>
</div>

<script>
// Данные проектов для модальных окон
const projects = <?php echo json_encode($portfolio); ?>;

// Данные услуг для модальных окон
const servicesData = <?php echo json_encode($services); ?>;

// Service modal functions
function openServiceModal(serviceId) {
    const service = servicesData.find(s => s.id == serviceId);
    if (!service) return;
    
    const modal = document.getElementById('serviceModal');
    const modalContent = document.getElementById('serviceModalContent');
    
    modalContent.innerHTML = `
        <div class="bg-white rounded-lg max-w-4xl mx-auto max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-900">${service.title}</h2>
                <button onclick="closeServiceModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <!-- Main Image -->
                ${service.image ? `
                <div class="mb-6">
                    <img src="${service.image}" alt="${service.title}" class="w-full h-64 object-cover rounded-lg">
                </div>
                ` : ''}
                
                <!-- Service Info -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Информация об услуге</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Категория:</span>
                                <span class="font-medium">${service.category || 'Не указана'}</span>
                            </div>
                            ${service.price ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Цена:</span>
                                <span class="font-medium">от ${service.price} €</span>
                            </div>
                            ` : ''}
                            <div class="flex justify-between">
                                <span class="text-gray-600">Статус:</span>
                                <span class="font-medium">${service.status === 'active' ? 'Активна' : 'Неактивна'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Описание</h3>
                        <p class="text-gray-700">${service.description}</p>
                    </div>
                </div>
                
                <!-- Features -->
                ${service.features && service.features.length > 0 ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Что входит в услугу</h3>
                    <ul class="grid md:grid-cols-2 gap-2">
                        ${service.features.map(feature => `
                            <li class="flex items-center text-sm">
                                <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                ${Array.isArray(feature) ? feature.join(', ') : feature}
                            </li>
                        `).join('')}
                    </ul>
                </div>
                ` : ''}
                
                <!-- Gallery -->
                ${service.gallery && service.gallery.length > 0 ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Галерея работ</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        ${service.gallery.map(image => `
                            <img src="${image}" alt="Галерея" class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('${image}')">
                        `).join('')}
                    </div>
                </div>
                ` : ''}
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeServiceModal() {
    const modal = document.getElementById('serviceModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

function openProjectModal(projectId) {
    const project = projects.find(p => p.id == projectId);
    if (!project) return;
    
    document.getElementById('modalTitle').textContent = project.title;
    
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <div class="mb-6">
            <img src="${project.image}" alt="${project.title}" class="w-full h-64 object-cover rounded-lg">
        </div>
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold mb-3">Информация о проекте</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Площадь:</span>
                        <span class="font-medium">${project.area}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Срок:</span>
                        <span class="font-medium">${project.duration}</span>
                    </div>
                    ${project.budget ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Бюджет:</span>
                        <span class="font-medium">€${new Intl.NumberFormat('ru-RU').format(project.budget)}</span>
                    </div>
                    ` : ''}
                    ${project.completion_date ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Дата завершения:</span>
                        <span class="font-medium">${new Date(project.completion_date).toLocaleDateString('ru-RU')}</span>
                    </div>
                    ` : ''}
                    ${project.client_name ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Клиент:</span>
                        <span class="font-medium">${project.client_name}</span>
                    </div>
                    ` : ''}
                    ${project.location ? `
                    <div class="flex justify-between">
                        <span class="text-gray-600">Местоположение:</span>
                        <span class="font-medium">${project.location}</span>
                    </div>
                    ` : ''}
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-3">Описание</h3>
                <p class="text-gray-700 leading-relaxed">${project.description}</p>
            </div>
        </div>
        ${project.technical_info && Object.keys(project.technical_info).length > 0 ? `
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3">Технические детали</h3>
            <div class="grid md:grid-cols-2 gap-4">
                ${project.technical_info.rooms ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Комнат:</span>
                    <span class="font-medium">${project.technical_info.rooms}</span>
                </div>
                ` : ''}
                ${project.technical_info.bathrooms ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Ванных:</span>
                    <span class="font-medium">${project.technical_info.bathrooms}</span>
                </div>
                ` : ''}
                ${project.technical_info.year ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Год:</span>
                    <span class="font-medium">${project.technical_info.year}</span>
                </div>
                ` : ''}
                ${project.technical_info.style ? `
                <div class="flex justify-between">
                    <span class="text-gray-600">Стиль:</span>
                    <span class="font-medium">${project.technical_info.style}</span>
                </div>
                ` : ''}
            </div>
            ${project.technical_info.features && project.technical_info.features.length > 0 ? `
            <div class="mt-4">
                <h4 class="font-medium mb-2">Особенности:</h4>
                <ul class="list-disc list-inside text-sm text-gray-700">
                    ${project.technical_info.features.map(feature => `<li>${feature}</li>`).join('')}
                </ul>
            </div>
            ` : ''}
        </div>
        ` : ''}
        ${project.tags && project.tags.length > 0 ? `
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-3">Теги</h3>
            <div class="flex flex-wrap gap-2">
                ${project.tags.map(tag => `<span class="inline-block px-3 py-1 bg-gray-100 text-gray-600 text-sm rounded">${tag}</span>`).join('')}
            </div>
        </div>
        ` : ''}
        <div class="flex gap-4">
            <button onclick="closeProjectModal()" class="px-6 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition-colors">
                Закрыть
            </button>
            ${project.gallery && project.gallery.length > 0 ? `
            <button onclick="closeProjectModal(); openGallery(${project.id})" class="px-6 py-2 bg-accent-blue text-white rounded hover:bg-blue-600 transition-colors">
                Открыть галерею
            </button>
            ` : ''}
        </div>
    `;
    
    document.getElementById('projectModal').classList.remove('hidden');
}

function closeProjectModal() {
    document.getElementById('projectModal').classList.add('hidden');
}

function openGallery(projectId) {
    const project = projects.find(p => p.id == projectId);
    if (!project || !project.gallery) return;
    
    const galleryContent = document.getElementById('galleryContent');
    galleryContent.innerHTML = project.gallery.map(image => `
        <div class="aspect-square overflow-hidden rounded-lg">
            <img src="${image}" alt="Галерея проекта" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300 cursor-pointer" onclick="openImageModal('${image}')">
        </div>
    `).join('');
    
    document.getElementById('galleryModal').classList.remove('hidden');
}

function closeGallery() {
    document.getElementById('galleryModal').classList.add('hidden');
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    
    modalImage.src = imageSrc;
    modalImage.alt = 'Галерея';
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

// Close modals on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeServiceModal();
        closeProjectModal();
        closeGallery();
    }
});

// Закрытие модальных окон по клику вне их
document.addEventListener('click', function(event) {
    if (event.target.id === 'serviceModal') {
        closeServiceModal();
    }
    if (event.target.id === 'projectModal') {
        closeProjectModal();
    }
    if (event.target.id === 'galleryModal') {
        closeGallery();
    }
});

// Scroll-triggered animations
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

function isElementPartiallyInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top < window.innerHeight &&
        rect.bottom > 0
    );
}

function animateOnScroll() {
    // Animate services section
    animateSection('services-title', 'services-subtitle', '.service-card-animate');
    
    // Animate portfolio section
    animateSection('portfolio-title', 'portfolio-subtitle', '.portfolio-card-animate');
    
    // Animate about section
    animateSection('about-title', null, '.about-stat-animate');
    
    // Animate reviews section
    animateSection('reviews-title', 'reviews-subtitle', '.review-card-animate');
}

function animateSection(titleId, subtitleId, cardsSelector) {
    // Animate title with smooth transition
    const title = document.getElementById(titleId);
    if (title && isElementPartiallyInViewport(title) && !title.classList.contains('animate')) {
        title.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
        title.classList.add('animate');
        
        // Animate subtitle after title with smooth delay
        if (subtitleId) {
            setTimeout(() => {
                const subtitle = document.getElementById(subtitleId);
                if (subtitle && !subtitle.classList.contains('animate')) {
                    subtitle.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                    subtitle.classList.add('animate');
                }
            }, 300);
        }
    }
    
    // Animate cards with smooth transitions
    const cards = document.querySelectorAll(cardsSelector);
    cards.forEach((card, index) => {
        if (isElementPartiallyInViewport(card) && !card.classList.contains('animate')) {
            const delay = parseFloat(card.getAttribute('data-delay')) * 1000;
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.classList.add('animate');
            }, delay);
        }
    });
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
    'active_page' => 'home',
    'content' => $content,
    'language' => 'ru'
]);
?>

