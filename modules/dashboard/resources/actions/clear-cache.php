<?php

foreach (explode(',', $_GET['key'] ?? '') as $key)
{
    $file = __DIR__ . '/../../cache/' . $key . '.ser';
    if (is_file($file))
        unlink($file);
}

header('Location: /');
die;
