<?php
/**
 * Страница услуг
 * Baumaster Frontend - Services Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение данных
$seo = get_page_seo_settings('services');
$services = get_services_data();

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section id="hero" class="pt-16 bg-cover bg-center bg-no-repeat relative min-h-screen flex items-center" style="background-image: url('/assets/images/preview/services.png'); background-size: cover; background-position: center center; background-attachment: scroll; -webkit-background-size: cover;">
    <!-- Overlay for better text readability -->
    <div class="hero-overlay absolute inset-0 bg-black bg-opacity-30" style="z-index: 1;"></div>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="text-center">
            <h1 id="hero-title" class="font-montserrat font-semibold text-3xl lg:text-6xl text-white mb-6 leading-tight hero-text-shadow">
                <?php 
                $title = $seo['h1'] ?? 'Наши услуги';
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
                Выполняем все виды внутренних работ во Франкфурте. От небольшого косметического ремонта 
                до комплексной реконструкции под ключ.
            </p>
            <?php render_frontend_button([
                'text' => 'Получить консультацию',
                'variant' => 'primary',
                'size' => 'lg',
                'href' => 'contact.php'
            ]); ?>
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

/* Scroll-triggered animations for sections */
.process-title-animate,
.process-subtitle-animate,
.pricing-title-animate,
.pricing-subtitle-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.8s ease-out;
}

.process-title-animate.animate,
.process-subtitle-animate.animate,
.pricing-title-animate.animate,
.pricing-subtitle-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Service card animations */
.service-card-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.service-card-animate.animate {
    opacity: 1;
    transform: translateY(0);
}

/* Process step animations */
.process-step-animate {
    opacity: 0;
    transform: translateX(-50px);
    transition: all 0.8s ease-out;
}

.process-step-animate.animate {
    opacity: 1;
    transform: translateX(0);
}

/* Pricing card animations */
.pricing-card-animate {
    opacity: 0;
    transform: translateY(30px);
    transition: all 0.6s ease-out;
}

.pricing-card-animate.animate {
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

.service-card-animate .bg-white:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.service-card-animate .bg-white:hover img {
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

/* Grid alignment for equal height cards */
.grid {
    align-items: stretch;
}

/* Ensure all service cards have equal height */
.service-card-animate {
    height: 100%;
}

/* Ensure all pricing cards have equal height */
.pricing-card-animate {
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

/* Enhanced Service Modal Animations */
#serviceModal {
    opacity: 0;
    visibility: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(0px);
}

#serviceModal.show {
    opacity: 1;
    visibility: visible;
    backdrop-filter: blur(8px);
    z-index: 99;
}

#serviceModalContent {
    transform: translateY(50px) scale(0.9);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.1s;
}

#serviceModal.show #serviceModalContent {
    transform: translateY(0) scale(1);
    opacity: 1;
}

/* Service Modal Header Animation */
#serviceModal .sticky.top-0 {
    transform: translateY(-20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.2s;
    z-index: 10;
    position: sticky;
    top: 0;
}

#serviceModal.show .sticky.top-0 {
    transform: translateY(0);
    opacity: 1;
}

/* Service Modal Content Animation */
#serviceModal .p-6 > * {
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#serviceModal.show .p-6 > * {
    transform: translateY(0);
    opacity: 1;
}

#serviceModal.show .p-6 > *:nth-child(1) { transition-delay: 0.3s; }
#serviceModal.show .p-6 > *:nth-child(2) { transition-delay: 0.4s; }
#serviceModal.show .p-6 > *:nth-child(3) { transition-delay: 0.5s; }
#serviceModal.show .p-6 > *:nth-child(4) { transition-delay: 0.6s; }
#serviceModal.show .p-6 > *:nth-child(5) { transition-delay: 0.7s; }

/* Service Modal Image Animation */
#serviceModal img {
    transform: scale(1.1);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.2s;
}

#serviceModal.show img {
    transform: scale(1);
    opacity: 1;
}

/* Service Modal Button Animation */
#serviceModal button {
    transform: translateY(10px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.4s;
}

