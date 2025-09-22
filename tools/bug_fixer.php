<?php
/**
 * Bug Fixer
 * Baumaster Tools - Automatic Bug Detection and Fixing
 */

require_once __DIR__ . '/../config.php';

class BugFixer {
    private $bugs_found = [];
    private $bugs_fixed = [];
    private $fixes_applied = 0;
    
    public function runBugFixer() {
        echo "<h1>🐛 Bug Fixer</h1>\n";
        echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;'>\n";
        
        $this->fixPhpErrors();
        $this->fixDatabaseIssues();
        $this->fixFilePermissions();
        $this->fixSecurityIssues();
        $this->fixPerformanceIssues();
        $this->fixUxIssues();
        
        $this->printBugReport();
        echo "</div>\n";
    }
    
    /**
     * Исправление PHP ошибок
     */
    private function fixPhpErrors() {
        echo "<h2>🐘 PHP Error Fixes</h2>\n";
        
        // Проверка синтаксиса PHP файлов
        $php_files = glob(__DIR__ . '/../**/*.php', GLOB_BRACE);
        
        foreach ($php_files as $file) {
            $output = [];
            $return_code = 0;
            exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $return_code);
            
            if ($return_code !== 0) {
                $this->bugs_found[] = "Syntax error in {$file}: " . implode(' ', $output);
                echo "❌ <span style='color: red;'>ERROR</span> - Syntax error in " . basename($file) . "\n";
            } else {
                echo "✅ <span style='color: green;'>OK</span> - " . basename($file) . " syntax is correct\n";
            }
        }
        
        // Проверка на неиспользуемые переменные
        $this->checkUnusedVariables();
        
