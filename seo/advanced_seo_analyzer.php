<?php
/**
 * Advanced SEO Analyzer
 * Baumaster SEO Tools - Comprehensive Page Analysis
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once __DIR__ . '/../functions.php';

/**
 * Комплексный анализ страницы
 */
function analyze_page_seo($url, $content = null) {
    if ($content === null) {
        $content = get_page_content($url);
    }
    
    $analysis = [
        'url' => $url,
        'timestamp' => time(),
        'score' => 0,
        'max_score' => 100,
        'checks' => [],
        'recommendations' => [],
        'critical_issues' => [],
        'warnings' => [],
        'successes' => []
    ];
    
    // 1. Анализ мета-тегов (20 баллов)
    $meta_analysis = analyze_meta_tags($content);
    $analysis['checks']['meta_tags'] = $meta_analysis;
    $analysis['score'] += $meta_analysis['score'];
    
    // 2. Анализ заголовков (15 баллов)
    $heading_analysis = analyze_headings($content);
    $analysis['checks']['headings'] = $heading_analysis;
    $analysis['score'] += $heading_analysis['score'];
    
    // 3. Анализ контента (15 баллов)
    $content_analysis = analyze_content($content);
    $analysis['checks']['content'] = $content_analysis;
    $analysis['score'] += $content_analysis['score'];
    
    // 4. Анализ изображений (10 баллов)
    $image_analysis = analyze_images($content);
    $analysis['checks']['images'] = $image_analysis;
    $analysis['score'] += $image_analysis['score'];
    
    // 5. Анализ внутренних ссылок (10 баллов)
    $link_analysis = analyze_internal_links($content);
    $analysis['checks']['internal_links'] = $link_analysis;
    $analysis['score'] += $link_analysis['score'];
    
    // 6. Анализ структуры URL (5 баллов)
    $url_analysis = analyze_url_structure($url);
    $analysis['checks']['url_structure'] = $url_analysis;
    $analysis['score'] += $url_analysis['score'];
    
    // 7. Анализ производительности (10 баллов)
    $performance_analysis = analyze_performance($content);
    $analysis['checks']['performance'] = $performance_analysis;
    $analysis['score'] += $performance_analysis['score'];
    
    // 8. Анализ мобильной оптимизации (10 баллов)
    $mobile_analysis = analyze_mobile_optimization($content);
    $analysis['checks']['mobile'] = $mobile_analysis;
    $analysis['score'] += $mobile_analysis['score'];
    
    // 9. Анализ технического SEO (5 баллов)
    $technical_analysis = analyze_technical_seo($content);
    $analysis['checks']['technical'] = $technical_analysis;
    $analysis['score'] += $technical_analysis['score'];
    
    // Собираем рекомендации
    $analysis['recommendations'] = collect_recommendations($analysis['checks']);
    $analysis['critical_issues'] = collect_critical_issues($analysis['checks']);
    $analysis['warnings'] = collect_warnings($analysis['checks']);
    $analysis['successes'] = collect_successes($analysis['checks']);
    
    return $analysis;
}

/**
 * Анализ мета-тегов
 */
