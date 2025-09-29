<?php
/**
 * Система подсчета просмотров
 * Baumaster Views Counter
 */

/**
 * Увеличить счетчик просмотров для элемента
 */
function increment_views($table, $id) {
    $db = get_database();
    
    // Проверяем, существует ли элемент
    $item = $db->select($table, ['id' => $id]);
    if (empty($item)) {
        return false;
    }
    
    // Увеличиваем счетчик просмотров
    $current_views = $db->select($table, ['id' => $id], ['columns' => 'views']);
    if (!empty($current_views)) {
        $new_views = $current_views[0]['views'] + 1;
        $db->update($table, ['views' => $new_views], ['id' => $id]);
    }
    
    return true;
}

/**
 * Получить топ контента по просмотрам
 */
function get_top_content_by_views($limit = 10) {
    $db = get_database();
    $top_content = [];
    
    // Топ услуг
    $services = $db->select('services', ['status' => 'active'], [
        'columns' => 'id, title, views',
        'order' => 'views DESC',
        'limit' => $limit
    ]);
    
    // Добавляем тип к каждому элементу
    foreach ($services as &$service) {
        $service['type'] = 'service';
    }
    
    foreach ($services as $service) {
        if ($service['views'] > 0) {
            $top_content[] = [
                'type' => 'service',
                'id' => $service['id'],
                'title' => $service['title'],
                'views' => $service['views'],
                'url' => "services.php?id={$service['id']}"
            ];
        }
    }
    
    // Топ проектов
    $portfolio = $db->select('portfolio', ['status' => 'active'], [
        'columns' => 'id, title, views',
        'order' => 'views DESC',
        'limit' => $limit
    ]);
    
    foreach ($portfolio as $project) {
        if ($project['views'] > 0) {
            $top_content[] = [
                'type' => 'portfolio',
                'id' => $project['id'],
                'title' => $project['title'],
                'views' => $project['views'],
                'url' => "portfolio.php?id={$project['id']}"
            ];
        }
    }
    
    // Топ статей блога
    $blog = $db->select('blog_posts', ['status' => 'published'], [
        'columns' => 'id, title, views, slug',
        'order' => 'views DESC',
        'limit' => $limit
    ]);
    
    foreach ($blog as $post) {
        if ($post['views'] > 0) {
            $top_content[] = [
                'type' => 'blog',
                'id' => $post['id'],
                'title' => $post['title'],
                'views' => $post['views'],
                'url' => "blog_post.php?slug={$post['slug']}"
            ];
        }
    }
    
    // Сортируем по количеству просмотров
    usort($top_content, function($a, $b) {
        return $b['views'] - $a['views'];
    });
    
    return array_slice($top_content, 0, $limit);
}

/**
 * Получить статистику просмотров
 */
function get_views_statistics() {
    $db = get_database();
    
    $stats = [];
    
    // Статистика услуг
    $services_data = $db->select('services', ['status' => 'active'], ['columns' => 'views']);
    $services_stats = [
        'total' => count($services_data),
        'total_views' => array_sum(array_column($services_data, 'views')),
        'avg_views' => count($services_data) > 0 ? array_sum(array_column($services_data, 'views')) / count($services_data) : 0,
        'max_views' => count($services_data) > 0 ? max(array_column($services_data, 'views')) : 0
    ];
    
    // Статистика портфолио
    $portfolio_data = $db->select('portfolio', ['status' => 'active'], ['columns' => 'views']);
    $portfolio_stats = [
        'total' => count($portfolio_data),
        'total_views' => array_sum(array_column($portfolio_data, 'views')),
        'avg_views' => count($portfolio_data) > 0 ? array_sum(array_column($portfolio_data, 'views')) / count($portfolio_data) : 0,
        'max_views' => count($portfolio_data) > 0 ? max(array_column($portfolio_data, 'views')) : 0
    ];
    
    // Статистика блога
    $blog_data = $db->select('blog_posts', ['status' => 'published'], ['columns' => 'views']);
    $blog_stats = [
        'total' => count($blog_data),
        'total_views' => array_sum(array_column($blog_data, 'views')),
        'avg_views' => count($blog_data) > 0 ? array_sum(array_column($blog_data, 'views')) / count($blog_data) : 0,
        'max_views' => count($blog_data) > 0 ? max(array_column($blog_data, 'views')) : 0
    ];
    
    return [
        'services' => $services_stats,
        'portfolio' => $portfolio_stats,
        'blog' => $blog_stats,
        'total_views' => ($services_stats['total_views'] ?? 0) + 
                        ($portfolio_stats['total_views'] ?? 0) + 
                        ($blog_stats['total_views'] ?? 0)
    ];
}

/**
 * Добавить просмотр для услуги
 */
function view_service($id) {
    return increment_views('services', $id);
}

/**
 * Добавить просмотр для проекта
 */
function view_portfolio($id) {
    return increment_views('portfolio', $id);
}

/**
 * Добавить просмотр для статьи блога
 */
function view_blog_post($id) {
    return increment_views('blog_posts', $id);
}

/**
 * Получить данные активности по дням для графика
 */
