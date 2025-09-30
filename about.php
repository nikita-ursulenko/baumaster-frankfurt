<?php
/**
 * Страница о компании
 * Baumaster Frontend - About Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение данных
$seo = get_seo_data()['about'];
$about_data = get_about_content();
$team_members = get_team_members();
$statistics = get_statistics();

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section id="hero" class="pt-16 bg-cover bg-center bg-no-repeat relative min-h-screen flex items-center" style="background-image: url('/assets/images/preview/about.png'); background-size: cover; background-position: center center; background-attachment: scroll; -webkit-background-size: cover;">
    <!-- Overlay for better text readability -->
    <div class="hero-overlay absolute inset-0 bg-black bg-opacity-30" style="z-index: 1;"></div>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="text-center">
            <h1 id="hero-title" class="font-montserrat font-semibold text-3xl lg:text-6xl text-white mb-6 leading-tight hero-text-shadow">
                <?php 
                $title = 'О компании Frankfurt Innenausbau';
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
                Мы — команда профессионалов с многолетним опытом в сфере внутренних работ и ремонта. 
                Наша миссия — превращать ваши идеи в реальность с премиальным качеством и вниманием к деталям.
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
.card, .team-member, .feature-item {
    transition: all 0.3s ease;
}

.card:hover, .feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* Statistics items - no hover effects, only scale for numbers */
.stat-item {
    transition: none;
}

.team-member:hover {
    transform: translateY(0);
    box-shadow: none;
}

.card img, .team-member img, .feature-item img {
    transition: transform 0.3s ease;
}

