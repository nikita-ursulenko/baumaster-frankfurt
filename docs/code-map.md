# Карта кода проекта Baumaster Frankfurt

## Глобальные переменные и константы

| Имя                        | Тип    | Модуль/Класс | Назначение                      | Пример использования                                                             |
| -------------------------- | ------ | ------------ | ------------------------------- | -------------------------------------------------------------------------------- |
| `ABSPATH`                  | string | config.php   | Абсолютный путь к корню проекта | `define('ABSPATH', __DIR__ . '/');`                                              |
| `SITE_NAME`                | string | config.php   | Название сайта                  | `define('SITE_NAME', 'Baumaster - Строительные услуги во Франкфурте');`          |
| `SITE_URL`                 | string | config.php   | URL сайта                       | `define('SITE_URL', 'http://localhost');`                                        |
| `ADMIN_URL`                | string | config.php   | URL админ-панели                | `define('ADMIN_URL', SITE_URL . '/admin/');`                                     |
| `VERSION`                  | string | config.php   | Версия проекта                  | `define('VERSION', '1.0.0');`                                                    |
| `ADMIN_PATH`               | string | config.php   | Путь к папке админки            | `define('ADMIN_PATH', ABSPATH . 'admin/');`                                      |
| `COMPONENTS_PATH`          | string | config.php   | Путь к компонентам              | `define('COMPONENTS_PATH', ABSPATH . 'components/');`                            |
| `UI_PATH`                  | string | config.php   | Путь к UI компонентам           | `define('UI_PATH', ABSPATH . 'ui/');`                                            |
| `ASSETS_PATH`              | string | config.php   | Путь к ресурсам                 | `define('ASSETS_PATH', ABSPATH . 'assets/');`                                    |
| `LANG_PATH`                | string | config.php   | Путь к языковым файлам          | `define('LANG_PATH', ABSPATH . 'lang/');`                                        |
| `DATA_PATH`                | string | config.php   | Путь к данным                   | `define('DATA_PATH', ABSPATH . 'data/');`                                        |
| `UPLOADS_PATH`             | string | config.php   | Путь к загрузкам                | `define('UPLOADS_PATH', ASSETS_PATH . 'uploads/');`                              |
| `ASSETS_URL`               | string | config.php   | URL ресурсов                    | `define('ASSETS_URL', SITE_URL . '/assets/');`                                   |
| `UPLOADS_URL`              | string | config.php   | URL загрузок                    | `define('UPLOADS_URL', ASSETS_URL . 'uploads/');`                                |
| `DB_TYPE`                  | string | config.php   | Тип базы данных                 | `define('DB_TYPE', 'sqlite');`                                                   |
| `DB_PATH`                  | string | config.php   | Путь к SQLite БД                | `define('DB_PATH', DATA_PATH . 'baumaster.db');`                                 |
| `DB_HOST`                  | string | config.php   | Хост MySQL                      | `define('DB_HOST', 'localhost');`                                                |
| `DB_NAME`                  | string | config.php   | Имя MySQL БД                    | `define('DB_NAME', 'baumaster');`                                                |
| `DB_USER`                  | string | config.php   | Пользователь MySQL              | `define('DB_USER', 'root');`                                                     |
| `DB_PASS`                  | string | config.php   | Пароль MySQL                    | `define('DB_PASS', '');`                                                         |
| `DB_CHARSET`               | string | config.php   | Кодировка БД                    | `define('DB_CHARSET', 'utf8mb4');`                                               |
| `SESSION_NAME`             | string | config.php   | Имя сессии                      | `define('SESSION_NAME', 'baumaster_admin');`                                     |
| `SESSION_LIFETIME`         | int    | config.php   | Время жизни сессии              | `define('SESSION_LIFETIME', 3600 * 24);`                                         |
| `HASH_ALGO`                | string | config.php   | Алгоритм хеширования            | `define('HASH_ALGO', 'sha256');`                                                 |
| `CSRF_TOKEN_NAME`          | string | config.php   | Имя CSRF токена                 | `define('CSRF_TOKEN_NAME', '_csrf_token');`                                      |
| `MAX_LOGIN_ATTEMPTS`       | int    | config.php   | Максимум попыток входа          | `define('MAX_LOGIN_ATTEMPTS', 5);`                                               |
| `LOGIN_LOCKOUT_TIME`       | int    | config.php   | Время блокировки                | `define('LOGIN_LOCKOUT_TIME', 900);`                                             |
| `DEFAULT_LANG`             | string | config.php   | Язык по умолчанию               | `define('DEFAULT_LANG', 'ru');`                                                  |
| `AVAILABLE_LANGS`          | array  | config.php   | Доступные языки                 | `define('AVAILABLE_LANGS', ['ru', 'de', 'en']);`                                 |
| `MAX_FILE_SIZE`            | int    | config.php   | Максимальный размер файла       | `define('MAX_FILE_SIZE', 10 * 1024 * 1024);`                                     |
| `ALLOWED_IMAGE_TYPES`      | array  | config.php   | Разрешенные типы изображений    | `define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);`          |
| `ALLOWED_DOC_TYPES`        | array  | config.php   | Разрешенные типы документов     | `define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx', 'txt']);`                    |
| `SMTP_HOST`                | string | config.php   | SMTP хост                       | `define('SMTP_HOST', 'smtp.gmail.com');`                                         |
| `SMTP_PORT`                | int    | config.php   | SMTP порт                       | `define('SMTP_PORT', 587);`                                                      |
| `SMTP_USERNAME`            | string | config.php   | SMTP пользователь               | `define('SMTP_USERNAME', '');`                                                   |
| `SMTP_PASSWORD`            | string | config.php   | SMTP пароль                     | `define('SMTP_PASSWORD', '');`                                                   |
| `FROM_EMAIL`               | string | config.php   | Email отправителя               | `define('FROM_EMAIL', 'info@baumaster.de');`                                     |
| `FROM_NAME`                | string | config.php   | Имя отправителя                 | `define('FROM_NAME', 'Baumaster Admin');`                                        |
| `DEFAULT_META_TITLE`       | string | config.php   | Заголовок по умолчанию          | `define('DEFAULT_META_TITLE', 'Baumaster - Строительные услуги во Франкфурте');` |
| `DEFAULT_META_DESCRIPTION` | string | config.php   | Описание по умолчанию           | `define('DEFAULT_META_DESCRIPTION', 'Профессиональные строительные услуги...');` |
| `DEFAULT_KEYWORDS`         | string | config.php   | Ключевые слова                  | `define('DEFAULT_KEYWORDS', 'строительство Франкфурт, ремонт квартир...');`      |
| `ITEMS_PER_PAGE`           | int    | config.php   | Элементов на странице           | `define('ITEMS_PER_PAGE', 20);`                                                  |
| `RECENT_ITEMS_LIMIT`       | int    | config.php   | Лимит недавних элементов        | `define('RECENT_ITEMS_LIMIT', 5);`                                               |
| `DEBUG_MODE`               | bool   | config.php   | Режим отладки                   | `define('DEBUG_MODE', true);`                                                    |
| `LOG_ERRORS`               | bool   | config.php   | Логирование ошибок              | `define('LOG_ERRORS', true);`                                                    |
| `ERROR_LOG_PATH`           | string | config.php   | Путь к логу ошибок              | `define('ERROR_LOG_PATH', DATA_PATH . 'error.log');`                             |

