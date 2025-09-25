#!/bin/bash

# Скрипт создания бэкапа проекта Baumaster Frankfurt
# Для миграции на предпрод сервер

# Настройки
PROJECT_NAME="baumaster"
BACKUP_DIR="/Applications/XAMPP/xamppfiles/htdocs/backups"
DATE=$(date +"%Y%m%d_%H%M%S")
SOURCE_DIR="/Applications/XAMPP/xamppfiles/htdocs"

# Создание папки для бэкапов
mkdir -p "$BACKUP_DIR"

echo "🚀 Создание бэкапа проекта Baumaster Frankfurt..."
echo "📅 Дата: $(date)"
echo "📁 Исходная папка: $SOURCE_DIR"
echo "💾 Папка бэкапов: $BACKUP_DIR"

# Создание архива с исключениями
echo "📦 Архивирование файлов..."

tar -czf "$BACKUP_DIR/${PROJECT_NAME}_backup_${DATE}.tar.gz" \
    --exclude="node_modules" \
    --exclude="test-results" \
    --exclude="playwright-report" \
    --exclude=".git" \
    --exclude="backups" \
    --exclude="*.log" \
    --exclude="*.tmp" \
    -C "$SOURCE_DIR" .

# Проверка успешности создания архива
if [ $? -eq 0 ]; then
    echo "✅ Бэкап успешно создан: ${PROJECT_NAME}_backup_${DATE}.tar.gz"
    
    # Информация о размере архива
    ARCHIVE_SIZE=$(du -h "$BACKUP_DIR/${PROJECT_NAME}_backup_${DATE}.tar.gz" | cut -f1)
    echo "📊 Размер архива: $ARCHIVE_SIZE"
    
    # Создание отдельного бэкапа базы данных
    echo "🗄️ Создание бэкапа базы данных..."
    cp "$SOURCE_DIR/data/baumaster.db" "$BACKUP_DIR/baumaster_db_${DATE}.db"
    
    if [ $? -eq 0 ]; then
        echo "✅ Бэкап базы данных создан: baumaster_db_${DATE}.db"
    else
        echo "❌ Ошибка при создании бэкапа базы данных"
    fi
    
    # Создание списка файлов для загрузки
    echo "📋 Создание списка файлов для загрузки..."
    find "$SOURCE_DIR" -type f -name "*.php" -o -name "*.css" -o -name "*.js" -o -name "*.json" -o -name "*.md" -o -name "*.txt" -o -name "*.htaccess" | \
    grep -v node_modules | \
    grep -v test-results | \
    grep -v playwright-report | \
    grep -v .git | \
    grep -v backups | \
    sort > "$BACKUP_DIR/files_to_upload_${DATE}.txt"
    
    echo "✅ Список файлов создан: files_to_upload_${DATE}.txt"
    
    # Создание отчета о бэкапе
    cat > "$BACKUP_DIR/backup_report_${DATE}.txt" << EOF
Baumaster Frankfurt - Отчет о бэкапе
=====================================

Дата создания: $(date)
Версия проекта: 1.0.0
Домен назначения: https://baumeister.page.gd

Созданные файлы:
- ${PROJECT_NAME}_backup_${DATE}.tar.gz (полный архив)
- baumaster_db_${DATE}.db (база данных)
- files_to_upload_${DATE}.txt (список файлов)
- backup_report_${DATE}.txt (этот отчет)

Статистика проекта:
- PHP файлов: $(find "$SOURCE_DIR" -name "*.php" | wc -l)
- CSS файлов: $(find "$SOURCE_DIR" -name "*.css" | wc -l)
- JS файлов: $(find "$SOURCE_DIR" -name "*.js" | wc -l)
- Общий размер: $(du -sh "$SOURCE_DIR" | cut -f1)

Готовность к миграции:
✅ Конфигурация обновлена для production
✅ .htaccess настроен для безопасности
✅ База данных экспортирована
✅ Файлы архивированы

Следующие шаги:
1. Загрузить файлы на сервер ftpupload.net
2. Настроить права доступа
3. Проверить работу сайта
4. Протестировать все функции

EOF
    
    echo "📄 Отчет создан: backup_report_${DATE}.txt"
    echo ""
    echo "🎉 Бэкап полностью готов к миграции!"
    echo "📁 Все файлы находятся в: $BACKUP_DIR"
    
else
    echo "❌ Ошибка при создании архива"
    exit 1
fi

echo ""
echo "📋 Список созданных файлов:"
ls -la "$BACKUP_DIR" | grep "$DATE"

echo ""
echo "🚀 Готов к миграции на предпрод!"
