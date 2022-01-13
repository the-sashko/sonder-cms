<?php
if (!defined('APP_MODE')) {
    define('APP_MODE', 'api');
}

if (!defined('APP_API_MODE')) {
    define('APP_API_MODE', 'prod');
}

if (!defined('APP_CACHE')) {
    define('APP_CACHE', true);
}

if (!defined('APP_CACHE_TTL')) {
    define('APP_CACHE_TTL', 60 * 30);
}
