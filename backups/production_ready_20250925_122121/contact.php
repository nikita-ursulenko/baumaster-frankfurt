<?php
/**
 * Страница контактов
 * Baumaster Frontend - Contact Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение данных
$seo = get_seo_data()['contact'];
$contact_info = get_contact_info();

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                Свяжитесь с нами
            </h1>
            <p class="text-xl text-text-secondary max-w-4xl mx-auto">
                Готовы обсудить ваш проект? Мы всегда рады ответить на вопросы и предоставить 
                бесплатную консультацию по любым видам внутренних работ.
            </p>
        </div>
    </div>
</section>

<!-- Contact Info -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg text-text-primary mb-2">Телефон</h3>
                <p class="text-text-secondary mb-1"><?php echo $contact_info['phone']; ?></p>
                <p class="text-sm text-text-secondary"><?php echo $contact_info['working_hours']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg text-text-primary mb-2">Email</h3>
                <p class="text-text-secondary"><?php echo $contact_info['email']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg text-text-primary mb-2">Адрес</h3>
                <p class="text-text-secondary"><?php echo $contact_info['address']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg text-text-primary mb-2">Режим работы</h3>
                <p class="text-text-secondary"><?php echo $contact_info['working_hours']; ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16">
            <!-- Form -->
            <div>
                <h2 class="font-montserrat font-semibold text-3xl text-text-primary mb-6">
                    Оставьте заявку
                </h2>
                <p class="text-lg text-text-secondary mb-8">
                    Заполните форму, и мы свяжемся с вами в течение 15 минут для обсуждения деталей проекта.
                </p>
                
                <?php render_contact_form([
                    'title' => '',
                    'show_title' => false,
                    'class' => 'shadow-2xl'
                ]); ?>
            </div>
            
            <!-- Additional Info -->
            <div class="space-y-8">
                <div>
                    <h3 class="font-semibold text-2xl text-text-primary mb-6">Быстрая связь</h3>
                    
                    <div class="space-y-4">
                        <?php render_frontend_button([
                            'text' => 'WhatsApp: ' . $contact_info['social']['whatsapp'],
                            'variant' => 'secondary',
                            'size' => 'lg',
                            'class' => 'w-full justify-start',
                            'onclick' => "window.open('https://wa.me/4969123456789', '_blank')",
                            'icon' => '<svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.688z"/></svg>'
                        ]); ?>
                        
                        <?php render_frontend_button([
                            'text' => 'Telegram: ' . $contact_info['social']['telegram'],
                            'variant' => 'secondary',
                            'size' => 'lg',
                            'class' => 'w-full justify-start',
                            'onclick' => "window.open('https://t.me/baumaster_frankfurt', '_blank')",
                            'icon' => '<svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>'
                        ]); ?>
                    </div>
                </div>
                
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h4 class="font-semibold text-xl text-text-primary mb-4">Преимущества работы с нами</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Бесплатный выезд мастера для замера</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Фиксированные цены в договоре</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Гарантия на все виды работ</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Соблюдение договорных сроков</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Уборка после завершения работ</span>
                        </li>
                    </ul>
                </div>
                
                <div class="bg-accent-blue text-white p-8 rounded-lg">
                    <h4 class="font-semibold text-xl mb-4">Экстренный ремонт</h4>
                    <p class="mb-4">Нужен срочный ремонт? Работаем в выходные и праздничные дни.</p>
                    <?php render_frontend_button([
                        'text' => 'Вызвать мастера',
                        'variant' => 'secondary',
                        'onclick' => 'window.open("tel:+4969123456789")',
                        'class' => 'bg-white text-accent-blue hover:bg-gray-100'
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Мы работаем по всему Франкфурту
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Выполняем проекты во всех районах города и пригородах
            </p>
        </div>
        
        <!-- Placeholder for map -->
        <div class="bg-gray-200 rounded-lg h-96 flex items-center justify-center">
            <div class="text-center text-gray-500">
                <svg class="h-16 w-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <p class="text-lg">Интерактивная карта</p>
                <p class="text-sm">Frankfurt am Main, Deutschland</p>
            </div>
        </div>
        
        <!-- Districts -->
        <div class="mt-12">
            <h3 class="text-center font-semibold text-xl text-text-primary mb-8">Районы работы</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 text-center">
                <div class="bg-gray-50 p-3 rounded">Altstadt</div>
                <div class="bg-gray-50 p-3 rounded">Sachsenhausen</div>
                <div class="bg-gray-50 p-3 rounded">Westend</div>
                <div class="bg-gray-50 p-3 rounded">Nordend</div>
                <div class="bg-gray-50 p-3 rounded">Ostend</div>
                <div class="bg-gray-50 p-3 rounded">Bornheim</div>
                <div class="bg-gray-50 p-3 rounded">Bockenheim</div>
                <div class="bg-gray-50 p-3 rounded">Gallus</div>
                <div class="bg-gray-50 p-3 rounded">Höchst</div>
                <div class="bg-gray-50 p-3 rounded">Fechenheim</div>
                <div class="bg-gray-50 p-3 rounded">Rödelheim</div>
                <div class="bg-gray-50 p-3 rounded">Und andere</div>
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
    'active_page' => 'contact',
    'content' => $content
]);
?>

