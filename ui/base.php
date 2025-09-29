<?php
/**
 * Базовые UI компоненты для Baumaster Admin
 * Все переиспользуемые элементы интерфейса
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * ФУНКЦИИ ГЕНЕРАЦИИ HTML HEAD
 */

/**
 * Генерация базового HTML head для страниц
 */
function render_admin_head($title, $description = '', $additional_css = '', $additional_js = '') {
    $lang = get_current_language();
    $site_name = SITE_NAME;
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?> - <?php echo $site_name; ?></title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1'
                        },
                        admin: {
                            bg: '#f8fafc',
                            sidebar: '#1e293b',
                            card: '#ffffff',
                            border: '#e2e8f0',
                            text: '#334155',
                            muted: '#64748b'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Chart.js для графиков -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo get_site_url('assets/images/favicon.ico'); ?>">
    
    <!-- Meta теги -->
    <meta name="robots" content="noindex, nofollow">
    <?php if ($description): ?>
        <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <?php endif; ?>
    
    <!-- Дополнительный CSS -->
    <?php if ($additional_css): ?>
        <?php echo $additional_css; ?>
    <?php endif; ?>
    
    <!-- Дополнительный JavaScript -->
    <?php if ($additional_js): ?>
        <?php echo $additional_js; ?>
    <?php endif; ?>
    <?php
}

/**
 * КОМПОНЕНТЫ УВЕДОМЛЕНИЙ
 */

/**
 * Отображение уведомления об ошибке
 */
function render_error_message($message) {
    if (empty($message)) return;
    ?>
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700 text-sm mb-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    </div>
    <?php
}

/**
 * Отображение уведомления об успехе
 */
function render_success_message($message) {
    if (empty($message)) return;
    ?>
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-700 text-sm mb-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    </div>
    <?php
}

/**
 * Отображение информационного уведомления
 */
function render_info_message($message) {
    if (empty($message)) return;
    ?>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-blue-700 text-sm mb-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span><?php echo htmlspecialchars($message); ?></span>
        </div>
    </div>
    <?php
}

/**
 * КОМПОНЕНТЫ ФОРМ
 */

/**
 * Генерация поля ввода текста
 */
function render_input_field($options = []) {
    $defaults = [
        'type' => 'text',
        'name' => '',
        'id' => '',
        'label' => '',
        'placeholder' => '',
        'value' => '',
        'required' => false,
        'autocomplete' => '',
        'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200',
        'container_class' => 'space-y-2'
    ];
    
    $opts = array_merge($defaults, $options);
    $id = $opts['id'] ?: $opts['name'];
    ?>
    <div class="<?php echo $opts['container_class']; ?>">
        <?php if ($opts['label']): ?>
            <label for="<?php echo htmlspecialchars($id); ?>" class="block text-sm font-medium text-gray-700">
                <?php echo htmlspecialchars($opts['label']); ?>
                <?php if ($opts['required']): ?>
                    <span class="text-red-500">*</span>
                <?php endif; ?>
            </label>
        <?php endif; ?>
        <input 
            type="<?php echo htmlspecialchars($opts['type']); ?>"
            id="<?php echo htmlspecialchars($id); ?>"
            name="<?php echo htmlspecialchars($opts['name']); ?>"
            <?php if ($opts['required']): ?>required<?php endif; ?>
            <?php if ($opts['autocomplete']): ?>autocomplete="<?php echo htmlspecialchars($opts['autocomplete']); ?>"<?php endif; ?>
            class="<?php echo $opts['class']; ?>"
            <?php if ($opts['placeholder']): ?>placeholder="<?php echo htmlspecialchars($opts['placeholder']); ?>"<?php endif; ?>
            value="<?php echo htmlspecialchars($opts['value']); ?>"
        >
    </div>
    <?php
}

/**
 * Генерация поля для пароля с кнопкой показа/скрытия
 */
