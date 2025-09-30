<?php
/**
 * Страница портфолио
 * Baumaster Frontend - Portfolio Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение данных
$seo = get_seo_data()['portfolio'];
$portfolio = get_portfolio_data();

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

/* Enhanced Project Modal Animations */
#projectModal {
    opacity: 0;
    visibility: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(0px);
}

#projectModal.show {
    opacity: 1;
    visibility: visible;
    backdrop-filter: blur(8px);
    z-index: 99;
}

#projectModalContent {
    transform: translateY(50px) scale(0.9);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.1s;
}

#projectModal.show #projectModalContent {
    transform: translateY(0) scale(1);
    opacity: 1;
}

/* Project Modal Header Animation */
#projectModal .sticky.top-0 {
    transform: translateY(-20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.2s;
    z-index: 10;
    position: sticky;
    top: 0;
}

#projectModal.show .sticky.top-0 {
    transform: translateY(0);
    opacity: 1;
}

/* Project Modal Content Animation */
#projectModal .p-6 > * {
    transform: translateY(20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#projectModal.show .p-6 > * {
    transform: translateY(0);
    opacity: 1;
}

#projectModal.show .p-6 > *:nth-child(1) { transition-delay: 0.3s; }
#projectModal.show .p-6 > *:nth-child(2) { transition-delay: 0.4s; }
#projectModal.show .p-6 > *:nth-child(3) { transition-delay: 0.5s; }
#projectModal.show .p-6 > *:nth-child(4) { transition-delay: 0.6s; }
#projectModal.show .p-6 > *:nth-child(5) { transition-delay: 0.7s; }

/* Project Modal Image Animation */
#projectModal img {
    transform: scale(1.1);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.2s;
}

#projectModal.show img {
    transform: scale(1);
    opacity: 1;
}

/* Project Modal Button Animation */
#projectModal button {
    transform: translateY(10px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transition-delay: 0.4s;
}

#projectModal.show button {
    transform: translateY(0);
    opacity: 1;
}

/* Project Modal Close Button Hover Effect */
#projectModal .sticky.top-0 button:hover {
    transform: rotate(90deg) scale(1.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Project Modal Gallery Images Animation */
#projectModal .grid img {
    transform: scale(0.8);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#projectModal.show .grid img {
    transform: scale(1);
    opacity: 1;
}

#projectModal.show .grid img:nth-child(1) { transition-delay: 0.3s; }
#projectModal.show .grid img:nth-child(2) { transition-delay: 0.4s; }
#projectModal.show .grid img:nth-child(3) { transition-delay: 0.5s; }
#projectModal.show .grid img:nth-child(4) { transition-delay: 0.6s; }
#projectModal.show .grid img:nth-child(5) { transition-delay: 0.7s; }
#projectModal.show .grid img:nth-child(6) { transition-delay: 0.8s; }

/* Project Modal List Items Animation */
#projectModal ul li {
    transform: translateX(-20px);
    opacity: 0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

#projectModal.show ul li {
    transform: translateX(0);
    opacity: 1;
}

#projectModal.show ul li:nth-child(1) { transition-delay: 0.3s; }
#projectModal.show ul li:nth-child(2) { transition-delay: 0.4s; }
#projectModal.show ul li:nth-child(3) { transition-delay: 0.5s; }
#projectModal.show ul li:nth-child(4) { transition-delay: 0.6s; }
#projectModal.show ul li:nth-child(5) { transition-delay: 0.7s; }
#projectModal.show ul li:nth-child(6) { transition-delay: 0.8s; }

/* Project Modal Responsive Animations */
@media (max-width: 768px) {
    #projectModalContent {
        transform: translateY(30px) scale(0.95);
        margin: 0.5rem;
        max-height: 98vh;
    }
    
    #projectModal.show #projectModalContent {
        transform: translateY(0) scale(1);
    }
}

/* Project Modal Gallery Optimization */
#projectModal .grid {
    padding-right: 8px;
}

#projectModal .grid::-webkit-scrollbar {
    width: 6px;
}

#projectModal .grid::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

#projectModal .grid::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

#projectModal .grid::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Project Info Cards Styling */
.project-info-card,
.project-description-card {
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

.project-info-card:hover,
.project-description-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    border-color: #d1d5db;
}

