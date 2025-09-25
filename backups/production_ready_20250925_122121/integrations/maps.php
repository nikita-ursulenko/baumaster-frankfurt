<?php
/**
 * Google Maps Integration
 * Baumaster Integrations - Maps
 */

/**
 * Генерация Google Maps кода
 */
function generate_google_maps_code($options = []) {
    $defaults = [
        'api_key' => get_setting('google_maps_api_key', ''),
        'center_lat' => get_setting('company_latitude', '50.1109'),
        'center_lng' => get_setting('company_longitude', '8.6821'),
        'zoom' => 15,
        'width' => '100%',
        'height' => '400px',
        'marker_title' => get_setting('company_name', 'Baumaster Frankfurt'),
        'marker_info' => get_setting('company_address', 'Frankfurt am Main, Deutschland')
    ];
    
    $config = array_merge($defaults, $options);
    
    if (empty($config['api_key'])) {
        return '<div class="map-placeholder" style="width: ' . $config['width'] . '; height: ' . $config['height'] . '; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd;">
            <p>Google Maps API ключ не настроен</p>
        </div>';
    }
    
    $map_id = 'map_' . uniqid();
    
    return <<<HTML
<div id="{$map_id}" style="width: {$config['width']}; height: {$config['height']};"></div>
<script>
function initMap() {
    const map = new google.maps.Map(document.getElementById('{$map_id}'), {
        zoom: {$config['zoom']},
        center: {lat: {$config['center_lat']}, lng: {$config['center_lng']}},
        mapTypeId: 'roadmap'
    });
    
    const marker = new google.maps.Marker({
        position: {lat: {$config['center_lat']}, lng: {$config['center_lng']}},
        map: map,
        title: '{$config['marker_title']}'
    });
    
    const infoWindow = new google.maps.InfoWindow({
        content: '<div><h3>{$config['marker_title']}</h3><p>{$config['marker_info']}</p></div>'
    });
    
    marker.addListener('click', function() {
        infoWindow.open(map, marker);
    });
}

// Загрузка Google Maps API
if (typeof google === 'undefined') {
    const script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key={$config['api_key']}&callback=initMap';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
} else {
    initMap();
}
</script>
HTML;
}

/**
 * Генерация кода для контактной страницы
 */
function generate_contact_map() {
    return generate_google_maps_code([
        'marker_title' => get_setting('company_name', 'Baumaster Frankfurt'),
        'marker_info' => get_setting('company_address', 'Frankfurt am Main, Deutschland') . '<br>' . 
                        get_setting('company_phone', '+49 69 123-456-789') . '<br>' . 
                        get_setting('company_email', 'info@baumaster-frankfurt.de')
    ]);
}

/**
 * Генерация кода для портфолио проектов
 */
function generate_project_map($project_data) {
    if (empty($project_data['location'])) {
        return '';
    }
    
    // Парсинг координат из адреса (упрощенная версия)
    $coordinates = parse_address_to_coordinates($project_data['location']);
    
    if (!$coordinates) {
        return '';
    }
    
    return generate_google_maps_code([
        'center_lat' => $coordinates['lat'],
        'center_lng' => $coordinates['lng'],
        'marker_title' => $project_data['title'],
        'marker_info' => $project_data['location'] . '<br>' . $project_data['description']
    ]);
}

/**
 * Парсинг адреса в координаты (упрощенная версия)
 */
function parse_address_to_coordinates($address) {
    // Для демонстрации возвращаем координаты Франкфурта
    // В реальном проекте здесь должен быть вызов Google Geocoding API
    $default_coordinates = [
        'lat' => '50.1109',
        'lng' => '8.6821'
    ];
    
    // Простая проверка на Франкфурт
    if (stripos($address, 'frankfurt') !== false) {
        return $default_coordinates;
    }
    
    return $default_coordinates;
}

/**
 * Генерация кода для встраивания карты
 */
function generate_embedded_map($width = '100%', $height = '300px') {
    $api_key = get_setting('google_maps_api_key', '');
    $lat = get_setting('company_latitude', '50.1109');
    $lng = get_setting('company_longitude', '8.6821');
    
    if (empty($api_key)) {
        return '<div class="map-placeholder" style="width: ' . $width . '; height: ' . $height . '; background: #f0f0f0; display: flex; align-items: center; justify-content: center; border: 1px solid #ddd;">
            <p>Google Maps API ключ не настроен</p>
        </div>';
    }
    
    $embed_url = "https://www.google.com/maps/embed/v1/place?key={$api_key}&q={$lat},{$lng}";
    
    return <<<HTML
<iframe
    width="{$width}"
    height="{$height}"
    style="border:0"
    loading="lazy"
    allowfullscreen
    referrerpolicy="no-referrer-when-downgrade"
    src="{$embed_url}">
</iframe>
HTML;
}
?>