## Публичные функции

### Функции безопасности

| Имя                                 | Тип      | Модуль/Класс  | Назначение                 | Пример использования                             |
| ----------------------------------- | -------- | ------------- | -------------------------- | ------------------------------------------------ |
| `sanitize_input($data)`             | function | functions.php | Санитизация входных данных | `$clean_data = sanitize_input($_POST['name']);`  |
| `validate_email($email)`            | function | functions.php | Валидация email            | `if (validate_email($email)) { ... }`            |
| `generate_csrf_token()`             | function | functions.php | Генерация CSRF токена      | `$token = generate_csrf_token();`                |
| `verify_csrf_token($token)`         | function | functions.php | Проверка CSRF токена       | `if (verify_csrf_token($token)) { ... }`         |
| `hash_password($password)`          | function | functions.php | Хеширование пароля         | `$hash = hash_password($password);`              |
| `verify_password($password, $hash)` | function | functions.php | Проверка пароля            | `if (verify_password($password, $hash)) { ... }` |

### Функции языка и локализации

| Имя                                             | Тип      | Модуль/Класс          | Назначение                   | Пример использования                            |
| ----------------------------------------------- | -------- | --------------------- | ---------------------------- | ----------------------------------------------- |
| `get_current_language()`                        | function | functions.php         | Получить текущий язык        | `$lang = get_current_language();`               |
| `set_language($lang)`                           | function | functions.php         | Установить язык              | `set_language('de');`                           |
| `load_language($lang)`                          | function | functions.php         | Загрузить языковые данные    | `$translations = load_language('de');`          |
| `__($key, $fallback)`                           | function | functions.php         | Получить переведенный текст  | `echo __('welcome.title', 'Добро пожаловать');` |
| `__i18n($key, $default, $language)`             | function | integrations/i18n.php | Функция перевода i18n        | `echo __i18n('welcome.title', 'Welcome');`      |
| `get_available_languages()`                     | function | integrations/i18n.php | Получить доступные языки     | `$languages = get_available_languages();`       |
| `switch_language($language)`                    | function | integrations/i18n.php | Переключение языка           | `switch_language('de');`                        |
| `get_current_language_i18n()`                   | function | integrations/i18n.php | Получить текущий язык i18n   | `$lang = get_current_language_i18n();`          |
| `format_date_localized($date, $format)`         | function | integrations/i18n.php | Форматирование даты          | `echo format_date_localized($date);`            |
| `format_number_localized($number, $decimals)`   | function | integrations/i18n.php | Форматирование числа         | `echo format_number_localized(1234.56);`        |
| `format_currency_localized($amount, $currency)` | function | integrations/i18n.php | Форматирование валюты        | `echo format_currency_localized(100, 'EUR');`   |
| `generate_language_selector($current_url)`      | function | integrations/i18n.php | Генерация селектора языков   | `echo generate_language_selector();`            |
| `handle_language_switch()`                      | function | integrations/i18n.php | Обработка переключения языка | `handle_language_switch();`                     |
| `init_i18n()`                                   | function | integrations/i18n.php | Инициализация i18n           | `init_i18n();`                                  |

