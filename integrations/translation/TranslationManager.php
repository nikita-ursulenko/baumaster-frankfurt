<?php
/**
 * Менеджер переводов
 * Управление автоматическими переводами и кэшированием
 */

require_once __DIR__ . '/TranslationService.php';

class TranslationManager {
    private $db;
    private $translation_service;
    private $cache_enabled = true;
    
    public function __construct() {
        require_once __DIR__ . '/../../config.php';
        require_once __DIR__ . '/../../database.php';
        $this->db = get_database();
        $this->translation_service = new TranslationService();
    }
    
    /**
     * Автоматический перевод контента при создании/обновлении
     */
    public function autoTranslateContent($table, $id, $fields, $from_lang = 'ru', $to_lang = 'de') {
        $translated_fields = [];
        
        foreach ($fields as $field => $text) {
            if (empty($text) || strlen(trim($text)) < 2) {
                continue;
            }
            
            // Проверяем, есть ли уже перевод
            $existing = $this->getTranslation($table, $id, $field, $to_lang);
            if ($existing && $existing['translated_text'] === $text) {
                $translated_fields[$field] = $existing['translated_text'];
                continue;
            }
            
            // Специальная обработка для JSON контента (about_content)
            if ($field === 'content' && $table === 'about_content' && is_string($text)) {
                $decoded_content = json_decode($text, true);
                if (is_array($decoded_content)) {
                    $translated_content = $this->translateJsonContent($decoded_content, $from_lang, $to_lang);
                    if ($translated_content) {
                        $translated_text = json_encode($translated_content);
                        $this->saveTranslation($table, $id, $field, $text, $translated_text, $from_lang, $to_lang);
                        $translated_fields[$field] = $translated_text;
                        continue;
                    }
                }
            }
            
            // Переводим текст
            try {
                $translated_text = $this->translation_service->translate($text, $from_lang, $to_lang);
                
                if ($translated_text && $translated_text !== $text) {
                    // Сохраняем перевод в БД
                    $this->saveTranslation($table, $id, $field, $text, $translated_text, $from_lang, $to_lang);
                    $translated_fields[$field] = $translated_text;
                } else {
                    $translated_fields[$field] = $text; // Если перевод не удался, используем исходный текст
                }
            } catch (Exception $e) {
                write_log("Translation failed for $table.$field: " . $e->getMessage(), 'ERROR');
                $translated_fields[$field] = $text;
            }
        }
        
        return $translated_fields;
    }
    
    /**
     * Перевод JSON контента (для about_content)
     */
    private function translateJsonContent($content, $from_lang = 'ru', $to_lang = 'de') {
        $translated_content = [];
        
        foreach ($content as $key => $value) {
            if (is_string($value) && !empty(trim($value))) {
                try {
                    $translated_value = $this->translation_service->translate($value, $from_lang, $to_lang);
                    $translated_content[$key] = $translated_value ?: $value;
                } catch (Exception $e) {
                    write_log("JSON translation failed for key $key: " . $e->getMessage(), 'ERROR');
                    $translated_content[$key] = $value;
                }
            } else {
                $translated_content[$key] = $value;
            }
        }
        
        return $translated_content;
    }
    
    /**
     * Получение переведенного контента
     */
    public function getTranslatedContent($table, $id, $lang = 'de', $fields = null) {
        $translations = $this->getTranslations($table, $id, $lang);
        
        if (empty($translations)) {
            return null;
        }
        
        $result = [];
        foreach ($translations as $translation) {
            $result[$translation['source_field']] = $translation['translated_text'];
        }
        
        // Если указаны конкретные поля, возвращаем только их
        if ($fields && is_array($fields)) {
            $filtered = [];
            foreach ($fields as $field) {
                if (isset($result[$field])) {
                    $filtered[$field] = $result[$field];
                }
            }
            return $filtered;
        }
        
        return $result;
    }
    
