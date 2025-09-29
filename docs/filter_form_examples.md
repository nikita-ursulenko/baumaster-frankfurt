# Универсальный компонент render_filter_form

## Описание

`render_filter_form` - это универсальный компонент для создания форм фильтрации в админ-панели. Он поддерживает различные типы полей и автоматически применяет правильное выравнивание.

## Основные возможности

- **Поле поиска** с иконкой
- **Dropdown поля** с кастомным дизайном
- **Скрытые поля** для передачи дополнительных параметров
- **Кастомные поля** для специфических случаев
- **Автоматическое выравнивание** элементов формы
- **Гибкая настройка** через массив опций

## Базовое использование

```php
<?php
render_filter_form([
    'fields' => [
        [
            'type' => 'search',
            'name' => 'search',
            'placeholder' => 'Поиск...',
            'value' => $search_value
        ],
        [
            'type' => 'dropdown',
            'name' => 'category',
            'label' => 'Категория',
            'value' => $category_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => 'active', 'text' => 'Активные'],
                ['value' => 'inactive', 'text' => 'Неактивные']
            ],
            'placeholder' => 'Выберите категорию'
        ]
    ],
    'button_text' => 'Фильтр'
]);
?>
```

## Параметры конфигурации

### Основные параметры

| Параметр       | Тип    | По умолчанию                                      | Описание               |
| -------------- | ------ | ------------------------------------------------- | ---------------------- |
| `action`       | string | ''                                                | URL для отправки формы |
| `method`       | string | 'GET'                                             | HTTP метод формы       |
| `class`        | string | 'grid grid-cols-1 md:grid-cols-5 gap-4 items-end' | CSS классы формы       |
| `fields`       | array  | []                                                | Массив полей формы     |
| `button_text`  | string | 'Фильтр'                                          | Текст кнопки           |
| `button_class` | string | 'px-4 py-2 bg-primary-500...'                     | CSS классы кнопки      |

### Типы полей

#### 1. Поле поиска (search)

```php
[
    'type' => 'search',
    'name' => 'search',
    'placeholder' => 'Поиск...',
    'value' => $search_value
]
```

#### 2. Dropdown поле

```php
[
    'type' => 'dropdown',
    'name' => 'status',
    'label' => 'Статус',
    'value' => $status_filter,
    'options' => [
        ['value' => '', 'text' => 'Все'],
        ['value' => 'active', 'text' => 'Активные'],
        ['value' => 'inactive', 'text' => 'Неактивные']
    ],
    'placeholder' => 'Выберите статус',
    'searchable' => false, // опционально
    'class' => 'w-full' // опционально
]
```

#### 3. Скрытое поле

```php
[
    'type' => 'hidden',
    'name' => 'page',
    'value' => '1'
]
```

#### 4. Кастомное поле

```php
[
    'type' => 'custom',
    'content' => '<div class="custom-field">...</div>'
]
```

## Примеры использования

### 1. Портфолио (5 полей)

```php
<?php
render_filter_form([
    'fields' => [
        [
            'type' => 'search',
            'name' => 'search',
            'placeholder' => 'Название проекта...',
            'value' => $search
        ],
        [
            'type' => 'dropdown',
            'name' => 'category',
            'label' => 'Категория',
            'value' => $category_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => 'apartment', 'text' => 'Квартиры'],
                ['value' => 'house', 'text' => 'Дома'],
                ['value' => 'office', 'text' => 'Офисы']
            ],
            'placeholder' => 'Все'
        ],
        [
            'type' => 'dropdown',
            'name' => 'status',
            'label' => 'Статус',
            'value' => $status_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => 'active', 'text' => 'Активные'],
                ['value' => 'inactive', 'text' => 'Скрытые']
            ],
            'placeholder' => 'Все'
        ],
        [
            'type' => 'dropdown',
            'name' => 'featured',
            'label' => 'Рекомендуемые',
            'value' => $featured_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => '1', 'text' => 'Рекомендуемые'],
                ['value' => '0', 'text' => 'Обычные']
            ],
            'placeholder' => 'Все'
        ]
    ],
    'button_text' => 'Фильтр'
]);
?>
```

### 2. Отзывы (4 поля)

