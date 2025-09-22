<?php
/**
 * Security Test Suite
 * Baumaster Testing - Security Vulnerability Testing
 */

require_once __DIR__ . '/../config.php';

class SecurityTest {
    private $vulnerabilities = [];
    private $passed_tests = 0;
    private $failed_tests = 0;
    
    public function runSecurityTests() {
        echo "<h1>🔒 Security Test Suite</h1>\n";
        echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;'>\n";
        
        $this->testSqlInjection();
        $this->testXssProtection();
        $this->testCsrfProtection();
        $this->testFileUploadSecurity();
        $this->testAuthenticationSecurity();
        $this->testSessionSecurity();
        $this->testInputValidation();
        $this->testErrorHandling();
        
        $this->printSecurityReport();
        echo "</div>\n";
    }
    
    /**
     * Тест SQL инъекций
     */
    private function testSqlInjection() {
        echo "<h2>🔍 Testing SQL Injection Protection</h2>\n";
        
        $malicious_inputs = [
            "'; DROP TABLE users; --",
            "' OR '1'='1",
            "admin'--",
            "' UNION SELECT * FROM users--",
            "'; INSERT INTO users VALUES ('hacker', 'password'); --"
        ];
        
        foreach ($malicious_inputs as $input) {
            $sanitized = sanitize_input($input);
            
            if (strpos($sanitized, 'DROP') !== false || 
                strpos($sanitized, 'UNION') !== false || 
                strpos($sanitized, 'INSERT') !== false) {
                $this->vulnerabilities[] = "SQL Injection vulnerability found with input: " . htmlspecialchars($input);
                echo "❌ <span style='color: red;'>VULNERABLE</span> - Input: " . htmlspecialchars($input) . "\n";
                $this->failed_tests++;
            } else {
                echo "✅ <span style='color: green;'>SAFE</span> - Input: " . htmlspecialchars($input) . "\n";
                $this->passed_tests++;
            }
        }
        echo "<br>\n";
    }
    
    /**
     * Тест XSS защиты
     */
    private function testXssProtection() {
        echo "<h2>🔍 Testing XSS Protection</h2>\n";
        
        $xss_payloads = [
            '<script>alert("XSS")</script>',
            '<img src=x onerror=alert("XSS")>',
            '<svg onload=alert("XSS")>',
            'javascript:alert("XSS")',
            '<iframe src="javascript:alert(\'XSS\')"></iframe>',
            '<body onload=alert("XSS")>',
            '<input onfocus=alert("XSS") autofocus>',
            '<select onfocus=alert("XSS") autofocus>',
            '<textarea onfocus=alert("XSS") autofocus>',
            '<keygen onfocus=alert("XSS") autofocus>',
            '<video><source onerror="alert(\'XSS\')">',
            '<audio src=x onerror=alert("XSS")>'
        ];
        
        foreach ($xss_payloads as $payload) {
            $sanitized = sanitize_input($payload);
            
            if (strpos($sanitized, '<script>') !== false || 
                strpos($sanitized, 'onerror') !== false || 
                strpos($sanitized, 'onload') !== false ||
                strpos($sanitized, 'onfocus') !== false ||
                strpos($sanitized, 'javascript:') !== false) {
                $this->vulnerabilities[] = "XSS vulnerability found with payload: " . htmlspecialchars($payload);
                echo "❌ <span style='color: red;'>VULNERABLE</span> - Payload: " . htmlspecialchars($payload) . "\n";
                $this->failed_tests++;
            } else {
                echo "✅ <span style='color: green;'>SAFE</span> - Payload: " . htmlspecialchars($payload) . "\n";
                $this->passed_tests++;
            }
        }
        echo "<br>\n";
    }
    
