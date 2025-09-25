// Автоматическая генерация slug из заголовка
function generateSlugFromTitle() {
    const titleInput = document.querySelector('input[name="title"]');
    const slugInput = document.querySelector('input[name="slug"]');
    if (titleInput && slugInput && !slugInput.value) {
        const title = titleInput.value;
        const slug = title.toLowerCase()
            .replace(/[^a-zа-яё0-9\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
        slugInput.value = slug;
    }
}

// Валидация формы перед отправкой
function validateForm() {
    const title = document.querySelector('input[name="title"]').value.trim();
    const content = document.querySelector('textarea[name="content"]').value.trim();
    const category = document.querySelector('select[name="category"]').value;

    let errors = [];

    if (title.length < 5) {
        errors.push('Заголовок должен содержать минимум 5 символов');
    }

    if (title.length > 100) {
        errors.push('Заголовок не должен превышать 100 символов');
    }

    if (content.length < 50) {
        errors.push('Содержание должно содержать минимум 50 символов');
    }

    if (!category) {
        errors.push('Необходимо выбрать категорию');
    }

    if (errors.length > 0) {
        alert('Ошибки валидации:\n' + errors.join('\n'));
        return false;
    }

    return true;
}

// Добавляем обработчики событий
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.querySelector('input[name="title"]');
    const form = document.querySelector('form');

    if (titleInput) {
        titleInput.addEventListener('blur', generateSlugFromTitle);
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
    }
});

function previewPost() {
    const form = document.querySelector("form");

    // Создаем временную форму для отправки данных
    const tempForm = document.createElement('form');
    tempForm.method = 'POST';
    tempForm.action = 'blog_preview.php'; // Ссылка на новый файл превью
    tempForm.target = '_blank'; // Открываем в новой вкладке/окне
    tempForm.style.display = 'none'; // Скрываем форму

    // Копируем данные из основной формы в новую временную форму
    const elements = form.elements;
    for (let i = 0; i < elements.length; i++) {
        const element = elements[i];
        // Важно копировать только те элементы, которые имеют имя и не отключены
        if (element.name && !element.disabled) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = element.name;
            input.value = element.value;
            tempForm.appendChild(input);
        }
    }

    // Добавляем форму в DOM, отправляем и удаляем
    document.body.appendChild(tempForm);
    tempForm.submit();
    document.body.removeChild(tempForm);
} 