.card:hover img, .team-member:hover img, .feature-item:hover img {
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
.card h3, .team-member h3, .feature-item h3 {
    transition: color 0.3s ease;
}

.card:hover h3, .team-member:hover h3, .feature-item:hover h3 {
    color: #3b82f6;
}

/* Price and button smooth transitions */
.card .font-semibold, .team-member .font-semibold, .feature-item .font-semibold {
    transition: all 0.3s ease;
}

.card:hover .font-semibold, .team-member:hover .font-semibold, .feature-item:hover .font-semibold {
    transform: scale(1.05);
}

/* Statistics numbers - only scale effect on hover */
.stat-item .font-bold {
    transition: transform 0.3s ease;
}

.stat-item:hover .font-bold {
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
.card, .team-member, .feature-item {
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

<!-- Company Story -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
                    <?php echo htmlspecialchars($about_data['history']['title'] ?? 'Наша история'); ?>
                </h2>
                <?php if (isset($about_data['history']['content'])): ?>
                    <p class="text-lg text-text-secondary mb-6 leading-relaxed">
                        <?php echo htmlspecialchars($about_data['history']['content']['paragraph1'] ?? ''); ?>
                    </p>
                    <p class="text-lg text-text-secondary mb-6 leading-relaxed">
                        <?php echo htmlspecialchars($about_data['history']['content']['paragraph2'] ?? ''); ?>
                    </p>
                    <p class="text-lg text-text-secondary mb-8 leading-relaxed">
                        <?php echo htmlspecialchars($about_data['history']['content']['paragraph3'] ?? ''); ?>
                    </p>
                <?php else: ?>
                    <p class="text-lg text-text-secondary mb-6 leading-relaxed">
                        Компания Frankfurt Innenausbau была основана в 2014 году группой опытных мастеров, 
                        которые решили объединить свои знания и навыки для предоставления качественных услуг 
                        в сфере внутренних работ.
                    </p>
                    <p class="text-lg text-text-secondary mb-6 leading-relaxed">
                        За 10 лет работы мы выполнили более 500 проектов различной сложности — от небольших 
                        косметических ремонтов до полной реконструкции квартир и офисов. Наш опыт охватывает 
                        все виды внутренних работ.
                    </p>
                    <p class="text-lg text-text-secondary mb-8 leading-relaxed">
                        Сегодня мы продолжаем развиваться, внедряя новые технологии и материалы, 
                        но неизменным остается наш принцип — качество превыше всего.
                    </p>
                <?php endif; ?>
            </div>
            <div class="relative">
                <?php if (!empty($about_data['history']['image'])): ?>
                    <img src="<?php echo htmlspecialchars($about_data['history']['image']); ?>" 
                         alt="Фото команды" class="w-full h-96 object-cover rounded-lg shadow-lg">
                <?php else: ?>
                    <div class="bg-gray-200 rounded-lg h-96 flex items-center justify-center">
                        <span class="text-gray-500">Фото команды</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-20 bg-accent-blue text-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="stats-title" class="font-montserrat font-semibold text-3xl lg:text-4xl mb-4 fade-in-up">
                Цифры, которые говорят за нас
            </h2>
            <p id="stats-subtitle" class="text-xl opacity-90 max-w-3xl mx-auto fade-in-up">
                Результаты нашей работы в цифрах
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (!empty($statistics)): ?>
                <?php foreach ($statistics as $index => $stat): ?>
                    <div class="text-center stat-item fade-in-up" style="animation-delay: <?php echo ($index * 0.2); ?>s;">
                        <div class="text-5xl font-bold mb-2"><?php echo htmlspecialchars($stat['number']); ?></div>
                        <div class="text-xl opacity-90"><?php echo htmlspecialchars($stat['label']); ?></div>
                        <div class="text-sm opacity-75 mt-2"><?php echo htmlspecialchars($stat['description']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-8">
                    <p class="text-white opacity-75">Статистика будет добавлена в ближайшее время</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Team -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="team-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 fade-in-up">
                Наша команда
            </h2>
            <p id="team-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto fade-in-up">
                Профессионалы своего дела с многолетним опытом
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (!empty($team_members)): ?>
                <?php foreach ($team_members as $index => $member): ?>
                    <div class="text-center team-member fade-in-up" style="animation-delay: <?php echo ($index * 0.2); ?>s;">
                        <div class="w-32 h-32 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($member['image'])): ?>
                                <img src="<?php echo htmlspecialchars($member['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <?php 
                                $initials = '';
                                $name_parts = explode(' ', $member['name']);
                                foreach ($name_parts as $part) {
                                    $initials .= strtoupper(substr($part, 0, 1));
                                }
                                ?>
                                <span class="text-2xl font-bold text-gray-500"><?php echo $initials; ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-semibold text-xl text-text-primary mb-2"><?php echo htmlspecialchars($member['name']); ?></h3>
                        <p class="text-accent-blue font-medium mb-3"><?php echo htmlspecialchars($member['position']); ?></p>
                        <p class="text-text-secondary text-sm"><?php echo htmlspecialchars($member['description']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-8">
                    <p class="text-text-secondary">Информация о команде будет добавлена в ближайшее время</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Values -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 id="principles-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 fade-in-up">
                Наши принципы
            </h2>
            <p id="principles-subtitle" class="text-xl text-text-secondary max-w-3xl mx-auto fade-in-up">
                Ценности, которыми мы руководствуемся в работе
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-lg text-center feature-item fade-in-up" style="animation-delay: 0s;">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Качество</h3>
                <p class="text-text-secondary">Используем только проверенные материалы и современные технологии. Каждая деталь важна.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center feature-item fade-in-up" style="animation-delay: 0.2s;">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Пунктуальность</h3>
                <p class="text-text-secondary">Соблюдаем договорные сроки. Ваше время ценно для нас, как и наше для вас.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center feature-item fade-in-up" style="animation-delay: 0.4s;">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Индивидуальный подход</h3>
                <p class="text-text-secondary">Каждый проект уникален. Учитываем ваши пожелания и особенности объекта.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center feature-item fade-in-up" style="animation-delay: 0.6s;">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Честные цены</h3>
                <p class="text-text-secondary">Никаких скрытых доплат. Цена в договоре остается неизменной до завершения работ.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center feature-item fade-in-up" style="animation-delay: 0.8s;">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Гарантии</h3>
                <p class="text-text-secondary">Предоставляем расширенную гарантию на все виды работ. Уверены в качестве нашей работы.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center feature-item fade-in-up" style="animation-delay: 1.0s;">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Открытость</h3>
                <p class="text-text-secondary">Держим вас в курсе всех этапов работы. Всегда готовы ответить на ваши вопросы.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 text-center">
        <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
            Готовы работать с профессионалами?
        </h2>
        <p class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto">
            Доверьте свой ремонт команде с опытом и репутацией. Получите бесплатную консультацию уже сегодня.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <?php render_frontend_button([
                'text' => 'Получить консультацию',
                'variant' => 'primary',
                'size' => 'lg',
                'href' => 'contact.php'
            ]); ?>
            <?php render_frontend_button([
                'text' => 'Смотреть портфолио',
                'variant' => 'outline',
                'size' => 'lg',
                'href' => 'portfolio.php'
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
    // Animate statistics section
    const statsTitle = document.getElementById('stats-title');
    const statsSubtitle = document.getElementById('stats-subtitle');
    const statItems = document.querySelectorAll('.stat-item');
    
    if (statsTitle) animateElement(statsTitle, 0);
    if (statsSubtitle) animateElement(statsSubtitle, 200);
    
    statItems.forEach((item, index) => {
        animateElement(item, 400 + (index * 100));
    });
    
    // Animate team section
    const teamTitle = document.getElementById('team-title');
    const teamSubtitle = document.getElementById('team-subtitle');
    const teamMembers = document.querySelectorAll('.team-member');
    
    if (teamTitle) animateElement(teamTitle, 0);
    if (teamSubtitle) animateElement(teamSubtitle, 200);
    
    teamMembers.forEach((member, index) => {
        animateElement(member, 400 + (index * 100));
    });
    
    // Animate principles section
    const principlesTitle = document.getElementById('principles-title');
    const principlesSubtitle = document.getElementById('principles-subtitle');
    const featureItems = document.querySelectorAll('.feature-item');
    
    if (principlesTitle) animateElement(principlesTitle, 0);
    if (principlesSubtitle) animateElement(principlesSubtitle, 200);
    
    featureItems.forEach((item, index) => {
        animateElement(item, 400 + (index * 100));
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
    'active_page' => 'about',
    'content' => $content
]);
?>

