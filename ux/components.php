<?php
/**
 * Переиспользуемые компоненты для frontend
 * Baumaster Frontend Components
 */

// Подключение UI компонентов
require_once __DIR__ . '/../ui/base.php';

/**
 * Компонент кнопки
 */
function render_frontend_button($options = []) {
    $defaults = [
        'text' => 'Button',
        'type' => 'button',
        'variant' => 'primary', // primary, secondary, outline, ghost
        'size' => 'md', // sm, md, lg, xl
        'class' => '',
        'onclick' => '',
        'href' => null,
        'icon' => '',
        'disabled' => false
    ];
    
    $opts = array_merge($defaults, $options);
    
    // Base classes
    $base_classes = 'inline-flex items-center justify-center font-medium transition-colors focus:outline-none';
    
    // Size classes
    $size_classes = [
        'sm' => 'px-3 py-2 text-sm rounded',
        'md' => 'px-4 py-2 text-base rounded',
        'lg' => 'px-8 py-4 text-lg rounded-lg',
        'xl' => 'px-10 py-5 text-xl rounded-xl'
    ];
    
    // Variant classes
    $variant_classes = [
        'primary' => 'bg-accent-blue text-white hover:bg-opacity-90 shadow-lg',
        'secondary' => 'bg-gray-100 text-text-primary hover:bg-gray-200',
        'outline' => 'border-2 border-accent-blue text-accent-blue hover:bg-accent-blue hover:text-white',
        'ghost' => 'text-accent-blue hover:bg-accent-blue hover:bg-opacity-10'
    ];
    
    $classes = $base_classes . ' ' . $size_classes[$opts['size']] . ' ' . $variant_classes[$opts['variant']];
    if ($opts['class']) {
        $classes .= ' ' . $opts['class'];
    }
    
    if ($opts['href']) {
        // Render as link
        ?>
        <a href="<?php echo htmlspecialchars($opts['href']); ?>" class="<?php echo $classes; ?>" 
           <?php if ($opts['onclick']): ?>onclick="<?php echo htmlspecialchars($opts['onclick']); ?>"<?php endif; ?>>
            <?php if ($opts['icon']): echo $opts['icon']; endif; ?>
            <?php echo htmlspecialchars($opts['text']); ?>
        </a>
        <?php
    } else {
        // Render as button
        ?>
        <button type="<?php echo htmlspecialchars($opts['type']); ?>" class="<?php echo $classes; ?>"
                <?php if ($opts['onclick']): ?>onclick="<?php echo htmlspecialchars($opts['onclick']); ?>"<?php endif; ?>
                <?php if ($opts['disabled']): ?>disabled<?php endif; ?>>
            <?php if ($opts['icon']): echo $opts['icon']; endif; ?>
            <?php echo htmlspecialchars($opts['text']); ?>
        </button>
        <?php
    }
}

/**
 * Компонент карточки
 */
