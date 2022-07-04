<?php
if (
    file_exists(__DIR__ . '/../env/cli.php') &&
    is_file(__DIR__ . '/../env/cli.php')
) {
    require_once __DIR__ . '/../env/cli.php';
}

require_once __DIR__ . '/env/cli.php';

//TODO: refactoring
if (
    file_exists(__DIR__ . '/../../vendor/autoload.php') &&
    is_file(__DIR__ . '/../../vendor/autoload.php')
) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} else {
    require_once __DIR__ . '/init.php';
}
