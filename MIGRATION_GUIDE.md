# 🚀 Руководство по миграции Baumaster Frankfurt на новый сервер

## 📋 Обзор проекта

**Baumaster Frankfurt** - это современный корпоративный сайт строительной компании с:

- Публичной частью (многостраничный SEO-оптимизированный сайт)
- Админ-панелью для управления контентом
- Многоязычностью (русский, немецкий, английский)
- SQLite базой данных
- Системой тестирования и оптимизации

## 🛠️ Системные требования

### Минимальные требования

- **PHP**: 7.4 или выше
- **Веб-сервер**: Apache с mod_rewrite или Nginx
- **База данных**: SQLite (включен в PHP) или MySQL 5.7+
- **Память**: 128MB+ (рекомендуется 256MB)
- **Дисковое пространство**: 100MB+ (без учета загруженных файлов)
- **Расширения PHP**: PDO, SQLite3, GD, mbstring, curl

### Рекомендуемые требования

- **PHP**: 8.0+
- **Память**: 512MB+
- **Дисковое пространство**: 1GB+
- **SSL сертификат** (для production)

## 📦 Подготовка к миграции

### 1. Создание бэкапа текущего сайта

```bash
# Создание полного бэкапа
cd /Applications/XAMPP/xamppfiles/htdocs
tar -czf baumaster_backup_$(date +%Y%m%d_%H%M%S).tar.gz .

# Или используйте встроенный скрипт
./scripts/backup.sh full --compress
```

### 2. Проверка целостности данных

```bash
# Проверка базы данных
sqlite3 data/baumaster.db "PRAGMA integrity_check;"

# Проверка файлов
php tests/test_suite.php
```

## 🔄 Пошаговая миграция

### Этап 1: Подготовка нового сервера

#### 1.1 Установка необходимого ПО

**Ubuntu/Debian:**

```bash
# Обновление системы
sudo apt update && sudo apt upgrade -y

# Установка Apache, PHP и расширений
sudo apt install apache2 php8.1 php8.1-sqlite3 php8.1-gd php8.1-mbstring php8.1-curl php8.1-zip -y

# Включение mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
```

**CentOS/RHEL:**

```bash
# Установка Apache и PHP
sudo yum install httpd php php-sqlite3 php-gd php-mbstring php-curl -y

# Включение mod_rewrite
sudo systemctl enable httpd
sudo systemctl start httpd
```

#### 1.2 Настройка веб-сервера

**Apache (.htaccess):**

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Безопасность
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

<Files "database.php">
    Order Allow,Deny
    Deny from all
</Files>

# Сжатие
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
</IfModule>
```

**Nginx:**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/baumaster;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }

    # Безопасность
    location ~ /\.(ht|env) {
        deny all;
    }

    location ~ /(config|database)\.php$ {
        deny all;
    }
}
```

### Этап 2: Загрузка файлов

#### 2.1 Копирование файлов

```bash
# Создание директории на новом сервере
sudo mkdir -p /var/www/baumaster
sudo chown -R www-data:www-data /var/www/baumaster

# Загрузка файлов (выберите один способ)
# Способ 1: SCP
scp -r /Applications/XAMPP/xamppfiles/htdocs/* user@new-server:/var/www/baumaster/

# Способ 2: rsync
rsync -avz --exclude='node_modules' --exclude='.git' /Applications/XAMPP/xamppfiles/htdocs/ user@new-server:/var/www/baumaster/

# Способ 3: Git (если проект в репозитории)
git clone https://github.com/your-repo/baumaster.git /var/www/baumaster
```

#### 2.2 Установка прав доступа

```bash
# Установка правильных прав
sudo chown -R www-data:www-data /var/www/baumaster
sudo chmod -R 755 /var/www/baumaster
sudo chmod -R 775 /var/www/baumaster/data
sudo chmod -R 775 /var/www/baumaster/assets/uploads
sudo chmod 644 /var/www/baumaster/.htaccess
```

### Этап 3: Настройка конфигурации

#### 3.1 Обновление config.php

```php
<?php
// Обновите следующие настройки для нового сервера:

// URL сайта
define('SITE_URL', 'https://your-new-domain.com');

// Пути (обычно не требуют изменений)
define('ADMIN_PATH', ABSPATH . 'admin/');
define('COMPONENTS_PATH', ABSPATH . 'components/');
define('ASSETS_PATH', ABSPATH . 'assets/');
define('DATA_PATH', ABSPATH . 'data/');

// Настройки базы данных
define('DB_TYPE', 'sqlite'); // или 'mysql' для MySQL
define('DB_PATH', DATA_PATH . 'baumaster.db');

// Для MySQL (если используете)
define('DB_HOST', 'localhost');
define('DB_NAME', 'baumaster');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');

// Email настройки
define('SMTP_HOST', 'your-smtp-server.com');
define('SMTP_USERNAME', 'your-email@domain.com');
define('SMTP_PASSWORD', 'your-password');
define('FROM_EMAIL', 'info@your-domain.com');

// Режим отладки (отключить для production)
define('DEBUG_MODE', false);
define('LOG_ERRORS', true);
?>
```

#### 3.2 Создание .htaccess

```apache
# Создайте файл .htaccess в корне сайта
RewriteEngine On

# Clean URLs
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Безопасность
<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>

<Files "database.php">
    Order Allow,Deny
    Deny from all
</Files>

# Сжатие
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain text/html text/xml text/css application/xml application/xhtml+xml application/rss+xml application/javascript application/x-javascript
</IfModule>

# Кэширование
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
    ExpiresByType application/x-shockwave-flash "access plus 1 month"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresDefault "access plus 2 days"
</IfModule>
```