    /**
     * Тест CSRF защиты
     */
    private function testCsrfProtection() {
        echo "<h2>🔍 Testing CSRF Protection</h2>\n";
        
        // Тест генерации уникальных токенов
        $tokens = [];
        for ($i = 0; $i < 10; $i++) {
            $tokens[] = generate_csrf_token();
        }
        
        $unique_tokens = array_unique($tokens);
        if (count($unique_tokens) !== count($tokens)) {
            $this->vulnerabilities[] = "CSRF token generation not unique";
            echo "❌ <span style='color: red;'>VULNERABLE</span> - CSRF tokens are not unique\n";
            $this->failed_tests++;
        } else {
            echo "✅ <span style='color: green;'>SAFE</span> - CSRF tokens are unique\n";
            $this->passed_tests++;
        }
        
        // Тест проверки токенов
        $valid_token = generate_csrf_token();
        if (!verify_csrf_token($valid_token)) {
            $this->vulnerabilities[] = "CSRF token verification failed";
            echo "❌ <span style='color: red;'>VULNERABLE</span> - CSRF token verification failed\n";
            $this->failed_tests++;
        } else {
            echo "✅ <span style='color: green;'>SAFE</span> - CSRF token verification works\n";
            $this->passed_tests++;
        }
        
        // Тест с неверным токеном
        if (verify_csrf_token('invalid_token')) {
            $this->vulnerabilities[] = "CSRF accepts invalid tokens";
            echo "❌ <span style='color: red;'>VULNERABLE</span> - CSRF accepts invalid tokens\n";
            $this->failed_tests++;
        } else {
            echo "✅ <span style='color: green;'>SAFE</span> - CSRF rejects invalid tokens\n";
            $this->passed_tests++;
        }
        
        echo "<br>\n";
    }
    
    /**
     * Тест безопасности загрузки файлов
     */
    private function testFileUploadSecurity() {
        echo "<h2>🔍 Testing File Upload Security</h2>\n";
        
        $dangerous_extensions = ['.php', '.phtml', '.php3', '.php4', '.php5', '.pl', '.py', '.jsp', '.asp', '.sh', '.cgi'];
        $safe_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.pdf', '.doc', '.docx', '.txt'];
        
        // Тест опасных расширений
        foreach ($dangerous_extensions as $ext) {
            $filename = 'test' . $ext;
            if (isAllowedFileType($filename)) {
                $this->vulnerabilities[] = "Dangerous file extension allowed: {$ext}";
                echo "❌ <span style='color: red;'>VULNERABLE</span> - Dangerous extension allowed: {$ext}\n";
                $this->failed_tests++;
            } else {
                echo "✅ <span style='color: green;'>SAFE</span> - Dangerous extension blocked: {$ext}\n";
                $this->passed_tests++;
            }
        }
        
        // Тест безопасных расширений
        foreach ($safe_extensions as $ext) {
            $filename = 'test' . $ext;
            if (!isAllowedFileType($filename)) {
                $this->vulnerabilities[] = "Safe file extension blocked: {$ext}";
                echo "❌ <span style='color: red;'>VULNERABLE</span> - Safe extension blocked: {$ext}\n";
                $this->failed_tests++;
            } else {
                echo "✅ <span style='color: green;'>SAFE</span> - Safe extension allowed: {$ext}\n";
                $this->passed_tests++;
            }
        }
        
        echo "<br>\n";
    }
    
    /**
     * Тест безопасности аутентификации
     */
    private function testAuthenticationSecurity() {
        echo "<h2>🔍 Testing Authentication Security</h2>\n";
        
        // Тест хеширования паролей
        $password = 'test_password_123';
        $hash = hash_password($password);
        
        if (empty($hash)) {
            $this->vulnerabilities[] = "Password hashing failed";
            echo "❌ <span style='color: red;'>VULNERABLE</span> - Password hashing failed\n";
            $this->failed_tests++;
        } else {
            echo "✅ <span style='color: green;'>SAFE</span> - Password hashing works\n";
            $this->passed_tests++;
        }
        
        // Тест проверки паролей
        if (!verify_password($password, $hash)) {
            $this->vulnerabilities[] = "Password verification failed";
            echo "❌ <span style='color: red;'>VULNERABLE</span> - Password verification failed\n";
            $this->failed_tests++;
        } else {
            echo "✅ <span style='color: green;'>SAFE</span> - Password verification works\n";
            $this->passed_tests++;
        }
        
        // Тест с неверным паролем
        if (verify_password('wrong_password', $hash)) {
            $this->vulnerabilities[] = "Password verification accepts wrong passwords";
            echo "❌ <span style='color: red;'>VULNERABLE</span> - Password verification accepts wrong passwords\n";
            $this->failed_tests++;
        } else {
            echo "✅ <span style='color: green;'>SAFE</span> - Password verification rejects wrong passwords\n";
            $this->passed_tests++;
        }
        
        echo "<br>\n";
    }
    