```php
<?php
// Подготовка опций рейтинга
$rating_options = [['value' => '', 'text' => 'Все']];
for ($i = 5; $i >= 1; $i--) {
    $rating_options[] = [
        'value' => $i,
        'text' => $i . ' ' . ($i == 1 ? 'звезда' : ($i < 5 ? 'звезды' : 'звезд'))
    ];
}

render_filter_form([
    'fields' => [
        [
            'type' => 'search',
            'name' => 'search',
            'placeholder' => 'Имя клиента...',
            'value' => $search
        ],
        [
            'type' => 'dropdown',
            'name' => 'status',
            'label' => 'Статус',
            'value' => $status_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => 'pending', 'text' => 'На модерации'],
                ['value' => 'published', 'text' => 'Опубликованы'],
                ['value' => 'rejected', 'text' => 'Отклонены']
            ],
            'placeholder' => 'Все'
        ],
        [
            'type' => 'dropdown',
            'name' => 'rating',
            'label' => 'Рейтинг',
            'value' => $rating_filter,
            'options' => $rating_options,
            'placeholder' => 'Все'
        ],
        [
            'type' => 'dropdown',
            'name' => 'verified',
            'label' => 'Проверенные',
            'value' => $verified_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => '1', 'text' => 'Проверенные'],
                ['value' => '0', 'text' => 'Не проверенные']
            ],
            'placeholder' => 'Все'
        ]
    ],
    'button_text' => 'Фильтр'
]);
?>
```

### 3. Услуги (3 поля)

```php
<?php
render_filter_form([
    'class' => 'grid grid-cols-1 md:grid-cols-4 gap-4 items-end',
    'fields' => [
        [
            'type' => 'search',
            'name' => 'search',
            'placeholder' => 'Название услуги...',
            'value' => $search
        ],
        [
            'type' => 'dropdown',
            'name' => 'status',
            'label' => 'Статус',
            'value' => $status_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => 'active', 'text' => 'Активные'],
                ['value' => 'inactive', 'text' => 'Неактивные']
            ],
            'placeholder' => 'Все'
        ],
        [
            'type' => 'dropdown',
            'name' => 'category',
            'label' => 'Категория',
            'value' => $category_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => 'painting', 'text' => 'Малярные работы'],
                ['value' => 'flooring', 'text' => 'Укладка полов'],
                ['value' => 'bathroom', 'text' => 'Ремонт ванных']
            ],
            'placeholder' => 'Все'
        ]
    ],
    'button_text' => 'Фильтр'
]);
?>
```

### 4. Блог с поиском по дате

```php
<?php
render_filter_form([
    'fields' => [
        [
            'type' => 'search',
            'name' => 'search',
            'placeholder' => 'Заголовок статьи...',
            'value' => $search
        ],
        [
            'type' => 'dropdown',
            'name' => 'category',
            'label' => 'Категория',
            'value' => $category_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => 'tips', 'text' => 'Советы'],
                ['value' => 'faq', 'text' => 'FAQ'],
                ['value' => 'news', 'text' => 'Новости']
            ],
            'placeholder' => 'Все'
        ],
        [
            'type' => 'dropdown',
            'name' => 'post_type',
            'label' => 'Тип',
            'value' => $post_type_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => 'article', 'text' => 'Статья'],
                ['value' => 'news', 'text' => 'Новость'],
                ['value' => 'faq', 'text' => 'FAQ']
            ],
            'placeholder' => 'Все'
        ],
        [
            'type' => 'dropdown',
            'name' => 'status',
            'label' => 'Статус',
            'value' => $status_filter,
            'options' => [
                ['value' => '', 'text' => 'Все'],
                ['value' => 'published', 'text' => 'Опубликованы'],
                ['value' => 'draft', 'text' => 'Черновики'],
                ['value' => 'archived', 'text' => 'Архив']
            ],
            'placeholder' => 'Все'
        ]
    ],
    'button_text' => 'Фильтр'
]);
?>
```

## Преимущества

1. **Единообразие** - все формы фильтрации выглядят одинаково
2. **Переиспользование** - один компонент для всех страниц
3. **Гибкость** - легко настраивается под разные нужды
4. **Чистый код** - меньше дублирования HTML
5. **Автоматическое выравнивание** - элементы всегда выровнены правильно
6. **Поддержка i18n** - работает с системой переводов

## Миграция существующих форм

### Было (60+ строк HTML):

```html
<form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1"> Поиск </label>
    <input
      type="text"
      name="search"
      value="<?php echo htmlspecialchars($search); ?>"
      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-primary-500 focus:border-primary-500"
      placeholder="Поиск..."
    />
  </div>

  <div>
    <?php render_dropdown_field([...]); ?>
  </div>

  <!-- еще 3 dropdown'а -->

  <div class="flex items-end">
    <?php render_button([...]); ?>
  </div>
</form>
```

### Стало (20 строк PHP):

```php
<?php
render_filter_form([
    'fields' => [
        ['type' => 'search', 'name' => 'search', 'placeholder' => 'Поиск...', 'value' => $search],
        ['type' => 'dropdown', 'name' => 'category', 'label' => 'Категория', 'value' => $category_filter, 'options' => [...]],
        // остальные поля
    ],
    'button_text' => 'Фильтр'
]);
?>
```

## Заключение

Универсальный компонент `render_filter_form` значительно упрощает создание и поддержку форм фильтрации в админ-панели, обеспечивая единообразие и чистоту кода.