### Функции работы с файлами

| Имя                                                                   | Тип      | Модуль/Класс  | Назначение                         | Пример использования                                            |
| --------------------------------------------------------------------- | -------- | ------------- | ---------------------------------- | --------------------------------------------------------------- |
| `upload_file($file, $destination, $allowed_types)`                    | function | functions.php | Безопасная загрузка файла          | `$result = upload_file($_FILES['image'], 'services');`          |
| `delete_file($filepath)`                                              | function | functions.php | Удаление файла                     | `delete_file('/path/to/file.jpg');`                             |
| `handle_image_upload($file, $destination_folder)`                     | function | functions.php | Загрузка и обработка изображения   | `$result = handle_image_upload($_FILES['image'], 'services');`  |
| `create_thumbnail($source_path, $dest_path, $max_width, $max_height)` | function | functions.php | Создание миниатюры                 | `create_thumbnail($source, $thumb, 300, 300);`                  |
| `delete_image($image_path)`                                           | function | functions.php | Удаление изображения               | `delete_image('/uploads/image.jpg');`                           |
| `handle_multiple_image_upload($files, $destination_folder)`           | function | functions.php | Множественная загрузка изображений | `$result = handle_multiple_image_upload($_FILES, 'portfolio');` |

### Функции форматирования

| Имя                                      | Тип      | Модуль/Класс  | Назначение                   | Пример использования                     |
| ---------------------------------------- | -------- | ------------- | ---------------------------- | ---------------------------------------- |
| `format_date($date, $format)`            | function | functions.php | Форматирование даты          | `echo format_date($date, 'd.m.Y H:i');`  |
| `format_filesize($bytes, $precision)`    | function | functions.php | Форматирование размера файла | `echo format_filesize(1024, 2);`         |
| `truncate_text($text, $length, $suffix)` | function | functions.php | Обрезка текста               | `echo truncate_text($text, 150, '...');` |
| `format_price($price, $currency)`        | function | functions.php | Форматирование цены          | `echo format_price(100, '€');`           |

