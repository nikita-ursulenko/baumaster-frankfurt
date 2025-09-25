#!/bin/bash

# Скрипт оптимизации проекта Baumaster Frankfurt для production
# Подготовка файлов для загрузки на предпрод сервер

PROJECT_DIR="/Applications/XAMPP/xamppfiles/htdocs"
BACKUP_DIR="/Applications/XAMPP/xamppfiles/htdocs/backups"
DATE=$(date +"%Y%m%d_%H%M%S")

echo "🚀 Оптимизация проекта Baumaster Frankfurt для production..."
echo "📅 Дата: $(date)"
echo "🎯 Цель: https://baumeister.page.gd"

# Создание папки для оптимизированных файлов
OPTIMIZED_DIR="$BACKUP_DIR/production_ready_${DATE}"
mkdir -p "$OPTIMIZED_DIR"

echo "📁 Создание оптимизированной версии в: $OPTIMIZED_DIR"

# Копирование файлов с исключениями
echo "📦 Копирование файлов для production..."

rsync -av \
    --exclude="node_modules/" \
    --exclude="test-results/" \
    --exclude="playwright-report/" \
    --exclude=".git/" \
    --exclude="backups/" \
    --exclude="*.log" \
    --exclude="*.tmp" \
    --exclude="test_image.png" \
    "$PROJECT_DIR/" "$OPTIMIZED_DIR/"

# Удаление временных файлов
echo "🧹 Очистка временных файлов..."
find "$OPTIMIZED_DIR" -name "*.tmp" -delete
find "$OPTIMIZED_DIR" -name "*.log" -delete
find "$OPTIMIZED_DIR" -name ".DS_Store" -delete

# Оптимизация изображений (если установлен ImageMagick)
if command -v convert &> /dev/null; then
    echo "🖼️ Оптимизация изображений..."
    find "$OPTIMIZED_DIR/assets/uploads" -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" | while read file; do
        # Создание оптимизированной версии
        convert "$file" -strip -quality 85 -resize '1200x1200>' "$file.optimized"
        if [ $? -eq 0 ]; then
            mv "$file.optimized" "$file"
        fi
    done
else
    echo "⚠️ ImageMagick не установлен, пропускаем оптимизацию изображений"
fi

# Создание production .htaccess
echo "⚙️ Создание production .htaccess..."
cat > "$OPTIMIZED_DIR/.htaccess" << 'EOF'
# Baumaster Frankfurt - Production .htaccess
# Настройки безопасности и производительности для baumeister.page.gd

# Включение модуля перезаписи
RewriteEngine On

# Принудительное перенаправление на HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Базовые правила перезаписи
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]

# Безопасность - запрет доступа к системным файлам
<Files "*.db">
    Order Allow,Deny
    Deny from all
</Files>

<Files "*.log">
    Order Allow,Deny
    Deny from all
</Files>

<Files "*.md">
    Order Allow,Deny
    Deny from all
</Files>

<Files "*.json">
    Order Allow,Deny
    Deny from all
</Files>

# Защита от просмотра содержимого папок
Options -Indexes

# Защита конфигурационных файлов
<FilesMatch "^(config|database|functions)\.php$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Защита папки data
<IfModule mod_rewrite.c>
    RewriteRule ^data/ - [F,L]
</IfModule>

# Защита папки components
<IfModule mod_rewrite.c>
    RewriteRule ^components/ - [F,L]
</IfModule>

# Кэширование статических файлов
<IfModule mod_expires.c>
    ExpiresActive On
    
    # Изображения
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/webp "access plus 1 month"
    ExpiresByType image/svg+xml "access plus 1 month"
    
    # CSS и JavaScript
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    
    # Шрифты
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    
    # HTML
    ExpiresByType text/html "access plus 1 hour"
</IfModule>

# Сжатие Gzip
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
    AddOutputFilterByType DEFLATE text/javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>

# Заголовки безопасности
<IfModule mod_headers.c>
    Header always set X-XSS-Protection "1; mode=block"
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</IfModule>

# Ограничения на размер загружаемых файлов
php_value upload_max_filesize 10M
php_value post_max_size 10M
php_value max_execution_time 300
php_value max_input_time 300
php_value memory_limit 256M

# Отключение отображения ошибок в production
php_flag display_errors Off
php_flag log_errors On
</IfModule>

