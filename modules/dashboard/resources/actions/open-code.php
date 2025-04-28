<?php

shell_exec('code "' . $_GET['directory'] . '"');

header('Location: /');