function get_activity_chart_data_real($date_from, $date_to) {
    $db = get_database();
    $activity_data = [];
    
    // Создаем массив дней в периоде
    $start_date = new DateTime($date_from);
    $end_date = new DateTime($date_to);
    $interval = new DateInterval('P1D');
    
    $period = new DatePeriod($start_date, $interval, $end_date->modify('+1 day'));
    
    foreach ($period as $date) {
        $date_str = $date->format('Y-m-d');
        
        // Получаем реальные данные активности за день из таблицы daily_activity
        $daily_data = $db->select('daily_activity', ['date' => $date_str]);
        
        if (!empty($daily_data)) {
            // Используем реальные данные из таблицы
            $day_data = $daily_data[0];
            $services_count = $day_data['services_views'];
            $portfolio_count = $day_data['portfolio_views'];
            $blog_count = $day_data['blog_views'];
            $reviews_count = $day_data['reviews_views'];
            $total_count = $day_data['total_views'];
        } else {
            // Если данных нет, возвращаем 0 (реальные данные будут записываться при просмотрах)
            $services_count = 0;
            $portfolio_count = 0;
            $blog_count = 0;
            $reviews_count = 0;
            $total_count = 0;
        }
        
        $activity_data[] = [
            'date' => $date_str,
            'label' => $date->format('d.m'),
            'services' => $services_count,
            'portfolio' => $portfolio_count,
            'reviews' => $reviews_count,
            'blog' => $blog_count,
            'total' => $total_count
        ];
    }
    
    return $activity_data;
}

/**
 * Записать данные активности за день
 */
function record_daily_activity($date, $services_views = 0, $portfolio_views = 0, $blog_views = 0, $reviews_views = 0) {
    $db = get_database();
    $total_views = $services_views + $portfolio_views + $blog_views + $reviews_views;
    
    // Проверяем, есть ли уже данные за этот день
    $existing = $db->select('daily_activity', ['date' => $date]);
    
    if (!empty($existing)) {
        // Обновляем существующие данные
        $db->update('daily_activity', [
            'services_views' => $services_views,
            'portfolio_views' => $portfolio_views,
            'blog_views' => $blog_views,
            'reviews_views' => $reviews_views,
            'total_views' => $total_views
        ], ['date' => $date]);
    } else {
        // Создаем новые данные
        $db->insert('daily_activity', [
            'date' => $date,
            'services_views' => $services_views,
            'portfolio_views' => $portfolio_views,
            'blog_views' => $blog_views,
            'reviews_views' => $reviews_views,
            'total_views' => $total_views
        ]);
    }
}

/**
 * Отслеживание просмотра портфолио
 */
function track_portfolio_view($portfolio_id) {
    $db = get_database();
    
    // Получаем текущий проект портфолио
    $portfolio = $db->select('portfolio', ['id' => $portfolio_id], ['limit' => 1]);
    
    if (!$portfolio || empty($portfolio)) {
        return false;
    }
    
    // Обновляем счетчик просмотров
    $db->update('portfolio', [
        'views' => $portfolio['views'] + 1
    ], ['id' => $portfolio_id]);
    
    // Записываем активность в daily_activity
    $today = date('Y-m-d');
    $existing_activity = $db->select('daily_activity', ['date' => $today], ['limit' => 1]);
    
    if (!empty($existing_activity)) {
        // Обновляем существующую запись
        $current_portfolio_views = $existing_activity['portfolio_views'] ?? 0;
        $current_total = $existing_activity['total_views'] ?? 0;
        
        $db->update('daily_activity', [
            'portfolio_views' => $current_portfolio_views + 1,
            'total_views' => $current_total + 1
        ], ['date' => $today]);
    } else {
        // Создаем новую запись
        $db->insert('daily_activity', [
            'date' => $today,
            'services_views' => 0,
            'portfolio_views' => 1,
            'blog_views' => 0,
            'reviews_views' => 0,
            'total_views' => 1
        ]);
    }
    
    return true;
}

/**
 * Генерировать тестовые данные активности за последние 30 дней
 */
function generate_test_daily_activity() {
    $db = get_database();
    
    // Получаем общие просмотры
    $services_data = $db->select('services', ['status' => 'active'], ['columns' => 'views']);
    $total_services_views = array_sum(array_column($services_data, 'views'));
    
    $portfolio_data = $db->select('portfolio', ['status' => 'active'], ['columns' => 'views']);
    $total_portfolio_views = array_sum(array_column($portfolio_data, 'views'));
    
    $blog_data = $db->select('blog_posts', ['status' => 'published'], ['columns' => 'views']);
    $total_blog_views = array_sum(array_column($blog_data, 'views'));
    
    // Генерируем данные за последние 30 дней
    for ($i = 29; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        
        // Генерируем случайные дневные просмотры
        $services_daily = round($total_services_views * (0.01 + rand(0, 50) / 1000));
        $portfolio_daily = round($total_portfolio_views * (0.01 + rand(0, 50) / 1000));
        $blog_daily = round($total_blog_views * (0.01 + rand(0, 50) / 1000));
        $reviews_daily = rand(0, 3);
        
        record_daily_activity($date, $services_daily, $portfolio_daily, $blog_daily, $reviews_daily);
    }
}
?>
