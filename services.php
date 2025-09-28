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
<section class="pt-16 bg-cover bg-center bg-no-repeat relative py-20" style="background-image: url('/assets/images/preview/services.png');">
    <!-- Overlay for better text readability -->
    <div class="hero-overlay absolute inset-0 bg-black bg-opacity-30"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-white mb-6 hero-text-shadow">
                <?php echo htmlspecialchars($seo['h1'] ?? 'Наши услуги'); ?>
            </h1>
            <p class="text-xl text-white max-w-3xl mx-auto mb-8 hero-text-shadow">
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

<!-- Services Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($services as $service): ?>
                <?php render_service_card($service); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Service Process -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Как мы работаем
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Простой и понятный процесс от заявки до сдачи объекта
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">1</div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Заявка</h3>
                <p class="text-text-secondary">Оставьте заявку на сайте или позвоните нам. Ответим в течение 15 минут.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">2</div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Замер</h3>
                <p class="text-text-secondary">Выезжаем на объект, делаем замеры и составляем подробную смету. Бесплатно.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold">3</div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Договор</h3>
                <p class="text-text-secondary">Заключаем договор с фиксированными ценами и сроками выполнения работ.</p>
            </div>
            <div class="text-center">
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
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Прозрачные цены
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Все цены указаны за квадратный метр работы. Окончательная стоимость рассчитывается после замера.
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white border-2 border-gray-200 rounded-lg p-8 text-center hover:border-accent-blue transition-colors">
                <h3 class="font-semibold text-2xl text-text-primary mb-4">Малярные работы</h3>
                <div class="text-4xl font-bold text-accent-blue mb-2">от 25€</div>
                <div class="text-text-secondary mb-6">за м²</div>
                <ul class="text-left space-y-2 text-text-secondary mb-8">
                    <li>• Подготовка поверхности</li>
                    <li>• Грунтовка</li>
                    <li>• Покраска в 2 слоя</li>
                    <li>• Материалы включены</li>
                </ul>
                <?php render_frontend_button([
                    'text' => 'Заказать',
                    'variant' => 'outline',
                    'class' => 'w-full'
                ]); ?>
            </div>
            
            <div class="bg-white border-2 border-accent-blue rounded-lg p-8 text-center relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-accent-blue text-white px-4 py-2 rounded-full text-sm">Популярно</span>
                </div>
                <h3 class="font-semibold text-2xl text-text-primary mb-4">Укладка полов</h3>
                <div class="text-4xl font-bold text-accent-blue mb-2">от 35€</div>
                <div class="text-text-secondary mb-6">за м²</div>
                <ul class="text-left space-y-2 text-text-secondary mb-8">
                    <li>• Демонтаж старого покрытия</li>
                    <li>• Выравнивание основания</li>
                    <li>• Укладка покрытия</li>
                    <li>• Плинтусы в подарок</li>
                </ul>
                <?php render_frontend_button([
                    'text' => 'Заказать',
                    'variant' => 'primary',
                    'class' => 'w-full'
                ]); ?>
            </div>
            
            <div class="bg-white border-2 border-gray-200 rounded-lg p-8 text-center hover:border-accent-blue transition-colors">
                <h3 class="font-semibold text-2xl text-text-primary mb-4">Ремонт ванной</h3>
                <div class="text-4xl font-bold text-accent-blue mb-2">от 150€</div>
                <div class="text-text-secondary mb-6">за м²</div>
                <ul class="text-left space-y-2 text-text-secondary mb-8">
                    <li>• Демонтаж и подготовка</li>
                    <li>• Гидроизоляция</li>
                    <li>• Укладка плитки</li>
                    <li>• Установка сантехники</li>
                </ul>
                <?php render_frontend_button([
                    'text' => 'Заказать',
                    'variant' => 'outline',
                    'class' => 'w-full'
                ]); ?>
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
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-[9997] hidden items-center justify-center p-4">
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

