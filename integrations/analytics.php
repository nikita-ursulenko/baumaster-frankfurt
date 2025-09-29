<?php
/**
 * Google Analytics Integration
 * Baumaster Integrations - Analytics
 */

/**
 * Генерация Google Analytics кода
 */
function generate_google_analytics_code() {
    $ga_id = get_setting('google_analytics', '');
    
    if (empty($ga_id)) {
        return '';
    }
    
    return <<<HTML
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id={$ga_id}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$ga_id}', {
    'anonymize_ip': true,
    'cookie_flags': 'SameSite=None;Secure'
  });
</script>
HTML;
}

/**
 * Отслеживание событий
 */
function track_analytics_event($event_name, $event_category, $event_action, $event_label = '', $event_value = null) {
    $ga_id = get_setting('google_analytics', '');
    
    if (empty($ga_id)) {
        return '';
    }
    
    $event_data = [
        'event_category' => $event_category,
        'event_action' => $event_action
    ];
    
    if (!empty($event_label)) {
        $event_data['event_label'] = $event_label;
    }
    
    if ($event_value !== null) {
        $event_data['event_value'] = $event_value;
    }
    
    $event_json = json_encode($event_data);
    
    return <<<HTML
<script>
  gtag('event', '{$event_name}', {$event_json});
</script>
HTML;
}

/**
 * Отслеживание просмотров страниц
 */
function track_page_view($page_title, $page_path) {
    $ga_id = get_setting('google_analytics', '');
    
    if (empty($ga_id)) {
        return '';
    }
    
    return <<<HTML
<script>
  gtag('config', '{$ga_id}', {
    'page_title': '{$page_title}',
    'page_location': window.location.href,
    'page_path': '{$page_path}'
  });
</script>
HTML;
}

/**
 * Отслеживание конверсий
 */
function track_conversion($conversion_type, $value = null) {
    $events = [
        'contact_form' => ['category' => 'Engagement', 'action' => 'Contact Form Submit'],
        'service_inquiry' => ['category' => 'Lead', 'action' => 'Service Inquiry'],
        'portfolio_view' => ['category' => 'Engagement', 'action' => 'Portfolio View'],
        'review_submit' => ['category' => 'Engagement', 'action' => 'Review Submit']
    ];
    
    if (!isset($events[$conversion_type])) {
        return '';
    }
    
    $event = $events[$conversion_type];
    return track_analytics_event(
        $conversion_type,
        $event['category'],
        $event['action'],
        '',
        $value
    );
}

/**
 * Генерация кода для отслеживания форм
 */
function generate_form_tracking_code($form_selector, $event_name) {
    return <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('{$form_selector}');
    if (form) {
        form.addEventListener('submit', function() {
            gtag('event', '{$event_name}', {
                'event_category': 'Form',
                'event_action': 'Submit'
            });
        });
    }
});
</script>
HTML;
}
?>

