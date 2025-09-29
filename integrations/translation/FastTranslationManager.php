<?php
/**
 * Быстрый менеджер переводов - только сохраненные переводы, без API вызовов
 */
class FastTranslationManager {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../../config.php';
        require_once __DIR__ . '/../../database.php';
        $this->db = get_database();
    }
    
    /**
     * Получить переведенный контент только из сохраненных переводов
     */
    public function getTranslatedContent($source_table, $source_id, $target_lang) {
        try {
            $translations = $this->db->select('translations', [
                'source_table' => $source_table,
                'source_id' => $source_id,
                'target_lang' => $target_lang
            ]);
            
            if (empty($translations)) {
                return null;
            }
            
            $result = [];
            foreach ($translations as $translation) {
                $field = $translation['source_field'];
                $result[$field] = $translation['translated_text'];
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("FastTranslationManager error: " . $e->getMessage());
            return null;
        }
    }
}
?>