### Функции авторизации

| Имя                              | Тип      | Модуль/Класс  | Назначение                     | Пример использования                          |
| -------------------------------- | -------- | ------------- | ------------------------------ | --------------------------------------------- |
| `is_logged_in()`                 | function | functions.php | Проверить авторизацию          | `if (is_logged_in()) { ... }`                 |
| `get_current_admin_user()`       | function | functions.php | Получить текущего пользователя | `$user = get_current_admin_user();`           |
| `require_role($required_role)`   | function | functions.php | Проверка роли пользователя     | `require_role('admin');`                      |
| `has_permission($action, $user)` | function | functions.php | Проверка прав доступа          | `if (has_permission('users.create')) { ... }` |
| `get_available_roles()`          | function | functions.php | Получить роли пользователей    | `$roles = get_available_roles();`             |
| `get_user_statuses()`            | function | functions.php | Получить статусы пользователей | `$statuses = get_user_statuses();`            |
| `user_has_role($role)`           | function | functions.php | Проверить роль пользователя    | `if (user_has_role('admin')) { ... }`         |
| `login_user($user_data)`         | function | functions.php | Логирование пользователя       | `login_user($user_data);`                     |
| `logout_user()`                  | function | functions.php | Разлогинивание пользователя    | `logout_user();`                              |

### Функции настроек

| Имя                                                  | Тип      | Модуль/Класс  | Назначение                      | Пример использования                                 |
| ---------------------------------------------------- | -------- | ------------- | ------------------------------- | ---------------------------------------------------- |
| `get_setting($key, $default)`                        | function | functions.php | Получить настройку              | `$value = get_setting('site_title', 'Default');`     |
| `set_setting($key, $value, $category, $description)` | function | functions.php | Установить настройку            | `set_setting('site_title', 'New Title', 'general');` |
| `get_settings_by_category($category)`                | function | functions.php | Получить настройки по категории | `$settings = get_settings_by_category('seo');`       |

### Функции логирования

| Имя                                                                             | Тип      | Модуль/Класс  | Назначение                  | Пример использования                          |
| ------------------------------------------------------------------------------- | -------- | ------------- | --------------------------- | --------------------------------------------- |
| `log_user_activity($action, $table_name, $record_id, $old_values, $new_values)` | function | functions.php | Логирование активности      | `log_user_activity('create', 'services', 1);` |
| `write_log($message, $level)`                                                   | function | functions.php | Записать в лог              | `write_log('Error occurred', 'ERROR');`       |
| `debug_dump($var, $die)`                                                        | function | functions.php | Дамп переменной для отладки | `debug_dump($variable);`                      |

### Функции JSON

| Имя                                  | Тип      | Модуль/Класс  | Назначение            | Пример использования                            |
| ------------------------------------ | -------- | ------------- | --------------------- | ----------------------------------------------- |
| `json_response($data, $status_code)` | function | functions.php | Безопасный JSON ответ | `json_response(['success' => true]);`           |
| `read_json_file($filepath)`          | function | functions.php | Чтение JSON файла     | `$data = read_json_file('/path/to/file.json');` |
| `write_json_file($filepath, $data)`  | function | functions.php | Запись JSON файла     | `write_json_file('/path/to/file.json', $data);` |

