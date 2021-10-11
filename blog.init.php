<?php
if (
    file_exists(__DIR__ . '/../env/blog.php') &&
    is_file(__DIR__ . '/../env/blog.php')
) {
    require_once __DIR__ . '/../env/blog.php';
}

require_once __DIR__ . '/env/blog.php';

require_once __DIR__ . '/init.php';
