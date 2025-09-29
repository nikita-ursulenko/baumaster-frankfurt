<?php
/**
 * Немецкая версия страницы портфолио
 * Baumaster Frontend - Portfolio Page (German)
 */

// Подключение компонентов
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../ux/layout.php';
require_once __DIR__ . '/../ux/components.php';
require_once __DIR__ . '/../ux/data.php';
require_once __DIR__ . '/../integrations/translation/FastTranslationManager.php';

// Установка языка
define('CURRENT_LANG', 'de');

// Получение данных
$seo = get_seo_data()['portfolio'];
$portfolio = get_portfolio_data_translated('de');
$translation_manager = new FastTranslationManager();

// Начало контента
ob_start();
?>

<style>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Grid alignment for equal height cards */
.grid {
    align-items: stretch;
}

/* Portfolio card content layout */
.portfolio-item {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.portfolio-item .p-6 {
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.portfolio-item .p-6 > p {
    flex-grow: 1;
    margin-bottom: 1rem;
}

.portfolio-item .p-6 > div:last-child {
    margin-top: auto;
}

/* Smooth transitions for all interactive elements */
.portfolio-item {
    transition: all 0.3s ease;
}

.portfolio-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.portfolio-item img {
    transition: transform 0.3s ease;
}

.portfolio-item:hover img {
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
.portfolio-item h3 {
    transition: color 0.3s ease;
}

.portfolio-item:hover h3 {
    color: #3b82f6;
}

/* Price and button smooth transitions */
.portfolio-item .font-semibold {
    transition: all 0.3s ease;
}

.portfolio-item:hover .font-semibold {
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
.portfolio-item {
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

/* Process steps animation - left to right sequence */
.process-step {
    opacity: 0;
    transform: translateX(-50px);
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.process-step.animate {
    opacity: 1;
    transform: translateX(0);
}

/* Staggered delays for process steps */
.process-step:nth-child(1) { transition-delay: 0s; }
.process-step:nth-child(2) { transition-delay: 0.2s; }
.process-step:nth-child(3) { transition-delay: 0.4s; }
.process-step:nth-child(4) { transition-delay: 0.6s; }
.process-step:nth-child(5) { transition-delay: 0.8s; }
.process-step:nth-child(6) { transition-delay: 1s; }
</style>

<!-- Hero Section -->
<section id="hero" class="pt-16 bg-cover bg-center bg-no-repeat relative min-h-screen flex items-center" style="background-image: url('/assets/images/preview/portfolio.png');">
    <!-- Overlay for better text readability -->
    <div class="hero-overlay absolute inset-0 bg-black bg-opacity-30"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="text-center">
            <h1 id="hero-title" class="font-montserrat font-semibold text-4xl lg:text-6xl text-white mb-6 leading-tight hero-text-shadow">
                <?php 
                $title = 'Unser Portfolio';
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
                Sehen Sie sich Beispiele unserer Arbeiten an — von kleinen kosmetischen Renovierungen bis zur 
                kompletten Rekonstruktion von Wohnungen und Büros in Frankfurt.
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
</style>

<!-- Filter Tabs -->
<section class="py-8 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center gap-4">
            <button class="filter-btn active px-6 py-2 rounded-full border-2 border-accent-blue text-accent-blue font-medium hover:bg-accent-blue hover:text-white transition-colors" data-filter="all">
                Alle Projekte
            </button>
            <button class="filter-btn px-6 py-2 rounded-full border-2 border-gray-300 text-text-secondary hover:border-accent-blue hover:text-accent-blue transition-colors" data-filter="apartment">
                Wohnungen
            </button>
            <button class="filter-btn px-6 py-2 rounded-full border-2 border-gray-300 text-text-secondary hover:border-accent-blue hover:text-accent-blue transition-colors" data-filter="bathroom">
                Badezimmer
            </button>
            <button class="filter-btn px-6 py-2 rounded-full border-2 border-gray-300 text-text-secondary hover:border-accent-blue hover:text-accent-blue transition-colors" data-filter="office">
                Büros
            </button>
        </div>
    </div>
</section>

<!-- Portfolio Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 align-items-stretch">
            <?php foreach ($portfolio as $project): ?>
                <?php 
                // Получаем переводы для проекта
                $translated_project = $translation_manager->getTranslatedContent('portfolio', $project['id'], 'de');
                if ($translated_project) {
                    $project = array_merge($project, $translated_project);
                }
                ?>
                <div class="portfolio-item bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden h-full flex flex-col" 
                     data-category="<?php echo strtolower(str_replace(' ', '-', $project['category'])); ?>">
                    
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
                    <div class="p-6 flex flex-col flex-grow">
                        <h3 class="font-semibold text-xl text-text-primary mb-3">
                            <?php echo htmlspecialchars($project['title']); ?>
                        </h3>
                        <p class="text-text-secondary mb-4 line-clamp-3 flex-grow">
                            <?php echo htmlspecialchars($project['description']); ?>
                        </p>
                        
                        <!-- Project Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm text-text-secondary">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                <?php echo htmlspecialchars($project['area']); ?>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php echo htmlspecialchars($project['duration']); ?>
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
                                    <?php 
                                    // Перевод тегов на немецкий
                                    $tag_translations = [
                                        'ремонт' => 'Renovierung',
                                        'квартира' => 'Wohnung',
                                        'лофт' => 'Loft',
                                        'современный стиль' => 'moderner Stil',
                                        'открытая планировка' => 'offene Planung',
                                        'Франкфурт' => 'Frankfurt'
                                    ];
                                    $translated_tag = $tag_translations[$tag] ?? $tag;
                                    echo htmlspecialchars($translated_tag); 
                                    ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-2 mt-auto">
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
    </div>
</section>

<!-- Process Section -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="process-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 fade-in-up">
                Projektphasen
            </h2>
            <p id="process-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto fade-in-up">
                Jedes unserer Projekte durchläuft sorgfältig durchdachte Phasen für ein perfektes Ergebnis
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center process-step">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Planung</h3>
                <p class="text-text-secondary">Wir besprechen Details, erstellen einen Arbeitsplan, wählen Materialien aus und vereinbaren Termine.</p>
            </div>
            
            <div class="text-center process-step">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Vorbereitung</h3>
                <p class="text-text-secondary">Abbruch alter Beschichtungen, Oberflächenvorbereitung, Schutz von Möbeln und Interieur.</p>
            </div>
            
            <div class="text-center process-step">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Ausführung</h3>
                <p class="text-text-secondary">Hauptarbeiten: Montage, Veredelung, Malerei. Regelmäßige Qualitätskontrolle in jeder Phase.</p>
            </div>
            
            <div class="text-center process-step">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Übergabe</h3>
                <p class="text-text-secondary">Endreinigung, Qualitätsprüfung, Beseitigung kleiner Mängel und feierliche Objektübergabe.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
            Möchten Sie ein ähnliches Ergebnis?
        </h2>
        <p class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto">
            Lassen Sie uns Ihr Projekt besprechen und ein einzigartiges Raumkonzept unter Berücksichtigung aller Wünsche und Besonderheiten erstellen.
        </p>
        
        <?php render_frontend_button([
            'text' => 'Projekt besprechen',
            'variant' => 'primary',
            'size' => 'lg',
            'href' => 'contact.php'
        ]); ?>
    </div>
</section>

<!-- Modals -->
<!-- Project Detail Modal -->
<div id="projectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div id="projectModalContent" class="w-full max-w-4xl"></div>
</div>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div id="galleryModalContent" class="w-full max-w-4xl"></div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4">
    <div id="imageModalContent" class="w-full max-w-4xl"></div>
</div>

<script>
// Portfolio data for JavaScript
const portfolioData = <?php echo json_encode($portfolio, JSON_UNESCAPED_UNICODE); ?>;
// Portfolio filtering
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        // Update active state
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('active');
            b.classList.add('border-gray-300', 'text-text-secondary');
            b.classList.remove('border-accent-blue', 'text-accent-blue');
        });
        
        this.classList.add('active');
        this.classList.remove('border-gray-300', 'text-text-secondary');
        this.classList.add('border-accent-blue', 'text-accent-blue');
        
        // Filter items
        const filter = this.dataset.filter;
        document.querySelectorAll('.portfolio-item').forEach(item => {
            if (filter === 'all' || item.dataset.category === filter) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Project modal
// Function to translate text to German
function translateToGerman(text) {
    const translations = {
        // Project titles and descriptions
        'Современный ремонт квартиры в стиле лофт': 'Moderne Wohnungsrenovierung im Loft-Stil',
        'Полная реконструкция двухкомнатной квартиры площадью 75 м² в современном стиле лофт. Проект включал демонтаж старых перегородок, устройство стяжки полов, монтаж теплых полов, установку новых окон и дверей, полную замену электропроводки и сантехники. Кухня была объединена с гостиной в единое пространство, ванная комната расширена за счет коридора. Использованы современные материалы: кварцвиниловая плитка, гипсокартонные конструкции, LED освещение, энергосберегающие окна. Результат - стильное и функциональное жилое пространство с открытой планировкой.': 'Vollständige Renovierung einer 2-Zimmer-Wohnung mit 75 m² im modernen Loft-Stil. Das Projekt umfasste den Abbau alter Trennwände, Estriche, Fußbodenheizung, neue Fenster und Türen sowie vollständigen Austausch von Elektro- und Sanitärinstallationen. Küche und Wohnzimmer wurden zu einem einheitlichen Raum zusammengefasst, das Badezimmer durch den Korridor erweitert. Verwendet wurden moderne Materialien: Quarzvinylfliesen, Gipskartonkonstruktionen, LED-Beleuchtung, energiesparende Fenster. Das Ergebnis: ein stilvoller und funktionaler Wohnraum mit offener Raumaufteilung.',
        
        // Client names and locations
        'Анна и Михаил Шмидт': 'Anna und Michael Schmidt',
        'Франкфурт-на-Майне, район Захсенхаузен': 'Frankfurt am Main, Stadtteil Sachsenhausen',
        
        // Technical details
        'Лофт, современный, минимализм': 'Loft, modern, Minimalismus',
        'Теплые полы': 'Fußbodenheizung',
        'LED освещение': 'LED-Beleuchtung',
        'открытая планировка': 'offene Raumaufteilung',
        'энергосберегающие окна': 'energiesparende Fenster',
        'кварцвиниловая плитка': 'Quarzvinylfliesen',
        'гипсокартонные конструкции': 'Gipskartonkonstruktionen',
        'встроенная мебель': 'Einbaumöbel'
    };
    
    return translations[text] || text;
}

function openProjectModal(projectId) {
    const project = portfolioData.find(p => p.id == projectId);
    if (!project) return;
    
    const modal = document.getElementById('projectModal');
    const modalContent = document.getElementById('projectModalContent');
    
    modalContent.innerHTML = `
        <div class="bg-white rounded-lg max-w-4xl mx-auto max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-900">${translateToGerman(project.title)}</h2>
                <button onclick="closeProjectModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <!-- Featured Image -->
                <div class="mb-6">
                    <img src="${project.image}" alt="${project.title}" class="w-full h-64 object-cover rounded-lg">
                </div>
                
                <!-- Project Info -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Projektinformationen</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fläche:</span>
                                <span class="font-medium">${project.area}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Dauer:</span>
                                <span class="font-medium">${project.duration}</span>
                            </div>
                            ${project.budget ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Budget:</span>
                                <span class="font-medium">€${new Intl.NumberFormat('de-DE').format(project.budget)}</span>
                            </div>
                            ` : ''}
                            ${project.completion_date ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Fertigstellung:</span>
                                <span class="font-medium">${new Date(project.completion_date).toLocaleDateString('de-DE')}</span>
                            </div>
                            ` : ''}
                            ${project.client_name ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Kunde:</span>
                                <span class="font-medium">${translateToGerman(project.client_name)}</span>
                            </div>
                            ` : ''}
                            ${project.location ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Standort:</span>
                                <span class="font-medium">${translateToGerman(project.location)}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Beschreibung</h3>
                        <p class="text-gray-700 leading-relaxed">${translateToGerman(project.description)}</p>
                    </div>
                </div>
                
                <!-- Technical Details -->
                ${project.technical_info && (project.technical_info.rooms || project.technical_info.bathrooms || project.technical_info.year || project.technical_info.style || project.technical_info.features) ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Technische Details</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-2 text-sm">
                            ${project.technical_info.rooms ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Zimmer:</span>
                                <span class="font-medium">${project.technical_info.rooms}</span>
                            </div>
                            ` : ''}
                            ${project.technical_info.bathrooms ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Badezimmer:</span>
                                <span class="font-medium">${project.technical_info.bathrooms}</span>
                            </div>
                            ` : ''}
                            ${project.technical_info.year ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Jahr:</span>
                                <span class="font-medium">${project.technical_info.year}</span>
                            </div>
                            ` : ''}
                            ${project.technical_info.style ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Stil:</span>
                                <span class="font-medium">${translateToGerman(project.technical_info.style)}</span>
                            </div>
                            ` : ''}
                        </div>
                        ${project.technical_info.features ? `
                        <div>
                            <h4 class="font-semibold mb-2">Besonderheiten:</h4>
                            <div class="flex flex-wrap gap-1">
                                ${Array.isArray(project.technical_info.features) ? 
                                    project.technical_info.features.map(feature => 
                                        `<span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded">${translateToGerman(feature.trim())}</span>`
                                    ).join('') :
                                    project.technical_info.features.split(', ').map(feature => 
                                        `<span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded">${translateToGerman(feature.trim())}</span>`
                                    ).join('')
                                }
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
                ` : ''}
                
                <!-- Before/After -->
                ${project.before_after && (project.before_after.before || project.before_after.after) ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Vorher und Nachher</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        ${project.before_after.before ? `
                        <div>
                            <h4 class="font-semibold mb-2">Vorher</h4>
                            <img src="${project.before_after.before}" alt="Vor der Renovierung" class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity" onclick="openImageModal('${project.before_after.before}')">
                        </div>
                        ` : ''}
                        ${project.before_after.after ? `
                        <div>
                            <h4 class="font-semibold mb-2">Nachher</h4>
                            <img src="${project.before_after.after}" alt="Nach der Renovierung" class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity" onclick="openImageModal('${project.before_after.after}')">
                        </div>
                        ` : ''}
                    </div>
                </div>
                ` : ''}
                
                <!-- Gallery -->
                ${project.gallery && project.gallery.length > 0 ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Galerie</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        ${project.gallery.map((img, index) => 
                            `<img src="${img}" alt="Galerie" class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-80 transition-opacity" onclick="openImageModal('${img}')">`
                        ).join('')}
                    </div>
                </div>
                ` : ''}
                
                <!-- Tags -->
                ${project.tags && project.tags.length > 0 ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Tags</h3>
                    <div class="flex flex-wrap gap-2">
                        ${project.tags.map(tag => {
                            const tagTranslations = {
                                'ремонт': 'Renovierung',
                                'квартира': 'Wohnung',
                                'лофт': 'Loft',
                                'современный стиль': 'moderner Stil',
                                'открытая планировка': 'offene Planung',
                                'Франкфурт': 'Frankfurt'
                            };
                            const translatedTag = tagTranslations[tag] || tag;
                            return `<span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">${translatedTag}</span>`;
                        }).join('')}
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

function closeProjectModal() {
    const modal = document.getElementById('projectModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}

function openImageModal(imageSrc) {
    const modal = document.getElementById('imageModal');
    const modalContent = document.getElementById('imageModalContent');
    
    modalContent.innerHTML = `
        <div class="bg-white rounded-lg max-w-4xl mx-auto">
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900">Bild anzeigen</h2>
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6">
                <img src="${imageSrc}" alt="Großansicht" class="w-full h-auto rounded-lg">
            </div>
        </div>
    `;
    
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

// Gallery modal
function openGallery(projectId) {
    const project = portfolioData.find(p => p.id == projectId);
    if (!project || !project.gallery || project.gallery.length === 0) return;
    
    const modal = document.getElementById('galleryModal');
    const modalContent = document.getElementById('galleryModalContent');
    
    modalContent.innerHTML = `
        <div class="bg-white rounded-lg max-w-4xl mx-auto max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-900">Galerie: ${translateToGerman(project.title)}</h2>
                <button onclick="closeGalleryModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Gallery Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    ${project.gallery.map(image => `
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
    const modalContent = document.getElementById('imageModalContent');
    
    modalContent.innerHTML = `
        <div class="bg-white rounded-lg max-w-4xl mx-auto">
            <div class="relative">
                <img src="${imageSrc}" alt="Bild" class="w-full h-auto max-h-[80vh] object-contain">
                <button onclick="closeImageModal()" class="absolute top-4 right-4 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
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
    // Animate process section
    const processTitle = document.getElementById('process-title');
    const processSubtitle = document.getElementById('process-subtitle');
    const processSteps = document.querySelectorAll('.process-step');
    
    if (processTitle) animateElement(processTitle, 0);
    if (processSubtitle) animateElement(processSubtitle, 200);
    
    processSteps.forEach((step, index) => {
        animateElement(step, 400 + (index * 200));
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

// Рендер страницы
render_frontend_layout([
    'title' => $seo['title'] ?? 'Unsere Arbeiten - Baumaster Frankfurt',
    'description' => $seo['description'] ?? 'Sehen Sie sich unsere Portfolio-Projekte an - von kleinen Renovierungen bis zur kompletten Rekonstruktion in Frankfurt.',
    'content' => $content
]);
?>