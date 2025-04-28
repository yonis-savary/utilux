<?php

require_once __DIR__ . '/../utils/utils.php';

$file = config()['style']['background'] ?? null;

if (is_file($file)) {
    http_response_code(200);
    header('Content-Type: ' . mime_content_type($file));
    header('Cache-control: max-age 36000');
    header_remove('Pragma');
    echo file_get_contents($file);
} else {
    http_response_code(404);
}
