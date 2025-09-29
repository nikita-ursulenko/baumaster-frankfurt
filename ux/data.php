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
            
            // Декодируем gallery если это JSON строка
            $gallery = [];
            if (!empty($service['gallery'])) {
                $decoded = json_decode($service['gallery'], true);
                $gallery = is_array($decoded) ? $decoded : [];
            }
            
            $formatted_services[] = [
                'id' => $service['id'],
                'title' => $service['title'],
                'description' => $service['description'],
                'image' => $service['image'] ?: '/assets/images/services/default.jpg',
                'price' => $service['price'],
                'category' => $service['category'] ?? '',
                'status' => $service['status'] ?? 'active',
                'features' => $features,
                'gallery' => $gallery
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
 * Получение данных об услугах с переводами для указанного языка
 */
function get_services_data_with_translations($lang = 'de') {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    require_once __DIR__ . '/../integrations/translation/TranslationManager.php';
    
    try {
        $db = get_database();
        $translation_manager = new TranslationManager();
        $services = $db->select('services');
        
        // Преобразуем данные из базы в нужный формат
        $formatted_services = [];
        foreach ($services as $service) {
            // Получаем переводы для этой услуги
            $translations = $translation_manager->getTranslatedContent('services', $service['id'], $lang);
            
            // Декодируем features если это JSON строка
            $features = [];
            if (!empty($service['features'])) {
                $decoded = json_decode($service['features'], true);
                $features = is_array($decoded) ? $decoded : [];
                
                // Если есть перевод features, используем его
                if (isset($translations['features'])) {
                    $translated_features = json_decode($translations['features'], true);
                    if (is_array($translated_features)) {
                        $features = $translated_features;
                    }
                }
            }
            
            // Декодируем gallery если это JSON строка
            $gallery = [];
            if (!empty($service['gallery'])) {
                $decoded = json_decode($service['gallery'], true);
                $gallery = is_array($decoded) ? $decoded : [];
            }
            
            $formatted_services[] = [
                'id' => $service['id'],
                'title' => $translations['title'] ?? $service['title'],
                'description' => $translations['description'] ?? $service['description'],
                'image' => $service['image'] ?: '/assets/images/services/default.jpg',
                'price' => $service['price'],
                'category' => $service['category'] ?? '',
                'status' => $service['status'] ?? 'active',
                'features' => $features,
                'gallery' => $gallery
            ];
        }
        
        return $formatted_services;
    } catch (Exception $e) {
        // В случае ошибки возвращаем пустой массив
        error_log("Ошибка загрузки услуг с переводами: " . $e->getMessage());
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
                if (is_array($decoded)) {
                    foreach ($decoded as $image) {
                        $gallery[] = '/assets/uploads/portfolio/' . $image;
                    }
                }
            }
            
            // Декодируем technical_info если это JSON строка
            $technical_info = [];
            if (!empty($project['technical_info'])) {
                $decoded = json_decode($project['technical_info'], true);
                $technical_info = is_array($decoded) ? $decoded : [];
            }
            
            // Декодируем before_after если это JSON строка
            $before_after = [];
            if (!empty($project['before_after'])) {
                $decoded = json_decode($project['before_after'], true);
                if (is_array($decoded)) {
                    $before_after = [
                        'before' => !empty($decoded['before']) ? '/assets/uploads/portfolio/' . $decoded['before'] : '',
                        'after' => !empty($decoded['after']) ? '/assets/uploads/portfolio/' . $decoded['after'] : ''
                    ];
                }
            }
            
            // Декодируем tags если это JSON строка
            $tags = [];
            if (!empty($project['tags'])) {
                $decoded = json_decode($project['tags'], true);
                $tags = is_array($decoded) ? $decoded : [];
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
                'image' => !empty($project['featured_image']) ? '/assets/uploads/portfolio/' . $project['featured_image'] : '/assets/images/portfolio/default.jpg',
                'gallery' => $gallery,
                'technical_info' => $technical_info,
                'before_after' => $before_after,
                'tags' => $tags,
                'client_name' => $project['client_name'] ?? '',
                'location' => $project['location'] ?? '',
                'featured' => $project['featured'] ?? 0,
                'sort_order' => $project['sort_order'] ?? 0,
                'meta_title' => $project['meta_title'] ?? '',
                'meta_description' => $project['meta_description'] ?? ''
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
 * Получение портфолио с автоматическими переводами
 */
function get_portfolio_data_translated($target_lang = 'de') {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    require_once __DIR__ . '/../integrations/translation/TranslationService.php';
    
    try {
        $db = get_database();
        $translation_service = new TranslationService();
        
        // Получаем все активные проекты портфолио
        $portfolio = $db->select('portfolio', ['status' => 'active'], ['order_by' => 'sort_order DESC, featured DESC, created_at DESC']);
        
        if (empty($portfolio)) {
            return [];
        }
        
        $formatted_portfolio = [];
        
        foreach ($portfolio as $project) {
            // Декодируем gallery если это JSON строка
            $gallery = [];
            if (!empty($project['gallery'])) {
                $decoded = json_decode($project['gallery'], true);
                if (is_array($decoded)) {
                    foreach ($decoded as $image) {
                        $gallery[] = '/assets/uploads/portfolio/' . $image;
                    }
                }
            }
            
            // Декодируем technical_info если это JSON строка
            $technical_info = [];
            if (!empty($project['technical_info'])) {
                $decoded = json_decode($project['technical_info'], true);
                $technical_info = is_array($decoded) ? $decoded : [];
            }
            
            // Декодируем before_after если это JSON строка
            $before_after = [];
            if (!empty($project['before_after'])) {
                $decoded = json_decode($project['before_after'], true);
                if (is_array($decoded)) {
                    $before_after = [
                        'before' => !empty($decoded['before']) ? '/assets/uploads/portfolio/' . $decoded['before'] : '',
                        'after' => !empty($decoded['after']) ? '/assets/uploads/portfolio/' . $decoded['after'] : ''
                    ];
                }
            }
            
            // Декодируем tags если это JSON строка
            $tags = [];
            if (!empty($project['tags'])) {
                $decoded = json_decode($project['tags'], true);
                $tags = is_array($decoded) ? $decoded : [];
            }
            
            // Переводим основные поля
            $translated_title = $translation_service->translate($project['title'], 'ru', $target_lang);
            $translated_description = $translation_service->translate($project['description'], 'ru', $target_lang);
            $translated_client_name = $translation_service->translate($project['client_name'], 'ru', $target_lang);
            $translated_location = $translation_service->translate($project['location'], 'ru', $target_lang);
            
            // Переводим техническую информацию
            $translated_technical_info = $technical_info;
            if (!empty($technical_info['style'])) {
                $translated_technical_info['style'] = $translation_service->translate($technical_info['style'], 'ru', $target_lang);
            }
            if (!empty($technical_info['features']) && is_array($technical_info['features'])) {
                $translated_features = [];
                foreach ($technical_info['features'] as $feature) {
                    $translated_features[] = $translation_service->translate($feature, 'ru', $target_lang);
                }
                $translated_technical_info['features'] = $translated_features;
            }
            
            // Переводим теги
            $translated_tags = [];
            foreach ($tags as $tag) {
                $translated_tags[] = $translation_service->translate($tag, 'ru', $target_lang);
            }
            
            $formatted_portfolio[] = [
                'id' => $project['id'],
                'title' => $translated_title,
                'description' => $translated_description,
                'category' => $project['category'],
                'area' => $project['area'] ?? '',
                'duration' => $project['duration'] ?? '',
                'budget' => $project['budget'] ?? 0,
                'completion_date' => $project['completion_date'] ?? '',
                'image' => !empty($project['featured_image']) ? '/assets/uploads/portfolio/' . $project['featured_image'] : '/assets/images/portfolio/default.jpg',
                'gallery' => $gallery,
                'technical_info' => $translated_technical_info,
                'before_after' => $before_after,
                'tags' => $translated_tags,
                'client_name' => $translated_client_name,
                'location' => $translated_location,
                'featured' => $project['featured'] ?? 0,
                'sort_order' => $project['sort_order'] ?? 0,
                'meta_title' => $project['meta_title'] ?? '',
                'meta_description' => $project['meta_description'] ?? ''
            ];
        }
        
        return $formatted_portfolio;
    } catch (Exception $e) {
        // В случае ошибки возвращаем пустой массив
        error_log("Ошибка загрузки портфолио с переводами: " . $e->getMessage());
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
 * Получение переведенных отзывов из базы данных
 */
function get_reviews_data_translated($target_lang = 'de') {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    require_once __DIR__ . '/../integrations/translation/FastTranslationManager.php';
    
    try {
        $db = get_database();
        $translation_manager = new FastTranslationManager();
        
        // Получаем отзывы из базы данных
        $reviews = $db->select('reviews', ['status' => 'published'], ['order_by' => 'sort_order DESC, review_date DESC']);
        
        // Преобразуем данные из базы в нужный формат с переводами
        $formatted_reviews = [];
        foreach ($reviews as $review) {
            // Получаем переводы для этого отзыва
            $translations = $translation_manager->getTranslatedContent('reviews', $review['id'], $target_lang);
            
            // Определяем название услуги
            $service_name = 'Услуга';
            if (!empty($review['service_id'])) {
                if (isset($translations['service_name'])) {
                    $service_name = $translations['service_name'];
                } else {
                    $service_name = 'Услуга #' . $review['service_id'];
                }
            }
            
            $formatted_reviews[] = [
                'id' => $review['id'],
                'name' => $review['client_name'],
                'rating' => intval($review['rating']),
                'service' => $service_name,
                'text' => $translations['review_text'] ?? $review['review_text'],
                'date' => $review['review_date'],
                'verified' => $review['verified'] ?? 0,
                'featured' => $review['featured'] ?? 0,
                'client_photo' => $review['client_photo'] ?? ''
            ];
        }
        
        return $formatted_reviews;
    } catch (Exception $e) {
        // В случае ошибки возвращаем пустой массив
        error_log("Ошибка загрузки переведенных отзывов: " . $e->getMessage());
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
        // Используем новую таблицу faq вместо blog_posts
        $faq = $db->select('faq', ['status' => 'active'], ['order' => 'sort_order DESC, created_at DESC']);
        
        // Преобразуем данные из базы в нужный формат
        $formatted_faq = [];
        foreach ($faq as $item) {
            $formatted_faq[] = [
                'id' => $item['id'],
                'question' => $item['question'],
                'answer' => $item['answer'],
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
 * Получение переведенных данных FAQ для немецкой версии
 */
function get_faq_data_translated($lang = 'de') {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    require_once __DIR__ . '/../integrations/translation/FastTranslationManager.php';
    
    try {
        $db = get_database();
        $translation_manager = new FastTranslationManager();
        
        // Получаем FAQ из базы данных
        $faq = $db->select('faq', ['status' => 'active'], ['order' => 'sort_order DESC, created_at DESC']);
        
        // Преобразуем данные из базы в нужный формат с переводами
        $formatted_faq = [];
        foreach ($faq as $item) {
            // Получаем переводы для этого FAQ
            $translations = $translation_manager->getTranslatedContent('faq', $item['id'], $lang);
            
            $formatted_faq[] = [
                'id' => $item['id'],
                'question' => $translations['question'] ?? $item['question'],
                'answer' => $translations['answer'] ?? $item['answer'],
                'category' => $item['category'] ?? 'general',
                'sort_order' => $item['sort_order'] ?? 0
            ];
        }
        
        return $formatted_faq;
    } catch (Exception $e) {
        // В случае ошибки возвращаем пустой массив
        error_log("Ошибка загрузки переведенных FAQ: " . $e->getMessage());
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
                'instagram' => '@baumaster.frankfurt',
                'facebook' => '',
                'linkedin' => ''
            ]
        ];
        
        // Заполняем данными из настроек
        $working_hours_data = [];
        $settings_array = []; // Создаем массив для удобного доступа к настройкам
        
        foreach ($settings as $setting) {
            $settings_array[$setting['setting_key']] = $setting['setting_value'];
            
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
                case 'working_hours':
                    $contact_info['working_hours'] = $setting['setting_value'];
                    break;
                default:
                    // Обработка рабочих часов по дням
                    if (strpos($setting['setting_key'], 'working_hours_') === 0) {
                        $day = str_replace('working_hours_', '', $setting['setting_key']);
                        $working_hours_data[$day] = $setting['setting_value'];
                    }
                    break;
            }
        }
        
        // Формируем компактную строку рабочих часов (упрощенная версия)
        $is_german = defined('CURRENT_LANG') && CURRENT_LANG === 'de';
        
        // Получаем новые настройки рабочих часов из массива
        $weekdays_from = $settings_array['working_hours_weekdays_from'] ?? '08:00';
        $weekdays_to = $settings_array['working_hours_weekdays_to'] ?? '17:00';
        $saturday_from = $settings_array['working_hours_saturday_from'] ?? '09:00';
        $saturday_to = $settings_array['working_hours_saturday_to'] ?? '14:00';
        $sunday_working = ($settings_array['sunday_working'] ?? '0') == '1';
        $sunday_from = $settings_array['working_hours_sunday_from'] ?? '10:00';
        $sunday_to = $settings_array['working_hours_sunday_to'] ?? '16:00';
        
        // Формируем строку рабочих часов
        $working_hours_parts = [];
        
        if ($is_german) {
            $working_hours_parts[] = "MO-FR {$weekdays_from}-{$weekdays_to}";
            $working_hours_parts[] = "SA {$saturday_from}-{$saturday_to}";
            if ($sunday_working) {
                $working_hours_parts[] = "SO {$sunday_from}-{$sunday_to}";
            } else {
                $working_hours_parts[] = "SO - X";
            }
        } else {
            $working_hours_parts[] = "ПН-ПТ {$weekdays_from}-{$weekdays_to}";
            $working_hours_parts[] = "СБ {$saturday_from}-{$saturday_to}";
            if ($sunday_working) {
                $working_hours_parts[] = "ВС {$sunday_from}-{$sunday_to}";
            } else {
                $working_hours_parts[] = "ВС - X";
            }
        }
        
        $contact_info['working_hours'] = implode('<br>', $working_hours_parts);
        
        // Получаем социальные сети
        $social_settings = $db->select('settings', ['category' => 'social']);
        foreach ($social_settings as $setting) {
            if (!empty($setting['setting_value'])) {
                switch ($setting['setting_key']) {
                    case 'whatsapp':
                        $contact_info['social']['whatsapp'] = $setting['setting_value'];
                        break;
                    case 'telegram':
                        $contact_info['social']['telegram'] = $setting['setting_value'];
                        break;
                    case 'facebook_url':
                        $contact_info['social']['facebook'] = $setting['setting_value'];
                        break;
                    case 'instagram_url':
                        $contact_info['social']['instagram'] = $setting['setting_value'];
                        break;
                    case 'linkedin_url':
                        $contact_info['social']['linkedin'] = $setting['setting_value'];
                        break;
                }
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
function get_seo_data($lang = null) {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    
    // Определяем язык
    if ($lang === null) {
        $lang = defined('CURRENT_LANG') ? CURRENT_LANG : 'ru';
    }
    
    try {
        $db = get_database();
        
        // Получаем SEO настройки для конкретного языка
        $settings = $db->select('settings', ['category' => 'seo']);
        
        // Базовые SEO данные по умолчанию
        $default_seo = [
            'ru' => [
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
            ],
            'de' => [
                'home' => [
                    'title' => 'Baumaster Frankfurt - Innenausbau & Renovierung',
                    'description' => 'Professionelle Innenausbau- und Renovierungsdienstleistungen in Frankfurt am Main. Malerarbeiten, Bodenverlegung, Badezimmerrenovierung.'
                ],
                'services' => [
                    'title' => 'Dienstleistungen | ' . SITE_NAME,
                    'description' => 'Vollständiges Spektrum von Innenarbeiten in Frankfurt: Malerarbeiten, Bodenverlegung, Badezimmerrenovierung, Trockenbau, Fliesen. Professionelle Qualität.'
                ],
                'portfolio' => [
                    'title' => 'Portfolio | ' . SITE_NAME,
                    'description' => 'Beispiele unserer Renovierungs- und Innenausbauarbeiten in Frankfurt. Vorher- und Nachher-Fotos, echte Projekte von Wohnungen, Häusern und Büros.'
                ],
                'about' => [
                    'title' => 'Über uns | ' . SITE_NAME,
                    'description' => 'Team von Profis mit mehr als 10 Jahren Erfahrung. Wir führen Innenarbeiten in Frankfurt mit Qualitätsgarantie und termingerecht aus.'
                ],
                'reviews' => [
                    'title' => 'Kundenbewertungen | ' . SITE_NAME,
                    'description' => 'Echte Bewertungen unserer Kunden über die Qualität der Renovierungsarbeiten in Frankfurt. Mehr als 500 zufriedene Kunden in 10 Jahren Arbeit.'
                ],
                'blog' => [
                    'title' => 'FAQ - Häufig gestellte Fragen | ' . SITE_NAME,
                    'description' => 'Antworten auf beliebte Fragen zu Renovierung und Innenarbeiten. Kosten, Termine, Garantien, Materialien - alles was Sie wissen müssen.'
                ],
                'contact' => [
                    'title' => 'Kontakt | ' . SITE_NAME,
                    'description' => 'Kontaktieren Sie uns für eine Beratung und kostenlose Berechnung. Telefon: +49 (0) 69 123 456 78. Wir arbeiten in ganz Frankfurt.'
                ]
            ]
        ];
        
        $seo_data = $default_seo[$lang] ?? $default_seo['ru'];
        
        // Заполняем данными из настроек БД
        foreach ($settings as $setting) {
            $key = $setting['setting_key'];
            
            // Проверяем, относится ли настройка к текущему языку
            if (preg_match('/^page_([^_]+)_' . $lang . '_(.+)$/', $key, $matches)) {
                $page = $matches[1];
                $field = $matches[2];
                
                if (isset($seo_data[$page]) && in_array($field, ['title', 'h1', 'description', 'keywords', 'og_title', 'og_description', 'og_image'])) {
                    $seo_data[$page][$field] = $setting['setting_value'];
                }
            }
            
            // Обрабатываем общие настройки сайта
            if ($lang === 'ru') {
                switch ($key) {
                    case 'site_title':
                        $seo_data['home']['title'] = $setting['setting_value'];
                        break;
                    case 'site_description':
                        $seo_data['home']['description'] = $setting['setting_value'];
                        break;
                }
            }
        }
        
        return $seo_data;
        
    } catch (Exception $e) {
        // В случае ошибки возвращаем значения по умолчанию
        error_log("Ошибка загрузки SEO данных: " . $e->getMessage());
        
        $default_seo = [
            'ru' => [
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
            ],
            'de' => [
                'home' => [
                    'title' => 'Baumaster Frankfurt - Innenausbau & Renovierung',
                    'description' => 'Professionelle Innenausbau- und Renovierungsdienstleistungen in Frankfurt am Main. Malerarbeiten, Bodenverlegung, Badezimmerrenovierung.'
                ],
                'services' => [
                    'title' => 'Dienstleistungen | ' . SITE_NAME,
                    'description' => 'Vollständiges Spektrum von Innenarbeiten in Frankfurt: Malerarbeiten, Bodenverlegung, Badezimmerrenovierung, Trockenbau, Fliesen. Professionelle Qualität.'
                ],
                'portfolio' => [
                    'title' => 'Portfolio | ' . SITE_NAME,
                    'description' => 'Beispiele unserer Renovierungs- und Innenausbauarbeiten in Frankfurt. Vorher- und Nachher-Fotos, echte Projekte von Wohnungen, Häusern und Büros.'
                ],
                'about' => [
                    'title' => 'Über uns | ' . SITE_NAME,
                    'description' => 'Team von Profis mit mehr als 10 Jahren Erfahrung. Wir führen Innenarbeiten in Frankfurt mit Qualitätsgarantie und termingerecht aus.'
                ],
                'reviews' => [
                    'title' => 'Kundenbewertungen | ' . SITE_NAME,
                    'description' => 'Echte Bewertungen unserer Kunden über die Qualität der Renovierungsarbeiten in Frankfurt. Mehr als 500 zufriedene Kunden in 10 Jahren Arbeit.'
                ],
                'blog' => [
                    'title' => 'FAQ - Häufig gestellte Fragen | ' . SITE_NAME,
                    'description' => 'Antworten auf beliebte Fragen zu Renovierung und Innenarbeiten. Kosten, Termine, Garantien, Materialien - alles was Sie wissen müssen.'
                ],
                'contact' => [
                    'title' => 'Kontakt | ' . SITE_NAME,
                    'description' => 'Kontaktieren Sie uns für eine Beratung und kostenlose Berechnung. Telefon: +49 (0) 69 123 456 78. Wir arbeiten in ganz Frankfurt.'
                ]
            ]
        ];
        
        return $default_seo[$lang] ?? $default_seo['ru'];
    }
}

/**
 * Получение списка статей блога для главной страницы блога
 */
function get_blog_posts($limit = 6, $category = null, $lang = 'ru') {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    require_once __DIR__ . '/../integrations/translation/TranslationManager.php';

    try {
        $db = get_database();
        $translation_manager = new TranslationManager();

        $filters = ['status' => 'published'];
        if ($category) {
            $filters['category'] = $category;
        }

        $posts = $db->select('blog_posts', $filters, [
            'order_by' => 'published_at DESC',
            'limit' => $limit
        ]);

        $formatted_posts = [];
        foreach ($posts as $post) {
            // Получаем переводы для немецкого языка
            $translated_content = null;
            if ($lang === 'de') {
                $translated_content = $translation_manager->getTranslatedContent('blog_posts', $post['id'], 'de');
            }

            // Декодируем теги
            $tags = [];
            if (!empty($post['tags'])) {
                $decoded = json_decode($post['tags'], true);
                $tags = is_array($decoded) ? $decoded : [];
            }

            $formatted_posts[] = [
                'id' => $post['id'],
                'title' => $translated_content['title'] ?? $post['title'],
                'slug' => $post['slug'],
                'excerpt' => $translated_content['excerpt'] ?? $post['excerpt'],
                'content' => $translated_content['content'] ?? $post['content'],
                'category' => $post['category'],
                'post_type' => $post['post_type'],
                'tags' => $tags,
                'featured_image' => !empty($post['featured_image']) ? '/assets/uploads/blog/' . $post['featured_image'] : '',
                'views' => $post['views'],
                'published_at' => $post['published_at'],
                'created_at' => $post['created_at']
            ];
        }

        return $formatted_posts;

    } catch (Exception $e) {
        error_log("Ошибка загрузки статей блога: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение статей блога с пагинацией
 */
function get_blog_posts_paginated($limit = 9, $offset = 0, $lang = 'ru') {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';
    require_once __DIR__ . '/../integrations/translation/TranslationManager.php';

    try {
        $db = get_database();
        $translation_manager = new TranslationManager();

        $posts = $db->select('blog_posts', ['status' => 'published'], [
            'order_by' => 'published_at DESC',
            'limit' => $limit,
            'offset' => $offset
        ]);

        $formatted_posts = [];
        foreach ($posts as $post) {
            // Получаем переводы для немецкого языка
            $translated_content = null;
            if ($lang === 'de') {
                $translated_content = $translation_manager->getTranslatedContent('blog_posts', $post['id'], 'de');
            }

            // Декодируем теги
            $tags = [];
            if (!empty($post['tags'])) {
                $decoded = json_decode($post['tags'], true);
                $tags = is_array($decoded) ? $decoded : [];
            }

            $formatted_posts[] = [
                'id' => $post['id'],
                'title' => $translated_content['title'] ?? $post['title'],
                'slug' => $post['slug'],
                'excerpt' => $translated_content['excerpt'] ?? $post['excerpt'],
                'content' => $translated_content['content'] ?? $post['content'],
                'category' => $post['category'],
                'post_type' => $post['post_type'],
                'tags' => $tags,
                'featured_image' => !empty($post['featured_image']) ? '/assets/uploads/blog/' . $post['featured_image'] : '',
                'views' => $post['views'],
                'published_at' => $post['published_at'],
                'created_at' => $post['created_at']
            ];
        }

        return $formatted_posts;

    } catch (Exception $e) {
        error_log("Ошибка загрузки статей блога с пагинацией: " . $e->getMessage());
        return [];
    }
}

/**
 * Получение отдельной статьи блога по slug
 */
function get_blog_post($slug, $lang = 'ru') {
    // Подключаем конфигурацию и базу данных
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';

    try {
        $db = get_database();
        $post = $db->select('blog_posts', [
            'slug' => $slug,
            'status' => 'published'
        ], ['limit' => 1]);

        if (!$post || empty($post)) {
            return null;
        }

        // Получаем переводы, если язык не русский
        if ($lang !== 'ru') {
            $translations = $db->select('translations', [
                'source_table' => 'blog_posts',
                'source_id' => $post['id'],
                'target_lang' => $lang
            ]);
            
            // Применяем переводы
            foreach ($translations as $translation) {
                if (isset($post[$translation['source_field']])) {
                    $post[$translation['source_field']] = $translation['translated_text'];
                }
            }
        }

        // Декодируем теги
        $tags = [];
        if (!empty($post['tags'])) {
            $decoded = json_decode($post['tags'], true);
            $tags = is_array($decoded) ? $decoded : [];
        }

        // Обновляем счетчик просмотров
        $db->update('blog_posts', [
            'views' => $post['views'] + 1
        ], ['id' => $post['id']]);
        
        // Записываем активность в daily_activity
        $today = date('Y-m-d');
        $existing_activity = $db->select('daily_activity', ['date' => $today], ['limit' => 1]);
        
        if (!empty($existing_activity)) {
            // Обновляем существующую запись
            $current_blog_views = $existing_activity['blog_views'] ?? 0;
            $current_total = $existing_activity['total_views'] ?? 0;
            
            $db->update('daily_activity', [
                'blog_views' => $current_blog_views + 1,
                'total_views' => $current_total + 1
            ], ['date' => $today]);
        } else {
            // Создаем новую запись
            $db->insert('daily_activity', [
                'date' => $today,
                'services_views' => 0,
                'portfolio_views' => 0,
                'blog_views' => 1,
                'reviews_views' => 0,
                'total_views' => 1
            ]);
        }

        // Получаем предыдущую и следующую статьи
        $prev_post = null;
        $next_post = null;

        // Получаем все опубликованные статьи для навигации
        $all_posts = $db->select('blog_posts', [
            'status' => 'published'
        ], [
            'order_by' => 'published_at DESC'
        ]);

        // Находим текущую статью в списке и определяем соседние
        $current_index = -1;
        foreach ($all_posts as $index => $p) {
            if ($p['id'] == $post['id']) {
                $current_index = $index;
                break;
            }
        }

        if ($current_index > 0) {
            $prev_post = [
                'id' => $all_posts[$current_index - 1]['id'],
                'title' => $all_posts[$current_index - 1]['title'],
                'slug' => $all_posts[$current_index - 1]['slug']
            ];
        }

        if ($current_index >= 0 && $current_index < count($all_posts) - 1) {
            $next_post = [
                'id' => $all_posts[$current_index + 1]['id'],
                'title' => $all_posts[$current_index + 1]['title'],
                'slug' => $all_posts[$current_index + 1]['slug']
            ];
        }

        // Получаем связанные статьи (по категории, исключая текущую)
        $related_posts = $db->select('blog_posts', [
            'category' => $post['category'],
            'status' => 'published'
        ], [
            'order_by' => 'published_at DESC',
            'limit' => 3
        ]);
        
        // Исключаем текущую статью из связанных
        $related_posts = array_filter($related_posts, function($related) use ($post) {
            return $related['id'] != $post['id'];
        });

        $formatted_related = [];
        foreach ($related_posts as $related) {
            $formatted_related[] = [
                'id' => $related['id'],
                'title' => $related['title'],
                'slug' => $related['slug'],
                'excerpt' => $related['excerpt'],
                'featured_image' => !empty($related['featured_image']) ? '/assets/uploads/blog/' . $related['featured_image'] : '',
                'published_at' => $related['published_at']
            ];
        }

        // Форматируем основную статью
        return [
            'id' => $post['id'],
            'title' => $post['title'],
            'slug' => $post['slug'],
            'excerpt' => $post['excerpt'],
            'content' => $post['content'],
            'category' => $post['category'],
            'post_type' => $post['post_type'],
            'tags' => $tags,
            'featured_image' => !empty($post['featured_image']) ? '/assets/uploads/blog/' . $post['featured_image'] : '',
            'meta_title' => $post['meta_title'] ?: $post['title'],
            'meta_description' => $post['meta_description'] ?: $post['excerpt'],
            'keywords' => $post['keywords'],
            'author_id' => $post['author_id'],
            'views' => $post['views'] + 1, // Увеличиваем на 1 для отображения
            'featured' => $post['featured'],
            'published_at' => $post['published_at'],
            'created_at' => $post['created_at'],
            'updated_at' => $post['updated_at'],
            'navigation' => [
                'prev' => $prev_post,
                'next' => $next_post
            ],
            'related_posts' => $formatted_related
        ];

    } catch (Exception $e) {
        error_log("Ошибка загрузки статьи блога: " . $e->getMessage());
        return null;
    }
}
/**
 * Функции для работы с данными страницы "О компании"
 */

/**
 * Получение контента страницы "О компании"
 */
function get_about_content($section = null, $lang = 'ru') {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';

    try {
        $db = get_database();
        
        if ($section) {
            $content = $db->select('about_content', [
                'section' => $section
            ], ['limit' => 1]);
        } else {
            $content = $db->select('about_content', [], ['order' => 'sort_order ASC']);
        }

        if (!$content) {
            return null;
        }

        // Если запрашивается конкретная секция
        if ($section && isset($content[0])) {
            $item = $content[0];
            
            // Декодируем JSON контент
            if (!empty($item['content'])) {
                $item['content'] = json_decode($item['content'], true);
            }
            
            // Получаем переводы для немецкой версии
            if ($lang !== 'ru') {
                $translations = $db->select('translations', [
                    'source_table' => 'about_content',
                    'source_id' => $item['id'],
                    'target_lang' => $lang
                ]);
                
                // Применяем переводы
                foreach ($translations as $translation) {
                    if ($translation['source_field'] === 'title') {
                        $item['title'] = $translation['translated_text'];
                    } elseif ($translation['source_field'] === 'content') {
                        $translated_content = json_decode($translation['translated_text'], true);
                        if ($translated_content) {
                            $item['content'] = $translated_content;
                        }
                    }
                }
            }
            
            return $item;
        }

        // Если запрашиваются все секции
        $result = [];
        if (is_array($content)) {
            foreach ($content as $item) {
                // Декодируем JSON контент
                if (!empty($item['content'])) {
                    $item['content'] = json_decode($item['content'], true);
                }
                
                // Получаем переводы для немецкой версии
                if ($lang !== 'ru') {
                    $translations = $db->select('translations', [
                        'source_table' => 'about_content',
                        'source_id' => $item['id'],
                        'target_lang' => $lang
                    ]);
                    
                    // Применяем переводы
                    if (is_array($translations)) {
                        foreach ($translations as $translation) {
                            if ($translation['source_field'] === 'title') {
                                $item['title'] = $translation['translated_text'];
                            } elseif ($translation['source_field'] === 'content') {
                                $translated_content = json_decode($translation['translated_text'], true);
                                if ($translated_content) {
                                    $item['content'] = $translated_content;
                                }
                            }
                        }
                    }
                }
                
                if (isset($item['section'])) {
                    $result[$item['section']] = $item;
                }
            }
        }
        
        return $result;
        
    } catch (Exception $e) {
        error_log("Error getting about content: " . $e->getMessage());
        return null;
    }
}

/**
 * Получение данных команды
 */
function get_team_members($lang = 'ru') {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';

    try {
        $db = get_database();
        $members = $db->select('team_members', [
            'status' => 'active'
        ], ['order' => 'sort_order ASC']);

        if (!$members) {
            return [];
        }

        // Применяем переводы для немецкой версии
        if ($lang !== 'ru') {
            foreach ($members as &$member) {
                $translations = $db->select('translations', [
                    'source_table' => 'team_members',
                    'source_id' => $member['id'],
                    'target_lang' => $lang
                ]);
                
                foreach ($translations as $translation) {
                    if ($translation['source_field'] === 'name') {
                        $member['name'] = $translation['translated_text'];
                    } elseif ($translation['source_field'] === 'position') {
                        $member['position'] = $translation['translated_text'];
                    } elseif ($translation['source_field'] === 'description') {
                        $member['description'] = $translation['translated_text'];
                    }
                }
            }
        }

        return $members;
    } catch (Exception $e) {
        error_log("Error getting team members: " . $e->getMessage());
        return [];
    }
}

/**
 * Сохранение члена команды
 */
function save_team_member($data, $lang = 'ru') {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';

    try {
        $db = get_database();
        
        $member_data = [
            'name' => $data['name'],
            'position' => $data['position'],
            'description' => $data['description'],
            'sort_order' => $data['sort_order'] ?? 0,
            'status' => 'active',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (isset($data['image']) && !empty($data['image'])) {
            $member_data['image'] = $data['image'];
        }

        if (isset($data['id']) && !empty($data['id'])) {
            // Обновляем существующего члена команды
            $result = $db->update('team_members', $member_data, [
                'id' => $data['id']
            ]);
            $member_id = $data['id'];
        } else {
            // Создаем нового члена команды
            $member_data['created_at'] = date('Y-m-d H:i:s');
            $member_id = $db->insert('team_members', $member_data);
            $result = $member_id !== false;
        }

        return $result ? $member_id : false;
    } catch (Exception $e) {
        error_log("Error saving team member: " . $e->getMessage());
        return false;
    }
}

/**
 * Удаление члена команды
 */
function delete_team_member($id) {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';

    try {
        $db = get_database();
        
        // Удаляем переводы
        $db->delete('translations', [
            'source_table' => 'team_members',
            'source_id' => $id
        ]);
        
        // Удаляем члена команды
        $result = $db->delete('team_members', [
            'id' => $id
        ]);

        return $result;
    } catch (Exception $e) {
        error_log("Error deleting team member: " . $e->getMessage());
        return false;
    }
}

/**
 * Получение члена команды по ID
 */
function get_team_member($id, $lang = 'ru') {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';

    try {
        $db = get_database();
        $member = $db->select('team_members', [
            'id' => $id
        ], ['limit' => 1]);

        if (!$member) {
            return null;
        }

        $member = $member[0];

        // Применяем переводы для немецкой версии
        if ($lang !== 'ru') {
            $translations = $db->select('translations', [
                'source_table' => 'team_members',
                'source_id' => $member['id'],
                'target_lang' => $lang
            ]);
            
            foreach ($translations as $translation) {
                if ($translation['source_field'] === 'name') {
                    $member['name'] = $translation['translated_text'];
                } elseif ($translation['source_field'] === 'position') {
                    $member['position'] = $translation['translated_text'];
                } elseif ($translation['source_field'] === 'description') {
                    $member['description'] = $translation['translated_text'];
                }
            }
        }

        return $member;
    } catch (Exception $e) {
        error_log("Error getting team member: " . $e->getMessage());
        return null;
    }
}

/**
 * Получение статистики
 */
function get_statistics($lang = 'ru') {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';

    try {
        $db = get_database();
        $statistics = $db->select('statistics', [
            'status' => 'active'
        ], ['order' => 'sort_order ASC']);

        if (!$statistics) {
            return [];
        }

        // Применяем переводы для немецкой версии
        if ($lang !== 'ru') {
            foreach ($statistics as &$stat) {
                $translations = $db->select('translations', [
                    'source_table' => 'statistics',
                    'source_id' => $stat['id'],
                    'target_lang' => $lang
                ]);
                
                foreach ($translations as $translation) {
                    if ($translation['source_field'] === 'label') {
                        $stat['label'] = $translation['translated_text'];
                    } elseif ($translation['source_field'] === 'description') {
                        $stat['description'] = $translation['translated_text'];
                    }
                }
            }
        }

        return $statistics;
    } catch (Exception $e) {
        error_log("Error getting statistics: " . $e->getMessage());
        return [];
    }
}

/**
 * Сохранение статистики
 */
function save_statistics($data, $lang = 'ru') {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';

    try {
        $db = get_database();
        
        // Удаляем все существующие записи статистики
        $db->delete('statistics', ['status' => 'active']);
        
        $result = true;
        $stat_ids = [];
        
        // Сохраняем новые записи
        foreach ($data as $index => $stat_data) {
            $stat_record = [
                'number' => $stat_data['number'],
                'label' => $stat_data['label'],
                'description' => $stat_data['description'],
                'sort_order' => $index + 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $stat_id = $db->insert('statistics', $stat_record);
            if ($stat_id) {
                $stat_ids[] = $stat_id;
            } else {
                $result = false;
            }
        }

        return $result ? $stat_ids : false;
    } catch (Exception $e) {
        error_log("Error saving statistics: " . $e->getMessage());
        return false;
    }
}

/**
 * Сохранение контента страницы "О компании"
 */
function save_about_content($section, $data, $lang = 'ru') {
    require_once __DIR__ . '/../config.php';
    require_once __DIR__ . '/../database.php';

    try {
        $db = get_database();
        
        // Проверяем, существует ли запись
        $existing = $db->select('about_content', [
            'section' => $section
        ], ['limit' => 1]);

        if ($existing) {
            // Обновляем существующую запись
            $update_data = [
                'title' => $data['title'],
                'content' => json_encode($data['content']),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if (isset($data['image']) && !empty($data['image'])) {
                $update_data['image'] = $data['image'];
            }
            
            $result = $db->update('about_content', $update_data, [
                'section' => $section
            ]);
            
            $record_id = $existing[0]['id'] ?? null;
        } else {
            // Создаем новую запись
            $insert_data = [
                'section' => $section,
                'title' => $data['title'],
                'content' => json_encode($data['content']),
                'sort_order' => $data['sort_order'] ?? 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if (isset($data['image']) && !empty($data['image'])) {
                $insert_data['image'] = $data['image'];
            }
            
            $result = $db->insert('about_content', $insert_data);
            $record_id = $db->lastInsertId();
        }

        // Если это не русская версия, создаем переводы
        if ($lang !== 'ru' && $record_id) {
            // Сохраняем перевод заголовка
            if (!empty($data['title'])) {
                $translation_data = [
                    'source_table' => 'about_content',
                    'source_id' => $record_id,
                    'source_field' => 'title',
                    'source_lang' => 'ru',
                    'target_lang' => $lang,
                    'source_text' => $data['title'],
                    'translated_text' => $data['title'],
                    'translation_service' => 'TranslationService',
                    'auto_translated' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // Проверяем, существует ли перевод
                $existing_translation = $db->select('translations', [
                    'source_table' => 'about_content',
                    'source_id' => $record_id,
                    'source_field' => 'title',
                    'target_lang' => $lang
                ], ['limit' => 1]);
                
                if ($existing_translation) {
                    $db->update('translations', [
                        'translated_text' => $data['title'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ], [
                        'source_table' => 'about_content',
                        'source_id' => $record_id,
                        'source_field' => 'title',
                        'target_lang' => $lang
                    ]);
                } else {
                    $db->insert('translations', $translation_data);
                }
            }
            
            // Сохраняем перевод контента
            if (!empty($data['content'])) {
                $translation_data = [
                    'source_table' => 'about_content',
                    'source_id' => $record_id,
                    'source_field' => 'content',
                    'source_lang' => 'ru',
                    'target_lang' => $lang,
                    'source_text' => json_encode($data['content']),
                    'translated_text' => json_encode($data['content']),
                    'translation_service' => 'TranslationService',
                    'auto_translated' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                // Проверяем, существует ли перевод
                $existing_translation = $db->select('translations', [
                    'source_table' => 'about_content',
                    'source_id' => $record_id,
                    'source_field' => 'content',
                    'target_lang' => $lang
                ], ['limit' => 1]);
                
                if ($existing_translation) {
                    $db->update('translations', [
                        'translated_text' => json_encode($data['content']),
                        'updated_at' => date('Y-m-d H:i:s')
                    ], [
                        'source_table' => 'about_content',
                        'source_id' => $record_id,
                        'source_field' => 'content',
                        'target_lang' => $lang
                    ]);
                } else {
                    $db->insert('translations', $translation_data);
                }
            }
        }

        return $result;
        
    } catch (Exception $e) {
        error_log("Error saving about content: " . $e->getMessage());
        return false;
    }
}

?>

