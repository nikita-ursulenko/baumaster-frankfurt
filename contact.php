<?php
/**
 * Страница контактов
 * Baumaster Frontend - Contact Page
 */

// Подключение компонентов
require_once __DIR__ . '/ux/layout.php';
require_once __DIR__ . '/ux/components.php';
require_once __DIR__ . '/ux/data.php';

// Получение данных
$seo = get_seo_data()['contact'];
$contact_info = get_contact_info();

// Начало контента
ob_start();
?>

<!-- Hero Section -->
<section class="pt-16 bg-gradient-to-br from-gray-50 to-gray-100 py-20 min-h-[50vh] flex items-center">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 id="hero-title" class="font-montserrat font-semibold text-4xl lg:text-5xl text-text-primary mb-6">
                <?php 
                $title = 'Свяжитесь с нами';
                $words = explode(' ', $title);
                $directions = ['left', 'right', 'top', 'bottom'];
                foreach ($words as $index => $word) {
                    $direction = $directions[$index % count($directions)];
                    echo '<span class="hero-word hero-word-' . $direction . ' inline-block opacity-0" style="animation-delay: ' . ($index * 0.15) . 's;">' . htmlspecialchars($word) . '</span>';
                    if ($index < count($words) - 1) {
                        echo ' ';
                    }
                }
                ?>
            </h1>
            <p id="hero-subtitle" class="text-xl text-text-secondary max-w-4xl mx-auto hero-subtitle-animate">
                Готовы обсудить ваш проект? Мы всегда рады ответить на вопросы и предоставить 
                бесплатную консультацию по любым видам внутренних работ.
            </p>
        </div>
    </div>
</section>

<style>
/* Hero word animations from different directions */
.hero-word {
    animation: fadeIn 0.8s ease-out forwards;
}

/* Word from left */
.hero-word-left {
    transform: translateX(-50px);
    animation: slideInFromLeft 0.8s ease-out forwards;
}

/* Word from right */
.hero-word-right {
    transform: translateX(50px);
    animation: slideInFromRight 0.8s ease-out forwards;
}

/* Word from top */
.hero-word-top {
    transform: translateY(-30px);
    animation: slideInFromTop 0.8s ease-out forwards;
}

/* Word from bottom */
.hero-word-bottom {
    transform: translateY(30px);
    animation: slideInFromBottom 0.8s ease-out forwards;
}