### Функции пагинации

| Имя                                                         | Тип      | Модуль/Класс  | Назначение        | Пример использования                           |
| ----------------------------------------------------------- | -------- | ------------- | ----------------- | ---------------------------------------------- |
| `create_pagination($total_items, $current_page, $per_page)` | function | functions.php | Создать пагинацию | `$pagination = create_pagination(100, 1, 20);` |

### Функции конфигурации

| Имя                          | Тип      | Модуль/Класс | Назначение                         | Пример использования                 |
| ---------------------------- | -------- | ------------ | ---------------------------------- | ------------------------------------ |
| `get_config($key, $default)` | function | config.php   | Получить настройку из конфигурации | `$value = get_config('SITE_NAME');`  |
| `is_debug()`                 | function | config.php   | Проверить режим отладки            | `if (is_debug()) { ... }`            |
| `get_site_url($path)`        | function | config.php   | Получить базовый URL сайта         | `$url = get_site_url('/admin');`     |
| `get_admin_url($path)`       | function | config.php   | Получить URL админки               | `$url = get_admin_url('users.php');` |

### Функции базы данных

| Имя              | Тип      | Модуль/Класс | Назначение            | Пример использования    |
| ---------------- | -------- | ------------ | --------------------- | ----------------------- |
| `get_database()` | function | database.php | Получить экземпляр БД | `$db = get_database();` |

### Функции рендеринга frontend

| Имя                                                             | Тип      | Модуль/Класс      | Назначение                 | Пример использования                                                   |
| --------------------------------------------------------------- | -------- | ----------------- | -------------------------- | ---------------------------------------------------------------------- |
| `render_frontend_head($title, $meta_description, $active_page)` | function | ux/layout.php     | Рендеринг HTML head        | `render_frontend_head('Title', 'Description');`                        |
| `render_frontend_layout($options)`                              | function | ux/layout.php     | Рендеринг основного layout | `render_frontend_layout(['title' => 'Title', 'content' => $content]);` |
| `render_frontend_navigation($active_page)`                      | function | ux/layout.php     | Рендеринг навигации        | `render_frontend_navigation('home');`                                  |
| `render_frontend_footer()`                                      | function | ux/layout.php     | Рендеринг footer           | `render_frontend_footer();`                                            |
| `render_frontend_scripts()`                                     | function | ux/layout.php     | Рендеринг JavaScript       | `render_frontend_scripts();`                                           |
| `render_frontend_button($options)`                              | function | ux/components.php | Компонент кнопки           | `render_frontend_button(['text' => 'Click', 'variant' => 'primary']);` |
| `render_frontend_card($options)`                                | function | ux/components.php | Компонент карточки         | `render_frontend_card(['title' => 'Title', 'content' => 'Content']);`  |
| `render_contact_form($options)`                                 | function | ux/components.php | Форма обратной связи       | `render_contact_form(['title' => 'Contact Us']);`                      |
| `render_service_card($service)`                                 | function | ux/components.php | Карточка услуги            | `render_service_card($service_data);`                                  |
| `render_review_card($review)`                                   | function | ux/components.php | Карточка отзыва            | `render_review_card($review_data);`                                    |
| `render_faq_item($faq, $index)`                                 | function | ux/components.php | Элемент FAQ                | `render_faq_item($faq_data, 0);`                                       |

### Функции данных frontend