        echo "<br>\n";
    }
    
    /**
     * Проверка неиспользуемых переменных
     */
    private function checkUnusedVariables() {
        $php_files = glob(__DIR__ . '/../**/*.php', GLOB_BRACE);
        
        foreach ($php_files as $file) {
            $content = file_get_contents($file);
            
            // Простая проверка на неиспользуемые переменные
            preg_match_all('/\$(\w+)\s*=/', $content, $matches);
            $variables = $matches[1];
            
            foreach ($variables as $var) {
                $usage_count = substr_count($content, '$' . $var);
                if ($usage_count === 1) {
                    $this->bugs_found[] = "Unused variable \${$var} in {$file}";
                    echo "⚠️ <span style='color: orange;'>WARNING</span> - Unused variable \${$var} in " . basename($file) . "\n";
                }
            }
        }
    }
    
    /**
     * Исправление проблем с базой данных
     */
    private function fixDatabaseIssues() {
        echo "<h2>🗄️ Database Issue Fixes</h2>\n";
        
        try {
            $db = get_database();
            
            // Проверка целостности базы данных
            $result = $db->query("PRAGMA integrity_check");
            $integrity = $result->fetch();
            
            if ($integrity && $integrity[0] !== 'ok') {
                $this->bugs_found[] = "Database integrity check failed: " . $integrity[0];
                echo "❌ <span style='color: red;'>ERROR</span> - Database integrity issue\n";
                
                // Попытка исправления
                $db->query("VACUUM");
                $this->bugs_fixed[] = "Database vacuumed to fix integrity";
                echo "🔧 <span style='color: blue;'>FIXED</span> - Database vacuumed\n";
            } else {
                echo "✅ <span style='color: green;'>OK</span> - Database integrity is good\n";
            }
            
            // Проверка на отсутствующие таблицы
            $required_tables = ['users', 'services', 'portfolio', 'reviews', 'blog_posts', 'settings'];
            foreach ($required_tables as $table) {
                $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$table}'");
                if (!$result->fetch()) {
                    $this->bugs_found[] = "Missing table: {$table}";
                    echo "❌ <span style='color: red;'>ERROR</span> - Missing table: {$table}\n";
                } else {
                    echo "✅ <span style='color: green;'>OK</span> - Table {$table} exists\n";
                }
            }
            
        } catch (Exception $e) {
            $this->bugs_found[] = "Database error: " . $e->getMessage();
            echo "❌ <span style='color: red;'>ERROR</span> - Database error: " . $e->getMessage() . "\n";
        }
        
        echo "<br>\n";
    }
    
    /**
     * Исправление проблем с правами доступа
     */
    private function fixFilePermissions() {
        echo "<h2>📁 File Permission Fixes</h2>\n";
        
        $directories = [
            DATA_PATH => 0755,
            ASSETS_PATH . 'images/' => 0755,
            ASSETS_PATH . 'css/' => 0755,
            ASSETS_PATH . 'js/' => 0755,
            __DIR__ . '/../admin/' => 0755
        ];
        
        foreach ($directories as $dir => $permission) {
            if (is_dir($dir)) {
                if (!is_writable($dir)) {
                    $this->bugs_found[] = "Directory not writable: {$dir}";
                    echo "❌ <span style='color: red;'>ERROR</span> - Directory not writable: " . basename($dir) . "\n";
                    
                    // Попытка исправления
                    if (chmod($dir, $permission)) {
                        $this->bugs_fixed[] = "Fixed permissions for {$dir}";
                        echo "🔧 <span style='color: blue;'>FIXED</span> - Permissions fixed for " . basename($dir) . "\n";
                    }
                } else {
                    echo "✅ <span style='color: green;'>OK</span> - " . basename($dir) . " permissions are correct\n";
                }
            } else {
                // Создание отсутствующей директории
                if (mkdir($dir, $permission, true)) {
                    $this->bugs_fixed[] = "Created missing directory: {$dir}";
                    echo "🔧 <span style='color: blue;'>FIXED</span> - Created missing directory: " . basename($dir) . "\n";
                } else {
                    $this->bugs_found[] = "Cannot create directory: {$dir}";
                    echo "❌ <span style='color: red;'>ERROR</span> - Cannot create directory: " . basename($dir) . "\n";
                }
            }
        }
        
        echo "<br>\n";
    }
    
    /**
     * Исправление проблем безопасности
     */
    private function fixSecurityIssues() {
        echo "<h2>🔒 Security Issue Fixes</h2>\n";
        
        // Проверка .htaccess файла
        $htaccess_file = __DIR__ . '/../.htaccess';
        if (!file_exists($htaccess_file)) {
            $this->bugs_found[] = ".htaccess file missing";
            echo "❌ <span style='color: red;'>ERROR</span> - .htaccess file missing\n";
            
            // Создание базового .htaccess
            $basic_htaccess = "# Security Headers\n<IfModule mod_headers.c>\n    Header always set X-Content-Type-Options nosniff\n    Header always set X-Frame-Options DENY\n    Header always set X-XSS-Protection \"1; mode=block\"\n</IfModule>\n\n# Hide sensitive files\n<Files ~ \"\\.(db|log|json)$\">\n    Order allow,deny\n    Deny from all\n</Files>\n";
            
            if (file_put_contents($htaccess_file, $basic_htaccess)) {
                $this->bugs_fixed[] = "Created basic .htaccess file";
                echo "🔧 <span style='color: blue;'>FIXED</span> - Created basic .htaccess file\n";
            }
        } else {
            echo "✅ <span style='color: green;'>OK</span> - .htaccess file exists\n";
        }
        
        // Проверка конфигурационных файлов
        $config_files = [
            __DIR__ . '/../config.php',
            __DIR__ . '/../database.php',
            __DIR__ . '/../functions.php'
        ];
        
        foreach ($config_files as $file) {
            if (file_exists($file)) {
                $content = file_get_contents($file);
                
                // Проверка на отладочную информацию
                if (strpos($content, 'var_dump') !== false || strpos($content, 'print_r') !== false) {
                    $this->bugs_found[] = "Debug code found in " . basename($file);
                    echo "⚠️ <span style='color: orange;'>WARNING</span> - Debug code found in " . basename($file) . "\n";
                } else {
                    echo "✅ <span style='color: green;'>OK</span> - " . basename($file) . " is clean\n";
                }
            }
        }
        
        echo "<br>\n";
    }
    
    /**
     * Исправление проблем производительности
     */
    private function fixPerformanceIssues() {
        echo "<h2>⚡ Performance Issue Fixes</h2>\n";
        
        // Проверка на неоптимизированные запросы
        $php_files = glob(__DIR__ . '/../**/*.php', GLOB_BRACE);
        
        foreach ($php_files as $file) {
            $content = file_get_contents($file);
            
            // Проверка на SELECT * запросы
            if (preg_match('/SELECT\s+\*\s+FROM/i', $content)) {
                $this->bugs_found[] = "SELECT * query found in " . basename($file);
                echo "⚠️ <span style='color: orange;'>WARNING</span> - SELECT * query in " . basename($file) . "\n";
            }
            
            // Проверка на отсутствие LIMIT в запросах
            if (preg_match('/SELECT.*FROM(?!.*LIMIT)/i', $content)) {
                $this->bugs_found[] = "Query without LIMIT found in " . basename($file);
                echo "⚠️ <span style='color: orange;'>WARNING</span> - Query without LIMIT in " . basename($file) . "\n";
            }
        }
        
        // Проверка размера логов
        $log_files = glob(__DIR__ . '/../**/*.log', GLOB_BRACE);
        foreach ($log_files as $log_file) {
            $size = filesize($log_file);
            if ($size > 10 * 1024 * 1024) { // Больше 10MB
                $this->bugs_found[] = "Large log file: " . basename($log_file) . " (" . format_file_size($size) . ")";
                echo "⚠️ <span style='color: orange;'>WARNING</span> - Large log file: " . basename($log_file) . "\n";
                
                // Очистка лога
                if (file_put_contents($log_file, '')) {
                    $this->bugs_fixed[] = "Cleared large log file: " . basename($log_file);
                    echo "🔧 <span style='color: blue;'>FIXED</span> - Cleared large log file\n";
                }
            }
        }
        
        echo "<br>\n";
    }
    
    /**
     * Исправление UX проблем
     */
    private function fixUxIssues() {
        echo "<h2>🎨 UX Issue Fixes</h2>\n";
        
        // Проверка на отсутствующие alt атрибуты
        $html_files = glob(__DIR__ . '/../**/*.php', GLOB_BRACE);
        
        foreach ($html_files as $file) {
            $content = file_get_contents($file);
            
            // Поиск img тегов без alt
            if (preg_match('/<img[^>]*(?!alt=)[^>]*>/i', $content)) {
                $this->bugs_found[] = "Image without alt attribute in " . basename($file);
                echo "⚠️ <span style='color: orange;'>WARNING</span> - Image without alt in " . basename($file) . "\n";
            }
            
            // Поиск ссылок без title
            if (preg_match('/<a[^>]*(?!title=)[^>]*href/i', $content)) {
                $this->bugs_found[] = "Link without title attribute in " . basename($file);
                echo "⚠️ <span style='color: orange;'>WARNING</span> - Link without title in " . basename($file) . "\n";
            }
        }
        
        echo "<br>\n";
    }
    
    /**
     * Вывод отчета об исправлениях
     */
    private function printBugReport() {
        echo "<h2>📊 Bug Fix Report</h2>\n";
        echo "Bugs found: " . count($this->bugs_found) . "<br>\n";
        echo "Bugs fixed: " . count($this->bugs_fixed) . "<br>\n";
        echo "Fixes applied: " . $this->fixes_applied . "<br>\n";
        
        if (!empty($this->bugs_found)) {
            echo "<h3>🐛 Bugs Found:</h3>\n";
            foreach ($this->bugs_found as $bug) {
                echo "• " . htmlspecialchars($bug) . "<br>\n";
            }
        }
        
        if (!empty($this->bugs_fixed)) {
            echo "<h3>🔧 Bugs Fixed:</h3>\n";
            foreach ($this->bugs_fixed as $fix) {
                echo "• " . htmlspecialchars($fix) . "<br>\n";
            }
        }
        
        if (empty($this->bugs_found)) {
            echo "<h3 style='color: green;'>🎉 No bugs found! System is clean.</h3>\n";
        } else {
            echo "<h3 style='color: orange;'>⚠️ Some issues need manual attention.</h3>\n";
        }
    }
}

// Запуск исправления багов если файл вызван напрямую
if (basename($_SERVER['PHP_SELF']) === 'bug_fixer.php') {
    $bug_fixer = new BugFixer();
    $bug_fixer->runBugFixer();
}
?>

