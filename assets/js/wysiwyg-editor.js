/**
 * WYSIWYG редактор на основе Quill.js
 * Baumaster Blog Editor
 */

class WysiwygEditor {
    constructor(containerId, options = {}) {
        this.containerId = containerId;
        this.container = document.getElementById(containerId);
        this.quill = null;
        this.options = {
            theme: 'snow',
            placeholder: 'Введите текст статьи...',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    [{ 'align': [] }],
                    ['link', 'image', 'video'],
                    ['blockquote', 'code-block'],
                    ['clean']
                ]
            },
            ...options
        };
        
        this.init();
    }
    
    init() {
        if (!this.container) {
            console.error('WysiwygEditor: Container not found:', this.containerId);
            return;
        }
        
        // Создаем контейнер для Quill
        this.container.innerHTML = '<div id="quill-editor-' + this.containerId + '"></div>';
        
        // Инициализируем Quill
        this.quill = new Quill('#quill-editor-' + this.containerId, this.options);
        
        // Устанавливаем начальное содержимое
        if (this.options.value) {
            this.quill.root.innerHTML = this.options.value;
        }
        
        // Обработчик изменений
        this.quill.on('text-change', () => {
            this.updateHiddenInput();
        });
        
        // Обработчик загрузки изображений
        this.setupImageUpload();
    }
    
    updateHiddenInput() {
        const hiddenInput = document.getElementById(this.containerId + '_hidden');
        if (hiddenInput) {
            hiddenInput.value = this.getContent();
        }
    }
    
    getContent() {
        return this.quill ? this.quill.root.innerHTML : '';
    }
    
    setContent(content) {
        if (this.quill) {
            this.quill.root.innerHTML = content;
        }
    }
    
    getText() {
        return this.quill ? this.quill.getText() : '';
    }
    
    setupImageUpload() {
        const toolbar = this.quill.getModule('toolbar');
        toolbar.addHandler('image', this.selectLocalImage.bind(this));
    }
    
    selectLocalImage() {
        const input = document.createElement('input');
        input.setAttribute('type', 'file');
        input.setAttribute('accept', 'image/*');
        input.click();
        
        input.onchange = () => {
            const file = input.files[0];
            if (file) {
                this.uploadImage(file);
            }
        };
    }
    
    uploadImage(file) {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('action', 'upload_image');
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        
        fetch('/admin/upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const range = this.quill.getSelection();
                this.quill.insertEmbed(range.index, 'image', data.url);
            } else {
                console.error('Ошибка загрузки изображения:', data.error);
                alert('Ошибка загрузки изображения: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Ошибка загрузки изображения:', error);
            alert('Ошибка загрузки изображения');
        });
    }
    
    focus() {
        if (this.quill) {
            this.quill.focus();
        }
    }
    
    enable() {
        if (this.quill) {
            this.quill.enable(true);
        }
    }
    
    disable() {
        if (this.quill) {
            this.quill.enable(false);
        }
    }
}

// Инициализация редактора при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    const editorContainer = document.getElementById('wysiwyg-editor');
    if (editorContainer) {
        window.blogEditor = new WysiwygEditor('wysiwyg-editor', {
            value: editorContainer.dataset.value || '',
            placeholder: 'Введите содержание статьи. Поддерживается HTML разметка.'
        });
    }
});

// Экспорт для использования в других скриптах
window.WysiwygEditor = WysiwygEditor;