/* Keyframes for different directions */
@keyframes slideInFromLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInFromRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInFromTop {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInFromBottom {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Ensure proper spacing between words */
.hero-word + .hero-word {
    margin-left: 0.1em;
}

/* Subtitle animation */
.hero-subtitle-animate {
    opacity: 0;
    transform: translateY(20px);
    animation: fadeInUp 0.6s ease-out forwards;
    animation-delay: 0.8s;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- Contact Info -->
<section class="py-8 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="text-center">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-base text-text-primary mb-1">Телефон</h3>
                <p class="text-text-secondary"><?php echo $contact_info['phone']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-base text-text-primary mb-1">Email</h3>
                <p class="text-text-secondary"><?php echo $contact_info['email']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-base text-text-primary mb-1">Адрес</h3>
                <p class="text-text-secondary"><?php echo $contact_info['address']; ?></p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-accent-blue text-white rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-base text-text-primary mb-1">Режим работы</h3>
                <p class="text-text-secondary"><?php echo $contact_info['working_hours']; ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form -->
<section class="py-20 bg-premium-gray">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16">
            <!-- Form -->
            <div>
                <h2 class="font-montserrat font-semibold text-3xl text-text-primary mb-6">
                    Оставьте заявку
                </h2>
                <p class="text-lg text-text-secondary mb-8">
                    Заполните форму, и мы свяжемся с вами в течение 15 минут для обсуждения деталей проекта.
                </p>
                
                <?php render_contact_form([
                    'title' => '',
                    'show_title' => false,
                    'class' => 'shadow-2xl'
                ]); ?>
            </div>
            
            <!-- Additional Info -->
            <div class="space-y-8">
                <div>
                    <h3 class="font-semibold text-2xl text-text-primary mb-6">Быстрая связь</h3>
                    
                    <div class="space-y-4">
                        <?php render_frontend_button([
                            'text' => 'WhatsApp: ' . $contact_info['social']['whatsapp'],
                            'variant' => 'secondary',
                            'size' => 'lg',
                            'class' => 'w-full justify-start',
                            'onclick' => "window.open('https://wa.me/4969123456789', '_blank')",
                            'icon' => '<svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.688z"/></svg>'
                        ]); ?>
                        
                        <?php render_frontend_button([
                            'text' => 'Telegram: ' . $contact_info['social']['telegram'],
                            'variant' => 'secondary',
                            'size' => 'lg',
                            'class' => 'w-full justify-start',
                            'onclick' => "window.open('https://t.me/baumaster_frankfurt', '_blank')",
                            'icon' => '<svg class="h-5 w-5 mr-3" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>'
                        ]); ?>
                    </div>
                </div>
                
                <div class="bg-white p-8 rounded-lg shadow-lg">
                    <h4 class="font-semibold text-xl text-text-primary mb-4">Преимущества работы с нами</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Бесплатный выезд мастера для замера</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Фиксированные цены в договоре</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Гарантия на все виды работ</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Соблюдение договорных сроков</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-text-secondary">Уборка после завершения работ</span>
                        </li>
                    </ul>
                </div>
                
                <div class="bg-accent-blue text-white p-8 rounded-lg">
                    <h4 class="font-semibold text-xl mb-4">Экстренный ремонт</h4>
                    <p class="mb-4">Нужен срочный ремонт? Работаем в выходные и праздничные дни.</p>
                    <?php render_frontend_button([
                        'text' => 'Вызвать мастера',
                        'variant' => 'secondary',
                        'onclick' => 'window.open("tel:+4969123456789")',
                        'class' => 'bg-white text-accent-blue hover:bg-gray-100'
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-montserrat font-semibold text-3xl lg:text-4xl text-text-primary mb-4">
                Мы работаем по всему Франкфурту
            </h2>
            <p class="text-xl text-text-secondary max-w-3xl mx-auto">
                Выполняем проекты во всех районах города и пригородах
            </p>
        </div>
        
        <!-- Interactive Map -->
        <div class="relative">
            <div id="frankfurt-map" class="rounded-lg h-96 w-full bg-gray-100 pointer-events-none"></div>
            
            <!-- Map Overlay - Click to Activate -->
            <div id="map-overlay" class="absolute inset-0 bg-black bg-opacity-20 rounded-lg flex items-center justify-center cursor-pointer transition-all duration-300 hover:bg-opacity-30">
                <div class="text-center">
                    <div class="mb-3">
                        <svg class="h-12 w-12 mx-auto text-white drop-shadow-lg" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <p class="text-white text-sm drop-shadow-md font-medium">Нажмите для активации карты</p>
                </div>
            </div>
            
            <div class="absolute top-4 left-4 bg-white bg-opacity-90 rounded-lg p-3 shadow-lg">
                <h4 class="font-semibold text-sm text-gray-800 mb-1">Frankfurt am Main</h4>
                <p class="text-xs text-gray-600">Deutschland</p>
            </div>
        </div>
        
        <!-- Map Script -->
        <script>
        // Глобальные переменные для карты
        let frankfurtMap = null;
        let mapInitialized = false;
        let districtsData = null;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Координаты Франкфурта
            const frankfurtCoords = [50.1109, 8.6821];
            
            // Элементы overlay
            const mapOverlay = document.getElementById('map-overlay');
            
            // Инициализируем карту сразу при загрузке
            initializeMap();
            
            // Функция инициализации карты
            function initializeMap() {
                if (mapInitialized) return;
                
                // Создаем карту
                frankfurtMap = L.map('frankfurt-map').setView(frankfurtCoords, 11);
                
                // Добавляем слой OpenStreetMap
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                    maxZoom: 18
                }).addTo(frankfurtMap);
            
                // Основная метка Франкфурта
                const mainMarker = L.marker(frankfurtCoords).addTo(frankfurtMap);
                mainMarker.bindPopup(`
                    <div class="text-center">
                        <h3 class="font-semibold text-lg mb-2">Frankfurt am Main</h3>
                        <p class="text-sm text-gray-600 mb-2">Deutschland</p>
                        <p class="text-xs text-gray-500">Мы работаем по всему городу</p>
                    </div>
                `);
                
                // Координаты районов Франкфурта
                districtsData = [
                    { name: 'Altstadt', coords: [50.1109, 8.6821], description: 'Исторический центр' },
                    { name: 'Sachsenhausen', coords: [50.1036, 8.6908], description: 'Южный район' },
                    { name: 'Westend', coords: [50.1200, 8.6500], description: 'Западный район' },
                    { name: 'Nordend', coords: [50.1300, 8.6800], description: 'Северный район' },
                    { name: 'Ostend', coords: [50.1150, 8.7200], description: 'Восточный район' },
                    { name: 'Bornheim', coords: [50.1250, 8.7100], description: 'Жилой район' },
                    { name: 'Bockenheim', coords: [50.1200, 8.6400], description: 'Университетский район' },
                    { name: 'Gallus', coords: [50.1000, 8.6500], description: 'Промышленный район' },
                    { name: 'Höchst', coords: [50.0900, 8.5500], description: 'Западный пригород' },
                    { name: 'Fechenheim', coords: [50.1300, 8.7500], description: 'Восточный пригород' },
                    { name: 'Rödelheim', coords: [50.1100, 8.6000], description: 'Западный пригород' }
                ];
                
                // Добавляем метки районов
                districtsData.forEach(district => {
                    const marker = L.circleMarker(district.coords, {
                        radius: 6,
                        fillColor: '#2C3E50',
                        color: '#fff',
                        weight: 2,
                        opacity: 1,
                        fillOpacity: 0.8
                    }).addTo(frankfurtMap);
                    
                    marker.bindPopup(`
                        <div class="text-center">
                            <h4 class="font-semibold text-sm mb-1">${district.name}</h4>
                            <p class="text-xs text-gray-600">${district.description}</p>
                        </div>
                    `);
                });
                
                // Добавляем кластер для лучшей производительности
                const markers = L.markerClusterGroup();
                markers.addLayer(mainMarker);
                frankfurtMap.addLayer(markers);
                
                // Сохраняем ссылки на карту и районы для глобального доступа
                window.frankfurtMap = frankfurtMap;
                window.districtsData = districtsData;
                
                mapInitialized = true;
            }
            
            // Функция активации карты
            function activateMap() {
                mapOverlay.style.display = 'none';
                // Включаем интерактивность карты
                const mapElement = document.getElementById('frankfurt-map');
                mapElement.classList.remove('pointer-events-none');
            }
            
            // Обработчики событий
            mapOverlay.addEventListener('click', activateMap);
        });
        
        // Функция для показа конкретного района на карте
        function showDistrictOnMap(districtName) {
            // Активируем карту если она еще не активирована
            if (!mapInitialized) {
                activateMap();
            }
            
            if (!window.frankfurtMap || !window.districtsData) return;
            
            const district = window.districtsData.find(d => d.name === districtName);
            if (district) {
                window.frankfurtMap.setView(district.coords, 14);
                
                // Находим и открываем popup для этого района
                window.frankfurtMap.eachLayer(function(layer) {
                    if (layer instanceof L.CircleMarker) {
                        if (layer.getLatLng().lat === district.coords[0] && 
                            layer.getLatLng().lng === district.coords[1]) {
                            layer.openPopup();
                        }
                    }
                });
            }
        }
        
        // Функция для показа всех районов
        function showAllDistricts() {
            // Активируем карту если она еще не активирована
            if (!mapInitialized) {
                activateMap();
            }
            
            if (!window.frankfurtMap || !window.districtsData) return;
            
            const group = new L.featureGroup();
            window.districtsData.forEach(district => {
                group.addLayer(L.marker(district.coords));
            });
            
            window.frankfurtMap.fitBounds(group.getBounds().pad(0.1));
        }
        </script>
        
        <!-- Leaflet CSS and JS -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
        <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
        
        <!-- Districts -->
        <div class="mt-12">
            <h3 class="text-center font-semibold text-xl text-text-primary mb-8">Районы работы</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 text-center">
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Altstadt')">
                    <div class="font-medium">Altstadt</div>
                    <div class="text-xs text-gray-500">Исторический центр</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Sachsenhausen')">
                    <div class="font-medium">Sachsenhausen</div>
                    <div class="text-xs text-gray-500">Южный район</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Westend')">
                    <div class="font-medium">Westend</div>
                    <div class="text-xs text-gray-500">Западный район</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Nordend')">
                    <div class="font-medium">Nordend</div>
                    <div class="text-xs text-gray-500">Северный район</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Ostend')">
                    <div class="font-medium">Ostend</div>
                    <div class="text-xs text-gray-500">Восточный район</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Bornheim')">
                    <div class="font-medium">Bornheim</div>
                    <div class="text-xs text-gray-500">Жилой район</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Bockenheim')">
                    <div class="font-medium">Bockenheim</div>
                    <div class="text-xs text-gray-500">Университетский район</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Gallus')">
                    <div class="font-medium">Gallus</div>
                    <div class="text-xs text-gray-500">Промышленный район</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Höchst')">
                    <div class="font-medium">Höchst</div>
                    <div class="text-xs text-gray-500">Западный пригород</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Fechenheim')">
                    <div class="font-medium">Fechenheim</div>
                    <div class="text-xs text-gray-500">Восточный пригород</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showDistrictOnMap('Rödelheim')">
                    <div class="font-medium">Rödelheim</div>
                    <div class="text-xs text-gray-500">Западный пригород</div>
                </div>
                <div class="bg-gray-50 p-3 rounded hover:bg-accent-blue hover:text-white transition-colors cursor-pointer" onclick="showAllDistricts()">
                    <div class="font-medium">И другие</div>
                    <div class="text-xs text-gray-500">Все районы</div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$content = ob_get_clean();

// Рендеринг страницы
render_frontend_layout([
    'title' => $seo['title'],
    'meta_description' => $seo['description'],
    'active_page' => 'contact',
    'content' => $content
]);
?>