| Имя                                          | Тип      | Модуль/Класс | Назначение                              | Пример использования                          |
| -------------------------------------------- | -------- | ------------ | --------------------------------------- | --------------------------------------------- |
| `get_services_data()`                        | function | ux/data.php  | Получить данные услуг                   | `$services = get_services_data();`            |
| `get_portfolio_data()`                       | function | ux/data.php  | Получить данные портфолио               | `$portfolio = get_portfolio_data();`          |
| `get_reviews_data()`                         | function | ux/data.php  | Получить данные отзывов                 | `$reviews = get_reviews_data();`              |
| `get_faq_data()`                             | function | ux/data.php  | Получить данные FAQ из таблицы faq      | `$faq = get_faq_data();`                      |
| `get_faq_data_translated($lang)`             | function | ux/data.php  | Получить переведенные данные FAQ        | `$faq = get_faq_data_translated('de');`       |
| `get_contact_info()`                         | function | ux/data.php  | Получить контактную информацию          | `$contact = get_contact_info();`              |
| `get_seo_data()`                             | function | ux/data.php  | Получить SEO данные                     | `$seo = get_seo_data();`                      |
| `get_blog_posts($limit, $category)`          | function | ux/data.php  | Получить статьи блога                   | `$posts = get_blog_posts(6, 'tips');`         |
| `get_blog_post($slug)`                       | function | ux/data.php  | Получить статью блога                   | `$post = get_blog_post('article-slug');`      |
| `get_about_content($section, $lang)`         | function | ux/data.php  | Получить контент страницы "О компании"  | `$data = get_about_content('history', 'de');` |
| `save_about_content($section, $data, $lang)` | function | ux/data.php  | Сохранить контент страницы "О компании" | `save_about_content('history', $data, 'ru');` |
| `get_team_members($lang)`                    | function | ux/data.php  | Получить список сотрудников             | `$members = get_team_members('de');`          |
| `save_team_member($data, $lang)`             | function | ux/data.php  | Сохранить сотрудника                    | `save_team_member($member_data, 'ru');`       |
| `delete_team_member($id)`                    | function | ux/data.php  | Удалить сотрудника                      | `delete_team_member(1);`                      |
| `get_team_member($id, $lang)`                | function | ux/data.php  | Получить сотрудника по ID               | `$member = get_team_member(1, 'de');`         |
| `get_statistics($lang)`                      | function | ux/data.php  | Получить статистику компании            | `$stats = get_statistics('de');`              |
| `save_statistics($data, $lang)`              | function | ux/data.php  | Сохранить статистику компании           | `save_statistics($stats_data, 'ru');`         |

### Функции админ-панели

| Имя                     | Тип      | Модуль/Класс    | Назначение                    | Пример использования                 |
| ----------------------- | -------- | --------------- | ----------------------------- | ------------------------------------ |
| `get_dashboard_stats()` | function | admin/index.php | Получить статистику dashboard | `$stats = get_dashboard_stats();`    |
| `get_recent_activity()` | function | admin/index.php | Получить последнюю активность | `$activity = get_recent_activity();` |
| `get_chart_data()`      | function | admin/index.php | Получить данные для графиков  | `$chart = get_chart_data();`         |

### Функции управления FAQ

| Имя                        | Тип      | Модуль/Класс  | Назначение           | Пример использования                  |
| -------------------------- | -------- | ------------- | -------------------- | ------------------------------------- |
| `create_faq($data)`        | function | admin/faq.php | Создать новый FAQ    | `$result = create_faq($faq_data);`    |
| `update_faq($id, $data)`   | function | admin/faq.php | Обновить FAQ         | `$result = update_faq(1, $faq_data);` |
| `delete_faq($id)`          | function | admin/faq.php | Удалить FAQ          | `$result = delete_faq(1);`            |
| `validate_faq_data($data)` | function | admin/faq.php | Валидация данных FAQ | `$errors = validate_faq_data($data);` |

## Классы

### Класс Database

