/**
 * Компонент выбора изображения для блога
 * Baumaster Image Selector
 */

class ImageSelector {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        this.options = {
            uploadUrl: '/admin/upload.php',
            galleryUrl: '/admin/gallery.php',
            allowedTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            maxSize: 5 * 1024 * 1024, // 5MB
            ...options
        };
        
        this.currentImage = null;
        this.init();
    }
    
    init() {
        if (!this.container) {
            console.error('ImageSelector: Container not found:', this.containerId);
            return;
        }
        
        this.render();
        this.bindEvents();
    }
    
    render() {
        this.container.innerHTML = `
            <div class="image-selector">
                <div class="image-preview" id="image-preview-${this.containerId}" style="display: none;">
                    <img id="preview-img-${this.containerId}" src="" alt="Предварительный просмотр" class="w-full h-48 object-cover rounded-lg">
                    <div class="image-actions mt-2 flex space-x-2">
                        <button type="button" class="px-3 py-1 bg-blue-500 text-white rounded text-sm hover:bg-blue-600" onclick="imageSelector${this.containerId}.openGallery()">
                            Выбрать другое
                        </button>
                        <button type="button" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600" onclick="imageSelector${this.containerId}.removeImage()">
                            Удалить
                        </button>
                    </div>
                </div>
                <div class="image-upload" id="image-upload-${this.containerId}">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors cursor-pointer" onclick="imageSelector${this.containerId}.openFileDialog()">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">Нажмите для загрузки изображения</p>
                        <p class="text-xs text-gray-500">PNG, JPG, GIF до 5MB</p>
                    </div>
                </div>
                <input type="file" id="file-input-${this.containerId}" accept="image/*" style="display: none;" onchange="imageSelector${this.containerId}.handleFileSelect(event)">
                <input type="hidden" id="image-url-${this.containerId}" name="featured_image" value="">
            </div>
        `;
    }
    
    bindEvents() {
        // Создаем глобальную ссылку для вызова из HTML
        window['imageSelector' + this.containerId] = this;
    }
    
    openFileDialog() {
        document.getElementById('file-input-' + this.containerId).click();
    }
    
    openGallery() {
        // Открываем модальное окно с галереей
        this.showGalleryModal();
    }
    
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            this.uploadImage(file);
        }
    }
    
    uploadImage(file) {
        // Проверка типа файла
        if (!this.options.allowedTypes.includes(file.type)) {
            alert('Недопустимый тип файла. Разрешены: JPEG, PNG, GIF, WebP');
            return;
        }
        
        // Проверка размера файла
        if (file.size > this.options.maxSize) {
            alert('Файл слишком большой. Максимальный размер: 5MB');
            return;
        }
        
        const formData = new FormData();
        formData.append('image', file);
        formData.append('action', 'upload_image');
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        
        // Показываем индикатор загрузки
        this.showLoading();
        
        fetch(this.options.uploadUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoading();
            if (data.success) {
                this.setImage(data.url, data.filename);
            } else {
                alert('Ошибка загрузки изображения: ' + data.error);
            }
        })
        .catch(error => {
            this.hideLoading();
            console.error('Ошибка загрузки изображения:', error);
            alert('Ошибка загрузки изображения');
        });
    }
    
    setImage(url, filename = '') {
        this.currentImage = { url, filename };
        
        // Обновляем скрытое поле
        document.getElementById('image-url-' + this.containerId).value = url;
        
        // Показываем превью
        const preview = document.getElementById('image-preview-' + this.containerId);
        const upload = document.getElementById('image-upload-' + this.containerId);
        const img = document.getElementById('preview-img-' + this.containerId);
        
        img.src = url;
        preview.style.display = 'block';
        upload.style.display = 'none';
    }
    
    removeImage() {
        this.currentImage = null;
        
        // Очищаем скрытое поле
        document.getElementById('image-url-' + this.containerId).value = '';
        
        // Скрываем превью
        const preview = document.getElementById('image-preview-' + this.containerId);
        const upload = document.getElementById('image-upload-' + this.containerId);
        
        preview.style.display = 'none';
        upload.style.display = 'block';
    }
    
    showLoading() {
        const upload = document.getElementById('image-upload-' + this.containerId);
        upload.innerHTML = `
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                <p class="mt-2 text-sm text-gray-600">Загрузка...</p>
            </div>
        `;
    }
    
    hideLoading() {
        this.render();
        this.bindEvents();
    }
    
    showGalleryModal() {
        // Создаем модальное окно с галереей
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50';
        modal.innerHTML = `
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Выберите изображение</h3>
                        <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="gallery-content-${this.containerId}" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 max-h-96 overflow-y-auto">
                        <div class="text-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500 mx-auto"></div>
                            <p class="mt-2 text-sm text-gray-600">Загрузка галереи...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Загружаем галерею
        this.loadGallery(modal);
    }
    
    loadGallery(modal) {
        fetch(this.options.galleryUrl + '?action=get_images')
        .then(response => response.json())
        .then(data => {
            const galleryContent = modal.querySelector('#gallery-content-' + this.containerId);
            
            if (data.success && data.images.length > 0) {
                galleryContent.innerHTML = data.images.map(image => `
                    <div class="relative group cursor-pointer" onclick="imageSelector${this.containerId}.selectFromGallery('${image.url}', '${image.filename}')">
                        <img src="${image.thumbnail || image.url}" alt="${image.filename}" class="w-full h-24 object-cover rounded-lg">
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                `).join('');
            } else {
                galleryContent.innerHTML = `
                    <div class="col-span-full text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-2">Изображения не найдены</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки галереи:', error);
            const galleryContent = modal.querySelector('#gallery-content-' + this.containerId);
            galleryContent.innerHTML = `
                <div class="col-span-full text-center py-8 text-red-500">
                    <p>Ошибка загрузки галереи</p>
                </div>
            `;
        });
    }
    
    selectFromGallery(url, filename) {
        this.setImage(url, filename);
        // Закрываем модальное окно
        document.querySelector('.fixed.inset-0').remove();
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const imageSelectorContainer = document.getElementById('image-selector');
    if (imageSelectorContainer) {
        window.imageSelector = new ImageSelector('image-selector');
        
        // Устанавливаем существующее изображение, если оно есть
        const currentImage = imageSelectorContainer.dataset.currentImage;
        if (currentImage) {
            window.imageSelector.setImage(currentImage);
        }
    }
});

// Экспорт для использования в других скриптах
window.ImageSelector = ImageSelector;
