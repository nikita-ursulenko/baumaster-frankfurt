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

<!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                Наше портфолио
            </h1>
            <p class="text-xl text-text-secondary max-w-4xl mx-auto">
                Посмотрите примеры наших работ — от небольших косметических ремонтов до комплексной 
                реконструкции квартир и офисов во Франкфурте.
            </p>
        </div>
    </div>
</section>

<!-- Filter Tabs -->
<section class="py-8 bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($portfolio as $project): ?>
                <div class="portfolio-item bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden" 
                     data-category="<?php echo strtolower(str_replace(' ', '-', $project['category'])); ?>">
                    <div class="relative h-64 bg-gray-200 overflow-hidden group">
                        <img src="<?php echo htmlspecialchars($project['image']); ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center">
                            <button class="opacity-0 group-hover:opacity-100 bg-white text-accent-blue px-4 py-2 rounded font-medium transition-opacity" 
                                    onclick="openGallery(<?php echo $project['id']; ?>)">
                                Смотреть фото
                            </button>
                        </div>
                        <div class="absolute top-4 left-4">
                            <span class="inline-block px-3 py-1 bg-accent-blue text-white text-sm rounded-full">
                                <?php echo htmlspecialchars($project['category']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h3 class="font-semibold text-xl text-text-primary mb-3">
                            <?php echo htmlspecialchars($project['title']); ?>
                        </h3>
                        <p class="text-text-secondary mb-4 leading-relaxed">
                            <?php echo htmlspecialchars($project['description']); ?>
                        </p>
                        
                        <div class="flex justify-between items-center text-sm text-text-secondary border-t pt-4">
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                                </svg>
                                <?php echo htmlspecialchars($project['area']); ?>
                            </div>
                            <div class="flex items-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <?php echo htmlspecialchars($project['duration']); ?>
                            </div>
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
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Этапы выполнения проекта
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Каждый наш проект проходит тщательно продуманные этапы для достижения идеального результата
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Планирование</h3>
                <p class="text-text-secondary">Обсуждаем детали, создаём план работ, выбираем материалы и согласовываем сроки.</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Подготовка</h3>
                <p class="text-text-secondary">Демонтаж старых покрытий, подготовка поверхностей, защита мебели и интерьера.</p>
            </div>
            
            <div class="text-center">
                <div class="w-20 h-20 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Выполнение</h3>
                <p class="text-text-secondary">Основные работы: монтаж, отделка, покраска. Регулярный контроль качества на каждом этапе.</p>
            </div>
            
            <div class="text-center">
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
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
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

<script>
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

// Gallery modal
function openGallery(projectId) {
    alert(`Галерея для проекта ${projectId} (будет реализована позже)`);
}
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