| Имя                                | Тип    | Модуль/Класс | Назначение                     | Пример использования                                      |
| ---------------------------------- | ------ | ------------ | ------------------------------ | --------------------------------------------------------- |
| `Database`                         | class  | database.php | Основной класс для работы с БД | `$db = new Database();`                                   |
| `select($table, $where, $options)` | method | Database     | SELECT запрос                  | `$result = $db->select('users', ['status' => 'active']);` |
| `insert($table, $data)`            | method | Database     | INSERT запрос                  | `$id = $db->insert('users', $user_data);`                 |
| `update($table, $data, $where)`    | method | Database     | UPDATE запрос                  | `$db->update('users', $data, ['id' => 1]);`               |
| `delete($table, $where)`           | method | Database     | DELETE запрос                  | `$db->delete('users', ['id' => 1]);`                      |

## Глобальные переменные

| Имя                 | Тип      | Модуль/Класс          | Назначение              | Пример использования               |
| ------------------- | -------- | --------------------- | ----------------------- | ---------------------------------- |
| `$GLOBALS['db']`    | Database | database.php          | Глобальный экземпляр БД | `$db = $GLOBALS['db'];`            |
| `$translations`     | array    | integrations/i18n.php | Массив переводов        | `$translations = load_language();` |
| `$current_language` | string   | integrations/i18n.php | Текущий язык            | `$lang = $current_language;`       |

## Примечания

- Все функции следуют принципам ООП и инкапсуляции
- Переменные состояния в классах приватные или защищённые
- Доступ к данным осуществляется через методы
- Функции разделены по зонам ответственности
- Код соответствует стандартам PSR-12 для PHP
- Все публичные методы документированы
- Система логирования отслеживает все изменения

## Обновления

- **2024-01-22**: Создана первоначальная карта кода
- **2024-01-22**: Добавлены все основные функции и константы
- **2024-01-22**: Документированы классы и методы
- **2024-01-22**: Обновлена структура согласно rules.mdc
- **2024-01-22**: Добавлена функциональность FAQ - меню в админке, отображение на frontend, функции управления
- **2024-01-26**: Реализована полная функциональность FAQ с автоматическим переводом
  - Добавлена таблица translations для хранения переводов
  - Интегрирован TranslationManager в create_faq и update_faq
  - Создана функция get_faq_data_translated для немецкой версии
  - Обновлен de/blog.php для использования переведенных FAQ
  - Реализовано удаление FAQ (hard delete)
  - Добавлено переключение статуса активный/неактивный
  - Протестированы все функции: создание, редактирование, удаление, статусы
- **2024-09-26**: Реализована полная функциональность блога с автоматическим переводом и пагинацией
  - Добавлена функция get_blog_posts_paginated для пагинации статей
  - Создана страница /blog_all.php для отображения всех статей (русская версия)
  - Создана страница /de/blog_all.php для отображения всех статей (немецкая версия)
  - Добавлены кнопки 'Все статьи' и 'Alle Artikel anzeigen' на главных страницах блога
  - Реализован автоматический перевод при создании статей через TranslationManager
  - Исправлена функция удаления постов - добавлено скрытое поле ID в форму
  - Улучшена функция delete_post - удаление связанных переводов и изображений
  - Протестированы все функции: админ-панель, публичные страницы, переводы, пагинация, удаление
- **2024-09-27**: Реализована полная функциональность управления страницей "О компании"
  - Создан админ-раздел /admin/about.php с тремя вкладками: "Наша история", "Наша команда", "Статистика"
  - Добавлены функции для управления контентом: get_about_content, save_about_content
  - Создана таблица team_members и функции: get_team_members, save_team_member, delete_team_member, get_team_member
  - Создана таблица statistics и функции: get_statistics, save_statistics
  - Реализован автоматический перевод для всех разделов через TranslationManager
  - Обновлены frontend страницы /about.php и /de/about.php для динамического отображения данных
  - Добавлена поддержка загрузки изображений для истории компании и фотографий сотрудников
  - Реализован полный CRUD для сотрудников с модальными окнами в админ-панели
  - Протестированы все функции: создание, редактирование, удаление, автоперевод, многоязычность
