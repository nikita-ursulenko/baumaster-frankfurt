<?php
/**
 * Основная конфигурация проекта Baumaster
 * Корпоративный сайт строительной фирмы + админ-панель
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

// Основные настройки
define('SITE_NAME', 'Baumaster - Строительные услуги во Франкфурте');
define('SITE_URL', 'http://localhost');
define('ADMIN_URL', SITE_URL . '/admin/');
define('VERSION', '1.0.0');

// Пути к папкам
define('ADMIN_PATH', ABSPATH . 'admin/');
define('COMPONENTS_PATH', ABSPATH . 'components/');
define('UI_PATH', ABSPATH . 'ui/');
define('ASSETS_PATH', ABSPATH . 'assets/');
define('LANG_PATH', ABSPATH . 'lang/');
define('DATA_PATH', ABSPATH . 'data/');
define('UPLOADS_PATH', ASSETS_PATH . 'uploads/');

// URLs для ресурсов
define('ASSETS_URL', SITE_URL . '/assets/');
define('UPLOADS_URL', ASSETS_URL . 'uploads/');

// База данных (SQLite)
define('DB_TYPE', 'sqlite');
define('DB_PATH', DATA_PATH . 'baumaster.db');

// Альтернативно для MySQL (если потребуется)
define('DB_HOST', 'localhost');
define('DB_NAME', 'baumaster');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Настройки сессий
define('SESSION_NAME', 'baumaster_admin');
define('SESSION_LIFETIME', 3600 * 24); // 24 часа

// Настройки безопасности
define('HASH_ALGO', 'sha256');
define('CSRF_TOKEN_NAME', '_csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 минут

// Настройки языка
define('DEFAULT_LANG', 'ru');
define('AVAILABLE_LANGS', ['ru', 'de', 'en']);

// Настройки загрузки файлов
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx', 'txt']);
define('UPLOADS_PATH', ABSPATH . 'assets/uploads/');
define('UPLOADS_URL', SITE_URL . 'assets/uploads/');

// Email настройки
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('FROM_EMAIL', 'info@baumaster.de');
define('FROM_NAME', 'Baumaster Admin');

// SEO настройки
define('DEFAULT_META_TITLE', 'Baumaster - Строительные услуги во Франкфурте');
define('DEFAULT_META_DESCRIPTION', 'Профессиональные строительные услуги во Франкфурте на Майне. Ремонт квартир, малярные работы, укладка пола, ремонт ванных комнат.');
define('DEFAULT_KEYWORDS', 'строительство Франкфурт, ремонт квартир, малярные работы, укладка пола, ремонт ванной');

// Настройки отображения
define('ITEMS_PER_PAGE', 20);
define('RECENT_ITEMS_LIMIT', 5);

// Режим отладки
define('DEBUG_MODE', true);
define('LOG_ERRORS', true);
define('ERROR_LOG_PATH', DATA_PATH . 'error.log');

// Часовой пояс
date_default_timezone_set('Europe/Berlin');

// Кодировка
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');

// Обработка ошибок
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Автозагрузка функций
require_once ABSPATH . 'functions.php';

// Запуск сессии
session_name(SESSION_NAME);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Получить настройку из конфигурации
 */
function get_config($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Проверить, включен ли режим отладки
 */
function is_debug() {
    return defined('DEBUG_MODE') && DEBUG_MODE === true;
}

/**
 * Получить базовый URL сайта
 */
function get_site_url($path = '') {
    return SITE_URL . ltrim($path, '/');
}

/**
 * Получить URL админки
 */
function get_admin_url($path = '') {
    return ADMIN_URL . ltrim($path, '/');
}

