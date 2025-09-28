<?php
/**
 * Страница отзывов - немецкая версия
 * Baumaster Frontend - Reviews Page (German)
 */

// Подключение компонентов
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../ux/layout.php';
require_once __DIR__ . '/../ui/base.php';
require_once __DIR__ . '/../ux/components.php';
require_once __DIR__ . '/../ux/data.php';

// Установка языка
define('CURRENT_LANG', 'de');

// Получение данных
$seo = get_seo_data()['reviews'];
$reviews = get_reviews_data_translated('de');
$statistics = get_statistics('de');

// Начало контента
ob_start();
?>

    <!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                Bewertungen unserer Kunden
                </h1>
            <p class="text-xl text-text-secondary max-w-4xl mx-auto">
                Lesen Sie, was unsere zufriedenen Kunden über die Qualität unserer Arbeit sagen. 
                Jede Bewertung ist eine Geschichte eines erfolgreichen Projekts.
                </p>
            </div>
        </div>
    </section>

<!-- Statistics -->
<section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-4 gap-8 text-center">
            <?php if (!empty($statistics)): ?>
                <?php foreach (array_slice($statistics, 0, 4) as $stat): ?>
                    <div>
                        <div class="text-4xl font-bold text-accent-blue mb-2"><?php echo htmlspecialchars($stat['number']); ?></div>
                        <div class="text-text-secondary"><?php echo htmlspecialchars($stat['label']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div>
                    <div class="text-4xl font-bold text-accent-blue mb-2">500+</div>
                    <div class="text-text-secondary">Zufriedene Kunden</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-accent-blue mb-2">4.9</div>
                    <div class="text-text-secondary">Durchschnittsbewertung</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-accent-blue mb-2">98%</div>
                    <div class="text-text-secondary">Empfehlen uns</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-accent-blue mb-2">100%</div>
                    <div class="text-text-secondary">Abgeschlossene Projekte</div>
                </div>
            <?php endif; ?>
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
                Hinterlassen Sie eine Bewertung unserer Arbeit
            </h2>
            <p class="text-xl text-text-secondary max-w-2xl mx-auto">
                Ihre Meinung ist uns wichtig! Teilen Sie Ihre Eindrücke über die Qualität der ausgeführten Arbeiten.
            </p>
        </div>
        
        <div class="bg-white p-8 rounded-lg shadow-xl border border-gray-200">
            <form action="../add-review.php" method="POST" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ihr Name *</label>
                        <input type="text" name="name" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors"
                               placeholder="Geben Sie Ihren Namen ein">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors"
                               placeholder="ihre@email.com">
                    </div>
                </div>

                <div>
                    <?php render_dropdown_field([
                        'name' => 'service',
                        'label' => 'Dienstleistung',
                        'placeholder' => 'Dienstleistung wählen',
                        'options' => [
                            ['value' => '', 'text' => 'Dienstleistung wählen'],
                            ['value' => 'painting', 'text' => 'Malerarbeiten'],
                            ['value' => 'flooring', 'text' => 'Bodenverlegung'],
                            ['value' => 'bathroom', 'text' => 'Badezimmerrenovierung'],
                            ['value' => 'drywall', 'text' => 'Trockenbau'],
                            ['value' => 'tiling', 'text' => 'Fliesenverlegung'],
                            ['value' => 'renovation', 'text' => 'Komplettrenovierung']
                        ],
                        'class' => 'w-full'
                    ]); ?>
                </div>
                
                        <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bewertung *</label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bewertung *</label>
                    <textarea name="review" rows="5" required 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors"
                              placeholder="Erzählen Sie über die Qualität der ausgeführten Arbeiten, Einhaltung der Termine, Professionalität der Handwerker..."></textarea>
                        </div>
                
                <div class="flex items-start space-x-3">
                    <input type="checkbox" name="agree" id="agree-review" required 
                           class="mt-1 h-4 w-4 text-accent-blue focus:ring-accent-blue border-gray-300 rounded">
                    <label for="agree-review" class="text-sm text-text-secondary">
                        Ich stimme der Veröffentlichung meiner Bewertung auf der Website und der Verarbeitung personenbezogener Daten zu *
                    </label>
                            </div>
                
                <?php render_frontend_button([
                    'text' => 'Bewertung senden',
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
                Video-Bewertungen von Kunden
            </h2>
            <p class="text-xl opacity-90 max-w-3xl mx-auto">
                Sehen Sie, was unsere Kunden über die Qualität der ausgeführten Arbeiten sagen
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Anna M.</h3>
                <p class="text-sm opacity-75 mb-4">Wohnungsrenovierung in Sachsenhausen</p>
                <button class="text-sm bg-white text-accent-blue px-4 py-2 rounded hover:bg-opacity-90 transition-colors">
                    Video ansehen
                </button>
            </div>
            
            <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Michael Sch.</h3>
                <p class="text-sm opacity-75 mb-4">Bodenverlegung im Büro</p>
                <button class="text-sm bg-white text-accent-blue px-4 py-2 rounded hover:bg-opacity-90 transition-colors">
                    Video ansehen
                </button>
                    </div>
            
            <div class="bg-white bg-opacity-10 rounded-lg p-6 text-center">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-lg mb-2">Thomas B.</h3>
                <p class="text-sm opacity-75 mb-4">Badezimmerrenovierung schlüsselfertig</p>
                <button class="text-sm bg-white text-accent-blue px-4 py-2 rounded hover:bg-opacity-90 transition-colors">
                    Video ansehen
                </button>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
<section class="py-20 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
                Werden Sie unser nächster zufriedener Kunde
            </h2>
        <p class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto">
            Schließen Sie sich Hunderten von zufriedenen Kunden an, die bereits die Qualität unserer Arbeit geschätzt haben.
            </p>
        
                <?php render_frontend_button([
                    'text' => 'Kostenlose Beratung',
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
                        <p style="margin: 0; font-size: 14px; font-weight: 600; color: #111827;">Erfolgreich!</p>
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
                        <p style="margin: 0; font-size: 14px; font-weight: 600; color: #111827;">Fehler!</p>
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
</script>

<?php
$content = ob_get_clean();

// Рендеринг страницы
render_frontend_layout([
    'title' => 'Bewertungen - Baumaster Frankfurt',
    'meta_description' => 'Lesen Sie die Bewertungen unserer zufriedenen Kunden. Echte Erfahrungen mit Frankfurt Innenausbau.',
    'active_page' => 'reviews',
    'content' => $content,
    'language' => 'de'
]);
?>
