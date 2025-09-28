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
<section id="hero" class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-6xl text-text-primary mb-6 leading-tight">
                <?php echo htmlspecialchars($seo['h1'] ?? 'Профессиональные внутренние работы во Франкфурте'); ?>
            </h1>
            <p class="text-xl lg:text-2xl text-text-secondary mb-8 leading-relaxed max-w-4xl mx-auto">
                Полный спектр внутренних работ — от малярки до укладки полов. 
                Премиальное качество и надёжность для вашего дома.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <?php render_frontend_button([
                    'text' => 'Бесплатный расчёт',
                    'variant' => 'primary',
                    'size' => 'lg',
                    'href' => 'contact.php'
                ]); ?>
                <?php render_frontend_button([
                    'text' => 'Наши услуги',
                    'variant' => 'outline',
                    'size' => 'lg',
                    'onclick' => "document.getElementById('services').scrollIntoView({behavior: 'smooth'})"
                ]); ?>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Наши услуги
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Выполняем все виды внутренних работ с гарантией качества и в договорные сроки
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach (array_slice($services, 0, 6) as $service): ?>
                <?php render_service_card($service); ?>
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
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Наши работы
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Посмотрите примеры наших проектов — от небольших ремонтов до комплексной реконструкции
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($portfolio as $project): ?>
                <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    
                    <!-- Featured Image -->
                    <div class="relative h-64 bg-gray-200 overflow-hidden group">
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
                    <div class="p-6">
                        <h3 class="font-semibold text-xl text-text-primary mb-3">
                            <?php echo htmlspecialchars($project['title']); ?>
                        </h3>
                        <p class="text-text-secondary mb-4 leading-relaxed line-clamp-3">
                            <?php 
                            $description = $project['description'];
                            $words = explode(' ', $description);
                            if (count($words) > 30) {
                                $description = implode(' ', array_slice($words, 0, 30)) . '...';
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
                        <div class="flex gap-2">
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
                <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
                    О компании Frankfurt Innenausbau
                </h2>
                <p class="text-lg text-text-secondary mb-6 leading-relaxed">
                    Мы команда опытных мастеров, работающих во Франкфурте более 10 лет. 
                    Специализируемся на внутренних работах и знаем все тонкости качественного ремонта.
                </p>
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <?php if (!empty($statistics)): ?>
                        <?php foreach (array_slice($statistics, 0, 4) as $stat): ?>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-accent-blue mb-2"><?php echo htmlspecialchars($stat['number']); ?></div>
                                <div class="text-text-secondary"><?php echo htmlspecialchars($stat['label']); ?></div>
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
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Отзывы наших клиентов
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Читайте, что говорят о нашей работе те, кто уже доверил нам свой ремонт
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($reviews as $review): ?>
                <?php render_review_card($review); ?>
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

<script>
// Данные проектов для модальных окон
const projects = <?php echo json_encode($portfolio); ?>;

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

function openImageModal(imageSrc) {
    // Простое открытие изображения в новой вкладке
    window.open(imageSrc, '_blank');
}

// Закрытие модальных окон по клику вне их
document.addEventListener('click', function(event) {
    if (event.target.id === 'projectModal') {
        closeProjectModal();
    }
    if (event.target.id === 'galleryModal') {
        closeGallery();
    }
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

