<?php

if (!defined('APP_MIDDLEWARES')) {
    define('APP_MIDDLEWARES', ['user', 'admin', 'csrf']);
}

if (!defined('APP_MODE')) {
    define('APP_MODE', 'prod');
}

if (!defined('APP_AREA')) {
    define('APP_AREA', 'admin');
}

if (!defined('APP_CACHE')) {
    define('APP_CACHE', true);
}

if (!defined('APP_CACHE_TTL')) {
    define('APP_CACHE_TTL', 1800); // 30 min
}

if (!defined('APP_NOT_FOUND_URL')) {
    define('APP_NOT_FOUND_URL', '/not-found/');
}

if (!defined('APP_DEFAULT_LANGUAGE')) {
    define('APP_DEFAULT_LANGUAGE', 'en');
}

if (!defined('APP_MULTI_LANGUAGE')) {
    define('APP_MULTI_LANGUAGE', false);
}
