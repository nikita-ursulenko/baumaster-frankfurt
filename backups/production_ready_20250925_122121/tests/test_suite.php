<?php
/**
 * Test Suite
 * Baumaster Testing - Comprehensive Test Suite
 */

require_once __DIR__ . '/../config.php';

class TestSuite {
    private $tests = [];
    private $results = [];
    private $passed = 0;
    private $failed = 0;
    
    public function __construct() {
        $this->setupTests();
    }
    
    /**
     * Настройка тестов
     */
    private function setupTests() {
        $this->tests = [
            'database_connection' => 'testDatabaseConnection',
            'authentication' => 'testAuthentication',
            'crud_operations' => 'testCrudOperations',
            'form_validation' => 'testFormValidation',
            'security' => 'testSecurity',
            'file_permissions' => 'testFilePermissions',
            'email_functionality' => 'testEmailFunctionality',
            'seo_functions' => 'testSeoFunctions',
            'image_optimization' => 'testImageOptimization',
            'performance' => 'testPerformance'
        ];
    }
    
    /**
     * Запуск всех тестов
     */
    public function runAllTests() {
        echo "<h1>🧪 Baumaster Test Suite</h1>\n";
        echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;'>\n";
        
        foreach ($this->tests as $test_name => $test_method) {
            echo "<h2>🔍 Testing: " . ucfirst(str_replace('_', ' ', $test_name)) . "</h2>\n";
            $this->runTest($test_name, $test_method);
        }
        
        $this->printSummary();
        echo "</div>\n";
    }
    
    /**
     * Запуск отдельного теста
     */
    private function runTest($test_name, $test_method) {
        try {
            $start_time = microtime(true);
            $result = $this->$test_method();
            $end_time = microtime(true);
            $duration = round(($end_time - $start_time) * 1000, 2);
            
            if ($result['success']) {
                echo "✅ <span style='color: green;'>PASSED</span> - {$result['message']} ({$duration}ms)\n";
                $this->passed++;
            } else {
                echo "❌ <span style='color: red;'>FAILED</span> - {$result['message']} ({$duration}ms)\n";
                if (isset($result['details'])) {
                    echo "   Details: {$result['details']}\n";
                }
                $this->failed++;
            }
            
            $this->results[$test_name] = $result;
            
        } catch (Exception $e) {
            echo "💥 <span style='color: red;'>ERROR</span> - Exception: " . $e->getMessage() . "\n";
            $this->failed++;
            $this->results[$test_name] = [
                'success' => false,
                'message' => 'Exception occurred',
                'details' => $e->getMessage()
            ];
        }
        
        echo "<br>\n";
    }
    
