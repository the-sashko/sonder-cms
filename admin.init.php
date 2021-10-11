<?php
if (
    file_exists(__DIR__ . '/../env/admin.php') &&
    is_file(__DIR__ . '/../env/admin.php')
) {
    require_once __DIR__ . '/../env/admin.php';
}

require_once __DIR__ . '/env/admin.php';

require_once __DIR__ . '/init.php';
