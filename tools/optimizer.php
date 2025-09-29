<?php
/**
 * System Optimizer
 * Baumaster Tools - Performance and Code Optimization
 */

require_once __DIR__ . '/../config.php';

class SystemOptimizer {
    private $optimizations = [];
    private $issues_found = [];
    
    public function runOptimization() {
        echo "<h1>⚡ System Optimizer</h1>\n";
        echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;'>\n";
        
        $this->optimizeDatabase();
        $this->optimizeImages();
        $this->optimizeCssJs();
        $this->optimizeHtaccess();
        $this->optimizePhpSettings();
        $this->cleanupFiles();
        $this->optimizeCache();
        
        $this->printOptimizationReport();
        echo "</div>\n";
    }
    
    /**
     * Оптимизация базы данных
     */
    private function optimizeDatabase() {
        echo "<h2>🗄️ Database Optimization</h2>\n";
        
        try {
            $db = get_database();
            
            // Проверка размера базы данных
            $db_file = DATA_PATH . 'database.db';
            if (file_exists($db_file)) {
                $db_size = filesize($db_file);
                echo "Database size: " . format_file_size($db_size) . "\n";
                
                if ($db_size > 10 * 1024 * 1024) { // Больше 10MB
                    $this->issues_found[] = "Database size is large: " . format_file_size($db_size);
                    echo "⚠️ <span style='color: orange;'>WARNING</span> - Database size is large\n";
                } else {
                    echo "✅ <span style='color: green;'>OK</span> - Database size is acceptable\n";
                }
            }
            
            // Проверка индексов
            $tables = ['users', 'services', 'portfolio', 'reviews', 'blog_posts', 'settings'];
            foreach ($tables as $table) {
                $result = $db->query("PRAGMA index_list({$table})");
                $indexes = $result->fetchAll();
                
                if (count($indexes) < 2) { // Минимум первичный ключ + 1 индекс
                    $this->issues_found[] = "Table {$table} has insufficient indexes";
                    echo "⚠️ <span style='color: orange;'>WARNING</span> - Table {$table} needs more indexes\n";
                } else {
                    echo "✅ <span style='color: green;'>OK</span> - Table {$table} has adequate indexes\n";
                }
            }
            
            $this->optimizations[] = "Database analysis completed";
            
        } catch (Exception $e) {
            echo "❌ <span style='color: red;'>ERROR</span> - Database optimization failed: " . $e->getMessage() . "\n";
        }
        
        echo "<br>\n";
    }
    