    /**
     * Тест безопасности сессий
     */
    private function testSessionSecurity() {
        echo "<h2>🔍 Testing Session Security</h2>\n";
        
        // Проверка настроек сессий
        $session_settings = [
            'session.cookie_httponly' => ini_get('session.cookie_httponly'),
            'session.cookie_secure' => ini_get('session.cookie_secure'),
            'session.use_strict_mode' => ini_get('session.use_strict_mode'),
            'session.cookie_samesite' => ini_get('session.cookie_samesite')
        ];
        
        $secure_settings = 0;
        foreach ($session_settings as $setting => $value) {
            if ($value) {
                $secure_settings++;
                echo "✅ <span style='color: green;'>SAFE</span> - {$setting}: {$value}\n";
            } else {
                echo "⚠️ <span style='color: orange;'>WARNING</span> - {$setting}: {$value}\n";
            }
        }
        
        if ($secure_settings >= 3) {
            $this->passed_tests++;
        } else {
            $this->vulnerabilities[] = "Session security settings not optimal";
            $this->failed_tests++;
        }
        
        echo "<br>\n";
    }
    
    /**
     * Тест валидации входных данных
     */
    private function testInputValidation() {
        echo "<h2>🔍 Testing Input Validation</h2>\n";
        
        $test_cases = [
            ['input' => 'normal_text', 'expected' => 'normal_text'],
            ['input' => '<script>alert("xss")</script>', 'expected' => 'alert("xss")'],
            ['input' => 'SELECT * FROM users', 'expected' => 'SELECT FROM users'],
            ['input' => 'test@example.com', 'expected' => 'test@example.com'],
            ['input' => 'invalid@', 'expected' => 'invalid@']
        ];
        
        foreach ($test_cases as $case) {
            $result = sanitize_input($case['input']);
            if ($result === $case['expected']) {
                echo "✅ <span style='color: green;'>SAFE</span> - Input: " . htmlspecialchars($case['input']) . "\n";
                $this->passed_tests++;
            } else {
                echo "❌ <span style='color: red;'>VULNERABLE</span> - Input: " . htmlspecialchars($case['input']) . " -> " . htmlspecialchars($result) . "\n";
                $this->failed_tests++;
            }
        }
        
        echo "<br>\n";
    }
    
    /**
     * Тест обработки ошибок
     */
    private function testErrorHandling() {
        echo "<h2>🔍 Testing Error Handling</h2>\n";
        
        // Проверка отображения ошибок
        $display_errors = ini_get('display_errors');
        $log_errors = ini_get('log_errors');
        
        if ($display_errors && !$log_errors) {
            $this->vulnerabilities[] = "Errors displayed to users without logging";
            echo "❌ <span style='color: red;'>VULNERABLE</span> - Errors displayed without logging\n";
            $this->failed_tests++;
        } else {
            echo "✅ <span style='color: green;'>SAFE</span> - Error handling configured properly\n";
            $this->passed_tests++;
        }
        
        echo "<br>\n";
    }
    
    /**
     * Функция проверки разрешенных типов файлов
     */
    private function isAllowedFileType($filename) {
        $allowed_extensions = ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.pdf', '.doc', '.docx', '.txt'];
        $extension = strtolower(substr($filename, strrpos($filename, '.')));
        return in_array($extension, $allowed_extensions);
    }
    
    /**
     * Вывод отчета о безопасности
     */
    private function printSecurityReport() {
        $total = $this->passed_tests + $this->failed_tests;
        $security_score = $total > 0 ? round(($this->passed_tests / $total) * 100, 2) : 0;
        
        echo "<h2>📊 Security Report</h2>\n";
        echo "Total Tests: {$total}<br>\n";
        echo "✅ Passed: {$this->passed_tests}<br>\n";
        echo "❌ Failed: {$this->failed_tests}<br>\n";
        echo "🛡️ Security Score: {$security_score}%<br>\n";
        
        if (!empty($this->vulnerabilities)) {
            echo "<h3>🚨 Vulnerabilities Found:</h3>\n";
            foreach ($this->vulnerabilities as $vuln) {
                echo "• " . htmlspecialchars($vuln) . "<br>\n";
            }
        }
        
        if ($security_score >= 90) {
            echo "<h3 style='color: green;'>🛡️ Excellent security! System is secure.</h3>\n";
        } elseif ($security_score >= 70) {
            echo "<h3 style='color: orange;'>⚠️ Good security, but some improvements needed.</h3>\n";
        } else {
            echo "<h3 style='color: red;'>🚨 Critical security issues found! Immediate action required.</h3>\n";
        }
    }
}

// Запуск тестов безопасности если файл вызван напрямую
if (basename($_SERVER['PHP_SELF']) === 'security_test.php') {
    $security_test = new SecurityTest();
    $security_test->runSecurityTests();
}
?>

