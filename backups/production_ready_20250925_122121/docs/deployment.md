# 🚀 Руководство по развертыванию

**Пошаговое руководство по развертыванию Baumaster Frankfurt на production сервере**

## 📋 Предварительные требования

### Системные требования

- **PHP**: 7.4 или выше
- **Веб-сервер**: Apache 2.4+ или Nginx 1.18+
- **База данных**: SQLite (встроена) или MySQL 5.7+
- **Память**: минимум 128MB PHP memory_limit
- **Дисковое пространство**: 100MB для файлов + место для изображений
- **SSL сертификат**: обязательно для production

### Рекомендуемые настройки

- **PHP**: 8.0+ с OPcache
- **Память**: 256MB+ PHP memory_limit
- **Диск**: SSD для лучшей производительности
- **CDN**: для статических файлов

## 🔧 Подготовка к развертыванию

### 1. Оптимизация проекта

```bash
# Запуск оптимизатора
php tools/optimizer.php

# Запуск тестов
php tests/test_suite.php

# Исправление багов
php tools/bug_fixer.php
```

### 2. Настройка production конфигурации

#### Обновление config.php

```php
<?php
// Production настройки
define('DEBUG_MODE', false);
define('SITE_URL', 'https://your-domain.com');
define('ADMIN_URL', 'https://your-domain.com/admin');

// Безопасность
define('CSRF_TOKEN_LIFETIME', 3600);
define('SESSION_LIFETIME', 7200);

// Производительность
define('CACHE_ENABLED', true);
define('COMPRESSION_ENABLED', true);
define('IMAGE_OPTIMIZATION', true);

// Логирование
define('LOG_LEVEL', 'ERROR');
define('LOG_FILE', DATA_PATH . 'logs/error.log');
?>
```

#### Обновление .htaccess

```apache
# Production .htaccess
RewriteEngine On

# HTTPS редирект
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Безопасность
<Files ~ "\.(db|log|json|sql)$">
    Order allow,deny
    Deny from all
</Files>

# Заголовки безопасности
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set Content-Security-Policy "default-src 'self'"
</IfModule>

# Gzip сжатие
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
    AddOutputFilterByType DEFLATE image/svg+xml
</IfModule>

# Кэширование
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType text/html "access plus 1 hour"
</IfModule>

# Clean URLs
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([^/]+)/?$ $1.php [L,QSA]
```

## 🌐 Настройка веб-сервера

### Apache 2.4+

#### Установка модулей

```bash
# Ubuntu/Debian
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires
sudo systemctl restart apache2

# CentOS/RHEL
sudo yum install mod_ssl
sudo systemctl enable httpd
sudo systemctl start httpd
```

#### Виртуальный хост

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/baumaster

    <Directory /var/www/baumaster>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/baumaster_error.log
    CustomLog ${APACHE_LOG_DIR}/baumaster_access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName your-domain.com
    ServerAlias www.your-domain.com
    DocumentRoot /var/www/baumaster

    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key

    <Directory /var/www/baumaster>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx 1.18+

#### Конфигурация сайта

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;

    root /var/www/baumaster;
    index index.php index.html;

    # SSL настройки
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;

    # Безопасность
    add_header X-Frame-Options DENY;
    add_header X-Content-Type-Options nosniff;
    add_header X-XSS-Protection "1; mode=block";

    # Gzip сжатие
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Кэширование статических файлов
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|webp)$ {
        expires 1M;
        add_header Cache-Control "public, immutable";
    }

    # PHP обработка
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Clean URLs
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Защита системных файлов
    location ~ /\.(ht|git|env) {
        deny all;
    }

    location ~ \.(db|log|json|sql)$ {
        deny all;
    }
}
```

## 🗄️ Настройка базы данных

### SQLite (рекомендуется)

```bash
# Создание директории для БД
mkdir -p /var/www/baumaster/data
chmod 755 /var/www/baumaster/data

# Создание файла БД (автоматически при первом запуске)
touch /var/www/baumaster/data/database.db
chmod 664 /var/www/baumaster/data/database.db
```

### MySQL (для больших проектов)

```sql
-- Создание базы данных
CREATE DATABASE baumaster CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Создание пользователя
CREATE USER 'baumaster_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON baumaster.* TO 'baumaster_user'@'localhost';
FLUSH PRIVILEGES;

-- Импорт структуры
mysql -u baumaster_user -p baumaster < database.sql
```

#### Обновление config.php для MySQL

```php
// config.php
define('DB_TYPE', 'mysql');
define('DB_HOST', 'localhost');
define('DB_NAME', 'baumaster');
define('DB_USER', 'baumaster_user');
define('DB_PASS', 'secure_password');
```

## 🔐 Настройка SSL

### Let's Encrypt (бесплатно)

```bash
# Установка Certbot
sudo apt install certbot python3-certbot-apache

# Получение сертификата
sudo certbot --apache -d your-domain.com -d www.your-domain.com

