<?php
/**
 * Страница FAQ/Блог - немецкая версия
 * Baumaster Frontend - FAQ/Blog Page (German)
 */

// Устанавливаем язык
define('CURRENT_LANG', 'de');

// Подключение компонентов
require_once __DIR__ . '/../ux/layout.php';
require_once __DIR__ . '/../ux/components.php';
require_once __DIR__ . '/../ux/data.php';

// Получение данных
$seo = get_seo_data()['blog'];
$faq = get_faq_data_translated('de');
$blog_posts = get_blog_posts(6, null, 'de');

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                Häufig gestellte Fragen
            </h1>
            <p class="text-xl text-text-secondary max-w-4xl mx-auto">
                Antworten auf die häufigsten Fragen zu Renovierungen, Zeitplänen, Kosten und Arbeitsabläufen. 
                Haben Sie keine Antwort gefunden? Kontaktieren Sie uns direkt.
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
                Nützliche Tipps
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Praktische Empfehlungen von unseren Meistern
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Vorbereitung auf die Renovierung</h3>
                <p class="text-text-secondary mb-4">
                    Wie Sie den Raum richtig auf den Beginn der Arbeiten vorbereiten und was Sie im Voraus berücksichtigen sollten.
                </p>
                <ul class="text-sm text-text-secondary space-y-1">
                    <li>• Räumen Sie den Raum von Möbeln frei</li>
                    <li>• Entfernen Sie wertvolle Gegenstände</li>
                    <li>• Sorgen Sie für Zugang zum Raum</li>
                    <li>• Koordinieren Sie die Arbeitszeiten mit den Nachbarn</li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Einsparungen bei der Renovierung</h3>
                <p class="text-text-secondary mb-4">
                    Legale Wege, bei der Renovierung zu sparen, ohne die Qualität der Arbeiten zu beeinträchtigen.
                </p>
                <ul class="text-sm text-text-secondary space-y-1">
                    <li>• Kaufen Sie Materialien selbst</li>
                    <li>• Wählen Sie die Saison für die Renovierung</li>
                    <li>• Führen Sie die Renovierung schrittweise durch</li>
                    <li>• Nutzen Sie Aktionen und Rabatte</li>
                </ul>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-lg flex items-center justify-center mb-4">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-xl text-text-primary mb-3">Qualitätskontrolle</h3>
                <p class="text-text-secondary mb-4">
                    Worauf Sie bei der Abnahme der Arbeiten achten sollten und wie Sie die Ausführungsqualität überprüfen.
                </p>
                <ul class="text-sm text-text-secondary space-y-1">
                    <li>• Überprüfen Sie die Ebenheit der Oberflächen</li>
                    <li>• Untersuchen Sie Ecken und Fugen</li>
                    <li>• Überprüfen Sie die Funktion aller Systeme</li>
                    <li>• Machen Sie Fotos für die Garantie</li>
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
                Artikel und Neuigkeiten
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Aktuelle Informationen über Renovierungen, neue Materialien und Technologien
            </p>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($blog_posts)): ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p>Blog-Artikel erscheinen bald</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($blog_posts as $post): ?>
                    <article class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <?php if (!empty($post['featured_image'])): ?>
                            <div class="h-48 overflow-hidden">
                                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>"
                                     alt="<?php echo htmlspecialchars($post['title']); ?>"
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            </div>
                        <?php else: ?>
                            <div class="h-48 bg-gray-200 flex items-center justify-center">
                                <span class="text-gray-500">Artikelbild</span>
                            </div>
                        <?php endif; ?>

                        <div class="p-6">
                            <div class="text-sm text-accent-blue font-medium mb-2">
                                <?php echo format_date($post['published_at'], 'd.m.Y'); ?>
                            </div>
                            <h3 class="font-semibold text-xl text-text-primary mb-3">
                                <a href="blog_post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>"
                                   class="hover:text-accent-blue transition-colors">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h3>
                            <?php if (!empty($post['excerpt'])): ?>
                                <p class="text-text-secondary mb-4 line-clamp-3">
                                    <?php echo htmlspecialchars($post['excerpt']); ?>
                                </p>
                            <?php endif; ?>
                            <a href="blog_post.php?slug=<?php echo htmlspecialchars($post['slug']); ?>"
                               class="text-accent-blue font-medium hover:underline">
                                Weiterlesen →
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Кнопка "Больше статей" -->
        <div class="text-center mt-12">
            <a href="blog_all.php" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-accent-blue hover:bg-accent-blue-dark transition-colors duration-200">
                Alle Artikel anzeigen
                <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-6">
            Haben Sie keine Antwort auf Ihre Frage gefunden?
        </h2>
        <p class="text-xl text-text-secondary mb-8 max-w-2xl mx-auto">
            Kontaktieren Sie uns direkt, und wir beantworten alle Ihre Fragen zu Renovierungen und Innenausbau.
        </p>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <?php render_frontend_button([
                'text' => 'Frage stellen',
                'variant' => 'primary',
                'size' => 'lg',
                'href' => 'contact.php'
            ]); ?>
            <?php render_frontend_button([
                'text' => 'Jetzt anrufen',
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