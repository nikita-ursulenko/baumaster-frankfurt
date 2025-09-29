<?php
/**
 * Общие JavaScript функции для админ-панели
 * Baumaster Admin Panel
 */

// Предотвращение прямого доступа
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Генерация базового JavaScript для админки
 */
function render_admin_javascript() {
    ?>
    <script>
        // Функция смены языка
        function changeLanguage(lang) {
            // Отправка AJAX запроса для смены языка
            fetch('?action=change_language', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'language=' + encodeURIComponent(lang) + '&csrf_token=' + getCsrfToken()
            })
            .then(() => {
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Получение CSRF токена
        function getCsrfToken() {
            return '<?php echo generate_csrf_token(); ?>';
        }

        // Подтверждение удаления
        function confirmDelete(message) {
            return confirm(message || '<?php echo __('common.confirm_delete', 'Вы уверены, что хотите удалить?'); ?>');
        }

        // Показ уведомлений
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            const typeClasses = {
                'success': 'bg-green-100 border border-green-200 text-green-700',
                'error': 'bg-red-100 border border-red-200 text-red-700',
                'warning': 'bg-yellow-100 border border-yellow-200 text-yellow-700',
                'info': 'bg-blue-100 border border-blue-200 text-blue-700'
            };
            
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${typeClasses[type] || typeClasses.info}`;
            
            notification.innerHTML = `
                <div class="flex items-center">
                    <span class="mr-2">${message}</span>
                    <button onclick="this.parentNode.parentNode.remove()" class="ml-auto text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Автоматическое скрытие через 5 секунд
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        // Обработка сообщений из URL параметров
        function handleUrlMessages() {
            const urlParams = new URLSearchParams(window.location.search);
            
            if (urlParams.has('error')) {
                showNotification(urlParams.get('error'), 'error');
            }
            
            if (urlParams.has('success')) {
                showNotification(urlParams.get('success'), 'success');
            }
            
            if (urlParams.has('message')) {
                showNotification(urlParams.get('message'), 'info');
            }
            
            // Очистка URL от параметров сообщений
            if (urlParams.has('error') || urlParams.has('success') || urlParams.has('message')) {
                urlParams.delete('error');
                urlParams.delete('success');
                urlParams.delete('message');
                
                const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
                window.history.replaceState({}, document.title, newUrl);
            }
        }

        // Валидация форм
        function validateForm(formElement) {
            const requiredFields = formElement.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            return isValid;
        }

        // Форматирование числа с разделителями
        function formatNumber(num) {
            return new Intl.NumberFormat('ru-RU').format(num);
        }

        // Копирование текста в буфер обмена
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                showNotification('<?php echo __('common.copied', 'Скопировано в буфер обмена'); ?>', 'success');
            }).catch(err => {
                console.error('Error copying to clipboard:', err);
                showNotification('<?php echo __('common.copy_error', 'Ошибка копирования'); ?>', 'error');
            });
        }

        // Инициализация при загрузке страницы
        document.addEventListener('DOMContentLoaded', function() {
            // Обработка сообщений из URL
            handleUrlMessages();
            
            // Добавление обработчиков для всех форм
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!validateForm(this)) {
                        e.preventDefault();
                        showNotification('<?php echo __('common.fill_required', 'Заполните все обязательные поля'); ?>', 'error');
                    }
                });
            });
            
            // Обработчики для кнопок с подтверждением удаления
            document.querySelectorAll('[data-confirm-delete]').forEach(button => {
                button.addEventListener('click', function(e) {
                    const message = this.dataset.confirmDelete || '<?php echo __('common.confirm_delete', 'Вы уверены, что хотите удалить?'); ?>';
                    if (!confirmDelete(message)) {
                        e.preventDefault();
                        return false;
                    }
                });
            });
        });

        // AJAX функции
        function sendAjaxRequest(url, data = {}, method = 'POST') {
            const formData = new FormData();
            
            // Добавляем CSRF токен
            formData.append('csrf_token', getCsrfToken());
            
            // Добавляем остальные данные
            for (const [key, value] of Object.entries(data)) {
                formData.append(key, value);
            }
            
            return fetch(url, {
                method: method,
                body: formData
            })
            .then(response => response.json())
            .catch(error => {
                console.error('AJAX Error:', error);
                throw error;
            });
        }

        // Функция для работы с модальными окнами
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                
                // Автофокус на первом поле ввода
                const firstInput = modal.querySelector('input, textarea, select');
                if (firstInput) {
                    setTimeout(() => firstInput.focus(), 100);
                }
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        }

        // Закрытие модального окна по клику вне его
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-backdrop')) {
                const modal = e.target.closest('.modal');
                if (modal && modal.id) {
                    closeModal(modal.id);
                }
            }
        });

        // Закрытие модального окна по ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.modal:not(.hidden)');
                openModals.forEach(modal => {
                    if (modal.id) {
                        closeModal(modal.id);
                    }
                });
            }
        });

        // Mobile Menu Functions
        function initMobileMenu() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenuClose = document.getElementById('mobile-menu-close');
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');

            if (mobileMenuButton && mobileSidebar) {
                mobileMenuButton.addEventListener('click', function() {
                    openMobileMenu();
                });
            }

            if (mobileMenuClose && mobileSidebar) {
                mobileMenuClose.addEventListener('click', function() {
                    closeMobileMenu();
                });
            }

            if (mobileMenuOverlay) {
                mobileMenuOverlay.addEventListener('click', function() {
                    closeMobileMenu();
                });
            }

            // Close menu when clicking on menu items
            const menuItems = document.querySelectorAll('#mobile-sidebar a');
            menuItems.forEach(item => {
                item.addEventListener('click', function() {
                    closeMobileMenu();
                });
            });
        }

        function openMobileMenu() {
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            
            if (mobileSidebar) {
                mobileSidebar.classList.add('open');
            }
            
            if (mobileMenuOverlay) {
                mobileMenuOverlay.classList.remove('hidden');
                mobileMenuOverlay.classList.add('show');
            }
            
            if (mobileMenuButton) {
                mobileMenuButton.classList.add('active');
            }
            
            document.body.classList.add('overflow-hidden');
        }

        function closeMobileMenu() {
            const mobileSidebar = document.getElementById('mobile-sidebar');
            const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            
            if (mobileSidebar) {
                mobileSidebar.classList.remove('open');
            }
            
            if (mobileMenuOverlay) {
                mobileMenuOverlay.classList.add('hidden');
                mobileMenuOverlay.classList.remove('show');
            }
            
            if (mobileMenuButton) {
                mobileMenuButton.classList.remove('active');
            }
            
            document.body.classList.remove('overflow-hidden');
        }

        // Initialize mobile menu when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            initMobileMenu();
        });

        // Close mobile menu on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeMobileMenu();
            }
        });
    </script>
    <?php
}