# Автоматическое обновление
sudo crontab -e
# Добавить: 0 12 * * * /usr/bin/certbot renew --quiet
```

### Коммерческий SSL

1. Купите SSL сертификат у провайдера
2. Скачайте файлы сертификата
3. Установите в конфигурацию веб-сервера
4. Проверьте работу через SSL Labs

## 📧 Настройка email

### SMTP настройки

```php
// В админ-панели: Интеграции → Email
SMTP Host: smtp.gmail.com
SMTP Port: 587
Encryption: TLS
Username: your-email@gmail.com
Password: your-app-password
```

### Gmail App Password

1. Включите 2FA в Google аккаунте
2. Создайте App Password для приложения
3. Используйте App Password вместо обычного пароля

### Тестирование email

```bash
# Тест отправки через админ-панель
# Перейдите в Интеграции → Email → Тест
```

## 🔍 Настройка SEO

### Google Analytics

1. Создайте аккаунт в Google Analytics
2. Получите Measurement ID (G-XXXXXXXXXX)
3. Введите в админ-панели: Интеграции → Аналитика

### Google Search Console

1. Добавьте сайт в Search Console
2. Подтвердите владение через HTML файл
3. Отправьте sitemap.xml

### Sitemap.xml

```bash
# Автоматическая генерация
curl https://your-domain.com/sitemap.xml

# Ручная генерация через админ-панель
# SEO → Генерация Sitemap
```

## 🧪 Тестирование развертывания

### 1. Функциональное тестирование

```bash
# Проверка основных страниц
curl -I https://your-domain.com/
curl -I https://your-domain.com/services
curl -I https://your-domain.com/portfolio
curl -I https://your-domain.com/admin/

# Проверка API
curl -X POST https://your-domain.com/contact_form.php
```

### 2. Тестирование производительности

```bash
# Использование инструментов
# GTmetrix: https://gtmetrix.com/
# PageSpeed Insights: https://pagespeed.web.dev/
# WebPageTest: https://www.webpagetest.org/
```

### 3. Тестирование безопасности

```bash
# SSL тест
# https://www.ssllabs.com/ssltest/

# Безопасность сайта
# https://observatory.mozilla.org/
```

## 📊 Мониторинг

### Настройка логирования

```bash
# Создание директории логов
mkdir -p /var/www/baumaster/data/logs
chmod 755 /var/www/baumaster/data/logs

# Настройка ротации логов
sudo nano /etc/logrotate.d/baumaster
```

#### Конфигурация logrotate

```
/var/www/baumaster/data/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

### Мониторинг производительности

```bash
# Установка htop для мониторинга
sudo apt install htop

# Мониторинг дискового пространства
df -h

# Мониторинг использования памяти
free -h
```

## 🔄 Бэкап и восстановление

### Автоматический бэкап

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/baumaster"
SITE_DIR="/var/www/baumaster"

# Создание директории бэкапа
mkdir -p $BACKUP_DIR

# Бэкап файлов
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C $SITE_DIR .

# Бэкап базы данных
if [ -f "$SITE_DIR/data/database.db" ]; then
    cp $SITE_DIR/data/database.db $BACKUP_DIR/database_$DATE.db
fi

# Удаление старых бэкапов (старше 30 дней)
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
find $BACKUP_DIR -name "*.db" -mtime +30 -delete

echo "Backup completed: $DATE"
```

### Настройка cron для бэкапов

```bash
# Добавление в crontab
crontab -e

# Ежедневный бэкап в 2:00
0 2 * * * /path/to/backup.sh

# Еженедельный полный бэкап
0 3 * * 0 /path/to/full_backup.sh
```

### Восстановление из бэкапа

```bash
# Остановка веб-сервера
sudo systemctl stop apache2

# Восстановление файлов
tar -xzf /backups/baumaster/files_YYYYMMDD_HHMMSS.tar.gz -C /var/www/baumaster/

# Восстановление базы данных
cp /backups/baumaster/database_YYYYMMDD_HHMMSS.db /var/www/baumaster/data/database.db

# Установка прав доступа
chown -R www-data:www-data /var/www/baumaster
chmod -R 755 /var/www/baumaster

# Запуск веб-сервера
sudo systemctl start apache2
```

## 🚨 Устранение неполадок

### Частые проблемы

#### 1. Ошибка 500

```bash
# Проверка логов
tail -f /var/log/apache2/error.log
tail -f /var/www/baumaster/data/logs/error.log

# Проверка прав доступа
ls -la /var/www/baumaster/
chmod -R 755 /var/www/baumaster/
chown -R www-data:www-data /var/www/baumaster/
```

#### 2. Проблемы с базой данных

```bash
# Проверка SQLite
sqlite3 /var/www/baumaster/data/database.db ".tables"

# Проверка MySQL
mysql -u baumaster_user -p -e "SHOW TABLES;" baumaster
```

#### 3. Проблемы с SSL

```bash
# Проверка сертификата
openssl x509 -in /path/to/certificate.crt -text -noout

# Проверка конфигурации
nginx -t
apache2ctl configtest
```

### Логи для диагностики

- **Apache**: `/var/log/apache2/error.log`
- **Nginx**: `/var/log/nginx/error.log`
- **PHP**: `/var/log/php_errors.log`
- **Приложение**: `/var/www/baumaster/data/logs/`

## 📞 Поддержка

### Контакты технической поддержки

- **Email**: dev@baumaster-frankfurt.de
- **Телефон**: +49 69 123-456-789
- **Документация**: https://your-domain.com/docs/

### Полезные ресурсы

- **PHP документация**: https://www.php.net/docs.php
- **Apache документация**: https://httpd.apache.org/docs/
- **Nginx документация**: https://nginx.org/en/docs/
- **SSL Labs**: https://www.ssllabs.com/ssltest/

---

**Удачного развертывания! 🚀**

