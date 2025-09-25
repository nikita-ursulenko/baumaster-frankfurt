<?php
/**
 * Главная страница сайта
 * Baumaster Frontend - Home Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение данных
$seo = get_seo_data()['home'];
$services = get_services_data();
$portfolio = array_slice(get_portfolio_data(), 0, 3); // Показываем только первые 3
$reviews = array_slice(get_reviews_data(), 0, 4); // Показываем только первые 4

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section id="hero" class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6 leading-tight">
                    Innenausbau & Renovierung<br>
                    <span class="text-accent-blue">in Frankfurt am Main</span>
                </h1>
                <p class="text-xl text-text-secondary mb-8 leading-relaxed">
                    Полный спектр внутренних работ — от малярки до укладки полов. 
                    Премиальное качество и надёжность для вашего дома.
                </p>
                <?php render_frontend_button([
                    'text' => 'Бесплатный расчёт',
                    'variant' => 'primary',
                    'size' => 'lg',
                    'onclick' => "document.getElementById('contact').scrollIntoView({behavior: 'smooth'})"
                ]); ?>
            </div>
            <div class="relative">
                <?php render_contact_form([
                    'title' => 'Получить консультацию',
                    'subtitle' => 'Оставьте заявку и мы свяжемся с вами в течение часа'
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
                    <div class="relative h-48 bg-gray-200">
                        <img src="<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-6">
                        <span class="inline-block px-3 py-1 bg-accent-blue text-white text-sm rounded-full mb-3">
                            <?php echo htmlspecialchars($project['category']); ?>
                        </span>
                        <h3 class="font-semibold text-xl text-text-primary mb-2">
                            <?php echo htmlspecialchars($project['title']); ?>
                        </h3>
                        <p class="text-text-secondary mb-4">
                            <?php echo htmlspecialchars($project['description']); ?>
                        </p>
                        <div class="flex justify-between items-center text-sm text-text-secondary">
                            <span><?php echo htmlspecialchars($project['area']); ?></span>
                            <span><?php echo htmlspecialchars($project['duration']); ?></span>
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

<!-- Contact Section -->
<section id="contact" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Свяжитесь с нами
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Готовы обсудить ваш проект? Оставьте заявку или позвоните нам прямо сейчас
            </p>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-16">
            <div>
                <?php render_contact_form([
                    'title' => 'Заказать звонок',
                    'subtitle' => 'Мы перезвоним в течение 15 минут'
                ]); ?>
            </div>
            
            <div class="space-y-8">
                <div>
                    <h3 class="font-semibold text-2xl text-text-primary mb-6">Наши контакты</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-text-primary mb-1">Телефон</h4>
                                <p class="text-text-secondary mb-1">+49 (0) 69 123 456 78</p>
                                <p class="text-sm text-text-secondary">Пн-Пт: 8:00-18:00, Сб: 9:00-15:00</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-text-primary mb-1">Email</h4>
                                <p class="text-text-secondary">info@baumaster-frankfurt.de</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold text-text-primary mb-1">Адрес</h4>
                                <p class="text-text-secondary">Frankfurt am Main, Deutschland</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-premium-gray p-6 rounded-lg">
                    <h4 class="font-semibold text-text-primary mb-3">Быстрая связь</h4>
                    <div class="flex space-x-4">
                        <?php render_frontend_button([
                            'text' => 'WhatsApp',
                            'variant' => 'secondary',
                            'size' => 'sm',
                            'onclick' => "window.open('https://wa.me/4969123456789', '_blank')"
                        ]); ?>
                        <?php render_frontend_button([
                            'text' => 'Telegram',
                            'variant' => 'secondary',
                            'size' => 'sm',
                            'onclick' => "window.open('https://t.me/baumaster_frankfurt', '_blank')"
                        ]); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

// Рендеринг страницы
render_frontend_layout([
    'title' => $seo['title'],
    'meta_description' => $seo['description'],
    'active_page' => 'home',
    'content' => $content
]);
?>