/* Project Info Header */
.project-info-header,
.project-description-header {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #f3f4f6;
}

.project-info-icon,
.project-description-icon {
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

.project-info-card:hover .project-info-icon,
.project-description-card:hover .project-description-icon {
    background: #e5e7eb;
    color: #374151;
}

.project-info-title,
.project-description-title {
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
    margin: 0;
}

/* Project Info Content */
.project-info-content {
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

.budget-value {
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

/* Project Description Content */
.project-description-content {
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
    .py-20 > div:first-child,
    .py-8 > div:first-child {
        margin: 0 5%;
    }
    
    footer > div:first-child {
        margin: 0 5%;
    }
    
    .project-info-card,
    .project-description-card {
        padding: 12px;
        margin-bottom: 12px;
    }
    
    .project-info-header,
    .project-description-header {
        margin-bottom: 8px;
        padding-bottom: 6px;
    }
    
    .project-info-icon,
    .project-description-icon {
        width: 28px;
        height: 28px;
        margin-right: 8px;
    }
    
    .project-info-title,
    .project-description-title {
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
<section id="hero" class="pt-16 bg-cover bg-center bg-no-repeat relative min-h-screen flex items-center" style="background-image: url('/assets/images/preview/portfolio.png'); background-size: cover; background-position: center center; background-attachment: scroll; -webkit-background-size: cover;">
    <!-- Overlay for better text readability -->
    <div class="hero-overlay absolute inset-0 bg-black bg-opacity-30" style="z-index: 1;"></div>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="text-center">
            <h1 id="hero-title" class="font-montserrat font-semibold text-3xl lg:text-6xl text-white mb-6 leading-tight hero-text-shadow">
                <?php 
                $title = 'Наше портфолио';
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
                Посмотрите примеры наших работ — от небольших косметических ремонтов до комплексной 
                реконструкции квартир и офисов во Франкфурте.
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
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="flex flex-wrap justify-center gap-4">
            <button class="filter-btn active px-6 py-2 rounded-full border-2 border-accent-blue text-accent-blue font-medium hover:bg-accent-blue hover:text-white transition-colors" data-filter="all">
                Все проекты
            </button>
            <button class="filter-btn px-6 py-2 rounded-full border-2 border-gray-300 text-text-secondary hover:border-accent-blue hover:text-accent-blue transition-colors" data-filter="apartment">
                Квартиры
            </button>
            <button class="filter-btn px-6 py-2 rounded-full border-2 border-gray-300 text-text-secondary hover:border-accent-blue hover:text-accent-blue transition-colors" data-filter="bathroom">
                Ванные комнаты
            </button>
            <button class="filter-btn px-6 py-2 rounded-full border-2 border-gray-300 text-text-secondary hover:border-accent-blue hover:text-accent-blue transition-colors" data-filter="office">
                Офисы
            </button>
        </div>
    </div>
</section>

<!-- Portfolio Grid -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 align-items-stretch">
            <?php foreach ($portfolio as $project): ?>
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
                            <?php echo htmlspecialchars($project['description']); ?>
                        </p>
                        
                        <!-- Project Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4 text-sm text-text-secondary">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-2 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
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
                                    <path xmlns="http://www.w3.org/2000/svg" d="M19 7.11111C17.775 5.21864 15.8556 4 13.6979 4C9.99875 4 7 7.58172 7 12C7 16.4183 9.99875 20 13.6979 20C15.8556 20 17.775 18.7814 19 16.8889M5 10H14M5 14H14" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <?php echo number_format($project['budget'], 0, ',', ' '); ?> €
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
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="process-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 fade-in-up">
                Этапы выполнения проекта
            </h2>
            <p id="process-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto fade-in-up">
                Каждый наш проект проходит тщательно продуманные этапы для достижения идеального результата
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center process-step">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Планирование</h3>
                <p class="text-text-secondary">Обсуждаем детали, создаём план работ, выбираем материалы и согласовываем сроки.</p>
            </div>
            
            <div class="text-center process-step">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Подготовка</h3>
                <p class="text-text-secondary">Демонтаж старых покрытий, подготовка поверхностей, защита мебели и интерьера.</p>
            </div>
            
            <div class="text-center process-step">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Выполнение</h3>
                <p class="text-text-secondary">Основные работы: монтаж, отделка, покраска. Регулярный контроль качества на каждом этапе.</p>
            </div>
            
            <div class="text-center process-step">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Сдача</h3>
                <p class="text-text-secondary">Финальная уборка, проверка качества, устранение мелких недочётов и торжественная сдача объекта.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 text-center">
        <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
            Хотите такой же результат?
        </h2>
        <p class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto">
            Обсудим ваш проект и создадим для вас уникальное пространство с учётом всех пожеланий и особенностей.
        </p>
        
        <?php render_frontend_button([
            'text' => 'Обсудить проект',
            'variant' => 'primary',
            'size' => 'lg',
            'href' => 'contact.php'
        ]); ?>
    </div>
</section>

<!-- Modals -->
<!-- Project Detail Modal -->
<div id="projectModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-2">
    <div id="projectModalContent" class="bg-white rounded-lg max-w-5xl mx-auto max-h-[95vh] overflow-y-auto w-full shadow-2xl">
        <!-- Modal content will be loaded here -->
    </div>
</div>

<!-- Gallery Modal -->
<div id="galleryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-2">
    <div id="galleryModalContent" class="bg-white rounded-lg max-w-6xl mx-auto max-h-[95vh] overflow-y-auto w-full shadow-2xl">
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
function openProjectModal(projectId) {
    const project = portfolioData.find(p => p.id == projectId);
    if (!project) return;
    
    // Отслеживаем просмотр портфолио
    fetch('track_portfolio_view.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `portfolio_id=${projectId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Portfolio view tracked successfully');
        } else {
            console.error('Failed to track portfolio view:', data.error);
        }
    })
    .catch(error => {
        console.error('Error tracking portfolio view:', error);
    });
    
    const modal = document.getElementById('projectModal');
    const modalContent = document.getElementById('projectModalContent');
    
    // Создаем контент модального окна
    modalContent.innerHTML = `
        <div class="bg-white rounded-lg max-w-5xl mx-auto max-h-[95vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-900">${project.title}</h2>
                <button onclick="closeProjectModal()" class="text-gray-400 hover:text-gray-600 transition-all duration-300 hover:rotate-90 hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <!-- Featured Image -->
                <div class="mb-6">
                    <img src="${project.image}" alt="${project.title}" class="w-full h-64 object-cover rounded-lg transition-all duration-300 hover:scale-105">
                </div>
                
                <!-- Project Info Cards -->
                <div class="grid md:grid-cols-2 gap-6 mb-8 py-2">
                    <!-- Project Information Card -->
                    <div class="project-info-card">
                        <div class="project-info-header">
                            <div class="project-info-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="project-info-title">Информация о проекте</h3>
                        </div>
                        <div class="project-info-content">
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Площадь
                                </div>
                                <div class="info-value">${project.area}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Срок
                                </div>
                                <div class="info-value">${project.duration}</div>
                            </div>
                            ${project.budget ? `
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path xmlns="http://www.w3.org/2000/svg" d="M19 7.11111C17.775 5.21864 15.8556 4 13.6979 4C9.99875 4 7 7.58172 7 12C7 16.4183 9.99875 20 13.6979 20C15.8556 20 17.775 18.7814 19 16.8889M5 10H14M5 14H14" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Бюджет
                                </div>
                                <div class="info-value budget-value">${new Intl.NumberFormat('de-DE').format(project.budget)} €</div>
                            </div>
                            ` : ''}
                            ${project.completion_date ? `
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Дата завершения
                                </div>
                                <div class="info-value">${new Date(project.completion_date).toLocaleDateString('ru-RU')}</div>
                            </div>
                            ` : ''}
                            ${project.client_name ? `
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Клиент
                                </div>
                                <div class="info-value">${project.client_name}</div>
                            </div>
                            ` : ''}
                            ${project.location ? `
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Местоположение
                                </div>
                                <div class="info-value">${project.location}</div>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    
                    <!-- Description Card -->
                    <div class="project-description-card">
                        <div class="project-description-header">
                            <div class="project-description-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="project-description-title">Описание проекта</h3>
                        </div>
                        <div class="project-description-content">
                            <p class="description-text">${project.description}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Technical Details -->
                ${project.technical_info && Object.keys(project.technical_info).length > 0 ? `
                <div class="mb-6">
                    <div class="project-info-card">
                        <div class="project-info-header">
                            <div class="project-info-icon">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                                </svg>
                            </div>
                            <h3 class="project-info-title">Технические детали</h3>
                        </div>
                        <div class="project-info-content">
                            ${project.technical_info.rooms ? `
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Комнат
                                </div>
                                <div class="info-value">${project.technical_info.rooms}</div>
                            </div>
                            ` : ''}
                            ${project.technical_info.bathrooms ? `
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M10.5 3L12 2l1.5 1H21v6H3V3h7.5z"></path>
                                    </svg>
                                    Ванных
                                </div>
                                <div class="info-value">${project.technical_info.bathrooms}</div>
                            </div>
                            ` : ''}
                            ${project.technical_info.year ? `
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Год
                                </div>
                                <div class="info-value">${project.technical_info.year}</div>
                            </div>
                            ` : ''}
                            ${project.technical_info.style ? `
                            <div class="info-item">
                                <div class="info-label">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"></path>
                                    </svg>
                                    Стиль
                                </div>
                                <div class="info-value">${project.technical_info.style}</div>
                            </div>
                            ` : ''}
                        </div>
                        ${project.technical_info.features && project.technical_info.features.length > 0 ? `
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Особенности
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                ${project.technical_info.features.map(feature => `
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs rounded-full border border-blue-200 hover:bg-blue-100 transition-colors">${feature}</span>
                                `).join('')}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
                ` : ''}
                
                <!-- Before/After Images -->
                ${project.before_after && (project.before_after.before || project.before_after.after) ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">До и после</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        ${project.before_after.before ? `
                        <div>
                            <h4 class="font-medium mb-2 text-center">До</h4>
                            <img src="${project.before_after.before}" alt="До ремонта" class="w-full h-48 object-cover rounded-lg transition-all duration-300 hover:scale-105 cursor-pointer" onclick="openImageModal('${project.before_after.before}')">
                        </div>
                        ` : ''}
                        ${project.before_after.after ? `
                        <div>
                            <h4 class="font-medium mb-2 text-center">После</h4>
                            <img src="${project.before_after.after}" alt="После ремонта" class="w-full h-48 object-cover rounded-lg transition-all duration-300 hover:scale-105 cursor-pointer" onclick="openImageModal('${project.before_after.after}')">
                        </div>
                        ` : ''}
                    </div>
                </div>
                ` : ''}
                
                <!-- Gallery -->
                ${project.gallery && project.gallery.length > 0 ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Галерея</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        ${project.gallery.map(image => `
                            <img src="${image}" alt="Галерея" class="w-full h-32 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-all duration-300 hover:scale-105" onclick="openImageModal('${image}')">
                        `).join('')}
                    </div>
                </div>
                ` : ''}
                
                <!-- Tags -->
                ${project.tags && project.tags.length > 0 ? `
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Теги</h3>
                    <div class="flex flex-wrap gap-2">
                        ${project.tags.map(tag => `
                            <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full">${tag}</span>
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

function closeProjectModal() {
    const modal = document.getElementById('projectModal');
    
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
function openGallery(projectId) {
    const project = portfolioData.find(p => p.id == projectId);
    if (!project || !project.gallery || project.gallery.length === 0) return;
    
    const modal = document.getElementById('galleryModal');
    const modalContent = document.getElementById('galleryModalContent');
    
    modalContent.innerHTML = `
        <div class="bg-white rounded-lg max-w-6xl mx-auto max-h-[95vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-white border-b px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-gray-900">Галерея: ${project.title}</h2>
                <button onclick="closeGalleryModal()" class="text-gray-400 hover:text-gray-600 transition-all duration-300 hover:rotate-90 hover:scale-110">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Gallery Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    ${project.gallery.map(image => `
                        <img src="${image}" alt="Галерея" class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-75 transition-all duration-300 hover:scale-105" onclick="openImageModal('${image}')">
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

// Close modals on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeProjectModal();
        closeGalleryModal();
        closeImageModal();
    }
});

// Close modals on backdrop click
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('fixed')) {
        closeProjectModal();
        closeGalleryModal();
        closeImageModal();
    }
});

// Предотвращаем закрытие при клике на контент модального окна
document.addEventListener('click', function(event) {
    if (event.target.closest('#projectModalContent')) {
        event.stopPropagation();
    }
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
    'active_page' => 'portfolio',
    'content' => $content
]);
?>

