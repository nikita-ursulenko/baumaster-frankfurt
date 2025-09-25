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
$seo = get_seo_data()['services'];
$services = get_services_data();

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                Наши услуги
            </h1>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto mb-8">
                Выполняем все виды внутренних работ во Франкфурте. От небольшого косметического ремонта 
                до комплексной реконструкции под ключ.
            </p>
            <?php render_frontend_button([
                'text' => 'Получить консультацию',
                'variant' => 'primary',
                'size' => 'lg',
                'onclick' => "document.getElementById('contact').scrollIntoView({behavior: 'smooth'})"
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
                'onclick' => "document.getElementById('contact').scrollIntoView({behavior: 'smooth'})"
            ]); ?>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact" class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Заказать услугу
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Оставьте заявку и мы свяжемся с вами для уточнения деталей и назначения встречи
            </p>
        </div>
        
        <div class="max-w-2xl mx-auto">
            <?php render_contact_form([
                'title' => 'Заказать услугу',
                'subtitle' => 'Укажите интересующую услугу и мы рассчитаем стоимость',
                'class' => 'shadow-2xl'
            ]); ?>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

// Рендеринг страницы
render_frontend_layout([
    'title' => $seo['title'],
    'meta_description' => $seo['description'],
    'active_page' => 'services',
    'content' => $content
]);
?>

