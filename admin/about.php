<?php
/**
 * Управление страницей "О компании"
 * Baumaster Admin - About Page Management
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../database.php';
require_once UI_PATH . 'base.php';
require_once COMPONENTS_PATH . 'admin_layout.php';
require_once __DIR__ . '/../integrations/translation/TranslationManager.php';
require_once __DIR__ . '/../ux/data.php';

// Обработка POST запросов
$error_message = '';
$success_message = '';

if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка сохранения контактов компании
    if (isset($_POST['save_contacts'])) {
        $category = $_POST['category'] ?? '';
        $settings_data = $_POST['settings'] ?? [];
        $working_days = $_POST['working_days'] ?? [];
        
        try {
            // Обработка обычных настроек
            foreach ($settings_data as $key => $value) {
                // Обработка обычных настроек
                if (true) {
                    // Обычные настройки
                $existing = $db->select('settings', ['setting_key' => $key], ['limit' => 1]);
                
                if ($existing) {
                    $db->update('settings', 
                        ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 
                        ['setting_key' => $key]
                    );
                } else {
                    $db->insert('settings', [
                        'setting_key' => $key,
                        'setting_value' => $value,
                        'category' => $category
                    ]);
                    }
                }
            }
            
            // Обработка рабочих часов (упрощенная версия)
            if ($category === 'company') {
                // Сохраняем настройки для будней, субботы и воскресенья
                $working_hours_keys = [
                    'working_hours_weekdays_from',
                    'working_hours_weekdays_to', 
                    'working_hours_saturday_from',
                    'working_hours_saturday_to',
                    'working_hours_sunday_from',
                    'working_hours_sunday_to',
                    'sunday_working'
                ];
                
                foreach ($working_hours_keys as $key) {
                    $value = $settings_data[$key] ?? '';
                    
                    $existing = $db->select('settings', ['setting_key' => $key], ['limit' => 1]);
                    if ($existing) {
                        $db->update('settings', 
                            ['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')], 
                            ['setting_key' => $key]
                        );
                    } else {
                        $db->insert('settings', [
                            'setting_key' => $key,
                            'setting_value' => $value,
                            'category' => 'company'
                        ]);
                    }
                }
            }
            
            $success_message = __('settings.update_success', 'Контакты компании успешно обновлены');
            log_user_activity('contacts_update', 'settings', 0);
            
            
        } catch (Exception $e) {
            $error_message = __('settings.update_error', 'Ошибка при обновлении контактов компании');
            write_log("Contacts update error: " . $e->getMessage(), 'ERROR');
            
        }
    }
    
    if (isset($_POST['save_history'])) {
        // Сохранение истории компании
        $history_data = [
            'title' => $_POST['history_title'] ?? '',
            'content' => [
                'paragraph1' => $_POST['history_paragraph1'] ?? '',
                'paragraph2' => $_POST['history_paragraph2'] ?? '',
                'paragraph3' => $_POST['history_paragraph3'] ?? ''
            ],
            'sort_order' => 1
        ];
        
        // Обработка загрузки изображения
        if (isset($_FILES['team_photo']) && $_FILES['team_photo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/uploads/about/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['team_photo']['name'], PATHINFO_EXTENSION);
            $filename = 'team_photo_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['team_photo']['tmp_name'], $upload_path)) {
                $history_data['image'] = 'assets/uploads/about/' . $filename;
            }
        }
        
        $save_result = save_about_content('history', $history_data, 'ru');
        if ($save_result) {
            // Автоматический перевод для немецкой версии через TranslationManager
            $translation_manager = new TranslationManager();
            
            // Получаем ID сохраненной записи
            $db = get_database();
            $existing = $db->select('about_content', ['section' => 'history'], ['limit' => 1]);
            
            if ($existing && !empty($existing)) {
                $record = is_array($existing) && isset($existing[0]) ? $existing[0] : $existing;
                $record_id = $record['id'] ?? null;
                
                // Подготавливаем поля для перевода
                $fields_to_translate = [
                    'title' => $history_data['title'],
                    'content' => json_encode($history_data['content'])
                ];
                
                // Выполняем автоматический перевод
                $translated_fields = $translation_manager->autoTranslateContent(
                    'about_content', 
                    $record_id, 
                    $fields_to_translate, 
                    'ru', 
                    'de'
                );
                
                if (!empty($translated_fields)) {
                    $success_message = 'История компании успешно сохранена и переведена на немецкий язык';
                } else {
                    $success_message = 'История компании сохранена, но перевод не выполнен';
                }
            } else {
                $success_message = 'История компании сохранена, но перевод не выполнен';
            }
            } else {
                $error_message = 'Ошибка при сохранении истории компании';
            }
        }
        
        // Обработка сохранения члена команды
        if (isset($_POST['save_team_member'])) {
            $member_data = [
                'name' => $_POST['member_name'] ?? '',
                'position' => $_POST['member_position'] ?? '',
                'description' => $_POST['member_description'] ?? '',
                'sort_order' => $_POST['member_sort_order'] ?? 0
            ];
            
            if (isset($_POST['member_id']) && !empty($_POST['member_id'])) {
                $member_data['id'] = $_POST['member_id'];
            }
            
            // Обработка загрузки изображения
            if (isset($_FILES['member_photo']) && $_FILES['member_photo']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assets/uploads/team/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = pathinfo($_FILES['member_photo']['name'], PATHINFO_EXTENSION);
                $filename = 'member_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['member_photo']['tmp_name'], $upload_path)) {
                    $member_data['image'] = 'assets/uploads/team/' . $filename;
                }
            }
            
            $member_id = save_team_member($member_data, 'ru');
            if ($member_id) {
                // Автоматический перевод для немецкой версии
                $translation_manager = new TranslationManager();
                
                $fields_to_translate = [
                    'name' => $member_data['name'],
                    'position' => $member_data['position'],
                    'description' => $member_data['description']
                ];
                
                $translated_fields = $translation_manager->autoTranslateContent(
                    'team_members', 
                    $member_id, 
                    $fields_to_translate, 
                    'ru', 
                    'de'
                );
                
                if (!empty($translated_fields)) {
                    $success_message = 'Член команды успешно сохранен и переведен на немецкий язык';
                } else {
                    $success_message = 'Член команды сохранен, но перевод не выполнен';
                }
            } else {
                $error_message = 'Ошибка при сохранении члена команды';
            }
        }
        
        // Обработка удаления члена команды
        if (isset($_POST['delete_team_member'])) {
            $member_id = $_POST['member_id'] ?? null;
            if ($member_id) {
                $result = delete_team_member($member_id);
                if ($result) {
                    $success_message = 'Член команды успешно удален';
                } else {
                    $error_message = 'Ошибка при удалении члена команды';
                }
            }
        }
        
        // Обработка сохранения статистики
        if (isset($_POST['save_statistics'])) {
            $statistics_data = [];
            
            // Собираем данные статистики из формы
            for ($i = 1; $i <= 4; $i++) {
                if (isset($_POST["stat{$i}_number"]) && !empty($_POST["stat{$i}_number"])) {
                    $statistics_data[] = [
                        'number' => $_POST["stat{$i}_number"],
                        'label' => $_POST["stat{$i}_label"] ?? '',
                        'description' => $_POST["stat{$i}_description"] ?? ''
                    ];
                }
            }
            
            if (!empty($statistics_data)) {
                $stat_ids = save_statistics($statistics_data, 'ru');
                if ($stat_ids) {
                    // Автоматический перевод для немецкой версии
                    $translation_manager = new TranslationManager();
                    
                    $translated_count = 0;
                    foreach ($stat_ids as $index => $stat_id) {
                        $fields_to_translate = [
                            'label' => $statistics_data[$index]['label'],
                            'description' => $statistics_data[$index]['description']
                        ];
                        
                        $translated_fields = $translation_manager->autoTranslateContent(
                            'statistics', 
                            $stat_id, 
                            $fields_to_translate, 
                            'ru', 
                            'de'
                        );
                        
                        if (!empty($translated_fields)) {
                            $translated_count++;
                        }
                    }
                    
                    if ($translated_count > 0) {
                        $success_message = "Статистика успешно сохранена и переведена на немецкий язык ($translated_count из " . count($statistics_data) . ")";
                    } else {
                        $success_message = 'Статистика сохранена, но перевод не выполнен';
                    }
                } else {
                    $error_message = 'Ошибка при сохранении статистики';
                }
            } else {
                $error_message = 'Необходимо заполнить хотя бы одну позицию статистики';
            }
        }
    }

// Загрузка данных для отображения
$history_data = get_about_content('history');
$team_members = get_team_members();
$statistics = get_statistics();

// Загрузка настроек компании
$settings = [];
$all_settings = $db->select('settings', [], ['order' => 'category, setting_key']);

foreach ($all_settings as $setting) {
    $settings[$setting['category']][$setting['setting_key']] = $setting;
}

// Генерация CSRF токена
$csrf_token = generate_csrf_token();

// Начало контента
ob_start();
?>

<div class="space-y-6">
    <!-- Сообщения об успехе/ошибке -->
    <?php if ($success_message): ?>
        <div class="bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>


    <!-- Вкладки -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200" style="overflow-y: hidden;">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button class="tab-button active py-4 px-1 border-b-2 border-primary-500 font-medium text-sm text-primary-600" data-tab="history">
                    Наша история
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="team">
                    Наша команда
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="statistics">
                    Статистика
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="contacts">
                    Контакты компании
                </button>
            </nav>
        </div>

        <!-- Содержимое вкладок -->
        <div class="p-6">
            <!-- Вкладка "Наша история" -->
            <div id="tab-history" class="tab-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="space-y-6">
                        <div>
                            <label for="history_title" class="block text-sm font-medium text-gray-700 mb-2">
                                Заголовок секции
                            </label>
                            <input type="text" id="history_title" name="history_title" 
                                   value="<?php echo htmlspecialchars($history_data['title'] ?? 'Наша история'); ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        </div>

                        <div>
                            <label for="history_paragraph1" class="block text-sm font-medium text-gray-700 mb-2">
                                Первый абзац
                            </label>
                            <textarea id="history_paragraph1" name="history_paragraph1" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"><?php echo htmlspecialchars($history_data['content']['paragraph1'] ?? 'Компания Frankfurt Innenausbau была основана в 2014 году группой опытных мастеров, которые решили объединить свои знания и навыки для предоставления качественных услуг в сфере внутренних работ.'); ?></textarea>
                        </div>

                        <div>
                            <label for="history_paragraph2" class="block text-sm font-medium text-gray-700 mb-2">
                                Второй абзац
                            </label>
                            <textarea id="history_paragraph2" name="history_paragraph2" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"><?php echo htmlspecialchars($history_data['content']['paragraph2'] ?? 'За 10 лет работы мы выполнили более 500 проектов различной сложности — от небольших косметических ремонтов до полной реконструкции квартир и офисов. Наш опыт охватывает все виды внутренних работ.'); ?></textarea>
                        </div>

                        <div>
                            <label for="history_paragraph3" class="block text-sm font-medium text-gray-700 mb-2">
                                Третий абзац
                            </label>
                            <textarea id="history_paragraph3" name="history_paragraph3" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"><?php echo htmlspecialchars($history_data['content']['paragraph3'] ?? 'Сегодня мы продолжаем развиваться, внедряя новые технологии и материалы, но неизменным остается наш принцип — качество превыше всего.'); ?></textarea>
                        </div>

                        <div>
                            <label for="team_photo" class="block text-sm font-medium text-gray-700 mb-2">
                                Фото команды
                            </label>
                            <div class="flex items-center space-x-4" style="display: flex; flex-direction: column; ">
                                <div class="w-32 h-32 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden">
                                    <?php if (!empty($history_data['image'])): ?>
                                        <img src="../<?php echo htmlspecialchars($history_data['image']); ?>" 
                                             alt="Фото команды" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <span class="text-gray-500 text-sm">Фото команды</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <input type="file" id="team_photo" name="team_photo" accept="image/*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                    <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF до 10MB</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" name="save_history" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Сохранить изменения
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Вкладка "Наша команда" -->
            <div id="tab-team" class="tab-content hidden">
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Сотрудники компании</h3>
                        <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Добавить сотрудника
                        </button>
                    </div>

                    <!-- Список сотрудников -->
                    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        <?php if (!empty($team_members)): ?>
                            <?php foreach ($team_members as $member): ?>
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="text-center">
                                        <div class="w-20 h-20 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center overflow-hidden">
                                            <?php if (!empty($member['image'])): ?>
                                                <img src="../<?php echo htmlspecialchars($member['image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($member['name']); ?>" 
                                                     class="w-full h-full object-cover">
                                            <?php else: ?>
                                                <?php 
                                                $initials = '';
                                                $name_parts = explode(' ', $member['name']);
                                                foreach ($name_parts as $part) {
                                                    $initials .= strtoupper(substr($part, 0, 1));
                                                }
                                                ?>
                                                <span class="text-xl font-bold text-gray-500"><?php echo $initials; ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <h4 class="font-semibold text-lg text-gray-900"><?php echo htmlspecialchars($member['name']); ?></h4>
                                        <p class="text-primary-600 font-medium mb-2"><?php echo htmlspecialchars($member['position']); ?></p>
                                        <p class="text-sm text-gray-600 mb-4"><?php echo htmlspecialchars($member['description']); ?></p>
                                        <div class="flex space-x-2">
                                            <button onclick="editMember(<?php echo $member['id']; ?>)" 
                                                    class="flex-1 px-3 py-1 text-xs font-medium text-primary-600 bg-primary-50 rounded hover:bg-primary-100">
                                                Редактировать
                                            </button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Вы уверены, что хотите удалить этого сотрудника?')">
                                                <input type="hidden" name="member_id" value="<?php echo $member['id']; ?>">
                                                <button type="submit" name="delete_team_member" 
                                                        class="px-3 py-1 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100">
                                                    Удалить
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-full text-center py-8">
                                <p class="text-gray-500">Сотрудники не найдены</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Вкладка "Статистика" -->
            <div id="tab-statistics" class="tab-content hidden">
                <form method="POST">
                    <div class="space-y-6">
                        <h3 class="text-lg font-medium text-gray-900">Статистика компании</h3>
                        
                        <div class="grid gap-6 md:grid-cols-2">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <?php $stat = $statistics[$i-1] ?? null; ?>
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <h4 class="font-medium text-gray-900 mb-4">Статистика <?php echo $i; ?></h4>
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Число</label>
                                            <input type="text" name="stat<?php echo $i; ?>_number" 
                                                   value="<?php echo htmlspecialchars($stat['number'] ?? ''); ?>" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Заголовок</label>
                                            <input type="text" name="stat<?php echo $i; ?>_label" 
                                                   value="<?php echo htmlspecialchars($stat['label'] ?? ''); ?>" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                                            <input type="text" name="stat<?php echo $i; ?>_description" 
                                                   value="<?php echo htmlspecialchars($stat['description'] ?? ''); ?>" 
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" name="save_statistics" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                Сохранить статистику
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Вкладка "Контакты компании" -->
            <div id="tab-contacts" class="tab-content hidden">
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="category" value="company">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php render_form_field([
                            'type' => 'text',
                            'name' => 'settings[company_name]',
                            'label' => __('settings.company_name', 'Название компании'),
                            'value' => $settings['company']['company_name']['setting_value'] ?? '',
                            'required' => true
                        ]); ?>
                        
                        <?php render_form_field([
                            'type' => 'email',
                            'name' => 'settings[company_email]',
                            'label' => __('settings.company_email', 'Email компании'),
                            'value' => $settings['company']['company_email']['setting_value'] ?? '',
                            'required' => true
                        ]); ?>
                        
                        <?php render_form_field([
                            'type' => 'text',
                            'name' => 'settings[company_phone]',
                            'label' => __('settings.company_phone', 'Телефон'),
                            'value' => $settings['company']['company_phone']['setting_value'] ?? ''
                        ]); ?>
                        
                        <?php render_form_field([
                            'type' => 'text',
                            'name' => 'settings[company_address]',
                            'label' => __('settings.company_address', 'Адрес'),
                            'value' => $settings['company']['company_address']['setting_value'] ?? ''
                        ]); ?>
                    </div>
                    
                    <?php render_form_field([
                        'type' => 'textarea',
                        'name' => 'settings[company_description]',
                        'label' => __('settings.company_description', 'Описание компании'),
                        'value' => $settings['company']['company_description']['setting_value'] ?? '',
                        'rows' => 3
                    ]); ?>
                    
                    <!-- Рабочие часы -->
                    <div class="space-y-4">
                        <h4 class="text-md font-medium text-gray-900"><?php echo __('settings.working_hours', 'Рабочие часы'); ?></h4>
                        
                        <!-- Упрощенная форма -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Будни (ПН-ПТ)</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">С</label>
                                        <input type="time" 
                                               name="settings[working_hours_weekdays_from]" 
                                               value="<?php echo $settings['company']['working_hours_weekdays_from']['setting_value'] ?? '08:00'; ?>"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">До</label>
                                        <input type="time" 
                                               name="settings[working_hours_weekdays_to]" 
                                               value="<?php echo $settings['company']['working_hours_weekdays_to']['setting_value'] ?? '20:00'; ?>"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Суббота (СБ)</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">С</label>
                                        <input type="time" 
                                               name="settings[working_hours_saturday_from]" 
                                               value="<?php echo $settings['company']['working_hours_saturday_from']['setting_value'] ?? '09:00'; ?>"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">До</label>
                                        <input type="time" 
                                               name="settings[working_hours_saturday_to]" 
                                               value="<?php echo $settings['company']['working_hours_saturday_to']['setting_value'] ?? '15:00'; ?>"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Воскресенье (ВС)</label>
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="sunday_working" 
                                           name="settings[sunday_working]" 
                                           value="1" 
                                           <?php echo ($settings['company']['sunday_working']['setting_value'] ?? '0') == '1' ? 'checked' : ''; ?>
                                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <label for="sunday_working" class="ml-2 text-sm text-gray-600">Работаем в воскресенье</label>
                                </div>
                                <div id="sunday-times" class="mt-3 grid grid-cols-2 gap-3" style="<?php echo ($settings['company']['sunday_working']['setting_value'] ?? '0') == '1' ? '' : 'display: none;'; ?>">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">С</label>
                                        <input type="time" 
                                               name="settings[working_hours_sunday_from]" 
                                               value="<?php echo $settings['company']['working_hours_sunday_from']['setting_value'] ?? '10:00'; ?>"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">До</label>
                                        <input type="time" 
                                               name="settings[working_hours_sunday_to]" 
                                               value="<?php echo $settings['company']['working_hours_sunday_to']['setting_value'] ?? '16:00'; ?>"
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Предварительный просмотр -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h5 class="text-sm font-medium text-gray-700 mb-2">Предварительный просмотр:</h5>
                            <div id="working-hours-preview" class="text-sm text-gray-600">
                                <!-- Здесь будет отображаться предварительный просмотр -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end">
                        <?php render_button([
                            'type' => 'submit',
                            'text' => __('common.save', 'Сохранить'),
                            'variant' => 'primary',
                            'name' => 'save_contacts'
                        ]); ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно для добавления/редактирования сотрудника -->
<div id="memberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Добавить сотрудника</h3>
            <form method="POST" enctype="multipart/form-data" id="memberForm">
                <input type="hidden" name="member_id" id="member_id">
                <input type="hidden" name="member_sort_order" id="member_sort_order" value="0">
                
                <div class="space-y-4">
                    <div>
                        <label for="member_name" class="block text-sm font-medium text-gray-700 mb-1">Имя</label>
                        <input type="text" name="member_name" id="member_name" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div>
                        <label for="member_position" class="block text-sm font-medium text-gray-700 mb-1">Должность</label>
                        <input type="text" name="member_position" id="member_position" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    
                    <div>
                        <label for="member_description" class="block text-sm font-medium text-gray-700 mb-1">Описание</label>
                        <textarea name="member_description" id="member_description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>
                    
                    <div>
                        <label for="member_photo" class="block text-sm font-medium text-gray-700 mb-1">Фото</label>
                        <input type="file" name="member_photo" id="member_photo" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeMemberModal()" 
                            class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Отмена
                    </button>
                    <button type="submit" name="save_team_member"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        Сохранить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Переключение вкладок
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            // Убираем активный класс у всех кнопок
            tabButtons.forEach(btn => {
                btn.classList.remove('active', 'border-primary-500', 'text-primary-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Добавляем активный класс к текущей кнопке
            this.classList.add('active', 'border-primary-500', 'text-primary-600');
            this.classList.remove('border-transparent', 'text-gray-500');

            // Скрываем все содержимое вкладок
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Показываем нужное содержимое
            document.getElementById('tab-' + targetTab).classList.remove('hidden');
        });
    });

    // Обработчик для кнопки "Добавить сотрудника"
    const addEmployeeBtn = document.querySelector('button[type="button"]');
    if (addEmployeeBtn && addEmployeeBtn.textContent.includes('Добавить сотрудника')) {
        addEmployeeBtn.addEventListener('click', function() {
            openMemberModal();
        });
    }
});

// Функции для работы с модальным окном
function openMemberModal(memberId = null) {
    const modal = document.getElementById('memberModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('memberForm');
    
    if (memberId) {
        title.textContent = 'Редактировать сотрудника';
        // Здесь можно загрузить данные сотрудника для редактирования
        // Пока что просто очищаем форму
        form.reset();
        document.getElementById('member_id').value = memberId;
    } else {
        title.textContent = 'Добавить сотрудника';
        form.reset();
        document.getElementById('member_id').value = '';
    }
    
    modal.classList.remove('hidden');
}

function closeMemberModal() {
    const modal = document.getElementById('memberModal');
    modal.classList.add('hidden');
}

function editMember(memberId) {
    openMemberModal(memberId);
}

// Закрытие модального окна при клике вне его
document.getElementById('memberModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMemberModal();
    }
});

// Управление рабочими часами для вкладки "Контакты компании"
const previewElement = document.getElementById('working-hours-preview');
const sundayCheckbox = document.getElementById('sunday_working');
const sundayTimes = document.getElementById('sunday-times');

function updateWorkingHoursPreview() {
    // Получаем значения из полей
    const weekdaysFrom = document.querySelector('input[name="settings[working_hours_weekdays_from]"]')?.value || '08:00';
    const weekdaysTo = document.querySelector('input[name="settings[working_hours_weekdays_to]"]')?.value || '20:00';
    const saturdayFrom = document.querySelector('input[name="settings[working_hours_saturday_from]"]')?.value || '09:00';
    const saturdayTo = document.querySelector('input[name="settings[working_hours_saturday_to]"]')?.value || '15:00';
    const sundayFrom = document.querySelector('input[name="settings[working_hours_sunday_from]"]')?.value || '10:00';
    const sundayTo = document.querySelector('input[name="settings[working_hours_sunday_to]"]')?.value || '16:00';
    const sundayWorking = sundayCheckbox ? sundayCheckbox.checked : false;
    
    // Формируем предварительный просмотр
    let previewParts = [];
    
    // Будни
    previewParts.push(`ПН-ПТ ${weekdaysFrom}-${weekdaysTo}`);
    
    // Суббота
    previewParts.push(`СБ ${saturdayFrom}-${saturdayTo}`);
    
    // Воскресенье
    if (sundayWorking) {
        previewParts.push(`ВС ${sundayFrom}-${sundayTo}`);
    } else {
        previewParts.push('ВС - X');
    }
    
    if (previewElement) {
        previewElement.innerHTML = previewParts.join('<br>');
    }
}

// Обработчик для чекбокса воскресенья
if (sundayCheckbox) {
    sundayCheckbox.addEventListener('change', function() {
        if (this.checked) {
            sundayTimes.style.display = 'block';
        } else {
            sundayTimes.style.display = 'none';
        }
        updateWorkingHoursPreview();
    });
}

// Обработчики для полей времени
const timeInputs = document.querySelectorAll('input[type="time"]');
timeInputs.forEach(input => {
    input.addEventListener('change', updateWorkingHoursPreview);
    input.addEventListener('input', updateWorkingHoursPreview);
});

// Инициализация предварительного просмотра
updateWorkingHoursPreview();
</script>

<?php
$content = ob_get_clean();

// Рендеринг страницы
render_admin_layout([
    'page_title' => 'О компании',
    'page_description' => 'Управление контентом страницы о компании',
    'active_menu' => 'about',
    'content' => $content
]);
?>