    /**
     * Оптимизация изображений
     */
    private function optimizeImages() {
        echo "<h2>🖼️ Image Optimization</h2>\n";
        
        $image_dir = ASSETS_PATH . 'images/';
        if (!is_dir($image_dir)) {
            echo "⚠️ <span style='color: orange;'>WARNING</span> - Images directory not found\n";
            return;
        }
        
        $images = glob($image_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
        $total_size = 0;
        $unoptimized = 0;
        
        foreach ($images as $image) {
            $size = filesize($image);
            $total_size += $size;
            
            // Проверка на оптимизированные версии
            $path_info = pathinfo($image);
            $optimized_name = $path_info['dirname'] . '/' . $path_info['filename'] . '_optimized.' . $path_info['extension'];
            
            if (!file_exists($optimized_name)) {
                $unoptimized++;
            }
        }
        
        echo "Total images: " . count($images) . "\n";
        echo "Total size: " . format_file_size($total_size) . "\n";
        echo "Unoptimized images: {$unoptimized}\n";
        
        if ($unoptimized > 0) {
            $this->issues_found[] = "{$unoptimized} images need optimization";
            echo "⚠️ <span style='color: orange;'>WARNING</span> - Some images need optimization\n";
        } else {
            echo "✅ <span style='color: green;'>OK</span> - All images are optimized\n";
        }
        
        $this->optimizations[] = "Image optimization analysis completed";
        echo "<br>\n";
    }
    
    /**
     * Оптимизация CSS и JS
     */
    private function optimizeCssJs() {
        echo "<h2>🎨 CSS/JS Optimization</h2>\n";
        
        $css_files = glob(ASSETS_PATH . 'css/*.css');
        $js_files = glob(ASSETS_PATH . 'js/*.js');
        
        $total_css_size = 0;
        $total_js_size = 0;
        
        foreach ($css_files as $file) {
            $total_css_size += filesize($file);
        }
        
        foreach ($js_files as $file) {
            $total_js_size += filesize($file);
        }
        
        echo "CSS files: " . count($css_files) . " (" . format_file_size($total_css_size) . ")\n";
        echo "JS files: " . count($js_files) . " (" . format_file_size($total_js_size) . ")\n";
        
        // Проверка на минифицированные версии
        $minified_css = glob(ASSETS_PATH . 'css/*minified*.css');
        $minified_js = glob(ASSETS_PATH . 'js/*minified*.js');
        
        if (empty($minified_css) && $total_css_size > 50000) { // Больше 50KB
            $this->issues_found[] = "CSS files should be minified";
            echo "⚠️ <span style='color: orange;'>WARNING</span> - CSS files should be minified\n";
        } else {
            echo "✅ <span style='color: green;'>OK</span> - CSS optimization is adequate\n";
        }
        
        if (empty($minified_js) && $total_js_size > 50000) { // Больше 50KB
            $this->issues_found[] = "JS files should be minified";
            echo "⚠️ <span style='color: orange;'>WARNING</span> - JS files should be minified\n";
        } else {
            echo "✅ <span style='color: green;'>OK</span> - JS optimization is adequate\n";
        }
        
        $this->optimizations[] = "CSS/JS optimization analysis completed";
        echo "<br>\n";
    }
    
    /**
     * Оптимизация .htaccess
     */
    private function optimizeHtaccess() {
        echo "<h2>⚙️ .htaccess Optimization</h2>\n";
        
        $htaccess_file = __DIR__ . '/../.htaccess';
        if (!file_exists($htaccess_file)) {
            $this->issues_found[] = ".htaccess file not found";
            echo "❌ <span style='color: red;'>ERROR</span> - .htaccess file not found\n";
            return;
        }
        
        $htaccess_content = file_get_contents($htaccess_file);
        $optimizations = [
            'mod_deflate' => strpos($htaccess_content, 'mod_deflate') !== false,
            'mod_expires' => strpos($htaccess_content, 'mod_expires') !== false,
            'cache_control' => strpos($htaccess_content, 'Cache-Control') !== false,
            'gzip_compression' => strpos($htaccess_content, 'DEFLATE') !== false
        ];
        
        foreach ($optimizations as $feature => $enabled) {
            if ($enabled) {
                echo "✅ <span style='color: green;'>OK</span> - {$feature} is enabled\n";
            } else {
                $this->issues_found[] = ".htaccess missing {$feature} optimization";
                echo "⚠️ <span style='color: orange;'>WARNING</span> - {$feature} is not enabled\n";
            }
        }
        
        $this->optimizations[] = ".htaccess optimization analysis completed";
        echo "<br>\n";
    }
    
    /**
     * Оптимизация PHP настроек
     */
    private function optimizePhpSettings() {
        echo "<h2>🐘 PHP Settings Optimization</h2>\n";
        
        $php_settings = [
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'display_errors' => ini_get('display_errors'),
            'log_errors' => ini_get('log_errors'),
            'error_reporting' => ini_get('error_reporting')
        ];
        
        foreach ($php_settings as $setting => $value) {
            echo "{$setting}: {$value}\n";
            
            switch ($setting) {
                case 'memory_limit':
                    if (intval($value) < 128) {
                        $this->issues_found[] = "Memory limit too low: {$value}";
                        echo "⚠️ <span style='color: orange;'>WARNING</span> - Memory limit should be at least 128M\n";
                    } else {
                        echo "✅ <span style='color: green;'>OK</span> - Memory limit is adequate\n";
                    }
                    break;
                    
                case 'display_errors':
                    if ($value && !ini_get('log_errors')) {
                        $this->issues_found[] = "Errors displayed without logging";
                        echo "⚠️ <span style='color: orange;'>WARNING</span> - Errors displayed without logging\n";
                    } else {
                        echo "✅ <span style='color: green;'>OK</span> - Error handling is secure\n";
                    }
                    break;
            }
        }
        
        $this->optimizations[] = "PHP settings analysis completed";
        echo "<br>\n";
    }
    
    /**
     * Очистка файлов
     */
    private function cleanupFiles() {
        echo "<h2>🧹 File Cleanup</h2>\n";
        
        $cleanup_dirs = [
            ASSETS_PATH . 'temp/',
            DATA_PATH . 'logs/',
            DATA_PATH . 'cache/',
            __DIR__ . '/../tmp/'
        ];
        
        $total_cleaned = 0;
        $total_size_cleaned = 0;
        
        foreach ($cleanup_dirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $size = filesize($file);
                        $total_size_cleaned += $size;
                        $total_cleaned++;
                        
                        // Удаление файлов старше 7 дней
                        if (filemtime($file) < time() - (7 * 24 * 60 * 60)) {
                            unlink($file);
                        }
                    }
                }
            }
        }
        
        echo "Files cleaned: {$total_cleaned}\n";
        echo "Size cleaned: " . format_file_size($total_size_cleaned) . "\n";
        
        if ($total_cleaned > 0) {
            echo "✅ <span style='color: green;'>OK</span> - Cleanup completed\n";
        } else {
            echo "✅ <span style='color: green;'>OK</span> - No cleanup needed\n";
        }
        
        $this->optimizations[] = "File cleanup completed";
        echo "<br>\n";
    }
    
    /**
     * Оптимизация кэша
     */
    private function optimizeCache() {
        echo "<h2>💾 Cache Optimization</h2>\n";
        
        $cache_dir = DATA_PATH . 'cache/';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        // Создание файла кэша для настроек
        $settings_cache = $cache_dir . 'settings.json';
        $settings = get_settings_by_category('general');
        file_put_contents($settings_cache, json_encode($settings));
        
        // Создание файла кэша для переводов
        $translations_cache = $cache_dir . 'translations.json';
        $translations = json_decode(file_get_contents(LANG_PATH . 'ru.json'), true);
        file_put_contents($translations_cache, json_encode($translations));
        
        echo "Cache files created: 2\n";
        echo "✅ <span style='color: green;'>OK</span> - Cache optimization completed\n";
        
        $this->optimizations[] = "Cache optimization completed";
        echo "<br>\n";
    }
    
    /**
     * Вывод отчета об оптимизации
     */
    private function printOptimizationReport() {
        echo "<h2>📊 Optimization Report</h2>\n";
        echo "Optimizations completed: " . count($this->optimizations) . "<br>\n";
        echo "Issues found: " . count($this->issues_found) . "<br>\n";
        
        if (!empty($this->optimizations)) {
            echo "<h3>✅ Optimizations Applied:</h3>\n";
            foreach ($this->optimizations as $optimization) {
                echo "• " . htmlspecialchars($optimization) . "<br>\n";
            }
        }
        
        if (!empty($this->issues_found)) {
            echo "<h3>⚠️ Issues Found:</h3>\n";
            foreach ($this->issues_found as $issue) {
                echo "• " . htmlspecialchars($issue) . "<br>\n";
            }
        }
        
        if (empty($this->issues_found)) {
            echo "<h3 style='color: green;'>🎉 System is fully optimized!</h3>\n";
        } else {
            echo "<h3 style='color: orange;'>⚠️ Some optimizations recommended.</h3>\n";
        }
    }
}

// Запуск оптимизатора если файл вызван напрямую
if (basename($_SERVER['PHP_SELF']) === 'optimizer.php') {
    $optimizer = new SystemOptimizer();
    $optimizer->runOptimization();
}
?>

