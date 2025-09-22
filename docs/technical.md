# 🔧 Техническая документация

**Техническое описание архитектуры и API системы Baumaster Frankfurt**

## 🏗️ Архитектура системы

### Общая структура

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Frontend      │    │   Backend       │    │   Database      │
│   (Public)      │◄──►│   (Admin)       │◄──►│   (SQLite/MySQL)│
│                 │    │                 │    │                 │
│ • HTML/CSS/JS   │    │ • PHP 7.4+      │    │ • SQLite        │
│ • TailwindCSS   │    │ • Modular       │    │ • JSON files    │
│ • Responsive    │    │ • Component     │    │ • File storage  │
│ • SEO Ready     │    │ • MVC Pattern   │    │                 │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Компонентная архитектура

- **UI Components** (`/ui/`) - базовые элементы интерфейса
- **Layout Components** (`/components/`) - переиспользуемые макеты
- **Frontend Components** (`/ux/`) - компоненты публичной части
- **Integration Modules** (`/integrations/`) - внешние сервисы
- **SEO Tools** (`/seo/`) - инструменты оптимизации

## 📁 Структура файлов

### Конфигурационные файлы

```php
config.php          // Основная конфигурация
functions.php       // Общие функции
database.php        // Работа с БД
.htaccess          // Настройки Apache
```

### Основные директории

```
admin/              // Админ-панель (PHP)
├── index.php       // Dashboard
├── services.php    // CRUD услуги
├── portfolio.php   // CRUD портфолио
├── reviews.php     // CRUD отзывы
├── blog.php        // CRUD блог
├── users.php       // CRUD пользователи
├── settings.php    // Настройки
├── stats.php       // Статистика
├── seo_analysis.php // SEO инструменты
├── integrations.php // Интеграции
└── testing.php     // Тестирование

components/         // Переиспользуемые компоненты
├── admin_layout.php // Layout админки
├── auth_layout.php  // Layout авторизации
└── admin_js.php     // JavaScript функции

ui/                 // Базовые UI компоненты
└── base.php        // Основные UI функции

ux/                 // Фронтенд компоненты
├── layout.php      // Layout сайта
├── components.php  // UI компоненты
└── data.php        // Статические данные
```

## 🗄️ База данных

### SQLite схема

```sql
-- Пользователи
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'editor',
    status VARCHAR(20) DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Услуги
CREATE TABLE services (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    price_type VARCHAR(50),
    image VARCHAR(255),
    gallery TEXT, -- JSON
    features TEXT, -- JSON
    meta_title VARCHAR(255),
    meta_description TEXT,
    keywords TEXT,
    status VARCHAR(20) DEFAULT 'active',
    priority INTEGER DEFAULT 0,
    category VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Портфолио
CREATE TABLE portfolio (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    completion_date DATE,
    area DECIMAL(8,2),
    duration VARCHAR(50),
    budget DECIMAL(12,2),
    client_name VARCHAR(100),
    location VARCHAR(255),
    featured_image VARCHAR(255),
    gallery TEXT, -- JSON
    technical_info TEXT, -- JSON
    before_after TEXT, -- JSON
    tags TEXT, -- JSON
    status VARCHAR(20) DEFAULT 'completed',
    featured INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Отзывы
CREATE TABLE reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    client_name VARCHAR(100) NOT NULL,
    client_email VARCHAR(100),
    client_phone VARCHAR(20),
    client_photo VARCHAR(255),
    review_text TEXT NOT NULL,
    rating INTEGER CHECK(rating >= 1 AND rating <= 5),
    project_id INTEGER,
    service_id INTEGER,
    status VARCHAR(20) DEFAULT 'pending',
    review_date DATE,
    verified INTEGER DEFAULT 0,
    featured INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    admin_notes TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Блог/FAQ
CREATE TABLE blog_posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT,
    content TEXT NOT NULL,
    category VARCHAR(100),
    tags TEXT, -- JSON
    featured_image VARCHAR(255),
    meta_title VARCHAR(255),
    meta_description TEXT,
    keywords TEXT,
    status VARCHAR(20) DEFAULT 'draft',
    post_type VARCHAR(20) DEFAULT 'article',
    author_id INTEGER,
    views INTEGER DEFAULT 0,
    featured INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    published_at DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Настройки
CREATE TABLE settings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type VARCHAR(20) DEFAULT 'text',
    category VARCHAR(50) DEFAULT 'general',
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Активность пользователей
CREATE TABLE user_activity (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    action VARCHAR(50) NOT NULL,
    table_name VARCHAR(50),
    record_id INTEGER,
    old_values TEXT, -- JSON
    new_values TEXT, -- JSON
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

## 🔌 API и функции

### Основные функции (`functions.php`)

#### Аутентификация

```php
// Проверка авторизации
function is_logged_in(): bool

// Получение текущего пользователя
function get_current_admin_user(): ?array

// Хеширование пароля
function hash_password(string $password): string

// Проверка пароля
function verify_password(string $password, string $hash): bool

// Генерация CSRF токена
function generate_csrf_token(): string

// Проверка CSRF токена
function verify_csrf_token(string $token): bool
```

#### Работа с данными

```php
// Санитизация ввода
function sanitize_input(string $input): string

// Валидация email
function validate_email(string $email): bool

// Форматирование даты
function format_date(string $date, string $format = 'd.m.Y H:i'): string

// Форматирование цены
function format_price(float $price, string $currency = '€'): string

// Генерация slug
function generate_slug(string $text): string
```

#### Настройки

```php
// Получение настройки
function get_setting(string $key, string $default = ''): string