#serviceModal.show button {
    transform: translateY(0);
    opacity: 1;
}

/* Service Modal Close Button Hover Effect */
#serviceModal .sticky.top-0 button:hover {
    transform: rotate(90deg) scale(1.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Service Modal Gallery Images Animation */
#serviceModal .grid img {
    transform: scale(0.8);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#serviceModal.show .grid img {
    transform: scale(1);
    opacity: 1;
}

#serviceModal.show .grid img:nth-child(1) { transition-delay: 0.3s; }
#serviceModal.show .grid img:nth-child(2) { transition-delay: 0.4s; }
#serviceModal.show .grid img:nth-child(3) { transition-delay: 0.5s; }
#serviceModal.show .grid img:nth-child(4) { transition-delay: 0.6s; }
#serviceModal.show .grid img:nth-child(5) { transition-delay: 0.7s; }
#serviceModal.show .grid img:nth-child(6) { transition-delay: 0.8s; }

/* Service Modal List Items Animation */
#serviceModal ul li {
    transform: translateX(-20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#serviceModal.show ul li {
    transform: translateX(0);
    opacity: 1;
}

#serviceModal.show ul li:nth-child(1) { transition-delay: 0.3s; }
#serviceModal.show ul li:nth-child(2) { transition-delay: 0.4s; }
#serviceModal.show ul li:nth-child(3) { transition-delay: 0.5s; }
#serviceModal.show ul li:nth-child(4) { transition-delay: 0.6s; }
#serviceModal.show ul li:nth-child(5) { transition-delay: 0.7s; }
#serviceModal.show ul li:nth-child(6) { transition-delay: 0.8s; }

/* Service Modal Responsive Animations */
@media (max-width: 768px) {
    #serviceModalContent {
        transform: translateY(30px) scale(0.95);
        margin: 0.5rem;
        max-height: 98vh;
    }
    
    #serviceModal.show #serviceModalContent {
        transform: translateY(0) scale(1);
    }
}

/* Service Modal Gallery Optimization */
#serviceModal .grid {
    padding-right: 8px;
}

#serviceModal .grid::-webkit-scrollbar {
    width: 6px;
}

#serviceModal .grid::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

#serviceModal .grid::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#serviceModal .grid::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Service Info Cards Styling */
.service-info-card,
.service-description-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: visible;
    height: auto;
    max-height: none;
}

.service-info-card:hover,
.service-description-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-color: #d1d5db;
}

/* Service Info Header */
.service-info-header,
.service-description-header {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f3f4f6;
}