function render_frontend_card($options = []) {
    $defaults = [
        'title' => '',
        'content' => '',
        'image' => '',
        'class' => '',
        'hover' => true,
        'padding' => 'p-6'
    ];
    
    $opts = array_merge($defaults, $options);
    
    $card_classes = 'bg-white rounded-lg shadow-lg';
    if ($opts['hover']) {
        $card_classes .= ' hover:shadow-xl transition-shadow duration-300';
    }
    if ($opts['class']) {
        $card_classes .= ' ' . $opts['class'];
    }
    ?>
    <div class="<?php echo $card_classes; ?>">
        <?php if ($opts['image']): ?>
            <div class="relative overflow-hidden rounded-t-lg">
                <img src="<?php echo htmlspecialchars($opts['image']); ?>" alt="<?php echo htmlspecialchars($opts['title']); ?>" 
                     class="w-full h-48 object-cover">
            </div>
        <?php endif; ?>
        
        <div class="<?php echo $opts['padding']; ?>">
            <?php if ($opts['title']): ?>
                <h3 class="font-semibold text-xl text-text-primary mb-3">
                    <?php echo htmlspecialchars($opts['title']); ?>
                </h3>
            <?php endif; ?>
            
            <?php if ($opts['content']): ?>
                <div class="text-text-secondary">
                    <?php echo $opts['content']; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Компонент формы обратной связи
 */
function render_contact_form($options = []) {
    $defaults = [
        'title' => 'Получить консультацию',
        'subtitle' => 'Оставьте заявку и мы свяжемся с вами в течение часа',
        'action' => '/contact-form.php',
        'class' => '',
        'show_title' => true
    ];
    
    $opts = array_merge($defaults, $options);
    ?>
    <div class="bg-white p-8 rounded-lg shadow-xl <?php echo $opts['class']; ?>">
        <?php if ($opts['show_title']): ?>
            <h3 class="font-semibold text-2xl text-text-primary mb-2">
                <?php echo htmlspecialchars($opts['title']); ?>
            </h3>
            <p class="text-text-secondary mb-6">
                <?php echo htmlspecialchars($opts['subtitle']); ?>
            </p>
        <?php endif; ?>
        
        <?php
        // Определяем язык для форм
        $is_german = defined('CURRENT_LANG') && CURRENT_LANG === 'de';
        ?>
        <form action="<?php echo htmlspecialchars($opts['action']); ?>" method="POST" class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <input type="text" name="name" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors" 
                           placeholder="<?php echo $is_german ? 'Ihr Name *' : 'Ваше имя *'; ?>">
                </div>
                <div>
                    <input type="tel" name="phone" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors" 
                           placeholder="<?php echo $is_german ? 'Telefon *' : 'Телефон *'; ?>">
                </div>
            </div>
            
            <div>
                <input type="email" name="email" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors" 
                       placeholder="Email">
            </div>
            
            <div>
                <?php render_dropdown_field([
                    'name' => 'service',
                    'placeholder' => $is_german ? 'Dienstleistung wählen' : 'Выберите услугу',
                    'options' => [
                        ['value' => '', 'text' => $is_german ? 'Dienstleistung wählen' : 'Выберите услугу'],
                        ['value' => 'painting', 'text' => $is_german ? 'Malerarbeiten' : 'Малярные работы'],
                        ['value' => 'flooring', 'text' => $is_german ? 'Bodenverlegung' : 'Укладка полов'],
                        ['value' => 'bathroom', 'text' => $is_german ? 'Badezimmerrenovierung' : 'Ремонт ванной'],
                        ['value' => 'drywall', 'text' => $is_german ? 'Trockenbau' : 'Гипсокартон'],
                        ['value' => 'tiling', 'text' => $is_german ? 'Fliesenverlegung' : 'Плитка'],
                        ['value' => 'other', 'text' => $is_german ? 'Andere' : 'Другое']
                    ],
                    'class' => 'w-full'
                ]); ?>
            </div>
            
            <div>
                <textarea name="message" rows="4" 
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent-blue focus:border-accent-blue transition-colors" 
                          placeholder="<?php echo $is_german ? 'Beschreiben Sie Ihr Projekt...' : 'Опишите ваш проект...'; ?>"></textarea>
            </div>
            
            <div class="flex items-start space-x-3">
                <input type="checkbox" name="agree" id="agree" required 
                       class="mt-1 h-4 w-4 text-accent-blue focus:ring-accent-blue border-gray-300 rounded">
                <label for="agree" class="text-sm text-text-secondary">
                    <?php echo $is_german ? 'Ich stimme der Verarbeitung personenbezogener Daten und dem Erhalt von Informationsnachrichten zu *' : 'Я согласен на обработку персональных данных и получение информационных сообщений *'; ?>
                </label>
            </div>
            
            <?php render_frontend_button([
                'text' => $is_german ? 'Anfrage senden' : 'Отправить заявку',
                'type' => 'submit',
                'variant' => 'primary',
                'size' => 'lg',
                'class' => 'w-full'
            ]); ?>
        </form>
    </div>
    <?php
}

/**
 * Компонент секции услуг
 */
function render_service_card($service) {
    ?>
    <div class="bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden h-full flex flex-col">
        <?php if (!empty($service['image'])): ?>
            <div class="relative h-48 bg-gray-200">
                <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" 
                     class="w-full h-full object-cover">
            </div>
        <?php endif; ?>
        
        <div class="p-6 flex flex-col flex-grow">
            <h3 class="font-semibold text-xl text-text-primary mb-3">
                <?php echo htmlspecialchars($service['title']); ?>
            </h3>
            <p class="text-text-secondary mb-4 leading-relaxed flex-grow">
                <?php 
                $description = $service['description'];
                if (strlen($description) > 200) {
                    $description = substr($description, 0, 200);
                    $lastSpace = strrpos($description, ' ');
                    if ($lastSpace !== false) {
                        $description = substr($description, 0, $lastSpace);
                    }
                    $description .= '...';
                }
                echo htmlspecialchars($description); 
                ?>
            </p>
            
            <?php if (!empty($service['features'])): ?>
                <?php 
                // Обрабатываем features - может быть массивом или JSON строкой
                $features = $service['features'];
                if (is_string($features)) {
                    $features = json_decode($features, true);
                }
                if (is_array($features) && !empty($features)): 
                ?>
                <ul class="space-y-2 mb-6">
                    <?php foreach (array_slice($features, 0, 3) as $feature): ?>
                        <li class="flex items-center text-sm text-text-secondary">
                            <svg class="h-4 w-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <?php echo htmlspecialchars(is_array($feature) ? implode(', ', $feature) : $feature); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            <?php endif; ?>
            
            <div class="flex justify-between items-center mt-auto">
                  <?php if (!empty($service['price'])): ?>
                      <span class="font-semibold text-lg text-accent-blue">
                         <?php echo (defined('CURRENT_LANG') && CURRENT_LANG === 'de') ? 'ab' : 'от'; ?> <?php echo htmlspecialchars($service['price']); ?> €
                      </span>
                  <?php endif; ?>
                  
                  <div class="flex gap-2">
                      <?php render_frontend_button([
                         'text' => (defined('CURRENT_LANG') && CURRENT_LANG === 'de') ? 'Mehr erfahren' : 'Подробнее',
                        'variant' => 'outline',
                        'size' => 'sm',
                        'onclick' => "openServiceModal('" . htmlspecialchars($service['id'] ?? '') . "')"
                    ]); ?>
                  </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Компонент отзыва
 */
function render_review_card($review) {
    ?>
    <div class="bg-white p-6 rounded-lg shadow-lg review-card">
        <div class="flex items-center mb-4">
            <div class="flex-shrink-0">
                <?php if (!empty($review['client_photo'])): ?>
                    <?php 
                    $photo_src = $review['client_photo'];
                    // Если это не URL (не начинается с http), добавляем путь к папке
                    if (!preg_match('/^https?:\/\//', $photo_src)) {
                        $photo_src = '/assets/uploads/clients/' . $photo_src;
                    }
                    ?>
                    <img class="w-12 h-12 rounded-full object-cover border-2 border-accent-blue" 
                         src="<?php echo htmlspecialchars($photo_src); ?>" 
                         alt="<?php echo htmlspecialchars($review['name']); ?>"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-12 h-12 bg-accent-blue rounded-full flex items-center justify-center text-white font-semibold" style="display: none;">
                        <?php echo strtoupper(substr($review['name'], 0, 1)); ?>
                    </div>
                <?php else: ?>
                    <div class="w-12 h-12 bg-accent-blue rounded-full flex items-center justify-center text-white font-semibold">
                        <?php echo strtoupper(substr($review['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ml-4">
                <h4 class="font-semibold text-text-primary"><?php echo htmlspecialchars($review['name']); ?></h4>
                <div class="flex items-center">
                    <?php for ($i = 0; $i < 5; $i++): ?>
                        <svg class="h-4 w-4 <?php echo $i < ($review['rating'] ?? 5) ? 'text-yellow-400' : 'text-gray-300'; ?>" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    <?php endfor; ?>
                    <span class="ml-1 text-sm text-text-secondary"><?php echo $review['rating'] ?? 5; ?>/5</span>
                </div>
            </div>
        </div>
        
        <p class="text-text-secondary leading-relaxed mb-3">
            "<?php 
            $text = $review['text'];
            if (strlen($text) > 250) {
                $text = substr($text, 0, 250);
                $lastSpace = strrpos($text, ' ');
                if ($lastSpace !== false) {
                    $text = substr($text, 0, $lastSpace);
                }
                $text .= '...';
            }
            echo htmlspecialchars($text); 
            ?>"
        </p>
        
        <?php if (!empty($review['service'])): ?>
            <div class="text-sm text-accent-blue font-medium">
                <?php 
                $service_label = (defined('CURRENT_LANG') && CURRENT_LANG === 'de') ? 'Dienstleistung' : 'Услуга';
                echo $service_label . ': ' . htmlspecialchars($review['service']); 
                ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Компонент FAQ элемента
 */
function render_faq_item($faq, $index = 0) {
    $id = 'faq-' . $index;
    ?>
    <div class="bg-white rounded-lg shadow-md">
        <button class="w-full text-left p-6 focus:outline-none" onclick="toggleFAQ('<?php echo $id; ?>')">
            <div class="flex justify-between items-center">
                <h3 class="font-semibold text-lg text-text-primary pr-4">
                    <?php echo htmlspecialchars($faq['question']); ?>
                </h3>
                <svg id="<?php echo $id; ?>-icon" class="h-5 w-5 text-text-secondary transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </button>
        <div id="<?php echo $id; ?>-content" class="hidden px-6 pb-6">
            <p class="text-text-secondary leading-relaxed">
                <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
            </p>
        </div>
    </div>
    
    <script>
    function toggleFAQ(id) {
        const content = document.getElementById(id + '-content');
        const icon = document.getElementById(id + '-icon');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            icon.style.transform = 'rotate(0deg)';
        }
    }
    </script>
    <?php
}
?>

