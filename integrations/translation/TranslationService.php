<?php
/**
 * Сервис переводов
 * Базовый сервис для автоматических переводов
 */

class TranslationService {
    
    /**
     * Реальный перевод текста через API
     * Использует бесплатные API для автоматического перевода
     */
    public function translate($text, $from_lang = 'ru', $to_lang = 'de') {
        // Если текст пустой, возвращаем как есть
        if (empty(trim($text))) {
            return $text;
        }
        
        // Сначала проверяем кэш переводов
        $cached_translation = $this->getCachedTranslation($text, $from_lang, $to_lang);
        if ($cached_translation !== null) {
            return $cached_translation;
        }
        
        // Пытаемся перевести через различные API
        $translated_text = $this->translateViaAPI($text, $from_lang, $to_lang);
        
        // Если API не сработал, используем fallback словарь
        if ($translated_text === $text || empty($translated_text)) {
            $translated_text = $this->translateViaDictionary($text, $from_lang, $to_lang);
        }
        
        // Сохраняем в кэш
        $this->saveCachedTranslation($text, $translated_text, $from_lang, $to_lang);
        
        return $translated_text;
    }
    
    /**
     * Перевод через API (приоритетные методы)
     */
    private function translateViaAPI($text, $from_lang, $to_lang) {
        // Метод 1: Google Translate через бесплатный прокси
        $result = $this->translateGoogle($text, $from_lang, $to_lang);
        if ($result && $result !== $text) {
            return $result;
        }
        
        // Метод 2: Yandex Translate (бесплатный)
        $result = $this->translateYandex($text, $from_lang, $to_lang);
        if ($result && $result !== $text) {
            return $result;
        }
        
        // Метод 3: LibreTranslate (открытый API)
        $result = $this->translateLibre($text, $from_lang, $to_lang);
        if ($result && $result !== $text) {
            return $result;
        }
        
        return $text;
    }
    
    /**
     * Google Translate через бесплатный прокси
     */
    private function translateGoogle($text, $from_lang, $to_lang) {
        try {
            // Используем бесплатный прокси для Google Translate
            $url = 'https://translate.googleapis.com/translate_a/single';
            $params = [
                'client' => 'gtx',
                'sl' => $from_lang,
                'tl' => $to_lang,
                'dt' => 't',
                'q' => $text
            ];
            
            $response = $this->makeRequest($url . '?' . http_build_query($params));
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data[0][0][0])) {
                    return $data[0][0][0];
                }
            }
        } catch (Exception $e) {
            error_log("Google Translate error: " . $e->getMessage());
        }
        
        return $text;
    }
    
    /**
     * Yandex Translate API (бесплатный)
     */
    private function translateYandex($text, $from_lang, $to_lang) {
        try {
            // Используем бесплатный Yandex API
            $url = 'https://translate.yandex.net/api/v1.5/tr.json/translate';
            $params = [
                'key' => 'trnsl.1.1.20240101T000000Z.fake_key', // Заглушка, нужен реальный ключ
                'text' => $text,
                'lang' => $from_lang . '-' . $to_lang
            ];
            
            $response = $this->makeRequest($url, $params);
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['text'][0])) {
                    return $data['text'][0];
                }
            }
        } catch (Exception $e) {
            error_log("Yandex Translate error: " . $e->getMessage());
        }
        
        return $text;
    }
    
    /**
     * LibreTranslate (открытый API)
     */
    private function translateLibre($text, $from_lang, $to_lang) {
        try {
            $url = 'https://libretranslate.de/translate';
            $params = [
                'q' => $text,
                'source' => $from_lang,
                'target' => $to_lang,
                'format' => 'text'
            ];
            
            $response = $this->makeRequest($url, $params, 'POST');
            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['translatedText'])) {
                    return $data['translatedText'];
                }
            }
        } catch (Exception $e) {
            error_log("LibreTranslate error: " . $e->getMessage());
        }
        
        return $text;
    }
    
    /**
     * Fallback перевод через словарь (минимальный)
     */
    private function translateViaDictionary($text, $from_lang, $to_lang) {
        // Минимальный словарь только для базовых слов
        $translations = [
            'ru' => [
                'de' => [
                    'м²' => 'm²',
                    'неделя' => 'Woche',
                    'недель' => 'Wochen',
                    'недели' => 'Wochen',
                    'Франкфурт' => 'Frankfurt',
                    'проект' => 'Projekt',
                    'квартира' => 'Wohnung',
                    'ремонт' => 'Renovierung',
                    'современный' => 'modern',
                    'дизайн' => 'Design',
                    'материалы' => 'Materialien',
                    'качественные' => 'hochwertige',
                    'профессиональное' => 'professionelle',
                    'исполнение' => 'Ausführung'
                ]
            ]
        ];
        
        // Простая замена слов
        $translated_text = $text;
        foreach ($translations[$from_lang][$to_lang] as $ru_word => $de_word) {
            $translated_text = str_replace($ru_word, $de_word, $translated_text);
        }
        
        return $translated_text;
    }
    
    /**
     * Выполняет HTTP запрос
     */
    private function makeRequest($url, $params = [], $method = 'GET') {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("CURL error: " . $error);
            return false;
        }
        
        if ($http_code !== 200) {
            error_log("HTTP error: " . $http_code);
            return false;
        }
        
        return $response;
    }
    
    /**
     * Получает перевод из кэша
     */
    private function getCachedTranslation($text, $from_lang, $to_lang) {
        try {
            require_once __DIR__ . '/../../config.php';
            require_once __DIR__ . '/../../database.php';
            
            $db = get_database();
            $result = $db->select('translation_cache', [
                'source_text' => $text,
                'source_lang' => $from_lang,
                'target_lang' => $to_lang
            ], 'created_at DESC', 1);
            
            if (!empty($result)) {
                return $result[0]['translated_text'];
            }
        } catch (Exception $e) {
            error_log("Cache read error: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Сохраняет перевод в кэш
     */
    private function saveCachedTranslation($source_text, $translated_text, $from_lang, $to_lang) {
        try {
            require_once __DIR__ . '/../../config.php';
            require_once __DIR__ . '/../../database.php';
            
            $db = get_database();
            
            // Проверяем, существует ли уже такой перевод
            $existing = $db->select('translation_cache', [
                'source_text' => $source_text,
                'source_lang' => $from_lang,
                'target_lang' => $to_lang
            ], 'created_at DESC', 1);
            
            if (empty($existing)) {
                $db->insert('translation_cache', [
                    'source_text' => $source_text,
                    'translated_text' => $translated_text,
                    'source_lang' => $from_lang,
                    'target_lang' => $to_lang,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }
        } catch (Exception $e) {
            error_log("Cache save error: " . $e->getMessage());
        }
    }
}
?>