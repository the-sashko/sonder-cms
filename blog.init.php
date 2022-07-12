<?php
if (
    file_exists(__DIR__ . '/../env/blog.php') &&
    is_file(__DIR__ . '/../env/blog.php')
) {
    require_once __DIR__ . '/../env/blog.php';
}

require_once __DIR__ . '/env/blog.php';

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);


//TODO: refactoring
require_once __DIR__ . '/init.php';
/*
if (
    file_exists(__DIR__ . '/../../vendor/autoload.php') &&
    is_file(__DIR__ . '/../../vendor/autoload.php')
) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} else {
    require_once __DIR__ . '/init.php';
}
*/