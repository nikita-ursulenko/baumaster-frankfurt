<?php
/**
 * Страница FAQ/Блог
 * Baumaster Frontend - FAQ/Blog Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение данных
$seo = get_seo_data()['blog'];
$faq = get_faq_data();

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                Часто задаваемые вопросы
            </h1>
            <p class="text-xl text-text-secondary max-w-4xl mx-auto">
                Ответы на самые популярные вопросы о ремонте, сроках, стоимости и процессе работы. 
                Не нашли ответ? Свяжитесь с нами напрямую.
            </p>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="space-y-4">
            <?php foreach ($faq as $index => $item): ?>
                <?php render_faq_item($item, $index); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Tips Section -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Полезные советы
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Практические рекомендации от наших мастеров
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Подготовка к ремонту</h3>
                <p class="text-text-secondary mb-4">
                    Как правильно подготовить помещение к началу работ и что нужно предусмотреть заранее.
                </p>
                <ul class="text-sm text-text-secondary space-y-1">
                    <li>• Освободите помещение от мебели</li>
                    <li>• Уберите ценные вещи</li>
                    <li>• Обеспечьте доступ к помещению</li>
                    <li>• Согласуйте время работ с соседями</li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Экономия на ремонте</h3>
                <p class="text-text-secondary mb-4">
                    Легальные способы сэкономить на ремонте без ущерба для качества работ.
                </p>
                <ul class="text-sm text-text-secondary space-y-1">
                    <li>• Покупайте материалы сами</li>
                    <li>• Выбирайте сезон для ремонта</li>
                    <li>• Делайте ремонт поэтапно</li>
                    <li>• Используйте акции и скидки</li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Контроль качества</h3>
                <p class="text-text-secondary mb-4">
                    На что обратить внимание при приёмке работ и как проверить качество выполнения.
                </p>
                <ul class="text-sm text-text-secondary space-y-1">
                    <li>• Проверьте ровность поверхностей</li>
                    <li>• Осмотрите углы и стыки</li>
                    <li>• Проверьте работу всех систем</li>
                    <li>• Сделайте фото для гарантии</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Blog Posts -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Статьи и новости
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Актуальная информация о ремонте, новых материалах и технологиях
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <article class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-500">Изображение статьи</span>
                </div>
                <div class="p-6">
                    <div class="text-sm text-accent-blue font-medium mb-2">15 декабря 2024</div>
                    <h3 class="font-semibold text-xl text-text-primary mb-3">
                        Тренды в дизайне интерьера 2024
                    </h3>
                    <p class="text-text-secondary mb-4">
                        Рассказываем о самых актуальных тенденциях в оформлении жилых помещений в этом году.
                    </p>
                    <a href="#" class="text-accent-blue font-medium hover:underline">Читать далее →</a>
                </div>
            </article>
            
            <article class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-500">Изображение статьи</span>
                </div>
                <div class="p-6">
                    <div class="text-sm text-accent-blue font-medium mb-2">10 декабря 2024</div>
                    <h3 class="font-semibold text-xl text-text-primary mb-3">
                        Выбор напольного покрытия
                    </h3>
                    <p class="text-text-secondary mb-4">
                        Подробный гид по выбору идеального пола для разных комнат вашего дома.
                    </p>
                    <a href="#" class="text-accent-blue font-medium hover:underline">Читать далее →</a>
                </div>
            </article>
            
            <article class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                <div class="h-48 bg-gray-200 flex items-center justify-center">
                    <span class="text-gray-500">Изображение статьи</span>
                </div>
                <div class="p-6">
                    <div class="text-sm text-accent-blue font-medium mb-2">5 декабря 2024</div>
                    <h3 class="font-semibold text-xl text-text-primary mb-3">
                        Ремонт ванной: пошаговый план
                    </h3>
                    <p class="text-text-secondary mb-4">
                        Детальная инструкция по планированию и выполнению ремонта в ванной комнате.
                    </p>
                    <a href="#" class="text-accent-blue font-medium hover:underline">Читать далее →</a>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
            Не нашли ответ на свой вопрос?
        </h2>
        <p class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto">
            Свяжитесь с нами напрямую, и мы ответим на все ваши вопросы о ремонте и отделке.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <?php render_frontend_button([
                'text' => 'Задать вопрос',
                'variant' => 'primary',
                'size' => 'lg',
                'href' => 'contact.php'
            ]); ?>
            <?php render_frontend_button([
                'text' => 'Позвонить сейчас',
                'variant' => 'outline',
                'size' => 'lg',
                'onclick' => 'window.open("tel:+4969123456789")'
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
    'active_page' => 'blog',
    'content' => $content
]);
?>

