<?php
/**
 * Базовый layout для frontend сайта
 * Baumaster Frontend Layout
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
}

/**
 * Рендеринг HTML head
 */
function render_frontend_head($title = '', $meta_description = '', $active_page = '') {
    $site_title = $title ? $title . ' | Innenausbau & Renovierung Frankfurt | Premium Bauunternehmen' : 'Innenausbau & Renovierung Frankfurt | Premium Bauunternehmen';
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($site_title); ?></title>
    
    <?php if ($meta_description): ?>
    <meta name="description" content="<?php echo htmlspecialchars($meta_description); ?>">
    <?php endif; ?>
    
    <!-- SEO Meta Tags -->
    <meta name="keywords" content="Innenausbau Frankfurt, Renovierung Frankfurt, Malerei Frankfurt, Bodenverlegung Frankfurt, Badezimmer Renovierung Frankfurt">
    <meta name="author" content="Frankfurt Innenausbau">
    <meta name="robots" content="index, follow">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($site_title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($meta_description ?: 'Премиальные внутренние работы во Франкфурте. Полный спектр услуг от профессионалов.'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://baumaster-frankfurt.de">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico">
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'premium-gray': '#F4F4F4',
                        'text-primary': '#1C1C1E',
                        'text-secondary': '#4A5568',
                        'accent-blue': '#2C3E50',
                        'steel-gray': '#5A5A5A'
                    },
                    fontFamily: {
                        'montserrat': ['Montserrat', 'sans-serif'],
                        'roboto': ['Roboto', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <!-- Tailwind Typography CDN -->
    <script src="https://unpkg.com/@tailwindcss/typography@0.5.10/dist/index.js"></script>
    
    <!-- Custom styles for blog content -->
    <style>
        .prose {
            color: #374151;
            max-width: none;
        }
        .prose h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-top: 2rem;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        .prose h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #374151;
        }
        .prose p {
            margin-bottom: 1rem;
            line-height: 1.7;
        }
        .prose ul {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }
        .prose li {
            margin-bottom: 0.5rem;
            list-style-type: disc;
        }
        .prose strong {
            font-weight: 600;
        }
        .prose em {
            font-style: italic;
        }
    </style>
    <?php
}

/**
 * Рендеринг основного layout
 */
function render_frontend_layout($options = []) {
    $defaults = [
        'title' => '',
        'meta_description' => '',
        'active_page' => '',
        'content' => '',
        'body_class' => 'bg-premium-gray font-roboto text-text-primary',
        'show_navigation' => true,
        'show_footer' => true
    ];
    
    $opts = array_merge($defaults, $options);
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <?php render_frontend_head($opts['title'], $opts['meta_description'], $opts['active_page']); ?>
    </head>
    <body class="<?php echo $opts['body_class']; ?>">
        
        <?php if ($opts['show_navigation']): ?>
            <?php render_frontend_navigation($opts['active_page']); ?>
        <?php endif; ?>
        
        <!-- Main Content -->
        <?php echo $opts['content']; ?>
        
        <?php if ($opts['show_footer']): ?>
            <?php render_frontend_footer(); ?>
        <?php endif; ?>
        
        <!-- JavaScript -->
        <?php render_frontend_scripts(); ?>
        
    </body>
    </html>
    <?php
}

/**
 * Рендеринг навигации
 */
function render_frontend_navigation($active_page = '') {
    $menu_items = [
        'home' => ['url' => 'index.php', 'title' => 'Главная', 'anchor' => '#hero'],
        'services' => ['url' => 'services.php', 'title' => 'Услуги', 'anchor' => '#services'],
        'portfolio' => ['url' => 'portfolio.php', 'title' => 'Портфолио', 'anchor' => '#portfolio'],
        'about' => ['url' => 'about.php', 'title' => 'О компании', 'anchor' => '#about'],
        'reviews' => ['url' => 'review.php', 'title' => 'Отзывы', 'anchor' => '#reviews'],
        'blog' => ['url' => 'blog.php', 'title' => 'FAQ', 'anchor' => '#faq'],
        'contact' => ['url' => 'contact.php', 'title' => 'Контакты', 'anchor' => '#contact']
    ];
    ?>
    <!-- Navigation -->
    <nav class="bg-white shadow-sm fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="font-montserrat font-semibold text-xl text-text-primary">
                    Frankfurt Innenausbau
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex space-x-8">
                    <?php foreach ($menu_items as $page => $item): ?>
                        <?php $is_active = $active_page === $page; ?>
                        <a href="<?php echo $item['url']; ?>" class="<?php echo $is_active ? 'text-accent-blue font-medium' : 'text-text-secondary hover:text-accent-blue'; ?> transition-colors">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Desktop Call Button -->
                <button class="hidden lg:block bg-accent-blue text-white px-4 py-2 rounded hover:bg-opacity-90 transition-colors" onclick="openCallModal()">
                    Позвонить
                </button>

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" class="lg:hidden p-2 rounded-md text-text-secondary hover:text-accent-blue focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobile-menu" class="lg:hidden hidden bg-white border-t border-gray-200 shadow-lg">
            <div class="px-4 py-2 space-y-1">
                <?php foreach ($menu_items as $page => $item): ?>
                    <a href="<?php echo $item['url']; ?>" class="block px-3 py-2 text-text-secondary hover:text-accent-blue hover:bg-gray-50 rounded-md transition-colors mobile-menu-link">
                        <?php echo htmlspecialchars($item['title']); ?>
                    </a>
                <?php endforeach; ?>
                <div class="pt-2 pb-1">
                    <button class="w-full bg-accent-blue text-white px-4 py-2 rounded hover:bg-opacity-90 transition-colors" onclick="openCallModal()">
                        Позвонить
                    </button>
                </div>
            </div>
        </div>
    </nav>
    <?php
}

/**
 * Рендеринг footer
 */
function render_frontend_footer() {
    ?>
    <!-- Footer -->
    <footer class="bg-text-primary text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="md:col-span-2">
                    <div class="font-montserrat font-semibold text-2xl mb-4">
                        Frankfurt Innenausbau
                    </div>
                    <p class="text-gray-300 mb-6 leading-relaxed">
                        Профессиональные внутренние работы во Франкфурте. Превращаем ваши идеи в реальность с премиальным качеством и вниманием к деталям.
                    </p>
                    <div class="flex space-x-4">
                        <button class="bg-accent-blue text-white p-3 rounded-full hover:bg-opacity-80 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </button>
                        <button class="bg-accent-blue text-white p-3 rounded-full hover:bg-opacity-80 transition-colors">
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Services -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Услуги</h3>
                    <ul class="space-y-2 text-gray-300">
                        <li><a href="services.php" class="hover:text-white transition-colors">Малярные работы</a></li>
                        <li><a href="services.php" class="hover:text-white transition-colors">Укладка полов</a></li>
                        <li><a href="services.php" class="hover:text-white transition-colors">Ремонт ванных</a></li>
                        <li><a href="services.php" class="hover:text-white transition-colors">Гипсокартон</a></li>
                        <li><a href="services.php" class="hover:text-white transition-colors">Плитка</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="font-semibold text-lg mb-4">Контакты</h3>
                    <div class="space-y-2 text-gray-300">
                        <div class="flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>+49 (0) 69 123 456 78</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>info@baumaster-frankfurt.de</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <svg class="h-5 w-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Frankfurt am Main,<br>Deutschland</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm">
                        © 2024 Frankfurt Innenausbau. Все права защищены.
                    </p>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Политика конфиденциальности</a>
                        <a href="#" class="text-gray-400 hover:text-white text-sm transition-colors">Условия использования</a>
                        <a href="admin/login.php" class="text-gray-400 hover:text-white text-sm transition-colors">Вход</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <?php
}

/**
 * Рендеринг JavaScript
 */
function render_frontend_scripts() {
    ?>
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Mobile menu links
        document.querySelectorAll('.mobile-menu-link').forEach(link => {
            link.addEventListener('click', function() {
                document.getElementById('mobile-menu').classList.add('hidden');
            });
        });

        // Call modal functionality
        function openCallModal() {
            alert('Телефон: +49 (0) 69 123 456 78\nEmail: info@baumaster-frankfurt.de');
        }

        // Form validation
        function validateForm(form) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });

            return isValid;
        }

        // Add form submission handlers
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!validateForm(this)) {
                    e.preventDefault();
                    alert('Пожалуйста, заполните все обязательные поля');
                }
            });
        });
    </script>
    <?php
}
?>

