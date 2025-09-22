#!/bin/bash
# 💾 Скрипт автоматического бэкапа Baumaster Frankfurt

set -e

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Конфигурация
SITE_PATH="/var/www/baumaster"
BACKUP_BASE="/backups/baumaster"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="$BACKUP_BASE/$DATE"

# Функции логирования
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}✅ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

error() {
    echo -e "${RED}❌ $1${NC}"
    exit 1
}

# Проверка аргументов
if [ $# -eq 0 ]; then
    echo "Использование: $0 <type> [options]"
    echo "Types: full, database, files, config"
    echo "Options: --compress, --encrypt, --upload"
    exit 1
fi

BACKUP_TYPE=$1
COMPRESS=true
ENCRYPT=false
UPLOAD=false

# Парсинг опций
for arg in "$@"; do
    case $arg in
        --compress)
            COMPRESS=true
            ;;
        --encrypt)
            ENCRYPT=true
            ;;
        --upload)
            UPLOAD=true
            ;;
    esac
done

log "Начинаем бэкап типа: $BACKUP_TYPE"
log "Путь к сайту: $SITE_PATH"
log "Директория бэкапа: $BACKUP_DIR"

# Проверка существования сайта
if [ ! -d "$SITE_PATH" ]; then
    error "Директория сайта не найдена: $SITE_PATH"
fi

# Создание директории бэкапа
mkdir -p "$BACKUP_DIR"
success "Директория бэкапа создана"

# Функция сжатия
compress_backup() {
    local source="$1"
    local target="$2"
    if [ "$COMPRESS" = true ]; then
        tar -czf "$target.tar.gz" -C "$(dirname "$source")" "$(basename "$source")"
        echo "$target.tar.gz"
    else
        cp -r "$source" "$target"
        echo "$target"
    fi
}

# Функция шифрования
encrypt_backup() {
    local file="$1"
    if [ "$ENCRYPT" = true ]; then
        gpg --symmetric --cipher-algo AES256 --output "$file.gpg" "$file"
        rm "$file"
        echo "$file.gpg"
    else
        echo "$file"
    fi
}

# Полный бэкап
if [ "$BACKUP_TYPE" = "full" ]; then
    log "Создание полного бэкапа..."
    
    # Бэкап файлов
    log "Бэкап файлов сайта..."
    FILES_BACKUP=$(compress_backup "$SITE_PATH" "$BACKUP_DIR/files")
    success "Файлы сохранены: $FILES_BACKUP"
    
    # Бэкап базы данных
    if [ -f "$SITE_PATH/data/database.db" ]; then
        log "Бэкап базы данных..."
        cp "$SITE_PATH/data/database.db" "$BACKUP_DIR/database.db"
        success "База данных сохранена"
    fi
    
    # Бэкап конфигурации
    log "Бэкап конфигурации..."
    cp "$SITE_PATH/config.php" "$BACKUP_DIR/config.php"
    cp "$SITE_PATH/.htaccess" "$BACKUP_DIR/.htaccess"
    success "Конфигурация сохранена"
    
    # Создание манифеста
    cat > "$BACKUP_DIR/manifest.txt" << EOF
Baumaster Frankfurt Backup
Date: $(date)
Type: Full Backup
Site Path: $SITE_PATH
Files: $(basename "$FILES_BACKUP")
Database: $(ls -la "$BACKUP_DIR/database.db" 2>/dev/null | awk '{print $5}' || echo "N/A")
Config: config.php, .htaccess
EOF
    
    success "Полный бэкап создан: $BACKUP_DIR"
fi

# Бэкап только базы данных
if [ "$BACKUP_TYPE" = "database" ]; then
    log "Создание бэкапа базы данных..."
    
    if [ -f "$SITE_PATH/data/database.db" ]; then
        cp "$SITE_PATH/data/database.db" "$BACKUP_DIR/database_$DATE.db"
        
        # Создание SQL дампа
        sqlite3 "$SITE_PATH/data/database.db" ".dump" > "$BACKUP_DIR/database_$DATE.sql"
        
        success "База данных сохранена: database_$DATE.db"
        success "SQL дамп создан: database_$DATE.sql"
    else
        error "Файл базы данных не найден"
    fi
fi

# Бэкап только файлов
if [ "$BACKUP_TYPE" = "files" ]; then
    log "Создание бэкапа файлов..."
    
    # Исключаем системные файлы
    tar --exclude='data/database.db' \
        --exclude='data/logs/*' \
        --exclude='data/cache/*' \
        --exclude='.git' \
        --exclude='*.tmp' \
        -czf "$BACKUP_DIR/files_$DATE.tar.gz" \
        -C "$SITE_PATH" .
    
    success "Файлы сохранены: files_$DATE.tar.gz"
fi

# Бэкап конфигурации
if [ "$BACKUP_TYPE" = "config" ]; then
    log "Создание бэкапа конфигурации..."
    
    mkdir -p "$BACKUP_DIR/config"
    cp "$SITE_PATH/config.php" "$BACKUP_DIR/config/"
    cp "$SITE_PATH/.htaccess" "$BACKUP_DIR/config/"
    cp "$SITE_PATH/functions.php" "$BACKUP_DIR/config/"
    cp -r "$SITE_PATH/lang" "$BACKUP_DIR/config/"
    
    success "Конфигурация сохранена в: $BACKUP_DIR/config/"
fi

# Шифрование бэкапа
if [ "$ENCRYPT" = true ]; then
    log "Шифрование бэкапа..."
    for file in "$BACKUP_DIR"/*; do
        if [ -f "$file" ] && [[ "$file" != *.gpg ]]; then
            encrypt_backup "$file"
        fi
    done
    success "Бэкап зашифрован"
fi

# Загрузка в облако (если настроено)
if [ "$UPLOAD" = true ]; then
    log "Загрузка бэкапа в облако..."
    # Здесь можно добавить загрузку в AWS S3, Google Drive и т.д.
    warning "Функция загрузки не настроена"
fi

# Очистка старых бэкапов
log "Очистка старых бэкапов..."
find "$BACKUP_BASE" -type d -mtime +30 -exec rm -rf {} \; 2>/dev/null || true
success "Старые бэкапы удалены"

# Создание символической ссылки на последний бэкап
ln -sfn "$BACKUP_DIR" "$BACKUP_BASE/latest"
success "Создана ссылка на последний бэкап"

# Статистика бэкапа
log "Статистика бэкапа:"
echo "   - Тип: $BACKUP_TYPE"
echo "   - Директория: $BACKUP_DIR"
echo "   - Размер: $(du -sh "$BACKUP_DIR" | cut -f1)"
echo "   - Файлов: $(find "$BACKUP_DIR" -type f | wc -l)"
echo "   - Дата: $(date)"

success "Бэкап завершен успешно! 💾"

# Уведомления
if command -v mail >/dev/null 2>&1; then
    echo "Baumaster Frankfurt backup completed successfully at $(date)" | \
        mail -s "Backup Completed" admin@baumaster-frankfurt.de
fi

echo ""
echo "📁 Бэкап сохранен в: $BACKUP_DIR"
echo "🔗 Последний бэкап: $BACKUP_BASE/latest"
echo ""

