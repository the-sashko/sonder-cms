<?php
if (
    file_exists(__DIR__ . '/../env/api.php') &&
    is_file(__DIR__ . '/../env/api.php')
) {
    require_once __DIR__ . '/../env/api.php';
}

require_once __DIR__ . '/env/api.php';

require_once __DIR__ . '/init.php';
