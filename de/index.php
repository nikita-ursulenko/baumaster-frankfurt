<?php
/**
 * Немецкая версия главной страницы
 * Baumaster Frontend - Home Page (German)
 */

// Подключение компонентов
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../ux/layout.php';
require_once __DIR__ . '/../ux/components.php';
require_once __DIR__ . '/../ux/data.php';

// Установка языка
define('CURRENT_LANG', 'de');

// Получение данных
$seo = get_seo_data()['home'];
$services = get_services_data_with_translations('de');
$portfolio = array_slice(get_portfolio_data_translated('de'), 0, 3); // Показываем только первые 3
$reviews = array_slice(get_reviews_data_translated('de'), 0, 4); // Показываем только первые 4
$statistics = get_statistics('de');

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section id="hero" class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-6xl text-text-primary mb-6 leading-tight">
                <?php echo htmlspecialchars($seo['h1'] ?? 'Professionelle Baudienstleistungen in Frankfurt'); ?>
            </h1>
            <p class="text-xl lg:text-2xl text-text-secondary mb-8 leading-relaxed max-w-4xl mx-auto">
                Vollständige Palette von Innenarbeiten — von Malerarbeiten bis zum Verlegen von Böden. 
                Premium-Qualität und Zuverlässigkeit für Ihr Zuhause.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <?php render_frontend_button([
                    'text' => 'Kostenlose Berechnung',
                    'variant' => 'primary',
                    'size' => 'lg',
                    'href' => 'contact.php'
                ]); ?>
                <?php render_frontend_button([
                    'text' => 'Unsere Dienstleistungen',
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
                Unsere Dienstleistungen
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Wir führen alle Arten von Innenarbeiten mit Qualitätsgarantie und pünktlich durch
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach (array_slice($services, -3, 3) as $service): ?>
                <?php render_service_card($service); ?>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <?php render_frontend_button([
                'text' => 'Alle Dienstleistungen',
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
                Unsere Arbeiten
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Sehen Sie sich Beispiele unserer Projekte an — von kleinen Renovierungen bis zur kompletten Rekonstruktion
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($portfolio as $project): ?>
                <div class="portfolio-item bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    
                    <!-- Featured Image -->
                    <div class="relative h-64 bg-gray-200 overflow-hidden group">
                        <img src="<?php echo htmlspecialchars($project['image']); ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                            <button class="opacity-0 group-hover:opacity-100 bg-white text-accent-blue px-4 py-2 rounded font-medium transition-opacity" 
                                    onclick="openProjectModal(<?php echo $project['id']; ?>)">
                                Mehr erfahren
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
                                ⭐ Empfohlen
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
                                // Переводим единицы измерения
                                $area = str_replace('м²', 'm²', $area);
                                echo htmlspecialchars($area); 
                                ?>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php 
                                $duration = $project['duration'];
                                // Переводим единицы времени
                                $duration = str_replace('недель', 'Wochen', $duration);
                                $duration = str_replace('недели', 'Wochen', $duration);
                                $duration = str_replace('неделя', 'Woche', $duration);
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
                                Mehr erfahren
                            </button>
                            <?php if (!empty($project['gallery'])): ?>
                            <button onclick="openGallery(<?php echo $project['id']; ?>)" 
                                    class="px-4 py-2 border border-accent-blue text-accent-blue rounded font-medium hover:bg-accent-blue hover:text-white transition-colors">
                                Galerie
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <?php render_frontend_button([
                'text' => 'Alle Projekte ansehen',
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
                    Über das Unternehmen Frankfurt Innenausbau
                </h2>
                <p class="text-lg text-text-secondary mb-6 leading-relaxed">
                    Wir sind ein Team erfahrener Handwerker, die seit über 10 Jahren in Frankfurt arbeiten. 
                    Wir spezialisieren uns auf Innenarbeiten und kennen alle Feinheiten einer qualitativ hochwertigen Renovierung.
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
                            <div class="text-text-secondary">Zufriedene Kunden</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-accent-blue mb-2">10+</div>
                            <div class="text-text-secondary">Jahre Erfahrung</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-accent-blue mb-2">100%</div>
                            <div class="text-text-secondary">Arbeitsqualität</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-accent-blue mb-2">24/7</div>
                            <div class="text-text-secondary">Kundensupport</div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php render_frontend_button([
                    'text' => 'Mehr über uns',
                    'variant' => 'outline',
                    'size' => 'lg',
                    'href' => 'about.php'
                ]); ?>
            </div>
            <div class="relative">
                <div class="bg-gradient-to-br from-accent-blue to-gray-700 rounded-lg p-8 text-white">
                    <h3 class="font-semibold text-2xl mb-4">Warum wählen Sie uns?</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Qualitätsgarantie für alle Arbeiten
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Einhaltung der vereinbarten Fristen
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Qualitätsmaterialien
                        </li>
                        <li class="flex items-center">
                            <svg class="h-6 w-6 text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Professionelles Team
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
                Bewertungen unserer Kunden
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Lesen Sie, was diejenigen über unsere Arbeit sagen, die uns bereits ihre Renovierung anvertraut haben
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($reviews as $review): ?>
                <?php render_review_card($review); ?>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-12">
            <?php render_frontend_button([
                'text' => 'Alle Bewertungen',
                'variant' => 'outline',
                'size' => 'lg',
                'href' => 'review.php'
            ]); ?>
        </div>
    </div>
</section>


<?php
$content = ob_get_clean();
?>

<!-- Service Modal -->
<div id="serviceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div id="serviceModalContent" class="bg-white rounded-lg max-w-4xl mx-auto max-h-[90vh] overflow-y-auto w-full">
        <!-- Modal content will be loaded here -->
    </div>
</div>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div id="galleryModalContent" class="bg-white rounded-lg max-w-6xl mx-auto max-h-[90vh] overflow-y-auto w-full">
        <!-- Gallery content will be loaded here -->
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden items-center justify-center p-4">
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
                        <h3 class="text-lg font-semibold mb-3">Service-Informationen</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kategorie:</span>
                                <span class="font-medium">${service.category || 'Nicht angegeben'}</span>
                            </div>
                            ${service.price ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Preis:</span>
                                <span class="font-medium">ab ${service.price} €</span>
                            </div>
                            ` : ''}
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-medium">${service.status === 'active' ? 'Aktiv' : 'Inaktiv'}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Beschreibung</h3>
                        <p class="text-gray-700">${service.description}</p>
                    </div>
                </div>
                
                <!-- Features -->
                ${service.features && service.features.length > 0 ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Was ist im Service enthalten</h3>
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
                    <h3 class="text-lg font-semibold mb-3">Arbeitsgalerie</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        ${service.gallery.map(image => `
                            <img src="${image}" alt="Galerie" class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('${image}')">
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
                <h2 class="text-2xl font-semibold text-gray-900">Galerie: ${service.title}</h2>
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
                        <img src="${image}" alt="Galerie" class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-opacity" onclick="openImageModal('${image}')">
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
    modalImage.alt = 'Galerie';
    
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
    'title' => 'Baumaster Frankfurt - Innenausbau & Renovierung',
    'meta_description' => 'Professionelle Innenausbau- und Renovierungsdienstleistungen in Frankfurt am Main. Malerarbeiten, Bodenverlegung, Badezimmerrenovierung.',
    'active_page' => 'home',
    'content' => $content,
    'language' => 'de'
]);
?>