# Настройки для robots.txt и sitemap
<IfModule mod_rewrite.c>
    RewriteRule ^robots\.txt$ /seo/robots.txt [L]
    RewriteRule ^sitemap\.xml$ /seo/sitemap.php [L]
</IfModule>
EOF

# Создание архива для загрузки
echo "📦 Создание архива для загрузки..."
cd "$BACKUP_DIR"
tar -czf "baumaster_production_${DATE}.tar.gz" -C "$OPTIMIZED_DIR" .

# Создание инструкций по развертыванию
echo "📋 Создание инструкций по развертыванию..."
cat > "$BACKUP_DIR/deployment_instructions_${DATE}.txt" << EOF
Baumaster Frankfurt - Инструкции по развертыванию
================================================

Дата создания: $(date)
Домен: https://baumeister.page.gd
FTP сервер: ftpupload.net:21
Пользователь: if0_39768140

ФАЙЛЫ ДЛЯ ЗАГРУЗКИ:
- baumaster_production_${DATE}.tar.gz (основной архив)
- baumaster_db_${DATE}.db (база данных)

ПОШАГОВЫЕ ИНСТРУКЦИИ:

1. ПОДКЛЮЧЕНИЕ К FTP:
   - Хост: ftpupload.net
   - Порт: 21
   - Пользователь: if0_39768140
   - Пароль: QFDnn5XPeMt
   - Протокол: FTP (Passive mode)

2. ЗАГРУЗКА ФАЙЛОВ:
   - Распаковать baumaster_production_${DATE}.tar.gz
   - Загрузить все файлы в корневую папку сайта
   - Загрузить baumaster_db_${DATE}.db в папку /data/

3. НАСТРОЙКА ПРАВ ДОСТУПА:
   - Папки: 755 (rwxr-xr-x)
   - Файлы: 644 (rw-r--r--)
   - Папка uploads: 777 (rwxrwxrwx)
   - Файл data/baumaster.db: 666 (rw-rw-rw-)

4. ПРОВЕРКА:
   - Открыть https://baumeister.page.gd
   - Проверить работу всех страниц
   - Войти в админ-панель (/admin/)
   - Тестировать загрузку файлов

5. БЕЗОПАСНОСТЬ:
   - Сменить пароль администратора
   - Проверить работу HTTPS
   - Убедиться в работе .htaccess

КРИТИЧЕСКИЕ ФАЙЛЫ:
- config.php (настроен для production)
- .htaccess (безопасность и производительность)
- data/baumaster.db (база данных)
- assets/uploads/ (загруженные файлы)

КОНТАКТЫ:
- Панель управления: https://dash.infinityfree.com/accounts/if0_39768140
- Документация: /docs/ в проекте

ГОТОВНОСТЬ: ✅ Production Ready
EOF

# Статистика
echo ""
echo "📊 Статистика оптимизации:"
echo "📁 Исходный размер: $(du -sh "$PROJECT_DIR" | cut -f1)"
echo "📁 Оптимизированный размер: $(du -sh "$OPTIMIZED_DIR" | cut -f1)"
echo "📦 Размер архива: $(du -sh "$BACKUP_DIR/baumaster_production_${DATE}.tar.gz" | cut -f1)"

# Финальная проверка
echo ""
echo "🔍 Финальная проверка готовности..."

# Проверка критических файлов
CRITICAL_FILES=(
    "$OPTIMIZED_DIR/config.php"
    "$OPTIMIZED_DIR/.htaccess"
    "$OPTIMIZED_DIR/index.php"
    "$OPTIMIZED_DIR/database.php"
    "$OPTIMIZED_DIR/functions.php"
)

for file in "${CRITICAL_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $(basename "$file") - найден"
    else
        echo "❌ $(basename "$file") - ОТСУТСТВУЕТ!"
    fi
done

# Проверка конфигурации
if grep -q "https://baumeister.page.gd" "$OPTIMIZED_DIR/config.php"; then
    echo "✅ Конфигурация настроена для production"
else
    echo "❌ Конфигурация не настроена для production"
fi

if grep -q "DEBUG_MODE.*false" "$OPTIMIZED_DIR/config.php"; then
    echo "✅ Режим отладки отключен"
else
    echo "❌ Режим отладки не отключен"
fi

echo ""
echo "🎉 Оптимизация завершена!"
echo "📁 Файлы готовы к загрузке: $BACKUP_DIR"
echo "📋 Инструкции: deployment_instructions_${DATE}.txt"
echo "🚀 Готов к развертыванию на предпрод!"