function render_password_field($options = []) {
    $defaults = [
        'name' => 'password',
        'id' => 'password',
        'label' => __('auth.password', 'Пароль'),
        'placeholder' => __('auth.password_placeholder', 'Введите пароль'),
        'required' => true,
        'autocomplete' => 'current-password'
    ];
    
    $opts = array_merge($defaults, $options);
    $id = $opts['id'];
    ?>
    <div class="space-y-2">
        <label for="<?php echo htmlspecialchars($id); ?>" class="block text-sm font-medium text-gray-700">
            <?php echo htmlspecialchars($opts['label']); ?>
            <?php if ($opts['required']): ?>
                <span class="text-red-500">*</span>
            <?php endif; ?>
        </label>
        <div class="relative">
            <input 
                type="password" 
                id="<?php echo htmlspecialchars($id); ?>" 
                name="<?php echo htmlspecialchars($opts['name']); ?>" 
                <?php if ($opts['required']): ?>required<?php endif; ?>
                <?php if ($opts['autocomplete']): ?>autocomplete="<?php echo htmlspecialchars($opts['autocomplete']); ?>"<?php endif; ?>
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200 pr-12"
                placeholder="<?php echo htmlspecialchars($opts['placeholder']); ?>"
            >
            <button 
                type="button" 
                onclick="togglePassword('<?php echo htmlspecialchars($id); ?>')"
                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
                <svg id="eye-icon-<?php echo htmlspecialchars($id); ?>" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <script>
    function togglePassword(fieldId) {
        const passwordInput = document.getElementById(fieldId);
        const eyeIcon = document.getElementById('eye-icon-' + fieldId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
            `;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        }
    }
    </script>
    <?php
}

/**
 * Генерация textarea
 */
function render_textarea_field($options = []) {
    $defaults = [
        'name' => '',
        'id' => '',
        'label' => '',
        'placeholder' => '',
        'value' => '',
        'required' => false,
        'rows' => 4,
        'class' => 'w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200',
        'container_class' => 'space-y-2'
    ];
    
    $opts = array_merge($defaults, $options);
    $id = $opts['id'] ?: $opts['name'];
    ?>
    <div class="<?php echo $opts['container_class']; ?>">
        <?php if ($opts['label']): ?>
            <label for="<?php echo htmlspecialchars($id); ?>" class="block text-sm font-medium text-gray-700">
                <?php echo htmlspecialchars($opts['label']); ?>
                <?php if ($opts['required']): ?>
                    <span class="text-red-500">*</span>
                <?php endif; ?>
            </label>
        <?php endif; ?>
        <textarea 
            id="<?php echo htmlspecialchars($id); ?>"
            name="<?php echo htmlspecialchars($opts['name']); ?>"
            rows="<?php echo intval($opts['rows']); ?>"
            <?php if ($opts['required']): ?>required<?php endif; ?>
            class="<?php echo $opts['class']; ?>"
            <?php if ($opts['placeholder']): ?>placeholder="<?php echo htmlspecialchars($opts['placeholder']); ?>"<?php endif; ?>
        ><?php echo htmlspecialchars($opts['value']); ?></textarea>
    </div>
    <?php
}

/**
 * Генерация кастомного dropdown
 */
function render_dropdown_field($options = []) {
    $defaults = [
        'name' => '',
        'id' => '',
        'label' => '',
        'placeholder' => 'Выберите опцию',
        'value' => '',
        'options' => [],
        'required' => false,
        'disabled' => false,
        'class' => 'w-full',
        'container_class' => 'space-y-2',
        'searchable' => false,
        'multiple' => false,
        'onchange' => ''
    ];
    
    $opts = array_merge($defaults, $options);
    $id = $opts['id'] ?: $opts['name'];
    $unique_id = $id . '_' . uniqid();
    ?>
    <div class="<?php echo $opts['container_class']; ?>">
        <?php if ($opts['label']): ?>
            <label for="<?php echo htmlspecialchars($id); ?>" class="block text-sm font-medium text-gray-700">
                <?php echo htmlspecialchars($opts['label']); ?>
                <?php if ($opts['required']): ?>
                    <span class="text-red-500">*</span>
                <?php endif; ?>
            </label>
        <?php endif; ?>
        
        <div class="relative <?php echo $opts['class']; ?>" id="dropdown-container-<?php echo $unique_id; ?>">
            <button 
                type="button" 
                class="w-full px-4 py-3 text-left bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200 flex items-center justify-between <?php echo $opts['disabled'] ? 'bg-gray-50 cursor-not-allowed' : 'hover:border-gray-400'; ?>"
                id="dropdown-button-<?php echo $unique_id; ?>"
                <?php if ($opts['disabled']): ?>disabled<?php endif; ?>
                onclick="toggleDropdown('<?php echo $unique_id; ?>')"
            >
                <span id="dropdown-selected-<?php echo $unique_id; ?>" class="block truncate">
                    <?php 
                    $selected_text = $opts['placeholder'];
                    if (!empty($opts['value'])) {
                        foreach ($opts['options'] as $option) {
                            if (is_array($option)) {
                                if ($option['value'] == $opts['value']) {
                                    $selected_text = $option['text'];
                                    break;
                                }
                            } else {
                                if ($option == $opts['value']) {
                                    $selected_text = $option;
                                    break;
                                }
                            }
                        }
                    }
                    echo htmlspecialchars($selected_text);
                    ?>
                </span>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" id="dropdown-arrow-<?php echo $unique_id; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <div 
                class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden" 
                id="dropdown-menu-<?php echo $unique_id; ?>"
            >
                <?php if ($opts['searchable']): ?>
                    <div class="p-2 border-b border-gray-200">
                        <input 
                            type="text" 
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            placeholder="Поиск..."
                            id="dropdown-search-<?php echo $unique_id; ?>"
                            onkeyup="filterDropdownOptions('<?php echo $unique_id; ?>', this.value)"
                        >
                    </div>
                <?php endif; ?>
                
                <div class="max-h-60 overflow-y-auto" id="dropdown-options-<?php echo $unique_id; ?>">
                    <?php foreach ($opts['options'] as $option): ?>
                        <?php 
                        $option_value = is_array($option) ? $option['value'] : $option;
                        $option_text = is_array($option) ? $option['text'] : $option;
                        $is_selected = $option_value == $opts['value'];
                        ?>
                        <button 
                            type="button"
                            class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 <?php echo $is_selected ? 'bg-primary-50 text-primary-700' : 'text-gray-900'; ?>"
                            data-value="<?php echo htmlspecialchars($option_value); ?>"
                            data-text="<?php echo htmlspecialchars($option_text); ?>"
                            onclick="selectDropdownOption('<?php echo $unique_id; ?>', '<?php echo htmlspecialchars($option_value); ?>', '<?php echo htmlspecialchars($option_text); ?>')"
                        >
                            <div class="flex items-center">
                                <?php if ($is_selected): ?>
                                    <svg class="w-4 h-4 mr-2 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($option_text); ?></span>
                            </div>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Скрытое поле для отправки значения -->
            <input 
                type="hidden" 
                name="<?php echo htmlspecialchars($opts['name']); ?>" 
                id="<?php echo htmlspecialchars($id); ?>"
                value="<?php echo htmlspecialchars($opts['value']); ?>"
                <?php if ($opts['onchange']): ?>onchange="<?php echo htmlspecialchars($opts['onchange']); ?>"<?php endif; ?>
            >
        </div>
    </div>
    
    <script>
    function toggleDropdown(uniqueId) {
        const menu = document.getElementById('dropdown-menu-' + uniqueId);
        const arrow = document.getElementById('dropdown-arrow-' + uniqueId);
        
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            arrow.style.transform = 'rotate(180deg)';
        } else {
            menu.classList.add('hidden');
            arrow.style.transform = 'rotate(0deg)';
        }
    }
    
    function selectDropdownOption(uniqueId, value, text) {
        const selectedSpan = document.getElementById('dropdown-selected-' + uniqueId);
        const hiddenInput = document.getElementById(uniqueId.split('_')[0]);
        const menu = document.getElementById('dropdown-menu-' + uniqueId);
        const arrow = document.getElementById('dropdown-arrow-' + uniqueId);
        
        selectedSpan.textContent = text;
        if (hiddenInput) {
            hiddenInput.value = value;
        }
        
        // Обновляем визуальное состояние опций
        const options = document.querySelectorAll('#dropdown-options-' + uniqueId + ' button');
        options.forEach(option => {
            option.classList.remove('bg-primary-50', 'text-primary-700');
            option.classList.add('text-gray-900');
            
            // Убираем галочку
            const checkIcon = option.querySelector('svg');
            if (checkIcon) {
                checkIcon.remove();
            }
            
            if (option.dataset.value === value) {
                option.classList.add('bg-primary-50', 'text-primary-700');
                option.classList.remove('text-gray-900');
                
                // Добавляем галочку
                const checkSvg = document.createElement('svg');
                checkSvg.className = 'w-4 h-4 mr-2 text-primary-600';
                checkSvg.setAttribute('fill', 'currentColor');
                checkSvg.setAttribute('viewBox', '0 0 20 20');
                checkSvg.innerHTML = '<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>';
                option.querySelector('div').insertBefore(checkSvg, option.querySelector('span'));
            }
        });
        
        // Закрываем меню
        menu.classList.add('hidden');
        arrow.style.transform = 'rotate(0deg)';
        
        // Вызываем onchange если есть
        if (hiddenInput.onchange) {
            hiddenInput.onchange();
        }
    }
    
    function filterDropdownOptions(uniqueId, searchTerm) {
        const options = document.querySelectorAll('#dropdown-options-' + uniqueId + ' button');
        const searchLower = searchTerm.toLowerCase();
        
        options.forEach(option => {
            const text = option.dataset.text.toLowerCase();
            if (text.includes(searchLower)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    }
    
    // Закрытие dropdown при клике вне его
    document.addEventListener('click', function(event) {
        const dropdowns = document.querySelectorAll('[id^="dropdown-container-"]');
        dropdowns.forEach(container => {
            if (!container.contains(event.target)) {
                const uniqueId = container.id.replace('dropdown-container-', '');
                const menu = document.getElementById('dropdown-menu-' + uniqueId);
                const arrow = document.getElementById('dropdown-arrow-' + uniqueId);
                
                if (menu && !menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                    arrow.style.transform = 'rotate(0deg)';
                }
            }
        });
    });
    </script>
    <?php
}

/**
 * КОМПОНЕНТЫ КНОПОК
 */

/**
 * Генерация кнопки
 */
function render_button($options = []) {
    $defaults = [
        'type' => 'button',
        'text' => 'Button',
        'variant' => 'primary', // primary, secondary, danger, success
        'size' => 'md', // sm, md, lg
        'disabled' => false,
        'onclick' => '',
        'class' => '',
        'icon' => '',
        'href' => null // для ссылок
    ];
    
    $opts = array_merge($defaults, $options);
    
    // Базовые классы
    $base_classes = 'inline-flex items-center justify-center font-medium rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    // Размеры
    $size_classes = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-3 text-sm',
        'lg' => 'px-6 py-4 text-base'
    ];
    
    // Варианты стилей
    $variant_classes = [
        'primary' => 'bg-gradient-to-r from-primary-500 to-primary-600 text-white hover:from-primary-600 hover:to-primary-700 focus:ring-primary-500',
        'secondary' => 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-primary-500',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 focus:ring-red-500',
        'success' => 'bg-green-500 text-white hover:bg-green-600 focus:ring-green-500'
    ];
    
    $classes = $base_classes . ' ' . $size_classes[$opts['size']] . ' ' . $variant_classes[$opts['variant']];
    if ($opts['class']) {
        $classes .= ' ' . $opts['class'];
    }
    
    // Если это ссылка
    if ($opts['href']) {
        ?>
        <a href="<?php echo htmlspecialchars($opts['href']); ?>" class="<?php echo $classes; ?>">
            <?php if ($opts['icon']): ?>
                <?php echo $opts['icon']; ?>
            <?php endif; ?>
            <span><?php echo htmlspecialchars($opts['text']); ?></span>
        </a>
        <?php
    } else {
        // Обычная кнопка
        ?>
        <button 
            type="<?php echo htmlspecialchars($opts['type']); ?>"
            class="<?php echo $classes; ?>"
            <?php if ($opts['disabled']): ?>disabled<?php endif; ?>
            <?php if ($opts['onclick']): ?>onclick="<?php echo htmlspecialchars($opts['onclick']); ?>"<?php endif; ?>
        >
            <?php if ($opts['icon']): ?>
                <?php echo $opts['icon']; ?>
            <?php endif; ?>
            <span><?php echo htmlspecialchars($opts['text']); ?></span>
        </button>
        <?php
    }
}

/**
 * КОМПОНЕНТЫ КАРТОЧЕК
 */

/**
 * Генерация статистической карточки
 */
function render_stat_card($options = []) {
    $defaults = [
        'title' => '',
        'value' => 0,
        'change' => '',
        'icon' => '',
        'color' => 'blue', // blue, green, yellow, purple, red
        'link' => '',
        'hover' => true
    ];
    
    $opts = array_merge($defaults, $options);
    
    $color_classes = [
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500',
        'yellow' => 'bg-yellow-500',
        'purple' => 'bg-purple-500',
        'red' => 'bg-red-500'
    ];
    
    $link_colors = [
        'blue' => 'text-blue-600 hover:text-blue-800',
        'green' => 'text-green-600 hover:text-green-800',
        'yellow' => 'text-yellow-600 hover:text-yellow-800',
        'purple' => 'text-purple-600 hover:text-purple-800',
        'red' => 'text-red-600 hover:text-red-800'
    ];
    
    $card_class = 'bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200 transition-all duration-300';
    if ($opts['hover']) {
        $card_class .= ' card-hover';
    }
    ?>
    <div class="<?php echo $card_class; ?>">
        <div class="p-5">
            <div class="flex items-center">
                <?php if ($opts['icon']): ?>
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 <?php echo $color_classes[$opts['color']]; ?> rounded-md flex items-center justify-center">
                            <?php echo $opts['icon']; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">
                            <?php echo htmlspecialchars($opts['title']); ?>
                        </dt>
                        <dd class="flex items-center">
                            <span class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($opts['value']); ?></span>
                            <?php if ($opts['change']): ?>
                                <?php 
                                $change_value = $opts['change'];
                                $is_positive = strpos($change_value, '+') === 0;
                                $change_class = $is_positive ? 'text-green-600' : 'text-red-600';
                                $arrow = $is_positive ? '↑' : '↓';
                                ?>
                                <span class="ml-2 text-sm <?php echo $change_class; ?> font-medium">
                                    <?php echo $arrow . ' ' . $change_value; ?>
                                </span>
                            <?php endif; ?>
                        </dd>
                    </dl>
                </div>
                <?php if ($opts['link']): ?>
                    <div class="flex-shrink-0">
                        <a href="<?php echo htmlspecialchars($opts['link']); ?>" class="<?php echo $link_colors[$opts['color']]; ?>">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * ФУНКЦИИ ДЛЯ РАБОТЫ С ИЗОБРАЖЕНИЯМИ
 */

/**
 * Генерация поля загрузки изображения
 */
function render_image_upload_field($options = []) {
    $defaults = [
        'name' => 'image',
        'id' => 'image',
        'label' => __('common.image', 'Изображение'),
        'current_image' => '',
        'required' => false,
        'accept' => 'image/*',
        'max_size' => '10MB',
        'container_class' => 'space-y-2'
    ];
    
    $opts = array_merge($defaults, $options);
    $id = $opts['id'];
    ?>
    <div class="<?php echo $opts['container_class']; ?>">
        <?php if ($opts['label']): ?>
            <label for="<?php echo htmlspecialchars($id); ?>" class="block text-sm font-medium text-gray-700">
                <?php echo htmlspecialchars($opts['label']); ?>
                <?php if ($opts['required']): ?>
                    <span class="text-red-500">*</span>
                <?php endif; ?>
            </label>
        <?php endif; ?>
        
        <!-- Текущее изображение -->
        <?php if ($opts['current_image']): ?>
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2"><?php echo __('common.current_image', 'Текущее изображение'); ?>:</p>
                <div class="relative inline-block">
                    <img src="<?php echo htmlspecialchars($opts['current_image']); ?>" 
                         alt="<?php echo __('common.current_image', 'Текущее изображение'); ?>" 
                         class="w-32 h-32 object-cover rounded-lg border border-gray-300">
                    <button type="button" 
                            onclick="removeCurrentImage_<?php echo htmlspecialchars($id); ?>()" 
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                        ×
                    </button>
                </div>
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($opts['current_image']); ?>">
            </div>
        <?php endif; ?>
        
        <!-- Поле загрузки -->
        <div class="relative">
            <input 
                type="file" 
                id="<?php echo htmlspecialchars($id); ?>"
                name="<?php echo htmlspecialchars($opts['name']); ?>"
                <?php if ($opts['required'] && !$opts['current_image']): ?>required<?php endif; ?>
                accept="<?php echo htmlspecialchars($opts['accept']); ?>"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                onchange="previewImage_<?php echo htmlspecialchars($id); ?>(this)"
            >
            <p class="text-xs text-gray-500 mt-1">
                <?php echo __('common.max_file_size', 'Максимальный размер файла'); ?>: <?php echo $opts['max_size']; ?>
            </p>
        </div>
        
        <!-- Превью нового изображения -->
        <div id="image-preview-<?php echo htmlspecialchars($id); ?>" class="hidden mt-4">
            <p class="text-sm text-gray-600 mb-2"><?php echo __('common.new_image_preview', 'Превью нового изображения'); ?>:</p>
            <img id="preview-img-<?php echo htmlspecialchars($id); ?>" 
                 class="w-32 h-32 object-cover rounded-lg border border-gray-300">
        </div>
    </div>
    
    <script>
    function previewImage_<?php echo htmlspecialchars($id); ?>(input) {
        const preview = document.getElementById('image-preview-<?php echo htmlspecialchars($id); ?>');
        const previewImg = document.getElementById('preview-img-<?php echo htmlspecialchars($id); ?>');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.classList.add('hidden');
        }
    }
    
    function removeCurrentImage_<?php echo htmlspecialchars($id); ?>() {
        const currentImageInput = document.querySelector('input[name="current_image"]');
        if (currentImageInput) {
            currentImageInput.value = '';
        }
        const currentImageDiv = document.querySelector('.relative.inline-block');
        if (currentImageDiv) {
            currentImageDiv.parentElement.remove();
        }
    }
    </script>
    <?php
}

/**
 * Генерация галереи изображений
 */
function render_image_gallery_field($options = []) {
    $defaults = [
        'name' => 'gallery',
        'id' => 'gallery',
        'label' => __('common.gallery', 'Галерея изображений'),
        'current_images' => [],
        'max_files' => 10,
        'container_class' => 'space-y-2'
    ];
    
    $opts = array_merge($defaults, $options);
    $id = $opts['id'];
    $current_images = is_string($opts['current_images']) ? json_decode($opts['current_images'], true) : $opts['current_images'];
    ?>
    <div class="<?php echo $opts['container_class']; ?>">
        <?php if ($opts['label']): ?>
            <label for="<?php echo htmlspecialchars($id); ?>" class="block text-sm font-medium text-gray-700">
                <?php echo htmlspecialchars($opts['label']); ?>
            </label>
        <?php endif; ?>
        
        <!-- Текущие изображения -->
        <?php if (!empty($current_images)): ?>
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2"><?php echo __('common.current_gallery', 'Текущая галерея'); ?>:</p>
                <div class="grid grid-cols-4 gap-2" id="current-gallery">
                    <?php foreach ($current_images as $index => $image): ?>
                        <div class="relative group">
                            <img src="<?php echo htmlspecialchars($image); ?>" 
                                 alt="<?php echo __('common.gallery_image', 'Изображение галереи'); ?>" 
                                 class="w-full h-20 object-cover rounded-lg border border-gray-300">
                            <button type="button" 
                                    onclick="removeGalleryImage_<?php echo htmlspecialchars($id); ?>(<?php echo $index; ?>)" 
                                    class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                ×
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" name="current_gallery" value="<?php echo htmlspecialchars(json_encode($current_images)); ?>">
            </div>
        <?php endif; ?>
        
        <!-- Поле загрузки множественных файлов -->
        <div class="relative">
            <input 
                type="file" 
                id="<?php echo htmlspecialchars($id); ?>"
                name="<?php echo htmlspecialchars($opts['name']); ?>[]"
                multiple
                accept="image/*"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100"
                onchange="previewGallery_<?php echo htmlspecialchars($id); ?>(this)"
            >
            <p class="text-xs text-gray-500 mt-1">
                <?php echo __('common.max_files', 'Максимум файлов'); ?>: <?php echo $opts['max_files']; ?>
            </p>
        </div>
        
        <!-- Превью новых изображений -->
        <div id="gallery-preview-<?php echo htmlspecialchars($id); ?>" class="hidden mt-4">
            <p class="text-sm text-gray-600 mb-2"><?php echo __('common.new_gallery_preview', 'Превью новых изображений'); ?>:</p>
            <div id="preview-gallery-<?php echo htmlspecialchars($id); ?>" class="grid grid-cols-4 gap-2"></div>
        </div>
    </div>
    
    <script>
    function previewGallery_<?php echo htmlspecialchars($id); ?>(input) {
        const preview = document.getElementById('gallery-preview-<?php echo htmlspecialchars($id); ?>');
        const previewContainer = document.getElementById('preview-gallery-<?php echo htmlspecialchars($id); ?>');
        
        if (input.files && input.files.length > 0) {
            // НЕ очищаем контейнер, добавляем к существующим изображениям
            let currentIndex = previewContainer.children.length;
            
            Array.from(input.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageWrapper = document.createElement('div');
                    imageWrapper.className = 'relative group';
                    
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-full h-20 object-cover rounded-lg border border-gray-300';
                    
                    const deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.className = 'absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity';
                    deleteButton.innerHTML = '×';
                    deleteButton.onclick = function() {
                        imageWrapper.remove();
                        updateGalleryInput();
                    };
                    
                    imageWrapper.appendChild(img);
                    imageWrapper.appendChild(deleteButton);
                    previewContainer.appendChild(imageWrapper);
                };
                reader.readAsDataURL(file);
            });
            
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }
    
    function updateGalleryInput() {
        // Обновляем скрытое поле с данными о загруженных файлах
        const previewContainer = document.getElementById('preview-gallery-<?php echo htmlspecialchars($id); ?>');
        const images = Array.from(previewContainer.querySelectorAll('img')).map(img => img.src);
        
        // Создаем скрытое поле для передачи данных
        let hiddenInput = document.querySelector('input[name="gallery_preview_data"]');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'gallery_preview_data';
            document.querySelector('form').appendChild(hiddenInput);
        }
        hiddenInput.value = JSON.stringify(images);
    }
    
    function removeGalleryImage_<?php echo htmlspecialchars($id); ?>(index) {
        const currentImages = <?php echo json_encode($current_images); ?>;
        currentImages.splice(index, 1);
        
        // Обновляем скрытое поле
        const hiddenInput = document.querySelector('input[name="current_gallery"]');
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(currentImages);
        }
        
        // Перезагружаем страницу для обновления галереи
        location.reload();
    }
    </script>
    <?php
}

/**
 * ИКОНКИ
 */

/**
 * Получить SVG иконку по имени
 */
function get_icon($name, $class = 'w-5 h-5') {
    $icons = [
        'dashboard' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v4m8-4v4"></path></svg>',
        'services' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H7m5 0v-5a2 2 0 00-2-2H8a2 2 0 00-2 2v5m10 0V9a2 2 0 00-2-2h-4a2 2 0 00-2 2v10"></path></svg>',
        'portfolio' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>',
        'reviews' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>',
        'blog' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>',
        'users' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>',
        'settings' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'statistics' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
        'plus' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>',
        'arrow-right' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>',
        'arrow-left' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>',
        'external-link' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>',
            'logout' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>',
            'portfolio' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>',
            'reviews' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>',
            'blog' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
        'building' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H7m5 0v-5a2 2 0 00-2-2H8a2 2 0 00-2 2v5m10 0V9a2 2 0 00-2-2h-4a2 2 0 00-2 2v10"></path></svg>',
        'cog' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'play' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v14l11-7z"></path></svg>',
        'shield' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
        'wrench' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
        'search' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>',
        'share' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path></svg>',
        'download' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>',
        'clock' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'chart' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
        'image' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>',
        'warning' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>',
        'phone' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>',
        'mail' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>',
        'information-circle' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'question-mark-circle' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
        'globe' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9m0 9c-5 0-9-4-9-9s4-9 9-9"></path></svg>',
        'map' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>',
        'x' => '<svg class="' . $class . '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
    ];
    
    return $icons[$name] ?? $icons['dashboard'];
}

/**
 * УНИВЕРСАЛЬНЫЕ КОМПОНЕНТЫ
 */

/**
 * Универсальная форма фильтрации для админ-панели
 * 
 * @param array $options Массив с настройками формы
 * @return void
 */
function render_filter_form($options = []) {
    $defaults = [
        'action' => '',
        'method' => 'GET',
        'class' => 'grid grid-cols-1 md:grid-cols-5 gap-4 items-end',
        'fields' => [],
        'button_text' => 'Фильтр',
        'button_class' => 'px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition duration-200',
        'search_placeholder' => 'Поиск...',
        'search_name' => 'search',
        'search_value' => ''
    ];
    
    $opts = array_merge($defaults, $options);
    
    // Если поля не переданы, создаем базовую структуру
    if (empty($opts['fields'])) {
        $opts['fields'] = [
            [
                'type' => 'search',
                'name' => $opts['search_name'],
                'placeholder' => $opts['search_placeholder'],
                'value' => $opts['search_value']
            ]
        ];
    }
    
    ?>
    <form method="<?php echo htmlspecialchars($opts['method']); ?>" 
          action="<?php echo htmlspecialchars($opts['action']); ?>" 
          class="<?php echo htmlspecialchars($opts['class']); ?>">
        
        <?php foreach ($opts['fields'] as $field): ?>
            <?php if ($field['type'] === 'search'): ?>
                <!-- Поле поиска -->
                <div class="space-y-2">
                    <label for="<?php echo htmlspecialchars($field['name']); ?>" class="block text-sm font-medium text-gray-700">
                        Поиск
                    </label>
                    <div class="relative">
                        <input 
                            type="text" 
                            name="<?php echo htmlspecialchars($field['name']); ?>" 
                            id="<?php echo htmlspecialchars($field['name']); ?>"
                            value="<?php echo htmlspecialchars($field['value'] ?? ''); ?>"
                            placeholder="<?php echo htmlspecialchars($field['placeholder'] ?? 'Поиск...'); ?>"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition duration-200"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <?php echo get_icon('search', 'w-5 h-5 text-gray-400'); ?>
                        </div>
                    </div>
                </div>
                
            <?php elseif ($field['type'] === 'dropdown'): ?>
                <!-- Dropdown поле -->
                <?php 
                render_dropdown_field([
                    'name' => $field['name'],
                    'label' => $field['label'] ?? '',
                    'value' => $field['value'] ?? '',
                    'options' => $field['options'] ?? [],
                    'placeholder' => $field['placeholder'] ?? 'Выберите опцию',
                    'searchable' => $field['searchable'] ?? false,
                    'class' => $field['class'] ?? 'w-full'
                ]);
                ?>
                
            <?php elseif ($field['type'] === 'hidden'): ?>
                <!-- Скрытое поле -->
                <input 
                    type="hidden" 
                    name="<?php echo htmlspecialchars($field['name']); ?>" 
                    value="<?php echo htmlspecialchars($field['value'] ?? ''); ?>"
                >
                
            <?php elseif ($field['type'] === 'custom'): ?>
                <!-- Кастомное поле -->
                <?php echo $field['content'] ?? ''; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        
        <!-- Кнопка фильтра -->
        <div class="space-y-2">
            <label class="block text-sm font-medium text-gray-700 opacity-0">Фильтр</label>
            <button 
                type="submit" 
                class="w-full px-4 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg shadow-sm hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition duration-200 flex items-center justify-center"
            >
                <?php echo get_icon('search', 'w-4 h-4 mr-2'); ?>
                <span><?php echo htmlspecialchars($opts['button_text']); ?></span>
            </button>
        </div>
    </form>
    <?php
}
?>
