<?php
/**
 * Модальное окно подтверждения
 * Baumaster Admin Panel - Confirmation Modal Component
 */

function render_confirmation_modal($config = []) {
    $defaults = [
        'id' => 'confirmationModal',
        'title' => 'Подтверждение действия',
        'message' => 'Вы уверены, что хотите выполнить это действие?',
        'confirm_text' => 'Да, подтвердить',
        'cancel_text' => 'Отмена',
        'confirm_variant' => 'danger',
        'icon' => 'warning'
    ];
    
    $config = array_merge($defaults, $config);
    ?>
    
    <!-- Модальное окно подтверждения -->
    <div id="<?php echo $config['id']; ?>" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Иконка -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-<?php echo $config['confirm_variant'] === 'danger' ? 'red' : 'yellow'; ?>-100 mb-4">
                    <?php if ($config['icon'] === 'warning'): ?>
                        <svg class="h-6 w-6 text-<?php echo $config['confirm_variant'] === 'danger' ? 'red' : 'yellow'; ?>-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    <?php elseif ($config['icon'] === 'question'): ?>
                        <svg class="h-6 w-6 text-<?php echo $config['confirm_variant'] === 'danger' ? 'red' : 'yellow'; ?>-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    <?php else: ?>
                        <svg class="h-6 w-6 text-<?php echo $config['confirm_variant'] === 'danger' ? 'red' : 'yellow'; ?>-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    <?php endif; ?>
                </div>
                
                <!-- Заголовок -->
                <h3 class="text-lg font-medium text-gray-900 text-center mb-2">
                    <?php echo htmlspecialchars($config['title']); ?>
                </h3>
                
                <!-- Сообщение -->
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center">
                        <?php echo htmlspecialchars($config['message']); ?>
                    </p>
                </div>
                
                <!-- Кнопки -->
                <div class="items-center px-4 py-3 flex justify-center space-x-3">
                    <button id="<?php echo $config['id']; ?>Cancel" 
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        <?php echo htmlspecialchars($config['cancel_text']); ?>
                    </button>
                    
                    <button id="<?php echo $config['id']; ?>Confirm" 
                            class="px-4 py-2 bg-<?php echo $config['confirm_variant'] === 'danger' ? 'red' : 'blue'; ?>-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-<?php echo $config['confirm_variant'] === 'danger' ? 'red' : 'blue'; ?>-700 focus:outline-none focus:ring-2 focus:ring-<?php echo $config['confirm_variant'] === 'danger' ? 'red' : 'blue'; ?>-500">
                        <?php echo htmlspecialchars($config['confirm_text']); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    // Инициализация модального окна подтверждения
    function initConfirmationModal(modalId) {
        const modal = document.getElementById(modalId);
        const confirmBtn = document.getElementById(modalId + 'Confirm');
        const cancelBtn = document.getElementById(modalId + 'Cancel');
        
        let resolveCallback = null;
        
        // Показать модальное окно
        window.showConfirmationModal = function(message, title = 'Подтверждение действия') {
            const messageElement = modal.querySelector('p');
            const titleElement = modal.querySelector('h3');
            
            if (messageElement) messageElement.textContent = message;
            if (titleElement) titleElement.textContent = title;
            
            modal.classList.remove('hidden');
            
            return new Promise((resolve) => {
                resolveCallback = resolve;
            });
        };
        
        // Скрыть модальное окно
        function hideModal() {
            modal.classList.add('hidden');
        }
        
        // Обработчики событий
        confirmBtn.addEventListener('click', () => {
            hideModal();
            if (resolveCallback) resolveCallback(true);
        });
        
        cancelBtn.addEventListener('click', () => {
            hideModal();
            if (resolveCallback) resolveCallback(false);
        });
        
        // Закрытие по клику на фон
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideModal();
                if (resolveCallback) resolveCallback(false);
            }
        });
        
        // Закрытие по Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                hideModal();
                if (resolveCallback) resolveCallback(false);
            }
        });
    }
    
    // Инициализация при загрузке DOM
    document.addEventListener('DOMContentLoaded', function() {
        initConfirmationModal('<?php echo $config['id']; ?>');
    });
    </script>
    <?php
}
?>
