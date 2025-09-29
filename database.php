<?php
/**
 * Система работы с базой данных для Baumaster
 * Поддержка SQLite и JSON файлов
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

class Database {
    private $pdo = null;
    private $use_json = false;
    private $json_path;
    
    public function __construct() {
        $this->json_path = DATA_PATH;
        $this->init_database();
    }
    
    /**
     * Инициализация подключения к БД
     */
    private function init_database() {
        if (DB_TYPE === 'sqlite') {
            $this->init_sqlite();
        } else {
            $this->use_json = true;
            $this->init_json_storage();
        }
    }
    
    /**
     * Инициализация SQLite
     */
    private function init_sqlite() {
        if (!is_dir(dirname(DB_PATH))) {
            mkdir(dirname(DB_PATH), 0755, true);
        }
        
        $this->pdo = new PDO('sqlite:' . DB_PATH);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Создание таблиц при первом запуске
        try {
            $this->create_tables();
        } catch (Exception $e) {
            write_log("Error creating tables: " . $e->getMessage(), 'ERROR');
            // Продолжаем работу, таблицы могут уже существовать
        }
    }
    
    /**
     * Инициализация JSON хранилища
     */
    private function init_json_storage() {
        if (!is_dir($this->json_path)) {
            mkdir($this->json_path, 0755, true);
        }
        
        $this->create_json_files();
    }
    
    /**
     * Создание таблиц SQLite
     */
    private function create_tables() {
        $tables = [
            'users' => "
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    username VARCHAR(50) UNIQUE NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    role VARCHAR(20) DEFAULT 'editor',
                    status VARCHAR(20) DEFAULT 'active',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            
            'services' => "
                CREATE TABLE IF NOT EXISTS services (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    price DECIMAL(10,2) DEFAULT 0,
                    price_type VARCHAR(20) DEFAULT 'fixed',
                    image VARCHAR(255),
                    gallery TEXT,
                    features TEXT,
                    meta_title VARCHAR(255),
                    meta_description TEXT,
                    keywords TEXT,
                    status VARCHAR(20) DEFAULT 'active',
                    priority INTEGER DEFAULT 0,
                    category VARCHAR(50) DEFAULT 'general',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            
            'portfolio' => "
                CREATE TABLE IF NOT EXISTS portfolio (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    category VARCHAR(100) NOT NULL,
                    completion_date DATE,
                    area VARCHAR(50),
                    duration VARCHAR(50),
                    budget DECIMAL(10,2),
                    client_name VARCHAR(255),
                    location VARCHAR(255),
                    featured_image VARCHAR(255),
                    gallery TEXT, -- JSON array of images
                    technical_info TEXT, -- JSON object with technical details
                    before_after TEXT, -- JSON array with before/after images
                    tags TEXT, -- JSON array of tags
                    status VARCHAR(20) DEFAULT 'active',
                    featured INTEGER DEFAULT 0,
                    sort_order INTEGER DEFAULT 0,
                    meta_title VARCHAR(255),
                    meta_description TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            
            'reviews' => "
                CREATE TABLE IF NOT EXISTS reviews (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    client_name VARCHAR(255) NOT NULL,
                    client_email VARCHAR(255),
                    client_phone VARCHAR(50),
                    client_photo VARCHAR(255),
                    review_text TEXT NOT NULL,
                    rating INTEGER DEFAULT 5,
                    project_id INTEGER,
                    service_id INTEGER,
                    status VARCHAR(20) DEFAULT 'pending',
                    review_date DATE DEFAULT CURRENT_DATE,
                    verified INTEGER DEFAULT 0,
                    featured INTEGER DEFAULT 0,
                    sort_order INTEGER DEFAULT 0,
                    admin_notes TEXT,
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            
            'faq' => "
                CREATE TABLE IF NOT EXISTS faq (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    question TEXT NOT NULL,
                    answer TEXT NOT NULL,
                    category VARCHAR(50) DEFAULT 'general',
                    status VARCHAR(20) DEFAULT 'active',
                    sort_order INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            
            'blog_posts' => "
                CREATE TABLE IF NOT EXISTS blog_posts (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title VARCHAR(255) NOT NULL,
                    slug VARCHAR(255) UNIQUE NOT NULL,
                    excerpt TEXT,
                    content TEXT NOT NULL,
                    category VARCHAR(100) NOT NULL,
                    tags TEXT, -- JSON array
                    featured_image VARCHAR(255),
                    meta_title VARCHAR(255),
                    meta_description TEXT,
                    keywords TEXT,
                    status VARCHAR(20) DEFAULT 'draft',
                    post_type VARCHAR(20) DEFAULT 'article', -- article, faq, news, tips
                    author_id INTEGER,
                    views INTEGER DEFAULT 0,
                    featured INTEGER DEFAULT 0,
                    sort_order INTEGER DEFAULT 0,
                    published_at DATETIME,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            
            'activity_log' => "
                CREATE TABLE IF NOT EXISTS activity_log (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER,
                    action VARCHAR(100) NOT NULL,
                    table_name VARCHAR(50),
                    record_id INTEGER,
                    old_values TEXT, -- JSON
                    new_values TEXT, -- JSON
                    ip_address VARCHAR(45),
                    user_agent TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            
            'settings' => "
                CREATE TABLE IF NOT EXISTS settings (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    setting_key VARCHAR(100) UNIQUE NOT NULL,
                    setting_value TEXT,
                    setting_type VARCHAR(20) DEFAULT 'text',
                    category VARCHAR(50) DEFAULT 'general',
                    description TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )
            ",
            
            'translation_cache' => "
                CREATE TABLE IF NOT EXISTS translation_cache (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    source_text TEXT NOT NULL,
                    translated_text TEXT NOT NULL,
                    source_lang VARCHAR(5) NOT NULL,
                    target_lang VARCHAR(5) NOT NULL,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(source_text, source_lang, target_lang)
                )
            ",
            
            'translations' => "
                CREATE TABLE IF NOT EXISTS translations (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    source_table VARCHAR(50) NOT NULL,
                    source_id INTEGER NOT NULL,
                    source_field VARCHAR(50) NOT NULL,
                    source_lang VARCHAR(5) NOT NULL,
                    target_lang VARCHAR(5) NOT NULL,
                    source_text TEXT NOT NULL,
                    translated_text TEXT NOT NULL,
                    translation_service VARCHAR(50) DEFAULT 'libretranslate',
                    confidence DECIMAL(3,2) DEFAULT 0.95,
                    auto_translated INTEGER DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    UNIQUE(source_table, source_id, source_field, target_lang)
                )
            "
        ];
        
        foreach ($tables as $name => $sql) {
            try {
                $this->pdo->exec($sql);
            } catch (PDOException $e) {
                write_log("Error creating table $name: " . $e->getMessage(), 'ERROR');
            }
        }
        
        // Создание индексов для оптимизации
        $this->create_indexes();
        
        // Создание администратора по умолчанию
        $this->create_default_admin();
        
        // Создание демо-данных
        $this->create_demo_data();
        
        // Создание настроек по умолчанию
        $this->create_default_settings();
    }
    
    /**
     * Создание индексов для оптимизации
     */
    private function create_indexes() {
        $indexes = [
            'idx_translation_cache_lookup' => 'CREATE INDEX IF NOT EXISTS idx_translation_cache_lookup ON translation_cache(source_text, source_lang, target_lang)',
            'idx_translations_lookup' => 'CREATE INDEX IF NOT EXISTS idx_translations_lookup ON translations(source_table, source_id, target_lang)',
            'idx_portfolio_status' => 'CREATE INDEX IF NOT EXISTS idx_portfolio_status ON portfolio(status, featured)',
            'idx_blog_posts_status' => 'CREATE INDEX IF NOT EXISTS idx_blog_posts_status ON blog_posts(status, published_at)'
        ];
        
        foreach ($indexes as $name => $sql) {
            try {
                $this->pdo->exec($sql);
            } catch (PDOException $e) {
                write_log("Error creating index $name: " . $e->getMessage(), 'ERROR');
            }
        }
    }
    
    /**
     * Создание JSON файлов для хранения данных
     */
    private function create_json_files() {
        $files = [
            'users.json' => [],
            'services.json' => [],
            'portfolio.json' => [],
            'reviews.json' => [],
            'faq.json' => [],
            'blog_posts.json' => [],
            'settings.json' => [],
            'activity_log.json' => []
        ];
        
        foreach ($files as $filename => $default_data) {
            $filepath = $this->json_path . $filename;
            if (!file_exists($filepath)) {
                write_json_file($filepath, $default_data);
            }
        }
        
        // Создание администратора по умолчанию
        $this->create_default_admin();
    }
    
    /**
     * Создание администратора по умолчанию
     */
    private function create_default_admin() {
        $admin_exists = $this->select('users', ['username' => 'root']);

        if (empty($admin_exists)) {
            $this->insert('users', [
                'username' => 'root',
                'email' => 'root@baumaster.de',
                'password' => hash_password('root'),
                'role' => 'admin',
                'status' => 'active'
            ]);
        }
    }
    
    
    /**
     * Создание демо-данных
     */
    private function create_demo_data() {
        // Создание демо-услуг
        $existing_services = $this->select('services');
        
        if (empty($existing_services)) {
            $demo_services = [
                [
                    'title' => 'Малярные работы',
                    'description' => 'Профессиональная покраска стен, потолков и декоративных элементов. Используем только качественные материалы и современные технологии нанесения. Подготовка поверхности, грунтовка, шпаклевка и финишное покрытие.',
                    'price' => 25.0,
                    'price_type' => 'per_m2',
                    'category' => 'painting',
                    'priority' => 100,
                    'features' => json_encode(['Подготовка поверхности', 'Грунтовка и шпаклевка', 'Покраска в 2-3 слоя', 'Декоративные покрытия', 'Гарантия 3 года']),
                    'meta_title' => 'Малярные работы во Франкфурте | Профессиональная покраска',
                    'meta_description' => 'Качественные малярные работы в Франкфурте. Покраска стен, потолков, декоративные покрытия. Гарантия качества и доступные цены.',
                    'keywords' => 'малярные работы Франкфурт, покраска стен, покраска потолков, декоративные покрытия'
                ],
                [
                    'title' => 'Укладка полов',
                    'description' => 'Укладка ламината, паркета, линолеума и других напольных покрытий. Профессиональная подготовка основания, качественная укладка и финишная отделка. Работаем с любыми типами покрытий.',
                    'price' => 35.0,
                    'price_type' => 'per_m2',
                    'category' => 'flooring',
                    'priority' => 90,
                    'features' => json_encode(['Демонтаж старого покрытия', 'Выравнивание основания', 'Укладка подложки', 'Монтаж покрытия', 'Установка плинтусов']),
                    'meta_title' => 'Укладка полов в Франкфурте | Ламинат, паркет, линолеум',
                    'meta_description' => 'Профессиональная укладка полов в Франкфурте. Ламинат, паркет, линолеум. Качественная подготовка основания и гарантия на работы.',
                    'keywords' => 'укладка полов Франкфурт, ламинат, паркет, линолеум, напольные покрытия'
                ],
                [
                    'title' => 'Ремонт ванных комнат',
                    'description' => 'Полный ремонт ванных комнат под ключ. От планировки и демонтажа до финишной отделки. Гидроизоляция, укладка плитки, установка сантехники. Современные решения и качественные материалы.',
                    'price' => 150.0,
                    'price_type' => 'per_m2',
                    'category' => 'bathroom',
                    'priority' => 80,
                    'features' => json_encode(['Демонтаж и подготовка', 'Замена сантехники', 'Гидроизоляция', 'Укладка плитки', 'Установка оборудования']),
                    'meta_title' => 'Ремонт ванных комнат во Франкфурте | Под ключ',
                    'meta_description' => 'Полный ремонт ванных комнат в Франкфурте под ключ. Гидроизоляция, плитка, сантехника. Современный дизайн и качественное исполнение.',
                    'keywords' => 'ремонт ванной Франкфурт, ремонт санузла, укладка плитки в ванной, гидроизоляция'
                ],
                [
                    'title' => 'Работы с гипсокартоном',
                    'description' => 'Монтаж перегородок, потолков и декоративных элементов из гипсокартона. Создаем любые архитектурные формы. Качественная установка каркаса, монтаж листов ГКЛ и финишная отделка.',
                    'price' => 30.0,
                    'price_type' => 'per_m2',
                    'category' => 'drywall',
                    'priority' => 70,
                    'features' => json_encode(['Каркасные конструкции', 'Монтаж листов ГКЛ', 'Заделка швов', 'Шпаклевка под покраску', 'Декоративные элементы']),
                    'meta_title' => 'Работы с гипсокартоном во Франкфурте | Перегородки, потолки',
                    'meta_description' => 'Профессиональные работы с гипсокартоном в Франкфурте. Перегородки, потолки, декоративные элементы. Качественный монтаж и отделка.',
                    'keywords' => 'гипсокартон Франкфурт, перегородки из гипсокартона, потолки из ГКЛ, монтаж гипсокартона'
                ],
                [
                    'title' => 'Укладка плитки',
                    'description' => 'Укладка керамической плитки, керамогранита и мозаики. Работаем с любыми сложными формами и дизайнерскими решениями. Качественная подготовка поверхности и профессиональная укладка.',
                    'price' => 40.0,
                    'price_type' => 'per_m2',
                    'category' => 'tiling',
                    'priority' => 60,
                    'features' => json_encode(['Подготовка поверхности', 'Разметка и планировка', 'Укладка на клей', 'Затирка швов', 'Финишная обработка']),
                    'meta_title' => 'Укладка плитки во Франкфурте | Керамогранит, мозаика',
                    'meta_description' => 'Профессиональная укладка плитки в Франкфурте. Керамическая плитка, керамогранит, мозаика. Сложные формы и дизайнерские решения.',
                    'keywords' => 'укладка плитки Франкфурт, керамогранит, мозаика, плиточные работы'
                ],
                [
                    'title' => 'Комплексный ремонт',
                    'description' => 'Полный ремонт квартир и домов под ключ. От планировки и черновых работ до финишной отделки. Управляем всем процессом, контролируем качество и соблюдаем сроки.',
                    'price' => 500.0,
                    'price_type' => 'per_m2',
                    'category' => 'renovation',
                    'priority' => 50,
                    'features' => json_encode(['Планировка и дизайн', 'Все виды работ', 'Поставка материалов', 'Контроль качества', 'Сдача под ключ']),
                    'meta_title' => 'Комплексный ремонт квартир во Франкфурте | Под ключ',
                    'meta_description' => 'Полный ремонт квартир и домов в Франкфурте под ключ. Планировка, все виды работ, контроль качества. Профессиональная команда.',
                    'keywords' => 'комплексный ремонт Франкфурт, ремонт квартир под ключ, капитальный ремонт, евроремонт'
                ]
            ];
            
            foreach ($demo_services as $service) {
                $this->insert('services', $service);
            }
        }
        
        // Создание демо-портфолио
        $existing_portfolio = $this->select('portfolio');
        
        if (empty($existing_portfolio)) {
            $demo_portfolio = [
                [
                    'title' => 'Квартира в центре Франкфурта',
                    'description' => 'Полная реконструкция трёхкомнатной квартиры площадью 85 м² с современным дизайном. Выполнены все виды внутренних работ: демонтаж, электрика, сантехника, отделка. Использованы качественные материалы европейских производителей.',
                    'category' => 'apartment',
                    'completion_date' => date('Y-m-d', strtotime('-2 months')),
                    'area' => '85 м²',
                    'duration' => '6 недель',
                    'budget' => 45000.00,
                    'client_name' => 'Семья Мюллер',
                    'location' => 'Frankfurt-Zentrum',
                    'gallery' => json_encode(['/assets/images/portfolio/apartment-1-1.jpg', '/assets/images/portfolio/apartment-1-2.jpg', '/assets/images/portfolio/apartment-1-3.jpg']),
                    'technical_info' => json_encode(['rooms' => 3, 'bathrooms' => 2, 'year' => 2024, 'style' => 'modern']),
                    'before_after' => json_encode(['before' => '/assets/images/portfolio/apartment-1-before.jpg', 'after' => '/assets/images/portfolio/apartment-1-after.jpg']),
                    'tags' => json_encode(['комплексный ремонт', 'квартира', 'современный стиль']),
                    'featured' => 1,
                    'sort_order' => 100,
                    'meta_title' => 'Ремонт квартиры в центре Франкфурта | Современный дизайн',
                    'meta_description' => 'Полный ремонт трёхкомнатной квартиры в Франкфурте. Современный дизайн, качественные материалы, выполнено за 6 недель.'
                ],
                [
                    'title' => 'Ванная комната премиум-класса',
                    'description' => 'Роскошная ванная комната с использованием натурального мрамора и современной сантехники премиум-класса. Выполнена полная гидроизоляция, установлена система тёплых полов, современное LED-освещение.',
                    'category' => 'bathroom',
                    'completion_date' => date('Y-m-d', strtotime('-1 month')),
                    'area' => '12 м²',
                    'duration' => '3 недели',
                    'budget' => 18000.00,
                    'client_name' => 'Г-н Шмидт',
                    'location' => 'Frankfurt-Westend',
                    'gallery' => json_encode(['/assets/images/portfolio/bathroom-1-1.jpg', '/assets/images/portfolio/bathroom-1-2.jpg']),
                    'technical_info' => json_encode(['features' => ['heated floors', 'marble', 'LED lighting', 'premium fixtures']]),
                    'before_after' => json_encode(['before' => '/assets/images/portfolio/bathroom-1-before.jpg', 'after' => '/assets/images/portfolio/bathroom-1-after.jpg']),
                    'tags' => json_encode(['ванная', 'премиум', 'мрамор', 'тёплые полы']),
                    'featured' => 1,
                    'sort_order' => 90,
                    'meta_title' => 'Ванная комната премиум-класса во Франкфурте',
                    'meta_description' => 'Роскошная ванная комната с натуральным мрамором и современной сантехникой. Премиальное качество исполнения.'
                ],
                [
                    'title' => 'Офис в деловом центре',
                    'description' => 'Современный офис с открытой планировкой для IT-компании. Создана комфортная рабочая среда с использованием экологически чистых материалов. Установлены системы кондиционирования и современного освещения.',
                    'category' => 'office',
                    'completion_date' => date('Y-m-d', strtotime('-3 weeks')),
                    'area' => '150 м²',
                    'duration' => '4 недели',
                    'budget' => 35000.00,
                    'client_name' => 'TechCorp GmbH',
                    'location' => 'Frankfurt Business District',
                    'gallery' => json_encode(['/assets/images/portfolio/office-1-1.jpg', '/assets/images/portfolio/office-1-2.jpg', '/assets/images/portfolio/office-1-3.jpg']),
                    'technical_info' => json_encode(['workspaces' => 25, 'meeting_rooms' => 3, 'eco_materials' => true]),
                    'before_after' => json_encode(['before' => '/assets/images/portfolio/office-1-before.jpg', 'after' => '/assets/images/portfolio/office-1-after.jpg']),
                    'tags' => json_encode(['офис', 'открытая планировка', 'IT', 'эко-материалы']),
                    'featured' => 0,
                    'sort_order' => 80,
                    'meta_title' => 'Ремонт офиса в деловом центре Франкфурта',
                    'meta_description' => 'Современный офис с открытой планировкой для IT-компании. Комфортная рабочая среда с эко-материалами.'
                ]
            ];
            
            foreach ($demo_portfolio as $project) {
                $this->insert('portfolio', $project);
            }
        }
        
        // Создание демо-отзывов
        $existing_reviews = $this->select('reviews');
        
        if (empty($existing_reviews)) {
            $demo_reviews = [
                [
                    'client_name' => 'Анна Мюллер',
                    'client_email' => 'anna.mueller@email.de',
                    'client_phone' => '+49 176 12345678',
                    'client_photo' => '/assets/images/reviews/anna-mueller.jpg',
                    'review_text' => 'Очень довольна работой команды! Сделали ремонт ванной комнаты быстро и качественно, учли все мои пожелания. Особенно понравилось, что мастера были аккуратными и убрали за собой. Рекомендую эту компанию!',
                    'rating' => 5,
                    'project_id' => 1,
                    'status' => 'published',
                    'review_date' => date('Y-m-d', strtotime('-2 months')),
                    'verified' => 1,
                    'featured' => 1,
                    'sort_order' => 100,
                    'admin_notes' => 'Отличный отзыв от довольного клиента. Проект выполнен в срок.'
                ],
                [
                    'client_name' => 'Петер Шмидт',
                    'client_email' => 'peter.schmidt@email.de',
                    'client_phone' => '+49 176 23456789',
                    'client_photo' => '/assets/images/reviews/peter-schmidt.jpg',
                    'review_text' => 'Заказывал укладку ламината в гостиной. Все выполнено профессионально, без нареканий. Мастера приехали вовремя, работали аккуратно. Результат превзошел ожидания!',
                    'rating' => 5,
                    'project_id' => 2,
                    'status' => 'published',
                    'review_date' => date('Y-m-d', strtotime('-1 month')),
                    'verified' => 1,
                    'featured' => 1,
                    'sort_order' => 90,
                    'admin_notes' => 'Клиент очень доволен качеством работ.'
                ],
                [
                    'client_name' => 'Елена Ковальчук',
                    'client_email' => 'elena.kovalchuk@email.de',
                    'client_phone' => '+49 176 34567890',
                    'client_photo' => '/assets/images/reviews/elena-kovalchuk.jpg',
                    'review_text' => 'Отличная компания! Помогли с дизайном и реализовали проект ванной комнаты мечты. Использовали качественные материалы, работали быстро. Теперь у меня красивая современная ванная!',
                    'rating' => 5,
                    'project_id' => 2,
                    'status' => 'published',
                    'review_date' => date('Y-m-d', strtotime('-3 weeks')),
                    'verified' => 1,
                    'featured' => 0,
                    'sort_order' => 80,
                    'admin_notes' => 'Клиентка довольна дизайном и качеством.'
                ],
                [
                    'client_name' => 'Макс Рихтер',
                    'client_email' => 'max.richter@email.de',
                    'client_phone' => '+49 176 45678901',
                    'client_photo' => '/assets/images/reviews/max-richter.jpg',
                    'review_text' => 'Быстро и аккуратно покрасили стены в офисе. Результат превзошел ожидания. Цены адекватные, качество отличное. Буду обращаться еще!',
                    'rating' => 4,
                    'project_id' => 3,
                    'status' => 'published',
                    'review_date' => date('Y-m-d', strtotime('-2 weeks')),
                    'verified' => 1,
                    'featured' => 0,
                    'sort_order' => 70,
                    'admin_notes' => 'Офисный проект выполнен качественно.'
                ],
                [
                    'client_name' => 'Мария Гонсалес',
                    'client_email' => 'maria.gonzalez@email.de',
                    'client_phone' => '+49 176 56789012',
                    'client_photo' => '/assets/images/reviews/maria-gonzalez.jpg',
                    'review_text' => 'Заказывала комплексный ремонт квартиры. Команда работала профессионально, соблюдали сроки. Единственное - немного задержались с доставкой материалов, но в итоге все сделали отлично.',
                    'rating' => 4,
                    'project_id' => 1,
                    'status' => 'published',
                    'review_date' => date('Y-m-d', strtotime('-1 week')),
                    'verified' => 0,
                    'featured' => 0,
                    'sort_order' => 60,
                    'admin_notes' => 'Клиентка довольна результатом, но были задержки с материалами.'
                ],
                [
                    'client_name' => 'Томас Вебер',
                    'client_email' => 'thomas.weber@email.de',
                    'client_phone' => '+49 176 67890123',
                    'client_photo' => '/assets/images/reviews/thomas-weber.jpg',
                    'review_text' => 'Отличная работа по укладке плитки в кухне. Мастера очень аккуратные, плитка легла идеально. Рекомендую эту компанию всем знакомым!',
                    'rating' => 5,
                    'project_id' => 1,
                    'status' => 'pending',
                    'review_date' => date('Y-m-d', strtotime('-3 days')),
                    'verified' => 0,
                    'featured' => 0,
                    'sort_order' => 50,
                    'admin_notes' => 'Новый отзыв, требует модерации.'
                ]
            ];
            
            foreach ($demo_reviews as $review) {
                $this->insert('reviews', $review);
            }
        }
        
        // Создание демо-статей блога
        $existing_blog_posts = $this->select('blog_posts');
        
        if (empty($existing_blog_posts)) {
            $demo_blog_posts = [
                [
                    'title' => 'Как выбрать правильные материалы для ремонта ванной комнаты',
                    'slug' => 'kak-vybrat-pravilnye-materialy-dlya-remonta-vannoy-komnaty',
                    'excerpt' => 'Подробное руководство по выбору материалов для ремонта ванной комнаты. Советы экспертов по плитке, сантехнике и гидроизоляции.',
                    'content' => '<h2>Введение</h2><p>Ремонт ванной комнаты — это серьезное мероприятие, которое требует тщательного подхода к выбору материалов. От правильного выбора зависит не только внешний вид, но и долговечность ремонта.</p><h2>Основные материалы</h2><h3>Плитка</h3><p>При выборе плитки для ванной комнаты важно учитывать:</p><ul><li>Влагостойкость</li><li>Износостойкость</li><li>Размер и дизайн</li><li>Качество клея</li></ul><h3>Сантехника</h3><p>Современная сантехника должна быть не только красивой, но и функциональной. Обратите внимание на:</p><ul><li>Качество смесителей</li><li>Надежность душевой кабины</li><li>Экономичность унитаза</li></ul><h2>Заключение</h2><p>Правильный выбор материалов — залог успешного ремонта ванной комнаты. Не экономьте на качестве, и результат превзойдет ваши ожидания.</p>',
                    'category' => 'tips',
                    'tags' => json_encode(['ремонт ванной', 'материалы', 'плитка', 'сантехника', 'советы']),
                    'featured_image' => '/assets/images/blog/bathroom-materials.jpg',
                    'meta_title' => 'Как выбрать материалы для ремонта ванной | Советы экспертов',
                    'meta_description' => 'Подробное руководство по выбору материалов для ремонта ванной комнаты. Советы экспертов по плитке, сантехнике и гидроизоляции.',
                    'keywords' => 'ремонт ванной, материалы, плитка, сантехника, гидроизоляция',
                    'status' => 'published',
                    'post_type' => 'tips',
                    'author_id' => 1,
                    'views' => 245,
                    'featured' => 1,
                    'sort_order' => 100,
                    'published_at' => date('Y-m-d H:i:s', strtotime('-1 week'))
                ],
                [
                    'title' => 'Часто задаваемые вопросы о внутренней отделке',
                    'slug' => 'chasto-zadavaemye-voprosy-o-vnutrenney-otdelke',
                    'excerpt' => 'Ответы на самые популярные вопросы клиентов о внутренней отделке, ремонте и выборе материалов.',
                    'content' => '<h2>Сколько времени занимает ремонт квартиры?</h2><p>Сроки ремонта зависят от объема работ. Стандартная однокомнатная квартира ремонтируется 4-6 недель, двухкомнатная — 6-8 недель, трехкомнатная — 8-12 недель.</p><h2>Какие материалы лучше использовать?</h2><p>Мы рекомендуем использовать только качественные материалы от проверенных производителей. Это гарантирует долговечность ремонта и его презентабельный вид.</p><h2>Предоставляете ли вы гарантию?</h2><p>Да, мы предоставляем гарантию на все виды работ согласно немецкому законодательству. Гарантийный срок составляет от 2 до 5 лет в зависимости от типа работ.</p><h2>Можно ли жить в квартире во время ремонта?</h2><p>Это зависит от объема работ. При косметическом ремонте можно, но при капитальном ремонте лучше временно переехать для вашего комфорта и безопасности.</p>',
                    'category' => 'faq',
                    'tags' => json_encode(['FAQ', 'вопросы', 'ремонт', 'отделка', 'гарантия']),
                    'featured_image' => '/assets/images/blog/faq-renovation.jpg',
                    'meta_title' => 'FAQ по внутренней отделке | Ответы на вопросы клиентов',
                    'meta_description' => 'Ответы на самые популярные вопросы о внутренней отделке, ремонте квартир и выборе материалов в Франкфурте.',
                    'keywords' => 'FAQ, вопросы, ремонт, отделка, гарантия, Франкфурт',
                    'status' => 'published',
                    'post_type' => 'faq',
                    'author_id' => 1,
                    'views' => 189,
                    'featured' => 1,
                    'sort_order' => 90,
                    'published_at' => date('Y-m-d H:i:s', strtotime('-2 weeks'))
                ],
                [
                    'title' => 'Новые тренды в дизайне интерьера 2024',
                    'slug' => 'novye-trendy-v-dizayne-interera-2024',
                    'excerpt' => 'Обзор актуальных трендов в дизайне интерьера на 2024 год. Современные решения для вашего дома.',
                    'content' => '<h2>Эко-материалы</h2><p>В 2024 году особое внимание уделяется экологически чистым материалам. Дерево, натуральный камень, переработанные материалы становятся все более популярными.</p><h2>Минимализм с акцентами</h2><p>Чистые линии и минималистичный дизайн дополняются яркими акцентами. Один яркий элемент может преобразить всю комнату.</p><h2>Умный дом</h2><p>Интеграция современных технологий в дизайн интерьера. Умное освещение, автоматические системы, энергоэффективные решения.</p><h2>Текстуры и фактуры</h2><p>Игра с различными текстурами — от грубого бетона до мягкого бархата. Смешение материалов создает интересные визуальные эффекты.</p>',
                    'category' => 'news',
                    'tags' => json_encode(['дизайн', 'тренды', '2024', 'интерьер', 'мода']),
                    'featured_image' => '/assets/images/blog/interior-trends-2024.jpg',
                    'meta_title' => 'Тренды дизайна интерьера 2024 | Современные решения',
                    'meta_description' => 'Обзор актуальных трендов в дизайне интерьера на 2024 год. Эко-материалы, минимализм, умный дом.',
                    'keywords' => 'дизайн интерьера, тренды 2024, эко-материалы, минимализм, умный дом',
                    'status' => 'published',
                    'post_type' => 'article',
                    'author_id' => 1,
                    'views' => 156,
                    'featured' => 0,
                    'sort_order' => 80,
                    'published_at' => date('Y-m-d H:i:s', strtotime('-3 weeks'))
                ],
                [
                    'title' => 'Как подготовить квартиру к ремонту',
                    'slug' => 'kak-podgotovit-kvartiru-k-remontu',
                    'excerpt' => 'Пошаговая инструкция по подготовке квартиры к ремонту. Что нужно сделать до начала работ.',
                    'content' => '<h2>Планирование</h2><p>Перед началом ремонта необходимо составить детальный план работ и определить последовательность этапов.</p><h2>Освобождение помещения</h2><p>Вынесите всю мебель и личные вещи. Оставьте только то, что планируете оставить в комнате.</p><h2>Защита поверхностей</h2><p>Накройте полы и мебель защитными материалами. Это поможет сохранить их в целости во время ремонта.</p><h2>Коммуникации</h2><p>Отключите электричество и воду в рабочей зоне. Убедитесь, что все коммуникации в порядке.</p>',
                    'category' => 'tips',
                    'tags' => json_encode(['подготовка', 'ремонт', 'инструкция', 'советы', 'планирование']),
                    'featured_image' => '/assets/images/blog/prepare-apartment.jpg',
                    'meta_title' => 'Как подготовить квартиру к ремонту | Пошаговая инструкция',
                    'meta_description' => 'Подробная инструкция по подготовке квартиры к ремонту. Планирование, освобождение помещения, защита поверхностей.',
                    'keywords' => 'подготовка к ремонту, планирование, защита поверхностей, коммуникации',
                    'status' => 'draft',
                    'post_type' => 'tips',
                    'author_id' => 1,
                    'views' => 0,
                    'featured' => 0,
                    'sort_order' => 70,
                    'published_at' => null
                ]
            ];
            
            foreach ($demo_blog_posts as $post) {
                $this->insert('blog_posts', $post);
            }
        }
    }
    
    /**
     * Создание настроек по умолчанию
     */
    private function create_default_settings() {
        $existing_settings = $this->select('settings');
        
        if (empty($existing_settings)) {
            $default_settings = [
                // Основная информация компании
                ['setting_key' => 'company_name', 'setting_value' => 'Baumaster Frankfurt', 'category' => 'company', 'description' => 'Название компании'],
                ['setting_key' => 'company_description', 'setting_value' => 'Профессиональные строительные и ремонтные услуги во Франкфурте-на-Майне', 'category' => 'company', 'description' => 'Описание компании'],
                ['setting_key' => 'company_address', 'setting_value' => 'Frankfurt am Main, Deutschland', 'category' => 'company', 'description' => 'Адрес компании'],
                ['setting_key' => 'company_phone', 'setting_value' => '+49 (0) 69 123-456-789', 'category' => 'company', 'description' => 'Телефон компании'],
                ['setting_key' => 'company_email', 'setting_value' => 'info@baumaster-frankfurt.de', 'category' => 'company', 'description' => 'Email компании'],
                
                // SEO настройки
                ['setting_key' => 'site_title', 'setting_value' => 'Baumaster Frankfurt - Строительные услуги во Франкфурте', 'category' => 'seo', 'description' => 'Заголовок сайта'],
                ['setting_key' => 'site_description', 'setting_value' => 'Профессиональные строительные и ремонтные услуги во Франкфурте. Ремонт квартир, домов, офисов. Качество, надежность, европейские стандарты.', 'category' => 'seo', 'description' => 'Описание сайта'],
                ['setting_key' => 'site_keywords', 'setting_value' => 'строительство Франкфурт, ремонт квартир, строительные услуги, baumaster, Frankfurt am Main', 'category' => 'seo', 'description' => 'Ключевые слова'],
                
                // Социальные сети
                ['setting_key' => 'facebook_url', 'setting_value' => '', 'category' => 'social', 'description' => 'Ссылка на Facebook'],
                ['setting_key' => 'instagram_url', 'setting_value' => '', 'category' => 'social', 'description' => 'Ссылка на Instagram'],
                ['setting_key' => 'linkedin_url', 'setting_value' => '', 'category' => 'social', 'description' => 'Ссылка на LinkedIn'],
                
                // Настройки сайта
                ['setting_key' => 'default_language', 'setting_value' => 'ru', 'category' => 'site', 'description' => 'Язык по умолчанию'],
                ['setting_key' => 'working_hours', 'setting_value' => 'Пн-Пт: 8:00-18:00, Сб: 9:00-15:00', 'category' => 'site', 'description' => 'Рабочие часы'],
                ['setting_key' => 'google_analytics', 'setting_value' => '', 'category' => 'seo', 'description' => 'Google Analytics код'],
            ];
            
            foreach ($default_settings as $setting) {
                $this->insert('settings', $setting);
            }
        }
    }
    
    /**
     * SELECT запрос
     */
    public function select($table, $where = [], $options = []) {
        if ($this->use_json) {
            return $this->json_select($table, $where, $options);
        }
        
        try {
            $sql = "SELECT * FROM {$table}";
            $params = [];
            
            if (!empty($where)) {
                $conditions = [];
                foreach ($where as $key => $value) {
                    if ($key === '_search' && is_array($value)) {
                        // Обработка поиска
                        $search_field = $value['field'];
                        $search_value = $value['value'];
                        $conditions[] = "$search_field LIKE :search_value";
                        $params['search_value'] = "%{$search_value}%";
                    } else {
                        $conditions[] = "$key = :$key";
                        $params[$key] = $value;
                    }
                }
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
            
            if (isset($options['order'])) {
                $sql .= " ORDER BY " . $options['order'];
            }
            
            if (isset($options['limit'])) {
                $sql .= " LIMIT " . $options['limit'];
                if (isset($options['offset'])) {
                    $sql .= " OFFSET " . $options['offset'];
                }
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            return isset($options['limit']) && $options['limit'] == 1 ? 
                   $stmt->fetch() : $stmt->fetchAll();
                   
        } catch (PDOException $e) {
            write_log("Select error: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
    
    /**
     * INSERT запрос
     */
    public function insert($table, $data) {
        if ($this->use_json) {
            return $this->json_insert($table, $data);
        }
        
        try {
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $keys = array_keys($data);
            $placeholders = ':' . implode(', :', $keys);
            
            $sql = "INSERT INTO {$table} (" . implode(', ', $keys) . ") 
                    VALUES ($placeholders)";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($data);
            
            return $result ? $this->pdo->lastInsertId() : false;
            
        } catch (PDOException $e) {
            write_log("Insert error: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * UPDATE запрос
     */
    public function update($table, $data, $where) {
        if ($this->use_json) {
            return $this->json_update($table, $data, $where);
        }
        
        try {
            $data['updated_at'] = date('Y-m-d H:i:s');
            
            $set_clauses = [];
            $params = $data;
            
            foreach ($data as $key => $value) {
                $set_clauses[] = "$key = :$key";
            }
            
            $where_clauses = [];
            foreach ($where as $key => $value) {
                $where_key = "where_$key";
                $where_clauses[] = "$key = :$where_key";
                $params[$where_key] = $value;
            }
            
            $sql = "UPDATE {$table} SET " . implode(', ', $set_clauses) . 
                   " WHERE " . implode(' AND ', $where_clauses);
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            write_log("Update error: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * DELETE запрос
     */
    public function delete($table, $where) {
        if ($this->use_json) {
            return $this->json_delete($table, $where);
        }
        
        try {
            $where_clauses = [];
            $params = [];
            
            foreach ($where as $key => $value) {
                $where_clauses[] = "$key = :$key";
                $params[$key] = $value;
            }
            
            $sql = "DELETE FROM {$table} WHERE " . implode(' AND ', $where_clauses);
            
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            write_log("Delete error: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    // JSON методы (упрощенная реализация)
    private function json_select($table, $where = [], $options = []) {
        $data = read_json_file($this->json_path . $table . '.json');
        
        if (!empty($where)) {
            $data = array_filter($data, function($row) use ($where) {
                foreach ($where as $key => $value) {
                    if ($key === '_search' && is_array($value)) {
                        // Обработка поиска
                        $search_field = $value['field'];
                        $search_value = $value['value'];
                        if (!isset($row[$search_field]) || stripos($row[$search_field], $search_value) === false) {
                            return false;
                        }
                    } else {
                        if (!isset($row[$key]) || $row[$key] != $value) {
                            return false;
                        }
                    }
                }
                return true;
            });
        }
        
        if (isset($options['limit']) && $options['limit'] == 1) {
            return !empty($data) ? array_values($data)[0] : null;
        }
        
        return array_values($data);
    }
    
    private function json_insert($table, $data) {
        $all_data = read_json_file($this->json_path . $table . '.json');
        
        $data['id'] = !empty($all_data) ? max(array_column($all_data, 'id')) + 1 : 1;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $all_data[] = $data;
        
        return write_json_file($this->json_path . $table . '.json', $all_data) ? 
               $data['id'] : false;
    }
    
    private function json_update($table, $data, $where) {
        $all_data = read_json_file($this->json_path . $table . '.json');
        
        foreach ($all_data as &$row) {
            $match = true;
            foreach ($where as $key => $value) {
                if (!isset($row[$key]) || $row[$key] != $value) {
                    $match = false;
                    break;
                }
            }
            
            if ($match) {
                $row = array_merge($row, $data);
                $row['updated_at'] = date('Y-m-d H:i:s');
            }
        }
        
        return write_json_file($this->json_path . $table . '.json', $all_data);
    }
    
    private function json_delete($table, $where) {
        $all_data = read_json_file($this->json_path . $table . '.json');
        
        $all_data = array_filter($all_data, function($row) use ($where) {
            foreach ($where as $key => $value) {
                if (isset($row[$key]) && $row[$key] == $value) {
                    return false;
                }
            }
            return true;
        });
        
        return write_json_file($this->json_path . $table . '.json', array_values($all_data));
    }
    
    /**
     * Получить PDO объект для прямых запросов
     */
    public function get_pdo() {
        return $this->pdo;
    }
}

// Создание глобального экземпляра базы данных
$GLOBALS['db'] = new Database();

/**
 * Получить экземпляр базы данных
 */
function get_database() {
    return $GLOBALS['db'];
}
