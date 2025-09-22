<?php
/**
 * Internationalization (i18n) System
 * Baumaster Integrations - Multi-language Support
 */

// Загрузка языковых файлов
$current_language = $_SESSION['language'] ?? get_setting('default_language', 'ru');
$language_files = [
    'ru' => LANG_PATH . 'ru.json',
    'de' => LANG_PATH . 'de.json',
    'en' => LANG_PATH . 'en.json'
];

$translations = [];
if (isset($language_files[$current_language]) && file_exists($language_files[$current_language])) {
    $translations = json_decode(file_get_contents($language_files[$current_language]), true) ?? [];
}

/**
 * Функция перевода
 */
function __i18n($key, $default = '', $language = null) {
    global $translations, $current_language;
    
    if ($language === null) {
        $language = $current_language;
    }
    
    // Загрузка переводов для конкретного языка если нужно
    if ($language !== $current_language) {
        $language_files = [
            'ru' => LANG_PATH . 'ru.json',
            'de' => LANG_PATH . 'de.json',
            'en' => LANG_PATH . 'en.json'
        ];
        
        if (isset($language_files[$language]) && file_exists($language_files[$language])) {
            $lang_translations = json_decode(file_get_contents($language_files[$language]), true) ?? [];
        } else {
            $lang_translations = $translations;
        }
    } else {
        $lang_translations = $translations;
    }
    
    // Поиск перевода по ключу
    $keys = explode('.', $key);
    $value = $lang_translations;
    
    foreach ($keys as $k) {
        if (isset($value[$k])) {
            $value = $value[$k];
        } else {
            return $default ?: $key;
        }
    }
    
    return is_string($value) ? $value : ($default ?: $key);
}

/**
 * Получение списка доступных языков
 */
function get_available_languages() {
    return [
        'ru' => [
            'name' => 'Русский',
            'native_name' => 'Русский',
            'flag' => '🇷🇺',
            'code' => 'ru'
        ],
        'de' => [
            'name' => 'Deutsch',
            'native_name' => 'Deutsch',
            'flag' => '🇩🇪',
            'code' => 'de'
        ],
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'flag' => '🇺🇸',
            'code' => 'en'
        ]
    ];
}

/**
 * Переключение языка
 */
function switch_language($language) {
    $available_languages = array_keys(get_available_languages());
    
    if (in_array($language, $available_languages)) {
        $_SESSION['language'] = $language;
        return true;
    }
    
    return false;
}

/**
 * Получение текущего языка
 */
function get_current_language_i18n() {
    global $current_language;
    return $current_language;
}

/**
 * Форматирование даты в зависимости от языка
 */
function format_date_localized($date, $format = null) {
    $language = get_current_language();
    
    if (is_string($date)) {
        $date = new DateTime($date);
    }
    
    if ($format === null) {
        $formats = [
            'ru' => 'd.m.Y H:i',
            'de' => 'd.m.Y H:i',
            'en' => 'm/d/Y g:i A'
        ];
        $format = $formats[$language] ?? 'Y-m-d H:i:s';
    }
    
    return $date->format($format);
}

/**
 * Форматирование числа в зависимости от языка
 */
function format_number_localized($number, $decimals = 0) {
    $language = get_current_language();
    
    $formats = [
        'ru' => ['decimal_separator' => ',', 'thousands_separator' => ' '],
        'de' => ['decimal_separator' => ',', 'thousands_separator' => '.'],
        'en' => ['decimal_separator' => '.', 'thousands_separator' => ',']
    ];
    
    $format = $formats[$language] ?? $formats['en'];
    
    return number_format($number, $decimals, $format['decimal_separator'], $format['thousands_separator']);
}

/**
 * Форматирование валюты в зависимости от языка
 */
function format_currency_localized($amount, $currency = 'EUR') {
    $language = get_current_language();
    
    $formats = [
        'ru' => ['symbol' => '€', 'position' => 'after', 'space' => true],
        'de' => ['symbol' => '€', 'position' => 'after', 'space' => false],
        'en' => ['symbol' => '$', 'position' => 'before', 'space' => false]
    ];
    
    $format = $formats[$language] ?? $formats['en'];
    $formatted_amount = format_number_localized($amount, 2);
    
    if ($format['position'] === 'before') {
        return $format['symbol'] . ($format['space'] ? ' ' : '') . $formatted_amount;
    } else {
        return $formatted_amount . ($format['space'] ? ' ' : '') . $format['symbol'];
    }
}

/**
 * Генерация HTML атрибутов для языка
 */
function get_language_attributes() {
    $language = get_current_language();
    return 'lang="' . $language . '" dir="ltr"';
}

/**
 * Генерация селектора языков
 */
function generate_language_selector($current_url = '') {
    $languages = get_available_languages();
    $current_lang = get_current_language();
    
    $output = '<div class="language-selector">';
    $output .= '<select onchange="changeLanguage(this.value)" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500">';
    
    foreach ($languages as $code => $lang) {
        $selected = $code === $current_lang ? 'selected' : '';
        $output .= '<option value="' . $code . '" ' . $selected . '>';
        $output .= $lang['flag'] . ' ' . $lang['native_name'];
        $output .= '</option>';
    }
    
    $output .= '</select>';
    $output .= '</div>';
    
    $output .= '<script>
    function changeLanguage(lang) {
        const url = new URL(window.location);
        url.searchParams.set("lang", lang);
        window.location.href = url.toString();
    }
    </script>';
    
    return $output;
}

/**
 * Обработка переключения языка
 */
function handle_language_switch() {
    if (isset($_GET['lang'])) {
        $new_language = $_GET['lang'];
        if (switch_language($new_language)) {
            // Удаляем параметр lang из URL
            $url = strtok($_SERVER["REQUEST_URI"], '?');
            $query_params = $_GET;
            unset($query_params['lang']);
            
            if (!empty($query_params)) {
                $url .= '?' . http_build_query($query_params);
            }
            
            header('Location: ' . $url);
            exit;
        }
    }
}

/**
 * Инициализация системы i18n
 */
function init_i18n() {
    handle_language_switch();
    
    // Установка локали
    $language = get_current_language();
    $locales = [
        'ru' => 'ru_RU.UTF-8',
        'de' => 'de_DE.UTF-8',
        'en' => 'en_US.UTF-8'
    ];
    
    if (isset($locales[$language])) {
        setlocale(LC_ALL, $locales[$language]);
    }
}

// Автоматическая инициализация
init_i18n();
?>

