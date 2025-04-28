<?php

$file = __DIR__ . '/../../cache/' . $_GET['key'] . '.ser';
if (is_file($file))
    unlink($file);

header('Location: /');
die;