// Установка настройки
function set_setting(string $key, string $value, string $category = 'general'): bool

// Получение настроек по категории
function get_settings_by_category(string $category): array
```

#### Логирование

```php
// Запись в лог
function write_log(string $message, string $level = 'INFO'): void

// Логирование активности пользователя
function log_user_activity(string $action, string $table, int $record_id): void
```

### UI компоненты (`ui/base.php`)

#### Рендеринг элементов

```php
// HTML head
function render_html_head(array $data): string

// Уведомления
function render_error_message(string $message): void
function render_success_message(string $message): void

// Формы
function render_form_field(array $config): void
function render_button(array $config): void

// Карточки статистики
function render_stat_card(array $config): void

// Иконки
function get_icon(string $name, string $class = 'w-5 h-5'): string
```

### SEO функции (`seo/seo_utils.php`)

#### Мета-теги

```php
// Генерация мета-тегов
function generate_meta_tags(array $page_data = []): string

// JSON-LD разметка
function generate_json_ld(string $type, array $data = []): string

// Schema.org для организации
function generate_organization_schema(): string

// Schema.org для услуги
function generate_service_schema(array $service): string
```

#### Оптимизация

```php
// Оптимизация изображения
function optimize_image(string $source_path, string $destination_path = null): string

// Создание WebP
function create_webp_image(string $source_path): string

// Минификация CSS
function minify_css(string $css): string

// Минификация JS
function minify_js(string $js): string
```

## 🔒 Безопасность

### Защита от атак

#### SQL Injection

```php
// Использование подготовленных запросов
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);

// Санитизация ввода
$clean_input = sanitize_input($_POST['input']);
```

#### XSS Protection

```php
// Экранирование вывода
echo htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8');

// Санитизация HTML
$clean_html = strip_tags($html_input, '<p><br><strong>');
```

#### CSRF Protection

```php
// Генерация токена
$token = generate_csrf_token();

// Проверка токена
if (!verify_csrf_token($_POST['csrf_token'])) {
    die('CSRF token mismatch');
}
```

### Настройки безопасности

#### .htaccess

```apache
# Безопасность
<Files ~ "\.(db|log|json)$">
    Order allow,deny
    Deny from all
</Files>

# Заголовки безопасности
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
</IfModule>
```

#### PHP настройки

```php
// Отключение отображения ошибок в production
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Настройки сессий
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);
ini_set('session.use_strict_mode', 1);
```

## ⚡ Производительность

### Оптимизация базы данных

```sql
-- Индексы для быстрого поиска
CREATE INDEX idx_services_status ON services(status);
CREATE INDEX idx_portfolio_category ON portfolio(category);
CREATE INDEX idx_reviews_status ON reviews(status);
CREATE INDEX idx_blog_status ON blog_posts(status);

-- Оптимизация таблиц
VACUUM;
ANALYZE;
```

### Кэширование

```php
// Кэш настроек
$settings_cache = 'data/cache/settings.json';
if (file_exists($settings_cache)) {
    $settings = json_decode(file_get_contents($settings_cache), true);
} else {
    $settings = get_all_settings();
    file_put_contents($settings_cache, json_encode($settings));
}
```

### Оптимизация изображений

```php
// Автоматическое сжатие
$optimized = optimize_image($source_path, $dest_path, 85, 1920, 1080);

// Создание WebP
$webp_path = create_webp_image($source_path, 85);
```

## 🧪 Тестирование

### Автоматические тесты

```php
// Запуск тестов
php tests/test_suite.php

// Тесты безопасности
php tests/security_test.php

// Оптимизация
php tools/optimizer.php

// Исправление багов
php tools/bug_fixer.php
```

### Ручное тестирование

1. **Функциональное тестирование**

   - Проверка всех CRUD операций
   - Тестирование форм и валидации
   - Проверка авторизации

2. **Тестирование безопасности**

   - SQL injection тесты
   - XSS атаки
   - CSRF защита

3. **Тестирование производительности**
   - Время загрузки страниц
   - Использование памяти
   - Размер базы данных

## 📊 Мониторинг

### Логирование

```php
// Системные логи
write_log("User login: {$username}", 'INFO');
write_log("Database error: {$error}", 'ERROR');

// Логи активности
log_user_activity('create', 'services', $service_id);
```

### Мониторинг производительности

```php
// Время выполнения
$start_time = microtime(true);
// ... код ...
$execution_time = microtime(true) - $start_time;

// Использование памяти
$memory_usage = memory_get_usage(true);
$memory_peak = memory_get_peak_usage(true);
```

## 🔄 Развертывание

### Production настройки

```php
// config.php для production
define('DEBUG_MODE', false);
define('LOG_LEVEL', 'ERROR');
define('CACHE_ENABLED', true);
define('COMPRESSION_ENABLED', true);
```

### Настройка веб-сервера

#### Apache

```apache
# .htaccess для production
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Gzip сжатие
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript
</IfModule>

# Кэширование
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType text/css "access plus 1 month"
</IfModule>
```

#### Nginx

```nginx
# Конфигурация Nginx
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl;
    server_name your-domain.com;

    root /var/www/baumaster;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

## 📈 Масштабирование

### Горизонтальное масштабирование

- Использование CDN для статических файлов
- Кэширование на уровне приложения
- Балансировка нагрузки

### Вертикальное масштабирование

- Увеличение ресурсов сервера
- Оптимизация запросов к БД
- Использование Redis для кэша

---

**Техническая поддержка: dev@baumaster-frankfurt.de**

