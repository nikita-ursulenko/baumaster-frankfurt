<?php
/**
 * Данные для frontend сайта
 * Baumaster Frontend Data
 */

/**
 * Получение данных об услугах из базы данных
 */
function get_services_data() {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    
    try {
        $db = get_database();
        $services = $db->select('services');
        
        // Преобразуем данные из базы в нужный формат
        $formatted_services = [];
        foreach ($services as $service) {
            // Декодируем features если это JSON строка
            $features = [];
            if (!empty($service['features'])) {
                $decoded = json_decode($service['features'], true);
                $features = is_array($decoded) ? $decoded : [];
            }
            
            $formatted_services[] = [
                'id' => $service['id'],
                'title' => $service['title'],
                'description' => $service['description'],
                'image' => $service['image'] ?: '/assets/images/services/default.jpg',
                'price' => $service['price'],
                'features' => $features
            ];
        }
        
        return $formatted_services;
    } catch (Exception $e) {
        // В случае ошибки возвращаем пустой массив
        error_log("Ошибка загрузки услуг: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение данных портфолио из базы данных
 */
function get_portfolio_data() {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    
    try {
        $db = get_database();
        $portfolio = $db->select('portfolio', ['status' => 'active'], ['order_by' => 'sort_order DESC, created_at DESC']);
        
        // Преобразуем данные из базы в нужный формат
        $formatted_portfolio = [];
        foreach ($portfolio as $project) {
            // Декодируем gallery если это JSON строка
            $gallery = [];
            if (!empty($project['gallery'])) {
                $decoded = json_decode($project['gallery'], true);
                $gallery = is_array($decoded) ? $decoded : [];
            }
            
            // Декодируем technical_info если это JSON строка
            $technical_info = [];
            if (!empty($project['technical_info'])) {
                $decoded = json_decode($project['technical_info'], true);
                $technical_info = is_array($decoded) ? $decoded : [];
            }
            
            $formatted_portfolio[] = [
                'id' => $project['id'],
                'title' => $project['title'],
                'description' => $project['description'],
                'category' => $project['category'],
                'area' => $project['area'] ?? '',
                'duration' => $project['duration'] ?? '',
                'budget' => $project['budget'] ?? 0,
                'completion_date' => $project['completion_date'] ?? '',
                'image' => ($project['featured_image'] ?? '') ?: '/assets/images/portfolio/default.jpg',
                'gallery' => $gallery,
                'technical_info' => $technical_info,
                'client_name' => $project['client_name'] ?? '',
                'location' => $project['location'] ?? '',
                'featured' => $project['featured'] ?? 0,
                'sort_order' => $project['sort_order'] ?? 0
            ];
        }
        
        return $formatted_portfolio;
    } catch (Exception $e) {
        // В случае ошибки возвращаем пустой массив
        error_log("Ошибка загрузки портфолио: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение отзывов из базы данных
 */
function get_reviews_data() {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    
    try {
        $db = get_database();
        $reviews = $db->select('reviews', ['status' => 'published'], ['order_by' => 'sort_order DESC, review_date DESC']);
        
        // Преобразуем данные из базы в нужный формат
        $formatted_reviews = [];
        foreach ($reviews as $review) {
            $formatted_reviews[] = [
                'id' => $review['id'],
                'name' => $review['client_name'],
                'rating' => intval($review['rating']),
                'service' => $review['service_id'] ? 'Услуга #' . $review['service_id'] : 'Услуга',
                'text' => $review['review_text'],
                'date' => $review['review_date'],
                'verified' => $review['verified'] ?? 0,
                'featured' => $review['featured'] ?? 0,
                'client_photo' => $review['client_photo'] ?? ''
            ];
        }
        
        return $formatted_reviews;
    } catch (Exception $e) {
        // В случае ошибки возвращаем пустой массив
        error_log("Ошибка загрузки отзывов: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение FAQ из базы данных
 */
function get_faq_data() {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    
    try {
        $db = get_database();
        $faq = $db->select('blog_posts', ['post_type' => 'faq', 'status' => 'published'], ['order_by' => 'sort_order DESC, created_at DESC']);
        
        // Преобразуем данные из базы в нужный формат
        $formatted_faq = [];
        foreach ($faq as $item) {
            $formatted_faq[] = [
                'id' => $item['id'],
                'question' => $item['title'],
                'answer' => $item['content'],
                'category' => $item['category'] ?? 'general',
                'sort_order' => $item['sort_order'] ?? 0
            ];
        }
        
        return $formatted_faq;
    } catch (Exception $e) {
        // В случае ошибки возвращаем пустой массив
        error_log("Ошибка загрузки FAQ: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение контактной информации из настроек
 */
function get_contact_info() {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    
    try {
        $db = get_database();
        $settings = $db->select('settings', ['category' => 'company']);
        
        // Преобразуем настройки в удобный формат
        $contact_info = [
            'company_name' => SITE_NAME,
            'phone' => '+49 (0) 69 123 456 78',
            'email' => 'info@baumaster-frankfurt.de',
            'address' => 'Frankfurt am Main, Deutschland',
            'working_hours' => 'Пн-Пт: 8:00-18:00, Сб: 9:00-15:00',
            'social' => [
                'telegram' => '@baumaster_frankfurt',
                'whatsapp' => '+4969123456789',
                'instagram' => '@baumaster.frankfurt'
            ]
        ];
        
        // Заполняем данными из настроек
        foreach ($settings as $setting) {
            switch ($setting['setting_key']) {
                case 'company_name':
                    $contact_info['company_name'] = $setting['setting_value'];
                    break;
                case 'company_phone':
                    $contact_info['phone'] = $setting['setting_value'];
                    break;
                case 'company_email':
                    $contact_info['email'] = $setting['setting_value'];
                    break;
                case 'company_address':
                    $contact_info['address'] = $setting['setting_value'];
                    break;
            }
        }
        
        // Получаем социальные сети
        $social_settings = $db->select('settings', ['category' => 'social']);
        foreach ($social_settings as $setting) {
            if (!empty($setting['setting_value'])) {
                $contact_info['social'][$setting['setting_key']] = $setting['setting_value'];
            }
        }
        
        return $contact_info;
    } catch (Exception $e) {
        // В случае ошибки возвращаем значения по умолчанию
        error_log("Ошибка загрузки контактной информации: " . $e->getMessage());
        return [
            'company_name' => SITE_NAME,
            'phone' => '+49 (0) 69 123 456 78',
            'email' => 'info@baumaster-frankfurt.de',
            'address' => 'Frankfurt am Main, Deutschland',
            'working_hours' => 'Пн-Пт: 8:00-18:00, Сб: 9:00-15:00',
            'social' => [
                'telegram' => '@baumaster_frankfurt',
                'whatsapp' => '+4969123456789',
                'instagram' => '@baumaster.frankfurt'
            ]
        ];
    }
}

/**
 * SEO данные для страниц из настроек
 */
function get_seo_data() {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    
    try {
        $db = get_database();
        $settings = $db->select('settings', ['category' => 'seo']);
        
        // Преобразуем настройки в удобный формат
        $seo_data = [
            'home' => [
                'title' => DEFAULT_META_TITLE,
                'description' => DEFAULT_META_DESCRIPTION
            ],
            'services' => [
                'title' => 'Услуги | ' . SITE_NAME,
                'description' => 'Полный спектр внутренних работ во Франкфурте: малярные работы, укладка полов, ремонт ванных, гипсокартон, плитка. Профессиональное качество.'
            ],
            'portfolio' => [
                'title' => 'Портфолио работ | ' . SITE_NAME,
                'description' => 'Примеры наших работ по ремонту и отделке во Франкфурте. Фото до и после, реальные проекты квартир, домов и офисов.'
            ],
            'about' => [
                'title' => 'О компании | ' . SITE_NAME,
                'description' => 'Команда профессионалов с опытом более 10 лет. Выполняем внутренние работы во Франкфурте с гарантией качества и в срок.'
            ],
            'reviews' => [
                'title' => 'Отзывы клиентов | ' . SITE_NAME,
                'description' => 'Реальные отзывы наших клиентов о качестве ремонтных работ во Франкфурте. Более 500 довольных заказчиков за 10 лет работы.'
            ],
            'blog' => [
                'title' => 'FAQ - Часто задаваемые вопросы | ' . SITE_NAME,
                'description' => 'Ответы на популярные вопросы о ремонте и внутренних работах. Стоимость, сроки, гарантии, материалы - всё что нужно знать.'
            ],
            'contact' => [
                'title' => 'Контакты | ' . SITE_NAME,
                'description' => 'Свяжитесь с нами для консультации и бесплатного расчёта. Телефон: +49 (0) 69 123 456 78. Работаем по всему Франкфурту.'
            ]
        ];
        
        // Заполняем данными из настроек
        foreach ($settings as $setting) {
            switch ($setting['setting_key']) {
                case 'site_title':
                    $seo_data['home']['title'] = $setting['setting_value'];
                    break;
                case 'site_description':
                    $seo_data['home']['description'] = $setting['setting_value'];
                    break;
            }
        }
        
        return $seo_data;
    } catch (Exception $e) {
        // В случае ошибки возвращаем значения по умолчанию
        error_log("Ошибка загрузки SEO данных: " . $e->getMessage());
        return [
            'home' => [
                'title' => DEFAULT_META_TITLE,
                'description' => DEFAULT_META_DESCRIPTION
            ],
            'services' => [
                'title' => 'Услуги | ' . SITE_NAME,
                'description' => 'Полный спектр внутренних работ во Франкфурте: малярные работы, укладка полов, ремонт ванных, гипсокартон, плитка. Профессиональное качество.'
            ],
            'portfolio' => [
                'title' => 'Портфолио работ | ' . SITE_NAME,
                'description' => 'Примеры наших работ по ремонту и отделке во Франкфурте. Фото до и после, реальные проекты квартир, домов и офисов.'
            ],
            'about' => [
                'title' => 'О компании | ' . SITE_NAME,
                'description' => 'Команда профессионалов с опытом более 10 лет. Выполняем внутренние работы во Франкфурте с гарантией качества и в срок.'
            ],
            'reviews' => [
                'title' => 'Отзывы клиентов | ' . SITE_NAME,
                'description' => 'Реальные отзывы наших клиентов о качестве ремонтных работ во Франкфурте. Более 500 довольных заказчиков за 10 лет работы.'
            ],
            'blog' => [
                'title' => 'FAQ - Часто задаваемые вопросы | ' . SITE_NAME,
                'description' => 'Ответы на популярные вопросы о ремонте и внутренних работах. Стоимость, сроки, гарантии, материалы - всё что нужно знать.'
            ],
            'contact' => [
                'title' => 'Контакты | ' . SITE_NAME,
                'description' => 'Свяжитесь с нами для консультации и бесплатного расчёта. Телефон: +49 (0) 69 123 456 78. Работаем по всему Франкфурту.'
            ]
        ];
    }
}
?>