    /**
     * Тест подключения к базе данных
     */
    private function testDatabaseConnection() {
        try {
            $db = get_database();
            $result = $db->select('users', [], ['limit' => 1]);
            
            return [
                'success' => true,
                'message' => 'Database connection successful'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Тест системы авторизации
     */
    private function testAuthentication() {
        try {
            // Тест функции хеширования пароля
            $password = 'test123';
            $hash = hash_password($password);
            
            if (empty($hash)) {
                return [
                    'success' => false,
                    'message' => 'Password hashing failed'
                ];
            }
            
            // Тест проверки пароля
            if (!verify_password($password, $hash)) {
                return [
                    'success' => false,
                    'message' => 'Password verification failed'
                ];
            }
            
            // Тест CSRF токена
            $token = generate_csrf_token();
            if (empty($token)) {
                return [
                    'success' => false,
                    'message' => 'CSRF token generation failed'
                ];
            }
            
            if (!verify_csrf_token($token)) {
                return [
                    'success' => false,
                    'message' => 'CSRF token verification failed'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Authentication system working correctly'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Authentication test failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Тест CRUD операций
     */
    private function testCrudOperations() {
        try {
            $db = get_database();
            
            // Тест создания записи
            $test_data = [
                'title' => 'Test Service',
                'description' => 'Test Description',
                'price' => '100',
                'status' => 'active'
            ];
            
            $id = $db->insert('services', $test_data);
            if (!$id) {
                return [
                    'success' => false,
                    'message' => 'Create operation failed'
                ];
            }
            
            // Тест чтения записи
            $record = $db->select('services', ['id' => $id], ['limit' => 1]);
            if (empty($record)) {
                return [
                    'success' => false,
                    'message' => 'Read operation failed'
                ];
            }
            
            // Тест обновления записи
            $update_data = ['title' => 'Updated Test Service'];
            $updated = $db->update('services', $update_data, ['id' => $id]);
            if (!$updated) {
                return [
                    'success' => false,
                    'message' => 'Update operation failed'
                ];
            }
            
            // Тест удаления записи
            $deleted = $db->delete('services', ['id' => $id]);
            if (!$deleted) {
                return [
                    'success' => false,
                    'message' => 'Delete operation failed'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'All CRUD operations working correctly'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'CRUD operations test failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Тест валидации форм
     */
    private function testFormValidation() {
        try {
            // Тест валидации email
            $valid_emails = ['test@example.com', 'user@domain.org', 'admin@site.de'];
            $invalid_emails = ['invalid-email', '@domain.com', 'user@', 'user.domain.com'];
            
            foreach ($valid_emails as $email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return [
                        'success' => false,
                        'message' => 'Valid email validation failed',
                        'details' => "Email: {$email}"
                    ];
                }
            }
            
            foreach ($invalid_emails as $email) {
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return [
                        'success' => false,
                        'message' => 'Invalid email validation failed',
                        'details' => "Email: {$email}"
                    ];
                }
            }
            
            // Тест санитизации данных
            $dirty_input = '<script>alert("xss")</script>Hello World';
            $clean_input = sanitize_input($dirty_input);
            
            if ($clean_input !== 'Hello World') {
                return [
                    'success' => false,
                    'message' => 'Input sanitization failed',
                    'details' => "Input: {$dirty_input}, Output: {$clean_input}"
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Form validation working correctly'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Form validation test failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Тест безопасности
     */
    private function testSecurity() {
        try {
            // Тест SQL инъекций
            $malicious_input = "'; DROP TABLE users; --";
            $sanitized = sanitize_input($malicious_input);
            
            if (strpos($sanitized, 'DROP') !== false) {
                return [
                    'success' => false,
                    'message' => 'SQL injection protection failed'
                ];
            }
            
            // Тест XSS защиты
            $xss_input = '<script>alert("XSS")</script><img src=x onerror=alert("XSS")>';
            $xss_cleaned = sanitize_input($xss_input);
            
            if (strpos($xss_cleaned, '<script>') !== false || strpos($xss_cleaned, 'onerror') !== false) {
                return [
                    'success' => false,
                    'message' => 'XSS protection failed'
                ];
            }
            
            // Тест CSRF защиты
            $token1 = generate_csrf_token();
            $token2 = generate_csrf_token();
            
            if ($token1 === $token2) {
                return [
                    'success' => false,
                    'message' => 'CSRF token uniqueness failed'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Security measures working correctly'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Security test failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Тест прав доступа к файлам
     */
    private function testFilePermissions() {
        try {
            $directories = [
                DATA_PATH,
                ASSETS_PATH . 'images/',
                ASSETS_PATH . 'css/',
                ASSETS_PATH . 'js/'
            ];
            
            foreach ($directories as $dir) {
                if (!is_dir($dir)) {
                    if (!mkdir($dir, 0755, true)) {
                        return [
                            'success' => false,
                            'message' => "Cannot create directory: {$dir}"
                        ];
                    }
                }
                
                if (!is_writable($dir)) {
                    return [
                        'success' => false,
                        'message' => "Directory not writable: {$dir}"
                    ];
                }
            }
            
            return [
                'success' => true,
                'message' => 'File permissions are correct'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'File permissions test failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Тест email функциональности
     */
    private function testEmailFunctionality() {
        try {
            // Проверка функции отправки email
            if (!function_exists('mail')) {
                return [
                    'success' => false,
                    'message' => 'PHP mail function not available'
                ];
            }
            
            // Тест генерации email заголовков
            $headers = [];
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'From: test@example.com';
            
            if (empty($headers)) {
                return [
                    'success' => false,
                    'message' => 'Email headers generation failed'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Email functionality is available'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email functionality test failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Тест SEO функций
     */
    private function testSeoFunctions() {
        try {
            // Тест генерации slug
            $title = 'Test Article Title';
            $slug = generate_slug($title);
            
            if (empty($slug) || strpos($slug, ' ') !== false) {
                return [
                    'success' => false,
                    'message' => 'Slug generation failed'
                ];
            }
            
            // Тест генерации мета-тегов
            $meta_tags = generate_meta_tags([
                'title' => 'Test Title',
                'description' => 'Test Description'
            ]);
            
            if (empty($meta_tags) || strpos($meta_tags, '<title>') === false) {
                return [
                    'success' => false,
                    'message' => 'Meta tags generation failed'
                ];
            }
            
            return [
                'success' => true,
                'message' => 'SEO functions working correctly'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'SEO functions test failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Тест оптимизации изображений
     */
    private function testImageOptimization() {
        try {
            // Проверка наличия GD расширения
            if (!extension_loaded('gd')) {
                return [
                    'success' => false,
                    'message' => 'GD extension not loaded'
                ];
            }
            
            // Проверка функций изображений
            $functions = ['imagecreatefromjpeg', 'imagecreatefrompng', 'imagejpeg', 'imagepng'];
            foreach ($functions as $func) {
                if (!function_exists($func)) {
                    return [
                        'success' => false,
                        'message' => "Image function not available: {$func}"
                    ];
                }
            }
            
            return [
                'success' => true,
                'message' => 'Image optimization functions available'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Image optimization test failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Тест производительности
     */
    private function testPerformance() {
        try {
            $start_time = microtime(true);
            
            // Тест времени загрузки страницы
            $db = get_database();
            $services = $db->select('services', [], ['limit' => 10]);
            
            $end_time = microtime(true);
            $load_time = ($end_time - $start_time) * 1000;
            
            if ($load_time > 1000) { // Больше 1 секунды
                return [
                    'success' => false,
                    'message' => "Page load time too slow: {$load_time}ms"
                ];
            }
            
            // Тест использования памяти
            $memory_usage = memory_get_usage(true);
            $memory_limit = ini_get('memory_limit');
            
            return [
                'success' => true,
                'message' => "Performance acceptable (Load: {$load_time}ms, Memory: " . format_file_size($memory_usage) . ")"
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Performance test failed',
                'details' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Вывод сводки тестов
     */
    private function printSummary() {
        $total = $this->passed + $this->failed;
        $success_rate = $total > 0 ? round(($this->passed / $total) * 100, 2) : 0;
        
        echo "<h2>📊 Test Summary</h2>\n";
        echo "Total Tests: {$total}<br>\n";
        echo "✅ Passed: {$this->passed}<br>\n";
        echo "❌ Failed: {$this->failed}<br>\n";
        echo "📈 Success Rate: {$success_rate}%<br>\n";
        
        if ($this->failed > 0) {
            echo "<h3>❌ Failed Tests:</h3>\n";
            foreach ($this->results as $test_name => $result) {
                if (!$result['success']) {
                    echo "• " . ucfirst(str_replace('_', ' ', $test_name)) . ": {$result['message']}<br>\n";
                }
            }
        }
        
        if ($success_rate >= 90) {
            echo "<h3 style='color: green;'>🎉 Excellent! System is ready for production.</h3>\n";
        } elseif ($success_rate >= 70) {
            echo "<h3 style='color: orange;'>⚠️ Good, but some issues need attention.</h3>\n";
        } else {
            echo "<h3 style='color: red;'>🚨 Critical issues found. System needs fixes before deployment.</h3>\n";
        }
    }
}

// Запуск тестов если файл вызван напрямую
if (basename($_SERVER['PHP_SELF']) === 'test_suite.php') {
    $test_suite = new TestSuite();
    $test_suite->runAllTests();
}
?>