.service-info-icon,
.service-description-icon {
    width: 32px;
    height: 32px;
    background: #f3f4f6;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    margin-right: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.service-info-card:hover .service-info-icon,
.service-description-card:hover .service-description-icon {
    background: #e5e7eb;
    color: #374151;
}

.service-info-title,
.service-description-title {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
}

/* Service Info Content */
.service-info-content {
    space-y: 8px;
    overflow: visible;
    height: auto;
    max-height: none;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f9fafb;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.info-item:last-child {
    border-bottom: none;
}

.info-item:hover {
    background-color: #f9fafb;
    border-radius: 4px;
    padding: 8px 12px;
    margin: 0 -12px;
}

.info-label {
    display: flex;
    align-items: center;
    font-size: 0.8rem;
    font-weight: 500;
    color: #6b7280;
    gap: 6px;
}

.info-value {
    font-size: 0.8rem;
    font-weight: 600;
    color: #374151;
    padding: 2px 8px;
    background-color: #f3f4f6;
    border-radius: 4px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.info-item:hover .info-value {
    background-color: #e5e7eb;
}

.price-value {
    background: #d1fae5;
    color: #065f46;
    font-weight: 600;
}

.status-value.active {
    background: #d1fae5;
    color: #065f46;
}

.status-value.inactive {
    background: #fee2e2;
    color: #991b1b;
}

/* Service Description Content */
.service-description-content {
    padding-top: 4px;
    overflow: visible;
    height: auto;
    max-height: none;
}

.description-text {
    font-size: 0.9rem;
    line-height: 1.6;
    color: #6b7280;
    margin: 0;
    text-align: left;
}

/* Responsive Design */
@media (max-width: 768px) {
    .py-20 > div:first-child {
        margin: 0 5%;
    }
    
    footer > div:first-child {
        margin: 0 5%;
    }
    
    .service-info-card,
    .service-description-card {
        padding: 12px;
        margin-bottom: 12px;
    }
    
    .service-info-header,
    .service-description-header {
        margin-bottom: 8px;
        padding-bottom: 6px;
    }
    
    .service-info-icon,
    .service-description-icon {
        width: 28px;
        height: 28px;
        margin-right: 8px;
    }
    
    .service-info-title,
    .service-description-title {
        font-size: 0.9rem;
    }
    
    .info-item {
        /* flex-direction: column; */
        align-items: flex-start;
        gap: 4px;
    }
    
    .info-label {
        font-size: 0.75rem;
    }
    
    .info-value {
        font-size: 0.75rem;
        align-self: flex-end;
    }
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
.pricing-card-animate .bg-white h3 {
    transition: color 0.3s ease;
}

.service-card-animate .bg-white:hover h3,
.pricing-card-animate .bg-white:hover h3 {
    color: #3b82f6;
}

/* Price and button smooth transitions */
.service-card-animate .bg-white .font-semibold,
.pricing-card-animate .bg-white .font-semibold {
    transition: all 0.3s ease;
}

.service-card-animate .bg-white:hover .font-semibold,
.pricing-card-animate .bg-white:hover .font-semibold {
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
.pricing-card-animate {
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

/* Pricing card hover effects */
.pricing-card-animate .bg-white:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.pricing-card-animate .bg-white img {
    transition: transform 0.3s ease;
}

.pricing-card-animate .bg-white:hover img {
    transform: scale(1.05);
}

/* Grid alignment for equal height cards */
.align-items-stretch {
    align-items: stretch;
}
</style>

<!-- Services Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 align-items-stretch">
            <?php foreach ($services as $index => $service): ?>
                <div class="service-card-animate" data-delay="<?php echo $index * 0.2; ?>">
                    <?php render_service_card($service); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Service Process -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="process-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 process-title-animate">
                Как мы работаем
            </h2>
            <p id="process-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto process-subtitle-animate">
                Простой и понятный процесс от заявки до сдачи объекта
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center process-step-animate" data-delay="0">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">1</div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Заявка</h3>
                <p class="text-text-secondary">Оставьте заявку на сайте или позвоните нам. Ответим в течение 15 минут.</p>
            </div>
            <div class="text-center process-step-animate" data-delay="0.4">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">2</div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Замер</h3>
                <p class="text-text-secondary">Выезжаем на объект, делаем замеры и составляем подробную смету. Бесплатно.</p>
            </div>
            <div class="text-center process-step-animate" data-delay="0.8">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">3</div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Договор</h3>
                <p class="text-text-secondary">Заключаем договор с фиксированными ценами и сроками выполнения работ.</p>
            </div>
            <div class="text-center process-step-animate" data-delay="1.2">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">4</div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Работы</h3>
                <p class="text-text-secondary">Выполняем работы в срок, убираем мусор, сдаём объект под ключ.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="pricing-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 pricing-title-animate">
                Прозрачные цены
            </h2>
            <p id="pricing-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto pricing-subtitle-animate">
                Все цены указаны за квадратный метр работы. Окончательная стоимость рассчитывается после замера.
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 align-items-stretch">
            <div class="pricing-card-animate" data-delay="0">
                <div class="bg-white border-2 border-gray-200 rounded-lg p-8 text-center hover:border-accent-blue transition-colors h-full flex flex-col">
                    <h3 class="font-semibold text-2xl text-text-primary mb-4">Малярные работы</h3>
                    <div class="text-4xl font-bold text-accent-blue mb-2">от 25€</div>
                    <div class="text-text-secondary mb-6">за м²</div>
                    <ul class="text-left space-y-2 text-text-secondary mb-8 flex-grow">
                        <li>• Подготовка поверхности</li>
                        <li>• Грунтовка</li>
                        <li>• Покраска в 2 слоя</li>
                        <li>• Материалы включены</li>
                    </ul>
                    <div class="mt-auto">
                        <?php render_frontend_button([
                            'text' => 'Заказать',
                            'variant' => 'outline',
                            'class' => 'w-full'
                        ]); ?>
                    </div>
                </div>
            </div>
            
            <div class="pricing-card-animate" data-delay="0.2">
                <div class="bg-white border-2 border-accent-blue rounded-lg p-8 text-center relative h-full flex flex-col">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-accent-blue text-white px-4 py-2 rounded-full text-sm">Популярно</span>
                    </div>
                    <h3 class="font-semibold text-2xl text-text-primary mb-4">Укладка полов</h3>
                    <div class="text-4xl font-bold text-accent-blue mb-2">от 35€</div>
                    <div class="text-text-secondary mb-6">за м²</div>
                    <ul class="text-left space-y-2 text-text-secondary mb-8 flex-grow">
                        <li>• Демонтаж старого покрытия</li>
                        <li>• Выравнивание основания</li>
                        <li>• Укладка покрытия</li>
                        <li>• Плинтусы в подарок</li>
                    </ul>
                    <div class="mt-auto">
                        <?php render_frontend_button([
                            'text' => 'Заказать',
                            'variant' => 'primary',
                            'class' => 'w-full'
                        ]); ?>
                    </div>
                </div>
            </div>
            
            <div class="pricing-card-animate" data-delay="0.4">
                <div class="bg-white border-2 border-gray-200 rounded-lg p-8 text-center hover:border-accent-blue transition-colors h-full flex flex-col">
                    <h3 class="font-semibold text-2xl text-text-primary mb-4">Ремонт ванной</h3>
                    <div class="text-4xl font-bold text-accent-blue mb-2">от 150€</div>
                    <div class="text-text-secondary mb-6">за м²</div>
                    <ul class="text-left space-y-2 text-text-secondary mb-8 flex-grow">
                        <li>• Демонтаж и подготовка</li>
                        <li>• Гидроизоляция</li>
                        <li>• Укладка плитки</li>
                        <li>• Установка сантехники</li>
                    </ul>
                    <div class="mt-auto">
                        <?php render_frontend_button([
                            'text' => 'Заказать',
                            'variant' => 'outline',
                            'class' => 'w-full'
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-12">
            <p class="text-text-secondary mb-6">Нужна индивидуальная смета? Оставьте заявку и получите расчёт бесплатно!</p>
            <?php render_frontend_button([
                'text' => 'Получить расчёт',
                'variant' => 'primary',
                'size' => 'lg',
                'href' => 'contact.php'
            ]); ?>
        </div>
    </div>
</section>


<?php
$content = ob_get_clean();
?>

<!-- Service Modal -->
<div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-2">
    <div id="serviceModalContent" class="bg-white rounded-lg max-w-5xl mx-auto max-h-[95vh] overflow-y-auto w-full shadow-2xl">
        <!-- Modal content will be loaded here -->
    </div>
</div>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9998] hidden items-center justify-center p-4">
    <div id="galleryModalContent" class="bg-white rounded-lg max-w-6xl mx-auto max-h-[90vh] overflow-y-auto w-full">
        <!-- Gallery content will be loaded here -->
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-[99999] hidden items-center justify-center p-4">
    <div class="relative max-w-7xl max-h-full h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-10">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full h-full object-contain">
    </div>
</div>

<script>
// Получаем данные услуг из PHP
const servicesData = <?php echo json_encode($services); ?>;

// Service modal
function openServiceModal(serviceId) {
    const service = servicesData.find(s => s.id == serviceId);
    if (!service) return;
    
    const modal = document.getElementById('serviceModal');
    const modalContent = document.getElementById('serviceModalContent');
    
    // Создаем контент модального окна
    modalContent.innerHTML = `
        <div class="bg-white rounded-lg max-w-5xl mx-auto max-h-[95vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-900">${service.title}</h2>
                <button onclick="closeServiceModal()" class="text-gray-400 hover:text-gray-600 transition-all duration-300 hover:rotate-90 hover:scale-110">
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
                    <img src="${service.image}" alt="${service.title}" class="w-full h-64 object-cover rounded-lg transition-all duration-300 hover:scale-105">
                </div>
                ` : ''}
                
                <!-- Service Info Cards -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <!-- Service Information Card -->
                    <div class="service-info-card">
                        <div class="service-info-header">
                            <div class="service-info-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="service-info-title">Информация об услуге</h3>
                        </div>
                        <div class="service-info-content">
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Категория
                                </div>
                                <div class="info-value">${service.category || 'Не указана'}</div>
                            </div>
                            ${service.price ? `
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    Цена
                                </div>
                                <div class="info-value price-value">от ${service.price} €</div>
                            </div>
                            ` : ''}
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Статус
                                </div>
                                <div class="info-value status-value ${service.status === 'active' ? 'active' : 'inactive'}">
                                    ${service.status === 'active' ? 'Активна' : 'Неактивна'}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description Card -->
                    <div class="service-description-card">
                        <div class="service-description-header">
                            <div class="service-description-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="service-description-title">Описание услуги</h3>
                        </div>
                        <div class="service-description-content">
                            <p class="description-text">${service.description}</p>
                        </div>
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
                            <img src="${image}" alt="Галерея" class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-all duration-300 hover:scale-105" onclick="openImageModal('${image}')">
                        `).join('')}
                    </div>
                </div>
                ` : ''}
            </div>
        </div>
    `;
    
    // Показываем модальное окно с анимацией
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Добавляем класс для анимации после небольшой задержки
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
}

function closeServiceModal() {
    const modal = document.getElementById('serviceModal');
    
    // Убираем класс анимации
    modal.classList.remove('show');
    
    // Скрываем модальное окно после завершения анимации
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }, 400); // Время должно совпадать с CSS transition
}

// Gallery modal
function openServiceGallery(serviceId) {
    const service = servicesData.find(s => s.id == serviceId);
    if (!service || !service.gallery || service.gallery.length === 0) return;
    
    const modal = document.getElementById('galleryModal');
    const modalContent = document.getElementById('galleryModalContent');
    
    modalContent.innerHTML = `
        <div class="bg-white rounded-lg max-w-6xl mx-auto max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-900">Галерея: ${service.title}</h2>
                <button onclick="closeGalleryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Gallery Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    ${service.gallery.map(image => `
                        <img src="${image}" alt="Галерея" class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('${image}')">
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeGalleryModal() {
    const modal = document.getElementById('galleryModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Image modal
function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    
    modalImage.src = imageSrc;
    modalImage.alt = 'Галерея';
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modals on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeServiceModal();
        closeGalleryModal();
        closeImageModal();
    }
});

// Close modals on backdrop click
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        closeServiceModal();
        closeGalleryModal();
        closeImageModal();
    }
});

// Предотвращаем закрытие при клике на контент модального окна
document.addEventListener('click', function(event) {
    if (event.target.closest('#serviceModalContent')) {
        event.stopPropagation();
    }
});

// Scroll-triggered animations
function isElementPartiallyInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top < window.innerHeight &&
        rect.bottom > 0
    );
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

function animateOnScroll() {
    // Animate services section
    animateSection(null, null, '.service-card-animate');
    
    // Animate process section
    animateSection('process-title', 'process-subtitle', '.process-step-animate');
    
    // Animate pricing section
    animateSection('pricing-title', 'pricing-subtitle', '.pricing-card-animate');
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
// Рендеринг страницы
render_frontend_layout([
    'title' => $seo['title'],
    'meta_description' => $seo['description'],
    'active_page' => 'services',
    'content' => $content
]);
?>