### Этап 4: Настройка базы данных

#### 4.1 Для SQLite (рекомендуется)

```bash
# База данных создастся автоматически при первом запуске
# Убедитесь, что папка data/ доступна для записи
sudo chmod 775 /var/www/baumaster/data
sudo chown www-data:www-data /var/www/baumaster/data
```

#### 4.2 Для MySQL

```sql
-- Создание базы данных
CREATE DATABASE baumaster CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'baumaster_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON baumaster.* TO 'baumaster_user'@'localhost';
FLUSH PRIVILEGES;
```

```bash
# Импорт структуры (если есть SQL дамп)
mysql -u baumaster_user -p baumaster < database_structure.sql
```

### Этап 5: Тестирование и проверка

#### 5.1 Проверка работоспособности

```bash
# Запуск тестов системы
cd /var/www/baumaster
php tests/test_suite.php

# Проверка через браузер
curl -I http://your-domain.com/
curl -I http://your-domain.com/admin/
```

#### 5.2 Проверка админ-панели

1. Откройте `http://your-domain.com/admin/`
2. Войдите с учетными данными:
   - Логин: `root`
   - Пароль: `root`
3. Смените пароль в разделе "Пользователи"
4. Проверьте все разделы админ-панели

#### 5.3 Проверка функциональности

- [ ] Главная страница загружается
- [ ] Все страницы сайта работают
- [ ] Админ-панель доступна
- [ ] Загрузка файлов работает
- [ ] Формы обратной связи работают
- [ ] Многоязычность работает
- [ ] SEO функции работают

## 🔧 Дополнительные настройки

### SSL сертификат (Let's Encrypt)

```bash
# Установка Certbot
sudo apt install certbot python3-certbot-apache -y

# Получение сертификата
sudo certbot --apache -d your-domain.com

# Автоматическое обновление
sudo crontab -e
# Добавьте: 0 12 * * * /usr/bin/certbot renew --quiet
```

### Настройка бэкапов

```bash
# Создание cron задачи для автоматических бэкапов
sudo crontab -e

# Добавьте строку для ежедневного бэкапа в 2:00
0 2 * * * /var/www/baumaster/scripts/backup.sh full --compress
```

### Мониторинг

```bash
# Установка мониторинга логов
sudo apt install logwatch -y

# Настройка уведомлений
echo "logwatch --detail High --mailto admin@your-domain.com --range yesterday" | sudo crontab -
```

## 🚨 Решение проблем

### Частые проблемы и решения

#### 1. Ошибка 500 Internal Server Error

```bash
# Проверка логов
sudo tail -f /var/log/apache2/error.log

# Проверка прав доступа
sudo chmod -R 755 /var/www/baumaster
sudo chmod -R 775 /var/www/baumaster/data
```

#### 2. База данных не создается

```bash
# Проверка прав на папку data
sudo chown -R www-data:www-data /var/www/baumaster/data
sudo chmod 775 /var/www/baumaster/data

# Проверка расширений PHP
php -m | grep sqlite
```

#### 3. Загрузка файлов не работает

```bash
# Проверка прав на папку uploads
sudo chmod -R 775 /var/www/baumaster/assets/uploads

# Проверка настроек PHP
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

#### 4. Clean URLs не работают

```bash
# Проверка mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# Проверка .htaccess
cat /var/www/baumaster/.htaccess
```

## 📊 Оптимизация производительности

### 1. Настройка PHP

```bash
# Редактирование php.ini
sudo nano /etc/php/8.1/apache2/php.ini

# Рекомендуемые настройки:
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 30
max_input_vars = 3000
```

### 2. Настройка Apache

```bash
# Включение сжатия
sudo a2enmod deflate
sudo a2enmod expires
sudo systemctl restart apache2
```

### 3. Кэширование

```bash
# Создание папки для кэша
sudo mkdir -p /var/www/baumaster/data/cache
sudo chown www-data:www-data /var/www/baumaster/data/cache
```

## 🔒 Безопасность

### 1. Настройка файрвола

```bash
# UFW (Ubuntu)
sudo ufw enable
sudo ufw allow 22
sudo ufw allow 80
sudo ufw allow 443
```

### 2. Регулярные обновления

```bash
# Автоматические обновления безопасности
sudo apt install unattended-upgrades -y
sudo dpkg-reconfigure -plow unattended-upgrades
```

### 3. Мониторинг безопасности

```bash
# Установка fail2ban
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

## 📋 Чек-лист миграции

### Перед миграцией

- [ ] Создан полный бэкап текущего сайта
- [ ] Проверена целостность базы данных
- [ ] Подготовлен новый сервер
- [ ] Настроены DNS записи

### Во время миграции

- [ ] Загружены все файлы проекта
- [ ] Настроена конфигурация
- [ ] Создана/импортирована база данных
- [ ] Установлены правильные права доступа
- [ ] Настроен веб-сервер

### После миграции

- [ ] Проверена работоспособность сайта
- [ ] Протестирована админ-панель
- [ ] Проверены все функции
- [ ] Настроен SSL сертификат
- [ ] Настроены бэкапы
- [ ] Обновлены DNS записи

## 📞 Поддержка

Если у вас возникли проблемы с миграцией:

1. Проверьте логи ошибок: `/var/log/apache2/error.log`
2. Запустите тесты системы: `php tests/test_suite.php`
3. Проверьте права доступа к файлам и папкам
4. Убедитесь, что все необходимые расширения PHP установлены

---

**Удачной миграции! 🚀**

_Это руководство создано специально для проекта Baumaster Frankfurt_