    /**
     * Сохранение перевода в БД
     */
    public function saveTranslation($table, $id, $field, $source_text, $translated_text, $from_lang = 'ru', $to_lang = 'de', $service = 'libretranslate') {
        try {
            // Проверяем, существует ли уже перевод
            $existing = $this->getTranslation($table, $id, $field, $to_lang);
            
            $translation_data = [
                'source_table' => $table,
                'source_id' => $id,
                'source_field' => $field,
                'source_lang' => $from_lang,
                'target_lang' => $to_lang,
                'source_text' => $source_text,
                'translated_text' => $translated_text,
                'translation_service' => $service,
                'confidence' => 0.95, // Примерная уверенность
                'auto_translated' => 1
            ];
            
            if ($existing) {
                // Обновляем существующий перевод
                $this->db->update('translations', $translation_data, [
                    'source_table' => $table,
                    'source_id' => $id,
                    'source_field' => $field,
                    'target_lang' => $to_lang
                ]);
            } else {
                // Создаем новый перевод
                $this->db->insert('translations', $translation_data);
            }
            
            write_log("Translation saved: $table.$field (ID: $id)", 'INFO');
            return true;
            
        } catch (Exception $e) {
            write_log("Error saving translation: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * Получение конкретного перевода
     */
    public function getTranslation($table, $id, $field, $lang = 'de') {
        $result = $this->db->select('translations', [
            'source_table' => $table,
            'source_id' => $id,
            'source_field' => $field,
            'target_lang' => $lang
        ], ['limit' => 1]);
        
        return $result ?: null;
    }
    
    /**
     * Получение всех переводов для записи
     */
    public function getTranslations($table, $id, $lang = 'de') {
        return $this->db->select('translations', [
            'source_table' => $table,
            'source_id' => $id,
            'target_lang' => $lang
        ]);
    }
    
    /**
     * Обновление перевода вручную
     */
    public function updateTranslation($table, $id, $field, $new_translation, $lang = 'de') {
        try {
            $result = $this->db->update('translations', [
                'translated_text' => $new_translation,
                'auto_translated' => 0, // Помечаем как ручной перевод
                'updated_at' => date('Y-m-d H:i:s')
            ], [
                'source_table' => $table,
                'source_id' => $id,
                'source_field' => $field,
                'target_lang' => $lang
            ]);
            
            if ($result) {
                write_log("Translation updated manually: $table.$field (ID: $id)", 'INFO');
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            write_log("Error updating translation: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * Удаление перевода
     */
    public function deleteTranslation($table, $id, $field = null, $lang = 'de') {
        try {
            $where = [
                'source_table' => $table,
                'source_id' => $id,
                'target_lang' => $lang
            ];
            
            if ($field) {
                $where['source_field'] = $field;
            }
            
            $result = $this->db->delete('translations', $where);
            
            if ($result) {
                write_log("Translation deleted: $table" . ($field ? ".$field" : "") . " (ID: $id)", 'INFO');
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            write_log("Error deleting translation: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
    
    /**
     * Пакетный перевод для существующих записей
     */
    public function batchTranslateExisting($table, $ids = null, $fields = null, $from_lang = 'ru', $to_lang = 'de') {
        try {
            // Получаем записи для перевода
            $where = [];
            if ($ids) {
                $where['id'] = is_array($ids) ? $ids : [$ids];
            }
            
            $records = $this->db->select($table, $where);
            if (empty($records)) {
                return ['success' => false, 'message' => 'No records found'];
            }
            
            $translated_count = 0;
            $error_count = 0;
            
            foreach ($records as $record) {
                $fields_to_translate = $fields ?: $this->getTranslatableFields($table);
                $field_data = [];
                
                foreach ($fields_to_translate as $field) {
                    if (isset($record[$field]) && !empty($record[$field])) {
                        $field_data[$field] = $record[$field];
                    }
                }
                
                if (!empty($field_data)) {
                    $result = $this->autoTranslateContent($table, $record['id'], $field_data, $from_lang, $to_lang);
                    if (!empty($result)) {
                        $translated_count++;
                    } else {
                        $error_count++;
                    }
                }
            }
            
            return [
                'success' => true,
                'translated_count' => $translated_count,
                'error_count' => $error_count,
                'total_records' => count($records)
            ];
            
        } catch (Exception $e) {
            write_log("Batch translation error: " . $e->getMessage(), 'ERROR');
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    /**
     * Получение полей, которые можно переводить
     */
    private function getTranslatableFields($table) {
        $translatable_fields = [
            'services' => ['title', 'description', 'meta_title', 'meta_description', 'keywords'],
            'portfolio' => ['title', 'description', 'meta_title', 'meta_description'],
            'blog_posts' => ['title', 'excerpt', 'content', 'meta_title', 'meta_description', 'keywords'],
            'reviews' => ['review_text'],
            'faq' => ['question', 'answer'],
            'about_content' => ['title', 'content'],
            'team_members' => ['name', 'position', 'description'],
            'statistics' => ['label', 'description']
        ];
        
        return $translatable_fields[$table] ?? [];
    }
    
    /**
     * Получение статистики переводов
     */
    public function getTranslationStats() {
        try {
            $stats = [];
            
            // Общее количество переводов
            $total_translations = $this->db->select('translations', [], ['limit' => 1]);
            $stats['total_translations'] = count($this->db->select('translations'));
            
            // Переводы по таблицам
            $tables = ['services', 'portfolio', 'blog_posts', 'reviews', 'faq', 'about_content', 'team_members', 'statistics'];
            foreach ($tables as $table) {
                $table_translations = $this->db->select('translations', ['source_table' => $table]);
                $stats['by_table'][$table] = count($table_translations);
            }
            
            // Автоматические vs ручные переводы
            $auto_translations = $this->db->select('translations', ['auto_translated' => 1]);
            $manual_translations = $this->db->select('translations', ['auto_translated' => 0]);
            
            $stats['auto_translations'] = count($auto_translations);
            $stats['manual_translations'] = count($manual_translations);
            
            return $stats;
            
        } catch (Exception $e) {
            write_log("Error getting translation stats: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }
    
    /**
     * Очистка старых переводов
     */
    public function cleanupOldTranslations($days = 30) {
        try {
            $cutoff_date = date('Y-m-d H:i:s', strtotime("-$days days"));
            
            $result = $this->db->delete('translations', [
                'created_at <' => $cutoff_date,
                'auto_translated' => 1
            ]);
            
            if ($result) {
                write_log("Cleaned up old translations older than $days days", 'INFO');
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            write_log("Error cleaning up translations: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }
}
