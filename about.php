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
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                О компании Frankfurt Innenausbau
            </h1>
            <p class="text-xl text-text-secondary max-w-4xl mx-auto">
                Мы — команда профессионалов с многолетним опытом в сфере внутренних работ и ремонта. 
                Наша миссия — превращать ваши идеи в реальность с премиальным качеством и вниманием к деталям.
            </p>
        </div>
    </div>
</section>

<!-- Company Story -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl mb-4">
                Цифры, которые говорят за нас
            </h2>
            <p class="text-xl opacity-90 max-w-3xl mx-auto">
                Результаты нашей работы в цифрах
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (!empty($statistics)): ?>
                <?php foreach ($statistics as $stat): ?>
                    <div class="text-center">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Наша команда
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Профессионалы своего дела с многолетним опытом
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (!empty($team_members)): ?>
                <?php foreach ($team_members as $member): ?>
                    <div class="text-center">
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Наши принципы
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Ценности, которыми мы руководствуемся в работе
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-8 rounded-lg text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Качество</h3>
                <p class="text-text-secondary">Используем только проверенные материалы и современные технологии. Каждая деталь важна.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Пунктуальность</h3>
                <p class="text-text-secondary">Соблюдаем договорные сроки. Ваше время ценно для нас, как и наше для вас.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Индивидуальный подход</h3>
                <p class="text-text-secondary">Каждый проект уникален. Учитываем ваши пожелания и особенности объекта.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Честные цены</h3>
                <p class="text-text-secondary">Никаких скрытых доплат. Цена в договоре остается неизменной до завершения работ.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center">
                <div class="w-16 h-16 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Гарантии</h3>
                <p class="text-text-secondary">Предоставляем расширенную гарантию на все виды работ. Уверены в качестве нашей работы.</p>
            </div>
            
            <div class="bg-white p-8 rounded-lg text-center">
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
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
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

