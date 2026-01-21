<?php
/**
 * Cloudinary Helper Functions
 * Handles image uploads, deletions, and URL generation via Cloudinary CDN
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

// Load Cloudinary SDK
require_once ABSPATH . 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Admin\AdminApi;

// Initialize Cloudinary configuration
Configuration::instance([
    'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME') ?: 'diqlvaasz',
        'api_key' => getenv('CLOUDINARY_API_KEY') ?: '869875762346156',
        'api_secret' => getenv('CLOUDINARY_API_SECRET') ?: 'AFsLaMr8VjiVI8w7XePmDALbEog'
    ],
    'url' => [
        'secure' => true
    ]
]);

/**
 * Upload single image to Cloudinary
 * 
 * @param array $file $_FILES array element
 * @param string $folder Cloudinary folder (e.g., 'services', 'portfolio')
 * @return array Result with success status, public_id, and url
 */
function cloudinary_upload_image($file, $folder = 'general')
{
    // Validate file
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'error' => 'Ошибка загрузки файла'];
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['success' => false, 'error' => 'Файл не выбран'];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'error' => 'Превышен максимальный размер файла'];
        default:
            return ['success' => false, 'error' => 'Неизвестная ошибка загрузки'];
    }

    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'error' => 'Файл слишком большой'];
    }

    // Check file type
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension'] ?? '');

    if (!in_array($extension, ALLOWED_IMAGE_TYPES)) {
        return ['success' => false, 'error' => 'Недопустимый тип файла. Разрешены: ' . implode(', ', ALLOWED_IMAGE_TYPES)];
    }

    try {
        // Get Cloudinary folder from env or use default
        $cloudinary_folder = getenv('CLOUDINARY_FOLDER') ?: 'bau_image';
        $full_folder = $cloudinary_folder . '/' . $folder;

        // Upload to Cloudinary
        $upload_result = (new UploadApi())->upload($file['tmp_name'], [
            'folder' => $full_folder,
            'resource_type' => 'image',
            'transformation' => [
                'quality' => 'auto:good',
                'fetch_format' => 'auto'
            ]
        ]);

        return [
            'success' => true,
            'public_id' => $upload_result['public_id'],
            'url' => $upload_result['secure_url'],
            'width' => $upload_result['width'],
            'height' => $upload_result['height'],
            'format' => $upload_result['format'],
            'bytes' => $upload_result['bytes']
        ];

    } catch (Exception $e) {
        write_log("Cloudinary upload error: " . $e->getMessage(), 'ERROR');
        return ['success' => false, 'error' => 'Ошибка загрузки на Cloudinary: ' . $e->getMessage()];
    }
}

/**
 * Upload multiple images to Cloudinary
 * 
 * @param array $files $_FILES array for multiple files
 * @param string $folder Cloudinary folder
 * @return array Results array with success status and uploaded images
 */
function cloudinary_upload_multiple($files, $folder = 'general')
{
    $results = [];
    $errors = [];

    // Handle single file case
    if (!is_array($files['name'])) {
        return cloudinary_upload_image($files, $folder);
    }

    $file_count = count($files['name']);

    for ($i = 0; $i < $file_count; $i++) {
        // Skip empty files
        if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }

        $file = [
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        ];

        $result = cloudinary_upload_image($file, $folder);

        if ($result['success']) {
            $results[] = $result;
        } else {
            $errors[] = $result['error'];
        }
    }

    return [
        'success' => !empty($results),
        'results' => $results,
        'errors' => $errors,
        'count' => count($results)
    ];
}

/**
 * Delete image from Cloudinary
 * 
 * @param string $public_id Cloudinary public_id
 * @return bool Success status
 */
function cloudinary_delete_image($public_id)
{
    if (empty($public_id)) {
        return true;
    }

    try {
        $result = (new UploadApi())->destroy($public_id, [
            'resource_type' => 'image'
        ]);

        return $result['result'] === 'ok';

    } catch (Exception $e) {
        write_log("Cloudinary delete error: " . $e->getMessage(), 'ERROR');
        return false;
    }
}

/**
 * Get Cloudinary image URL with transformations
 * 
 * @param string $public_id Cloudinary public_id
 * @param array $transformations Transformation options
 * @return string Image URL
 */
function cloudinary_get_url($public_id, $transformations = [])
{
    if (empty($public_id)) {
        return '';
    }

    // Check if it's already a full URL (legacy local image)
    if (strpos($public_id, 'http') === 0 || strpos($public_id, '/assets/') === 0) {
        return $public_id;
    }

    try {
        $cloud_name = getenv('CLOUDINARY_CLOUD_NAME') ?: 'diqlvaasz';

        // Build transformation string
        $transform_str = '';
        if (!empty($transformations)) {
            $parts = [];
            foreach ($transformations as $key => $value) {
                $parts[] = "{$key}_{$value}";
            }
            $transform_str = implode(',', $parts) . '/';
        }

        return "https://res.cloudinary.com/{$cloud_name}/image/upload/{$transform_str}{$public_id}";

    } catch (Exception $e) {
        write_log("Cloudinary URL generation error: " . $e->getMessage(), 'ERROR');
        return '';
    }
}

/**
 * Get thumbnail URL from Cloudinary
 * 
 * @param string $public_id Cloudinary public_id
 * @param int $width Thumbnail width
 * @param int $height Thumbnail height
 * @return string Thumbnail URL
 */
function cloudinary_get_thumbnail($public_id, $width = 300, $height = 300)
{
    return cloudinary_get_url($public_id, [
        'w' => $width,
        'h' => $height,
        'c' => 'fill',
        'q' => 'auto',
        'f' => 'auto'
    ]);
}

/**
 * Check if image is stored on Cloudinary
 * 
 * @param string $image_path Image path or public_id
 * @return bool True if Cloudinary image
 */
function is_cloudinary_image($image_path)
{
    if (empty($image_path)) {
        return false;
    }

    // Check if it's a Cloudinary URL or public_id
    return strpos($image_path, 'cloudinary.com') !== false ||
        (strpos($image_path, 'http') !== 0 && strpos($image_path, '/assets/') !== 0);
}
