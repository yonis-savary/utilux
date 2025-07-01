<?php 


require_once __DIR__ . '/../../../utils/utils.php';

$notesConfig = service('notes');
$notesPath = $notesConfig['path'] ?? null;

$text = $_POST['text'];

if (is_file($notesPath)) {
    file_put_contents($notesPath, $text);
}

header('Location: /');
http_response_code(301);
