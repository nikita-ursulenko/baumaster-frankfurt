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
<section id="hero" class="pt-16 bg-cover bg-center bg-no-repeat relative min-h-screen flex items-center" style="background-image: url('/assets/images/preview/services.png');">
    <!-- Overlay for better text readability -->
    <div class="hero-overlay absolute inset-0 bg-black bg-opacity-30"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="text-center">
            <h1 id="hero-title" class="font-montserrat font-semibold text-4xl lg:text-6xl text-white mb-6 leading-tight hero-text-shadow">
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
            <p id="hero-subtitle" class="text-xl lg:text-2xl text-white mb-8 leading-relaxed max-w-4xl mx-auto hero-text-shadow hero-subtitle-animate">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
<div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-50 z-[9999] hidden items-center justify-center p-4">
    <div id="serviceModalContent" class="bg-white rounded-lg max-w-4xl mx-auto max-h-[90vh] overflow-y-auto w-full">
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
// Получаем данные услуг из PHP
const servicesData = <?php echo json_encode($services); ?>;

// Service modal
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

