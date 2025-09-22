<?php
/**
 * Страница отзывов
 * Baumaster Frontend - Reviews Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение данных
$seo = get_seo_data()['reviews'];
$reviews = get_reviews_data();

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                Отзывы наших клиентов
            </h1>
            <p class="text-xl text-text-secondary max-w-4xl mx-auto">
                Читайте, что говорят о качестве нашей работы те, кто уже доверил нам свой ремонт. 
                Каждый отзыв — это история успешного проекта.
            </p>
        </div>
    </div>
</section>

<!-- Statistics -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-accent-blue mb-2">500+</div>
                <div class="text-text-secondary">Довольных клиентов</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-accent-blue mb-2">4.9</div>
                <div class="text-text-secondary">Средний рейтинг</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-accent-blue mb-2">98%</div>
                <div class="text-text-secondary">Рекомендуют нас</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-accent-blue mb-2">100%</div>
                <div class="text-text-secondary">Выполненных проектов</div>
            </div>
        </div>
    </div>
</section>

<!-- Reviews Grid -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($reviews as $review): ?>
                <?php render_review_card($review); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Add Review Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Оставьте отзыв о нашей работе
            </h2>
            <p class="text-xl text-text-secondary max-w-2xl mx-auto">
                Ваше мнение важно для нас! Поделитесь впечатлениями о качестве выполненных работ.
            </p>
        </div>
        
        <div class="bg-white p-8 rounded-lg shadow-xl border border-gray-200">
            <form action="/add-review.php" method="POST" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ваше имя *</label>
                        <input type="text" name="name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors"
                               placeholder="Введите ваше имя">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors"
                               placeholder="your@email.com">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Услуга</label>
                    <select name="service" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors">
                        <option value="">Выберите услугу</option>
                        <option value="painting">Малярные работы</option>
                        <option value="flooring">Укладка полов</option>
                        <option value="bathroom">Ремонт ванной</option>
                        <option value="drywall">Гипсокартон</option>
                        <option value="tiling">Плитка</option>
                        <option value="renovation">Комплексный ремонт</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Оценка *</label>
                    <div class="flex space-x-2" id="rating">
                        <button type="button" class="star text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="1">★</button>
                        <button type="button" class="star text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="2">★</button>
                        <button type="button" class="star text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="3">★</button>
                        <button type="button" class="star text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="4">★</button>
                        <button type="button" class="star text-3xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="5">★</button>
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="5">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Отзыв *</label>
                    <textarea name="review" rows="5" required 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors"
                              placeholder="Расскажите о качестве выполненных работ, соблюдении сроков, профессионализме мастеров..."></textarea>
                </div>
                
                <div class="flex items-start space-x-3">
                    <input type="checkbox" name="agree" id="agree-review" required 
                           class="mt-1 h-4 w-4 text-accent-blue focus:ring-accent-blue border-gray-300 rounded">
                    <label for="agree-review" class="text-sm text-text-secondary">
                        Я согласен на публикацию моего отзыва на сайте и обработку персональных данных *
                    </label>
                </div>
                
                <?php render_frontend_button([
                    'text' => 'Отправить отзыв',
                    'type' => 'submit',
                    'variant' => 'primary',
                    'size' => 'lg',
                    'class' => 'w-full'
                ]); ?>
            </form>
        </div>
    </div>
</section>

<!-- Testimonials Video -->
<section class="py-20 bg-accent-blue text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl mb-4">
                Видеоотзывы клиентов
            </h2>
            <p class="text-xl opacity-90 max-w-3xl mx-auto">
                Посмотрите, что говорят наши клиенты о качестве выполненных работ
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Анна М.</h3>
                <p class="text-sm opacity-75 mb-4">Ремонт квартиры в Sachsenhausen</p>
                <button class="text-sm bg-white text-accent-blue px-4 py-2 rounded hover:bg-opacity-90 transition-colors">
                    Смотреть видео
                </button>
            </div>
            
            <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Михаэль Ш.</h3>
                <p class="text-sm opacity-75 mb-4">Укладка полов в офисе</p>
                <button class="text-sm bg-white text-accent-blue px-4 py-2 rounded hover:bg-opacity-90 transition-colors">
                    Смотреть видео
                </button>
            </div>
            
            <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Томас Б.</h3>
                <p class="text-sm opacity-75 mb-4">Ремонт ванной под ключ</p>
                <button class="text-sm bg-white text-accent-blue px-4 py-2 rounded hover:bg-opacity-90 transition-colors">
                    Смотреть видео
                </button>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
            Станьте нашим следующим довольным клиентом
        </h2>
        <p class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto">
            Присоединяйтесь к сотням довольных клиентов, которые уже оценили качество нашей работы.
        </p>
        
        <?php render_frontend_button([
            'text' => 'Получить консультацию',
            'variant' => 'primary',
            'size' => 'lg',
            'href' => 'contact.php'
        ]); ?>
    </div>
</section>

<script>
// Rating stars functionality
document.querySelectorAll('.star').forEach(star => {
    star.addEventListener('click', function() {
        const rating = parseInt(this.dataset.rating);
        document.getElementById('rating-input').value = rating;
        
        // Update visual state
        document.querySelectorAll('.star').forEach((s, index) => {
            if (index < rating) {
                s.classList.remove('text-gray-300');
                s.classList.add('text-yellow-400');
            } else {
                s.classList.remove('text-yellow-400');
                s.classList.add('text-gray-300');
            }
        });
    });
});

// Set default 5-star rating
document.querySelectorAll('.star').forEach(star => {
    star.classList.remove('text-gray-300');
    star.classList.add('text-yellow-400');
});
</script>

<?php
$content = ob_get_clean();

// Рендеринг страницы
render_frontend_layout([
    'title' => $seo['title'],
    'meta_description' => $seo['description'],
    'active_page' => 'reviews',
    'content' => $content
]);
?>

