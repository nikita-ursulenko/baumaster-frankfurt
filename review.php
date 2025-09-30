<?php
/**
 * Страница отзывов
 * Baumaster Frontend - Reviews Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';
require_once __DIR__ . '/ui/base.php';

// Получение данных
$seo = get_seo_data()['reviews'];
$reviews = get_reviews_data_translated('ru');
$statistics = get_statistics();

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section id="hero" class="pt-16 bg-cover bg-center bg-no-repeat relative min-h-screen flex items-center" style="background-image: url('/assets/images/preview/reviews.png'); background-size: cover; background-position: center center; background-attachment: scroll; -webkit-background-size: cover;">
    <!-- Overlay for better text readability -->
    <div class="hero-overlay absolute inset-0 bg-black bg-opacity-30" style="z-index: 1;"></div>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-20 relative z-10">
        <div class="text-center">
            <h1 id="hero-title" class="font-montserrat font-semibold text-3xl lg:text-6xl text-white mb-6 leading-tight hero-text-shadow">
                <?php 
                $title = 'Отзывы наших клиентов';
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
                Читайте, что говорят о качестве нашей работы те, кто уже доверил нам свой ремонт. 
                Каждый отзыв — это история успешного проекта.
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
.card, .review-card, .stat-item, .feature-item {
    transition: all 0.3s ease;
}

.card:hover, .review-card:hover, .feature-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stat-item:hover {
    transform: translateY(0);
    box-shadow: none;
}

.card img, .review-card img, .stat-item img, .feature-item img {
    transition: transform 0.3s ease;
}

.card:hover img, .review-card:hover img, .feature-item:hover img {
    transform: scale(1.05);
}

.stat-item:hover img {
    transform: scale(1);
}

/* Review card smooth upscale on hover */
.review-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.review-card:hover {
    transform: scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
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
.card h3, .review-card h3, .stat-item h3, .feature-item h3 {
    transition: color 0.3s ease;
}

.card:hover h3, .review-card:hover h3, .feature-item:hover h3 {
    color: #3b82f6;
}

.stat-item:hover h3 {
    color: inherit;
}

/* Price and button smooth transitions */
.card .font-semibold, .review-card .font-semibold, .stat-item .font-semibold, .feature-item .font-semibold {
    transition: all 0.3s ease;
}

.card:hover .font-semibold, .review-card:hover .font-semibold, .feature-item:hover .font-semibold {
    transform: scale(1.05);
}

.stat-item:hover .font-semibold {
    transform: scale(1);
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
.card, .review-card, .stat-item, .feature-item {
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
    .py-20 > div:first-child,
    .py-16 > div:first-child {
        margin: 0 5%;
    }
    
    footer > div:first-child {
        margin: 0 5%;
    }
}
</style>

<!-- Statistics -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 text-center">
            <?php if (!empty($statistics)): ?>
                <?php foreach (array_slice($statistics, 0, 4) as $index => $stat): ?>
                    <div class="stat-item fade-in-up" style="animation-delay: <?php echo ($index * 0.2); ?>s;">
                        <div class="text-4xl font-bold text-accent-blue mb-2"><?php echo htmlspecialchars($stat['number']); ?></div>
                        <div class="text-text-secondary"><?php echo htmlspecialchars($stat['label']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Reviews Grid -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($reviews as $review): ?>
                <?php render_review_card($review); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Add Review Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 id="review-form-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4 fade-in-up">
                Оставьте отзыв о нашей работе
            </h2>
            <p id="review-form-subtitle" class="text-xl text-text-secondary max-w-2xl mx-auto fade-in-up">
                Ваше мнение важно для нас! Поделитесь впечатлениями о качестве выполненных работ.
            </p>
        </div>
        
        <div class="bg-white p-8 rounded-lg shadow-xl border border-gray-200">
            <!-- Сообщения об ошибках/успехе -->
            <div id="message-container" class="hidden mb-6">
                <div id="message-content" class="p-4 rounded-lg"></div>
            </div>
            
            <form action="add-review.php" method="POST" class="space-y-6" id="review-form">
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
                    <?php render_dropdown_field([
                        'name' => 'service',
                        'label' => 'Услуга',
                        'placeholder' => 'Выберите услугу',
                        'options' => [
                            ['value' => '', 'text' => 'Выберите услугу'],
                            ['value' => 'painting', 'text' => 'Малярные работы'],
                            ['value' => 'flooring', 'text' => 'Укладка полов'],
                            ['value' => 'bathroom', 'text' => 'Ремонт ванной'],
                            ['value' => 'drywall', 'text' => 'Гипсокартон'],
                            ['value' => 'tiling', 'text' => 'Плитка'],
                            ['value' => 'renovation', 'text' => 'Комплексный ремонт']
                        ],
                        'class' => 'w-full'
                    ]); ?>
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


<!-- CTA Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 text-center">
        <h2 id="cta-title" class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6 fade-in-up">
            Станьте нашим следующим довольным клиентом
        </h2>
        <p id="cta-subtitle" class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto fade-in-up">
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

// Функция для показа всплывающих уведомлений
function showNotification(message, type = 'error') {
    // Создаем контейнер для уведомлений, если его нет
    let notificationContainer = document.getElementById('notification-container');
    if (!notificationContainer) {
        notificationContainer = document.createElement('div');
        notificationContainer.id = 'notification-container';
        notificationContainer.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px; width: 100%;';
        document.body.appendChild(notificationContainer);
    }
    
    // Создаем уведомление
    const notification = document.createElement('div');
    notification.style.cssText = `
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease-in-out;
        margin-bottom: 10px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        pointer-events: auto;
    `;
    
    if (type === 'success') {
        notification.innerHTML = `
            <div style="padding: 16px;">
                <div style="display: flex; align-items: flex-start;">
                    <div style="flex-shrink: 0; margin-right: 12px;">
                        <div style="width: 24px; height: 24px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 16px; height: 16px; color: white;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="margin: 0; font-size: 14px; font-weight: 600; color: #111827;">Успешно!</p>
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">${message}</p>
                    </div>
                    <div style="flex-shrink: 0; margin-left: 12px;">
                        <button onclick="this.closest('[style*=\"transform\"]').remove()" style="background: none; border: none; padding: 4px; cursor: pointer; color: #9ca3af; border-radius: 4px; transition: color 0.2s;" onmouseover="this.style.color='#6b7280'" onmouseout="this.style.color='#9ca3af'">
                            <svg style="width: 16px; height: 16px;" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    } else {
        notification.innerHTML = `
            <div style="padding: 16px;">
                <div style="display: flex; align-items: flex-start;">
                    <div style="flex-shrink: 0; margin-right: 12px;">
                        <div style="width: 24px; height: 24px; background: #ef4444; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 16px; height: 16px; color: white;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="margin: 0; font-size: 14px; font-weight: 600; color: #111827;">Ошибка!</p>
                        <p style="margin: 4px 0 0 0; font-size: 14px; color: #6b7280;">${message}</p>
                    </div>
                    <div style="flex-shrink: 0; margin-left: 12px;">
                        <button onclick="this.closest('[style*=\"transform\"]').remove()" style="background: none; border: none; padding: 4px; cursor: pointer; color: #9ca3af; border-radius: 4px; transition: color 0.2s;" onmouseover="this.style.color='#6b7280'" onmouseout="this.style.color='#9ca3af'">
                            <svg style="width: 16px; height: 16px;" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Добавляем уведомление в контейнер
    notificationContainer.appendChild(notification);
    
    // Анимация появления
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
        notification.style.opacity = '1';
    }, 100);
    
    // Автоматически скрываем через 5 секунд
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Функция для показа сообщений (для совместимости)
function showMessage(message, type = 'error') {
    showNotification(message, type);
}

// Проверяем URL параметры при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const message = urlParams.get('message');
    
    if (success !== null && message) {
        const isSuccess = success === '1';
        showNotification(decodeURIComponent(message), isSuccess ? 'success' : 'error');
        
        // Очищаем URL от параметров
        const newUrl = window.location.pathname;
        window.history.replaceState({}, document.title, newUrl);
    }
});

// Обработка отправки формы
document.getElementById('review-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;
    
    // Показываем загрузку
    submitButton.disabled = true;
    submitButton.textContent = 'Отправка...';
    
    try {
        const response = await fetch('add-review.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            this.reset();
            // Сбрасываем рейтинг на 5 звезд
            document.querySelectorAll('.star').forEach(star => {
                star.classList.remove('text-gray-300');
                star.classList.add('text-yellow-400');
            });
            document.getElementById('rating-input').value = '5';
        } else {
            showMessage(result.message, 'error');
        }
    } catch (error) {
        showMessage('Произошла ошибка при отправке отзыва. Попробуйте позже.', 'error');
    } finally {
        // Восстанавливаем кнопку
        submitButton.disabled = false;
        submitButton.textContent = originalText;
    }
});

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
    const statItems = document.querySelectorAll('.stat-item');
    statItems.forEach((item, index) => {
        animateElement(item, 0);
    });
    
    // Animate review form section
    const reviewFormTitle = document.getElementById('review-form-title');
    const reviewFormSubtitle = document.getElementById('review-form-subtitle');
    
    if (reviewFormTitle) animateElement(reviewFormTitle, 0);
    if (reviewFormSubtitle) animateElement(reviewFormSubtitle, 200);
    
    
    // Animate CTA section
    const ctaTitle = document.getElementById('cta-title');
    const ctaSubtitle = document.getElementById('cta-subtitle');
    
    if (ctaTitle) animateElement(ctaTitle, 0);
    if (ctaSubtitle) animateElement(ctaSubtitle, 200);
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
    'active_page' => 'reviews',
    'content' => $content
]);
?>