function analyze_meta_tags($content) {
    $analysis = [
        'score' => 0,
        'max_score' => 20,
        'checks' => [],
        'issues' => []
    ];
    
    // Проверка title
    if (preg_match('/<title[^>]*>(.*?)<\/title>/i', $content, $matches)) {
        $title = trim(strip_tags($matches[1]));
        $title_length = mb_strlen($title);
        
        if ($title_length >= 30 && $title_length <= 60) {
            $analysis['checks']['title'] = ['status' => 'good', 'score' => 5, 'message' => 'Длина title оптимальна'];
            $analysis['score'] += 5;
        } elseif ($title_length > 0) {
            $analysis['checks']['title'] = ['status' => 'warning', 'score' => 2, 'message' => 'Длина title неоптимальна'];
            $analysis['score'] += 2;
        } else {
            $analysis['checks']['title'] = ['status' => 'bad', 'score' => 0, 'message' => 'Title отсутствует'];
        }
    } else {
        $analysis['checks']['title'] = ['status' => 'bad', 'score' => 0, 'message' => 'Title отсутствует'];
    }
    
    // Проверка meta description
    if (preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $content, $matches)) {
        $description = trim($matches[1]);
        $desc_length = mb_strlen($description);
        
        if ($desc_length >= 120 && $desc_length <= 160) {
            $analysis['checks']['description'] = ['status' => 'good', 'score' => 5, 'message' => 'Meta description оптимальна'];
            $analysis['score'] += 5;
        } elseif ($desc_length > 0) {
            $analysis['checks']['description'] = ['status' => 'warning', 'score' => 2, 'message' => 'Meta description неоптимальна'];
            $analysis['score'] += 2;
        } else {
            $analysis['checks']['description'] = ['status' => 'bad', 'score' => 0, 'message' => 'Meta description пуста'];
        }
    } else {
        $analysis['checks']['description'] = ['status' => 'bad', 'score' => 0, 'message' => 'Meta description отсутствует'];
    }
    
    // Проверка Open Graph тегов
    $og_tags = ['og:title', 'og:description', 'og:image', 'og:url'];
    $og_score = 0;
    foreach ($og_tags as $tag) {
        if (preg_match('/<meta[^>]*property=["\']' . preg_quote($tag, '/') . '["\'][^>]*>/i', $content)) {
            $og_score += 1;
        }
    }
    
    if ($og_score >= 3) {
        $analysis['checks']['og_tags'] = ['status' => 'good', 'score' => 5, 'message' => 'Open Graph теги настроены'];
        $analysis['score'] += 5;
    } elseif ($og_score > 0) {
        $analysis['checks']['og_tags'] = ['status' => 'warning', 'score' => 2, 'message' => 'Open Graph теги частично настроены'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['og_tags'] = ['status' => 'bad', 'score' => 0, 'message' => 'Open Graph теги отсутствуют'];
    }
    
    // Проверка canonical URL
    if (preg_match('/<link[^>]*rel=["\']canonical["\'][^>]*>/i', $content)) {
        $analysis['checks']['canonical'] = ['status' => 'good', 'score' => 3, 'message' => 'Canonical URL настроен'];
        $analysis['score'] += 3;
    } else {
        $analysis['checks']['canonical'] = ['status' => 'warning', 'score' => 0, 'message' => 'Canonical URL отсутствует'];
    }
    
    // Проверка robots meta
    if (preg_match('/<meta[^>]*name=["\']robots["\'][^>]*>/i', $content)) {
        $analysis['checks']['robots'] = ['status' => 'good', 'score' => 2, 'message' => 'Robots meta настроен'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['robots'] = ['status' => 'warning', 'score' => 0, 'message' => 'Robots meta отсутствует'];
    }
    
    return $analysis;
}

/**
 * Анализ заголовков
 */
function analyze_headings($content) {
    $analysis = [
        'score' => 0,
        'max_score' => 15,
        'checks' => [],
        'issues' => []
    ];
    
    // Подсчет заголовков (улучшенная версия для многострочного контента)
    $heading_counts = [];
    for ($i = 1; $i <= 6; $i++) {
        preg_match_all("/<h{$i}[^>]*>(.*?)<\/h{$i}>/is", $content, $matches);
        $heading_counts["h{$i}"] = count($matches[0]);
    }
    
    // Проверка H1 (улучшенная версия для анимированных заголовков)
    if ($heading_counts['h1'] === 1) {
        // Дополнительная проверка: извлекаем текст из H1
        preg_match_all('/<h1[^>]*>(.*?)<\/h1>/is', $content, $h1_matches);
        if (!empty($h1_matches[1])) {
            $h1_text = strip_tags($h1_matches[1][0]);
            $h1_text = preg_replace('/\s+/', ' ', trim($h1_text));
            
            if (!empty($h1_text)) {
                $analysis['checks']['h1_count'] = ['status' => 'good', 'score' => 5, 'message' => 'H1 заголовок присутствует: ' . $h1_text];
                $analysis['score'] += 5;
            } else {
                $analysis['checks']['h1_count'] = ['status' => 'bad', 'score' => 0, 'message' => 'H1 заголовок пустой'];
            }
        } else {
            $analysis['checks']['h1_count'] = ['status' => 'bad', 'score' => 0, 'message' => 'H1 заголовок не найден'];
        }
    } elseif ($heading_counts['h1'] === 0) {
        $analysis['checks']['h1_count'] = ['status' => 'bad', 'score' => 0, 'message' => 'H1 заголовок отсутствует'];
    } else {
        $analysis['checks']['h1_count'] = ['status' => 'warning', 'score' => 2, 'message' => 'Несколько H1 заголовков'];
        $analysis['score'] += 2;
    }
    
    // Проверка иерархии заголовков
    $total_headings = array_sum($heading_counts);
    if ($total_headings >= 3) {
        $analysis['checks']['heading_structure'] = ['status' => 'good', 'score' => 5, 'message' => 'Хорошая структура заголовков'];
        $analysis['score'] += 5;
    } elseif ($total_headings > 0) {
        $analysis['checks']['heading_structure'] = ['status' => 'warning', 'score' => 2, 'message' => 'Слабая структура заголовков'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['heading_structure'] = ['status' => 'bad', 'score' => 0, 'message' => 'Заголовки отсутствуют'];
    }
    
    // Проверка длины заголовков
    preg_match_all('/<h[1-6][^>]*>(.*?)<\/h[1-6]>/i', $content, $matches);
    $long_headings = 0;
    foreach ($matches[1] as $heading) {
        $heading_text = trim(strip_tags($heading));
        if (mb_strlen($heading_text) > 60) {
            $long_headings++;
        }
    }
    
    if ($long_headings === 0) {
        $analysis['checks']['heading_length'] = ['status' => 'good', 'score' => 5, 'message' => 'Заголовки оптимальной длины'];
        $analysis['score'] += 5;
    } else {
        $analysis['checks']['heading_length'] = ['status' => 'warning', 'score' => 2, 'message' => 'Некоторые заголовки слишком длинные'];
        $analysis['score'] += 2;
    }
    
    return $analysis;
}

/**
 * Анализ контента
 */
function analyze_content($content) {
    $analysis = [
        'score' => 0,
        'max_score' => 15,
        'checks' => [],
        'issues' => []
    ];
    
    // Извлечение текстового контента
    $text_content = strip_tags($content);
    $text_length = mb_strlen(trim($text_content));
    $word_count = str_word_count($text_content);
    
    // Проверка длины контента
    if ($word_count >= 300) {
        $analysis['checks']['content_length'] = ['status' => 'good', 'score' => 5, 'message' => 'Достаточная длина контента'];
        $analysis['score'] += 5;
    } elseif ($word_count >= 150) {
        $analysis['checks']['content_length'] = ['status' => 'warning', 'score' => 2, 'message' => 'Контент средней длины'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['content_length'] = ['status' => 'bad', 'score' => 0, 'message' => 'Слишком короткий контент'];
    }
    
    // Проверка плотности ключевых слов (базовая)
    $analysis['checks']['keyword_density'] = ['status' => 'warning', 'score' => 3, 'message' => 'Проверьте плотность ключевых слов'];
    $analysis['score'] += 3;
    
    // Проверка внутренних ссылок в тексте
    preg_match_all('/<a[^>]*href=["\']([^"\']*)["\'][^>]*>/i', $content, $matches);
    $internal_links = 0;
    foreach ($matches[1] as $link) {
        if (!preg_match('/^(https?:|\/\/)/', $link) || strpos($link, $_SERVER['HTTP_HOST'] ?? '') !== false) {
            $internal_links++;
        }
    }
    
    if ($internal_links >= 2) {
        $analysis['checks']['internal_links'] = ['status' => 'good', 'score' => 4, 'message' => 'Достаточно внутренних ссылок'];
        $analysis['score'] += 4;
    } elseif ($internal_links > 0) {
        $analysis['checks']['internal_links'] = ['status' => 'warning', 'score' => 2, 'message' => 'Мало внутренних ссылок'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['internal_links'] = ['status' => 'bad', 'score' => 0, 'message' => 'Внутренние ссылки отсутствуют'];
    }
    
    // Проверка читаемости (базовая)
    $sentences = preg_split('/[.!?]+/', $text_content);
    $avg_sentence_length = 0;
    if (count($sentences) > 0) {
        $total_words = 0;
        foreach ($sentences as $sentence) {
            $total_words += str_word_count(trim($sentence));
        }
        $avg_sentence_length = $total_words / count($sentences);
    }
    
    if ($avg_sentence_length <= 20) {
        $analysis['checks']['readability'] = ['status' => 'good', 'score' => 3, 'message' => 'Хорошая читаемость'];
        $analysis['score'] += 3;
    } else {
        $analysis['checks']['readability'] = ['status' => 'warning', 'score' => 1, 'message' => 'Улучшите читаемость'];
        $analysis['score'] += 1;
    }
    
    return $analysis;
}

/**
 * Анализ изображений
 */
function analyze_images($content) {
    $analysis = [
        'score' => 0,
        'max_score' => 10,
        'checks' => [],
        'issues' => []
    ];
    
    preg_match_all('/<img[^>]+>/i', $content, $images);
    $total_images = count($images[0]);
    
    if ($total_images === 0) {
        $analysis['checks']['images'] = ['status' => 'warning', 'score' => 0, 'message' => 'Изображения отсутствуют'];
        return $analysis;
    }
    
    // Проверка alt атрибутов
    $images_with_alt = 0;
    $images_without_alt = 0;
    
    foreach ($images[0] as $img) {
        if (preg_match('/alt\s*=\s*["\']([^"\']*)["\']/', $img, $matches)) {
            $alt_text = trim($matches[1]);
            if (!empty($alt_text)) {
                $images_with_alt++;
            } else {
                $images_without_alt++;
            }
        } else {
            $images_without_alt++;
        }
    }
    
    $alt_percentage = ($images_with_alt / $total_images) * 100;
    
    if ($alt_percentage >= 90) {
        $analysis['checks']['alt_attributes'] = ['status' => 'good', 'score' => 5, 'message' => 'Alt атрибуты настроены'];
        $analysis['score'] += 5;
    } elseif ($alt_percentage >= 50) {
        $analysis['checks']['alt_attributes'] = ['status' => 'warning', 'score' => 2, 'message' => 'Alt атрибуты частично настроены'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['alt_attributes'] = ['status' => 'bad', 'score' => 0, 'message' => 'Alt атрибуты отсутствуют'];
    }
    
    // Проверка размеров изображений
    $optimized_images = 0;
    foreach ($images[0] as $img) {
        if (preg_match('/class\s*=\s*["\']([^"\']*)["\']/', $img, $matches)) {
            if (strpos($matches[1], 'lazy') !== false) {
                $optimized_images++;
            }
        }
    }
    
    if ($optimized_images >= $total_images * 0.5) {
        $analysis['checks']['image_optimization'] = ['status' => 'good', 'score' => 3, 'message' => 'Изображения оптимизированы'];
        $analysis['score'] += 3;
    } else {
        $analysis['checks']['image_optimization'] = ['status' => 'warning', 'score' => 1, 'message' => 'Изображения требуют оптимизации'];
        $analysis['score'] += 1;
    }
    
    // Проверка количества изображений
    if ($total_images >= 1 && $total_images <= 10) {
        $analysis['checks']['image_count'] = ['status' => 'good', 'score' => 2, 'message' => 'Оптимальное количество изображений'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['image_count'] = ['status' => 'warning', 'score' => 1, 'message' => 'Проверьте количество изображений'];
        $analysis['score'] += 1;
    }
    
    return $analysis;
}

/**
 * Анализ внутренних ссылок
 */
function analyze_internal_links($content) {
    $analysis = [
        'score' => 0,
        'max_score' => 10,
        'checks' => [],
        'issues' => []
    ];
    
    preg_match_all('/<a[^>]*href=["\']([^"\']*)["\'][^>]*>/i', $content, $matches);
    $total_links = count($matches[0]);
    $internal_links = 0;
    $external_links = 0;
    
    foreach ($matches[1] as $link) {
        if (preg_match('/^(https?:|\/\/)/', $link)) {
            if (strpos($link, $_SERVER['HTTP_HOST'] ?? '') !== false) {
                $internal_links++;
            } else {
                $external_links++;
            }
        } else {
            $internal_links++;
        }
    }
    
    // Проверка соотношения внутренних и внешних ссылок
    if ($total_links > 0) {
        $internal_ratio = ($internal_links / $total_links) * 100;
        
        if ($internal_ratio >= 70) {
            $analysis['checks']['link_ratio'] = ['status' => 'good', 'score' => 5, 'message' => 'Хорошее соотношение ссылок'];
            $analysis['score'] += 5;
        } elseif ($internal_ratio >= 40) {
            $analysis['checks']['link_ratio'] = ['status' => 'warning', 'score' => 2, 'message' => 'Среднее соотношение ссылок'];
            $analysis['score'] += 2;
        } else {
            $analysis['checks']['link_ratio'] = ['status' => 'bad', 'score' => 0, 'message' => 'Плохое соотношение ссылок'];
        }
    }
    
    // Проверка количества ссылок
    if ($total_links >= 3 && $total_links <= 20) {
        $analysis['checks']['link_count'] = ['status' => 'good', 'score' => 3, 'message' => 'Оптимальное количество ссылок'];
        $analysis['score'] += 3;
    } elseif ($total_links > 0) {
        $analysis['checks']['link_count'] = ['status' => 'warning', 'score' => 1, 'message' => 'Проверьте количество ссылок'];
        $analysis['score'] += 1;
    } else {
        $analysis['checks']['link_count'] = ['status' => 'bad', 'score' => 0, 'message' => 'Ссылки отсутствуют'];
    }
    
    // Проверка nofollow атрибутов
    $nofollow_links = 0;
    foreach ($matches[0] as $link_tag) {
        if (preg_match('/rel\s*=\s*["\']([^"\']*)["\']/', $link_tag, $rel_matches)) {
            if (strpos($rel_matches[1], 'nofollow') !== false) {
                $nofollow_links++;
            }
        }
    }
    
    if ($external_links > 0 && $nofollow_links >= $external_links * 0.5) {
        $analysis['checks']['nofollow'] = ['status' => 'good', 'score' => 2, 'message' => 'Nofollow атрибуты настроены'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['nofollow'] = ['status' => 'warning', 'score' => 0, 'message' => 'Добавьте nofollow для внешних ссылок'];
    }
    
    return $analysis;
}

/**
 * Анализ структуры URL
 */
function analyze_url_structure($url) {
    $analysis = [
        'score' => 0,
        'max_score' => 5,
        'checks' => [],
        'issues' => []
    ];
    
    $parsed_url = parse_url($url);
    $path = $parsed_url['path'] ?? '';
    
    // Проверка длины URL
    $url_length = mb_strlen($url);
    if ($url_length <= 100) {
        $analysis['checks']['url_length'] = ['status' => 'good', 'score' => 2, 'message' => 'URL оптимальной длины'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['url_length'] = ['status' => 'warning', 'score' => 0, 'message' => 'URL слишком длинный'];
    }
    
    // Проверка структуры URL
    if (preg_match('/^\/[a-z0-9\-\/]*$/', $path) && !preg_match('/[A-Z]/', $path)) {
        $analysis['checks']['url_structure'] = ['status' => 'good', 'score' => 2, 'message' => 'Хорошая структура URL'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['url_structure'] = ['status' => 'warning', 'score' => 0, 'message' => 'Улучшите структуру URL'];
    }
    
    // Проверка параметров URL
    if (empty($parsed_url['query'])) {
        $analysis['checks']['url_params'] = ['status' => 'good', 'score' => 1, 'message' => 'URL без параметров'];
        $analysis['score'] += 1;
    } else {
        $analysis['checks']['url_params'] = ['status' => 'warning', 'score' => 0, 'message' => 'URL содержит параметры'];
    }
    
    return $analysis;
}

/**
 * Анализ производительности
 */
function analyze_performance($content) {
    $analysis = [
        'score' => 0,
        'max_score' => 10,
        'checks' => [],
        'issues' => []
    ];
    
    $content_size = strlen($content);
    
    // Проверка размера страницы
    if ($content_size < 100000) {
        $analysis['checks']['page_size'] = ['status' => 'good', 'score' => 3, 'message' => 'Оптимальный размер страницы'];
        $analysis['score'] += 3;
    } elseif ($content_size < 500000) {
        $analysis['checks']['page_size'] = ['status' => 'warning', 'score' => 1, 'message' => 'Средний размер страницы'];
        $analysis['score'] += 1;
    } else {
        $analysis['checks']['page_size'] = ['status' => 'bad', 'score' => 0, 'message' => 'Слишком большой размер страницы'];
    }
    
    // Проверка минификации CSS/JS
    preg_match_all('/<link[^>]*rel=["\']stylesheet["\'][^>]*>/i', $content, $css_links);
    preg_match_all('/<script[^>]*src=["\']([^"\']*)["\'][^>]*>/i', $content, $js_links);
    
    $minified_css = 0;
    foreach ($css_links[0] as $css_link) {
        if (strpos($css_link, 'min') !== false) {
            $minified_css++;
        }
    }
    
    $minified_js = 0;
    foreach ($js_links[1] as $js_src) {
        if (strpos($js_src, 'min') !== false) {
            $minified_js++;
        }
    }
    
    if ($minified_css > 0 || $minified_js > 0) {
        $analysis['checks']['minification'] = ['status' => 'good', 'score' => 3, 'message' => 'CSS/JS минифицированы'];
        $analysis['score'] += 3;
    } else {
        $analysis['checks']['minification'] = ['status' => 'warning', 'score' => 0, 'message' => 'Минифицируйте CSS/JS'];
    }
    
    // Проверка lazy loading
    preg_match_all('/<img[^>]*>/i', $content, $images);
    $lazy_images = 0;
    foreach ($images[0] as $img) {
        if (strpos($img, 'loading="lazy"') !== false || strpos($img, 'lazy') !== false) {
            $lazy_images++;
        }
    }
    
    if ($lazy_images > 0) {
        $analysis['checks']['lazy_loading'] = ['status' => 'good', 'score' => 2, 'message' => 'Lazy loading настроен'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['lazy_loading'] = ['status' => 'warning', 'score' => 0, 'message' => 'Добавьте lazy loading'];
    }
    
    // Проверка сжатия
    $analysis['checks']['compression'] = ['status' => 'warning', 'score' => 2, 'message' => 'Настройте сжатие на сервере'];
    $analysis['score'] += 2;
    
    return $analysis;
}

/**
 * Анализ мобильной оптимизации
 */
function analyze_mobile_optimization($content) {
    $analysis = [
        'score' => 0,
        'max_score' => 10,
        'checks' => [],
        'issues' => []
    ];
    
    // Проверка viewport
    if (preg_match('/<meta[^>]*name=["\']viewport["\'][^>]*>/i', $content)) {
        $analysis['checks']['viewport'] = ['status' => 'good', 'score' => 3, 'message' => 'Viewport настроен'];
        $analysis['score'] += 3;
    } else {
        $analysis['checks']['viewport'] = ['status' => 'bad', 'score' => 0, 'message' => 'Viewport отсутствует'];
    }
    
    // Проверка адаптивности
    preg_match_all('/<link[^>]*rel=["\']stylesheet["\'][^>]*>/i', $content, $css_links);
    $responsive_css = 0;
    foreach ($css_links[0] as $css_link) {
        if (strpos($css_link, 'responsive') !== false || strpos($css_link, 'mobile') !== false) {
            $responsive_css++;
        }
    }
    
    if ($responsive_css > 0 || strpos($content, '@media') !== false) {
        $analysis['checks']['responsive'] = ['status' => 'good', 'score' => 4, 'message' => 'Адаптивный дизайн'];
        $analysis['score'] += 4;
    } else {
        $analysis['checks']['responsive'] = ['status' => 'warning', 'score' => 1, 'message' => 'Добавьте адаптивность'];
        $analysis['score'] += 1;
    }
    
    // Проверка размера шрифтов
    if (strpos($content, 'font-size') !== false || strpos($content, 'text-') !== false) {
        $analysis['checks']['font_size'] = ['status' => 'good', 'score' => 2, 'message' => 'Шрифты настроены'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['font_size'] = ['status' => 'warning', 'score' => 0, 'message' => 'Настройте размеры шрифтов'];
    }
    
    // Проверка touch targets
    preg_match_all('/<a[^>]*>/i', $content, $links);
    preg_match_all('/<button[^>]*>/i', $content, $buttons);
    $touch_elements = count($links[0]) + count($buttons[0]);
    
    if ($touch_elements > 0) {
        $analysis['checks']['touch_targets'] = ['status' => 'good', 'score' => 1, 'message' => 'Touch targets присутствуют'];
        $analysis['score'] += 1;
    } else {
        $analysis['checks']['touch_targets'] = ['status' => 'warning', 'score' => 0, 'message' => 'Добавьте touch targets'];
    }
    
    return $analysis;
}

/**
 * Анализ технического SEO
 */
function analyze_technical_seo($content) {
    $analysis = [
        'score' => 0,
        'max_score' => 5,
        'checks' => [],
        'issues' => []
    ];
    
    // Проверка HTML5 семантики
    $semantic_tags = ['<header', '<nav', '<main', '<section', '<article', '<aside', '<footer'];
    $semantic_count = 0;
    foreach ($semantic_tags as $tag) {
        if (strpos($content, $tag) !== false) {
            $semantic_count++;
        }
    }
    
    if ($semantic_count >= 3) {
        $analysis['checks']['semantic'] = ['status' => 'good', 'score' => 2, 'message' => 'Хорошая семантическая разметка'];
        $analysis['score'] += 2;
    } elseif ($semantic_count > 0) {
        $analysis['checks']['semantic'] = ['status' => 'warning', 'score' => 1, 'message' => 'Частичная семантическая разметка'];
        $analysis['score'] += 1;
    } else {
        $analysis['checks']['semantic'] = ['status' => 'bad', 'score' => 0, 'message' => 'Добавьте семантическую разметку'];
    }
    
    // Проверка структурированных данных
    if (strpos($content, 'application/ld+json') !== false || strpos($content, 'schema.org') !== false) {
        $analysis['checks']['structured_data'] = ['status' => 'good', 'score' => 2, 'message' => 'Структурированные данные настроены'];
        $analysis['score'] += 2;
    } else {
        $analysis['checks']['structured_data'] = ['status' => 'warning', 'score' => 0, 'message' => 'Добавьте структурированные данные'];
    }
    
    // Проверка favicon
    if (preg_match('/<link[^>]*rel=["\']icon["\'][^>]*>/i', $content)) {
        $analysis['checks']['favicon'] = ['status' => 'good', 'score' => 1, 'message' => 'Favicon настроен'];
        $analysis['score'] += 1;
    } else {
        $analysis['checks']['favicon'] = ['status' => 'warning', 'score' => 0, 'message' => 'Добавьте favicon'];
    }
    
    return $analysis;
}

/**
 * Получение контента страницы
 */
function get_page_content($url) {
    $context = stream_context_create([
        'http' => [
            'timeout' => 10,
            'user_agent' => 'Baumaster SEO Analyzer'
        ]
    ]);
    
    $content = @file_get_contents($url, false, $context);
    return $content ?: '';
}

/**
 * Сбор рекомендаций
 */
function collect_recommendations($checks) {
    $recommendations = [];
    
    foreach ($checks as $category => $analysis) {
        foreach ($analysis['checks'] as $check_name => $check) {
            if ($check['status'] === 'warning' || $check['status'] === 'bad') {
                $recommendations[] = [
                    'category' => $category,
                    'check' => $check_name,
                    'message' => $check['message'],
                    'priority' => $check['status'] === 'bad' ? 'high' : 'medium'
                ];
            }
        }
    }
    
    return $recommendations;
}

/**
 * Сбор критических проблем
 */
function collect_critical_issues($checks) {
    $issues = [];
    
    foreach ($checks as $category => $analysis) {
        foreach ($analysis['checks'] as $check_name => $check) {
            if ($check['status'] === 'bad' && $check['score'] === 0) {
                $issues[] = [
                    'category' => $category,
                    'check' => $check_name,
                    'message' => $check['message']
                ];
            }
        }
    }
    
    return $issues;
}

/**
 * Сбор предупреждений
 */
function collect_warnings($checks) {
    $warnings = [];
    
    foreach ($checks as $category => $analysis) {
        foreach ($analysis['checks'] as $check_name => $check) {
            if ($check['status'] === 'warning') {
                $warnings[] = [
                    'category' => $category,
                    'check' => $check_name,
                    'message' => $check['message']
                ];
            }
        }
    }
    
    return $warnings;
}

/**
 * Сбор успехов
 */
function collect_successes($checks) {
    $successes = [];
    
    foreach ($checks as $category => $analysis) {
        foreach ($analysis['checks'] as $check_name => $check) {
            if ($check['status'] === 'good') {
                $successes[] = [
                    'category' => $category,
                    'check' => $check_name,
                    'message' => $check['message']
                ];
            }
        }
    }
    
    return $successes;
}

/**
 * Форматирование размера файла для SEO анализа
 */
function format_seo_file_size($bytes) {
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}
?>
