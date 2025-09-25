#!/bin/bash
# 🚀 Скрипт автоматического развертывания Baumaster Frankfurt

set -e  # Остановка при ошибке

# Цвета для вывода
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Функция логирования
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
    echo "Использование: $0 <environment> [options]"
    echo "Environments: dev, staging, production"
    echo "Options: --skip-tests, --skip-backup, --force"
    exit 1
fi

ENVIRONMENT=$1
SKIP_TESTS=false
SKIP_BACKUP=false
FORCE=false

# Парсинг опций
for arg in "$@"; do
    case $arg in
        --skip-tests)
            SKIP_TESTS=true
            ;;
        --skip-backup)
            SKIP_BACKUP=true
            ;;
        --force)
            FORCE=true
            ;;
    esac
done

# Конфигурация для разных окружений
case $ENVIRONMENT in
    dev)
        DEPLOY_PATH="/var/www/baumaster-dev"
        BACKUP_PATH="/backups/baumaster-dev"
        ;;
    staging)
        DEPLOY_PATH="/var/www/baumaster-staging"
        BACKUP_PATH="/backups/baumaster-staging"
        ;;
    production)
        DEPLOY_PATH="/var/www/baumaster"
        BACKUP_PATH="/backups/baumaster"
        ;;
    *)
        error "Неизвестное окружение: $ENVIRONMENT"
        ;;
esac

log "Начинаем развертывание в окружение: $ENVIRONMENT"
log "Путь развертывания: $DEPLOY_PATH"

# Проверка прав доступа
if [ "$EUID" -ne 0 ]; then
    error "Запустите скрипт с правами root: sudo $0 $@"
fi

# Создание директорий
log "Создание необходимых директорий..."
mkdir -p $DEPLOY_PATH
mkdir -p $BACKUP_PATH
mkdir -p $DEPLOY_PATH/data/logs
mkdir -p $DEPLOY_PATH/data/cache
mkdir -p $DEPLOY_PATH/assets/images

# Бэкап существующей версии
if [ "$SKIP_BACKUP" = false ] && [ -d "$DEPLOY_PATH" ] && [ "$(ls -A $DEPLOY_PATH)" ]; then
    log "Создание бэкапа существующей версии..."
    BACKUP_NAME="backup_$(date +%Y%m%d_%H%M%S)"
    tar -czf "$BACKUP_PATH/$BACKUP_NAME.tar.gz" -C "$DEPLOY_PATH" .
    success "Бэкап создан: $BACKUP_PATH/$BACKUP_NAME.tar.gz"
fi

# Копирование файлов
log "Копирование файлов проекта..."
cp -r . $DEPLOY_PATH/
success "Файлы скопированы"

# Установка прав доступа
log "Установка прав доступа..."
chown -R www-data:www-data $DEPLOY_PATH
chmod -R 755 $DEPLOY_PATH
chmod -R 775 $DEPLOY_PATH/data
chmod -R 775 $DEPLOY_PATH/assets/images
chmod 644 $DEPLOY_PATH/.htaccess
success "Права доступа установлены"

# Настройка конфигурации для окружения
log "Настройка конфигурации для $ENVIRONMENT..."

case $ENVIRONMENT in
    dev)
        # Development настройки
        sed -i "s/DEBUG_MODE.*/DEBUG_MODE', true);/" $DEPLOY_PATH/config.php
        sed -i "s/LOG_LEVEL.*/LOG_LEVEL', 'DEBUG');/" $DEPLOY_PATH/config.php
        ;;
    staging)
        # Staging настройки
        sed -i "s/DEBUG_MODE.*/DEBUG_MODE', false);/" $DEPLOY_PATH/config.php
        sed -i "s/LOG_LEVEL.*/LOG_LEVEL', 'INFO');/" $DEPLOY_PATH/config.php
        ;;
    production)
        # Production настройки
        sed -i "s/DEBUG_MODE.*/DEBUG_MODE', false);/" $DEPLOY_PATH/config.php
        sed -i "s/LOG_LEVEL.*/LOG_LEVEL', 'ERROR');/" $DEPLOY_PATH/config.php
        ;;
esac

success "Конфигурация обновлена"

# Инициализация базы данных
log "Инициализация базы данных..."
cd $DEPLOY_PATH
if [ ! -f "data/database.db" ]; then
    php -r "
    require_once 'config.php';
    require_once 'database.php';
    \$db = get_database();
    echo 'База данных создана\n';
    "
    success "База данных инициализирована"
else
    warning "База данных уже существует"
fi

# Запуск тестов
if [ "$SKIP_TESTS" = false ]; then
    log "Запуск тестов системы..."
    php tests/test_suite.php > /tmp/baumaster_tests.log 2>&1
    if [ $? -eq 0 ]; then
        success "Все тесты пройдены"
    else
        warning "Некоторые тесты не прошли. Проверьте /tmp/baumaster_tests.log"
    fi
fi

# Оптимизация системы
log "Запуск оптимизации..."
php tools/optimizer.php > /tmp/baumaster_optimization.log 2>&1
success "Оптимизация завершена"

# Очистка временных файлов
log "Очистка временных файлов..."
find $DEPLOY_PATH -name "*.tmp" -delete
find $DEPLOY_PATH -name ".DS_Store" -delete
success "Временные файлы удалены"

# Перезапуск веб-сервера
log "Перезапуск веб-сервера..."
if systemctl is-active --quiet apache2; then
    systemctl reload apache2
    success "Apache перезагружен"
elif systemctl is-active --quiet nginx; then
    systemctl reload nginx
    success "Nginx перезагружен"
else
    warning "Веб-сервер не найден или не запущен"
fi

# Проверка работоспособности
log "Проверка работоспособности..."
if curl -f -s http://localhost/ > /dev/null; then
    success "Сайт доступен"
else
    error "Сайт недоступен после развертывания"
fi

# Финальная проверка
log "Финальная проверка развертывания..."
echo "📊 Статистика развертывания:"
echo "   - Окружение: $ENVIRONMENT"
echo "   - Путь: $DEPLOY_PATH"
echo "   - Размер: $(du -sh $DEPLOY_PATH | cut -f1)"
echo "   - Файлов: $(find $DEPLOY_PATH -type f | wc -l)"
echo "   - База данных: $(ls -lh $DEPLOY_PATH/data/database.db 2>/dev/null | awk '{print $5}' || echo 'N/A')"

success "Развертывание завершено успешно! 🎉"

# Уведомления
if [ "$ENVIRONMENT" = "production" ]; then
    log "Отправка уведомления о развертывании..."
    # Здесь можно добавить отправку email или webhook
    echo "Production развертывание завершено: $(date)" >> $DEPLOY_PATH/data/logs/deploy.log
fi

echo ""
echo "🔗 Полезные ссылки:"
echo "   - Сайт: http://localhost/"
echo "   - Админка: http://localhost/admin/"
echo "   - Логи: $DEPLOY_PATH/data/logs/"
echo "   - Бэкапы: $BACKUP_PATH/"
echo ""
echo "📚 Документация:"
echo "   - README: $DEPLOY_PATH/README.md"
echo "   - Руководство: $DEPLOY_PATH/docs/user-guide.md"
echo "   - Техническая: $DEPLOY_PATH/docs/technical.md"
echo ""

