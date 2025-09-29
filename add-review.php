<?php
/**
 * Обработка формы добавления отзыва
 * Baumaster - Add Review Handler
 */

// Подключение компонентов
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/functions.php';

// Установка заголовков
header('Content-Type: application/json; charset=utf-8');

// Проверка метода запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Метод не разрешен']);
    exit;
}

// Функция для отправки ответа
function sendResponse($success, $message, $data = []) {
    // Получаем URL страницы, откуда пришел запрос
    $referer = $_SERVER['HTTP_REFERER'] ?? '/';
    
    // Определяем параметры для URL
    $params = [
        'success' => $success ? '1' : '0',
        'message' => urlencode($message)
    ];
    
    if (!empty($data)) {
        $params['data'] = urlencode(json_encode($data));
    }
    
    // Формируем URL с параметрами
    $redirect_url = $referer . '?' . http_build_query($params);
    
    // Перенаправляем обратно на страницу
    header('Location: ' . $redirect_url);
    exit;
}

// Функция для валидации email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Функция для проверки на спам
function isSpam($text, $name, $email) {
    $spamKeywords = [
        'viagra', 'casino', 'loan', 'credit', 'debt', 'free money',
        'click here', 'buy now', 'discount', 'offer', 'deal',
        'http://', 'https://', 'www.'
    ];
    
    $text = strtolower($text . ' ' . $name);
    
    error_log("Spam check text: " . $text);
    
    foreach ($spamKeywords as $keyword) {
        if (strpos($text, $keyword) !== false) {
            error_log("Spam detected - keyword: " . $keyword);
            return true;
        }
    }
    
    error_log("No spam detected");
    return false;
}

// Функция для проверки лимита отзывов с одного IP
function checkRateLimit($ip) {
    $db = get_database();
    
    // Проверяем количество отзывов за последний час
    $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
    
    // Получаем все отзывы с этого IP и фильтруем по времени
    $reviews = $db->select('reviews', ['ip_address' => $ip]);
    
    $recent_reviews = 0;
    foreach ($reviews as $review) {
        if (strtotime($review['created_at']) >= strtotime($oneHourAgo)) {
            $recent_reviews++;
        }
    }
    
    return $recent_reviews < REVIEW_RATE_LIMIT; // Максимум отзывов в час
}

try {
    // Отладочная информация
    error_log("POST data received: " . print_r($_POST, true));
    
    // Получение и валидация данных
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $service = trim($_POST['service'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);
    $review_text = trim($_POST['review'] ?? '');
    $agree = isset($_POST['agree']) ? true : false;
    
    error_log("Processed data - Name: $name, Email: $email, Service: $service, Rating: $rating, Agree: " . ($agree ? 'true' : 'false'));
    
    // Преобразуем строковые значения услуг в ID
    $serviceMap = [
        'painting' => 1,
        'flooring' => 2,
        'bathroom' => 3,
        'drywall' => 4,
        'tiling' => 5,
        'renovation' => 6
    ];
    
    if (!empty($service) && isset($serviceMap[$service])) {
        $service = $serviceMap[$service];
    } else {
        $service = '';
    }
    
    // Валидация обязательных полей
    if (empty($name)) {
        sendResponse(false, 'Имя обязательно для заполнения');
    }
    
    if (empty($review_text)) {
        sendResponse(false, 'Текст отзыва обязателен для заполнения');
    }
    
    if ($rating < 1 || $rating > 5) {
        sendResponse(false, 'Рейтинг должен быть от 1 до 5 звезд');
    }
    
    if (!$agree) {
        sendResponse(false, 'Необходимо согласие на обработку данных');
    }
    
    // Валидация email если указан
    if (!empty($email) && !validateEmail($email)) {
        sendResponse(false, 'Некорректный email адрес');
    }
    
    // Получение IP адреса
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    
    // Проверка лимита отзывов
    if (!checkRateLimit($ip_address)) {
        sendResponse(false, 'Слишком много отзывов. Попробуйте позже.');
    }
    
    // Проверка на спам
    if (isSpam($review_text, $name, $email)) {
        sendResponse(false, 'Отзыв не может быть опубликован');
    }
    
    // Санитизация данных
    $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $review_text = htmlspecialchars($review_text, ENT_QUOTES, 'UTF-8');
    
    // Ограничение длины текста
    if (strlen($review_text) > 2000) {
        sendResponse(false, 'Текст отзыва слишком длинный (максимум 2000 символов)');
    }
    
    if (strlen($name) > 100) {
        sendResponse(false, 'Имя слишком длинное (максимум 100 символов)');
    }
    
    // Получение User Agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Подключение к базе данных
    $db = get_database();
    
    // Сохранение отзыва в базу данных
    $review_data = [
        'client_name' => $name,
        'client_email' => $email,
        'review_text' => $review_text,
        'rating' => $rating,
        'status' => 'pending',
        'ip_address' => $ip_address,
        'user_agent' => $user_agent
    ];
    
    // Добавляем service_id только если услуга выбрана
    if (!empty($service) && is_numeric($service)) {
        $review_data['service_id'] = intval($service);
    }
    
    // Отладочная информация
    error_log("Review data: " . print_r($review_data, true));
    
    $result = $db->insert('reviews', $review_data);
    
    if (!$result) {
        sendResponse(false, 'Ошибка сохранения отзыва. Попробуйте позже.');
    }
    
    // Получение ID созданного отзыва
    $review_id = $result;
    
    // Логирование
    write_log("Новый отзыв создан: ID {$review_id}, Имя: {$name}, Рейтинг: {$rating}", 'INFO');
    
    // Отправка уведомления администратору (если настроено)
    if (defined('ADMIN_EMAIL') && !empty(ADMIN_EMAIL)) {
        $subject = "Новый отзыв на сайте - {$name}";
        $message = "
        Новый отзыв ожидает модерации:
        
        Имя: {$name}
        Email: {$email}
        Услуга: {$service}
        Рейтинг: {$rating}/5
        Текст: {$review_text}
        
        Для модерации перейдите в админ-панель.
        ";
        
        $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        @mail(ADMIN_EMAIL, $subject, $message, $headers);
    }
    
    // Автоматический перевод отзыва (если включено)
    if (defined('AUTO_TRANSLATE_REVIEWS') && AUTO_TRANSLATE_REVIEWS) {
        try {
            require_once __DIR__ . '/integrations/translation/TranslationManager.php';
            $translation_manager = new TranslationManager();
            
            // Определяем язык отзыва (простая проверка по символам)
            $is_russian = preg_match('/[а-яё]/iu', $review_text);
            $is_german = preg_match('/[äöüß]/iu', $review_text);
            
            $from_lang = 'ru';
            $to_lang = 'de';
            
            // Если отзыв на немецком, переводим на русский
            if ($is_german && !$is_russian) {
                $from_lang = 'de';
                $to_lang = 'ru';
            }
            // Если отзыв на русском, переводим на немецкий
            elseif ($is_russian && !$is_german) {
                $from_lang = 'ru';
                $to_lang = 'de';
            }
            // Если язык не определен, считаем русским и переводим на немецкий
            else {
                $from_lang = 'ru';
                $to_lang = 'de';
            }
            
            // Поля для перевода
            $fields_to_translate = [
                'review_text' => $review_text
            ];
            
            // Добавляем перевод для услуги, если она выбрана
            if (!empty($service) && is_numeric($service)) {
                // Получаем название услуги из базы данных
                $service_data = $db->select('services', ['id' => intval($service)], ['limit' => 1]);
                if (!empty($service_data)) {
                    $service_name = $service_data['title'] ?? '';
                    if (!empty($service_name)) {
                        $fields_to_translate['service_name'] = $service_name;
                    }
                }
            }
            
            // Выполняем автоматический перевод
            $translated_fields = $translation_manager->autoTranslateContent('reviews', $review_id, $fields_to_translate, $from_lang, $to_lang);
            
            if (!empty($translated_fields)) {
                write_log("Отзыв ID {$review_id} переведен с {$from_lang} на {$to_lang}", 'INFO');
            }
        } catch (Exception $e) {
            write_log("Ошибка перевода отзыва ID {$review_id}: " . $e->getMessage(), 'ERROR');
        }
    }
    
    // Успешный ответ
    sendResponse(true, 'Отзыв успешно отправлен и ожидает модерации. Спасибо за ваш отзыв!', [
        'review_id' => $review_id
    ]);
    
} catch (Exception $e) {
    // Логирование ошибки
    write_log("Ошибка обработки отзыва: " . $e->getMessage(), 'ERROR');
    
    // Отправка ошибки
    sendResponse(false, 'Произошла ошибка при обработке отзыва. Попробуйте позже.');
}
?>
