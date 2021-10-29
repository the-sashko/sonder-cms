<?php
if (!defined('APP_ROUTING_TYPE')) {
    define('APP_ROUTING_TYPE', 'annotations');
}

if (!defined('APP_PROTECTED_DIR_PATH')) {
    define(
        'APP_PROTECTED_DIR_PATH',
        realpath(__DIR__ . '/../')
    );
}

if (!defined('APP_CMS_DIR_PATH')) {
    define(
        'APP_CMS_DIR_PATH',
        realpath(APP_PROTECTED_DIR_PATH . '/cms')
    );
}

if (!defined('APP_FRAMEWORK_DIR_PATH')) {
    define(
        'APP_FRAMEWORK_DIR_PATH',
        realpath(APP_CMS_DIR_PATH . '/framework')
    );
}

if (!defined('APP_PUBLIC_DIR_PATH')) {
    define(
        'APP_PUBLIC_DIR_PATH',
        realpath(APP_PROTECTED_DIR_PATH . '/../public')
    );
}

if (!defined('APP_SOURCE_PATHS')) {
    define(
        'APP_SOURCE_PATHS',
        [
            'endpoints' => [
                APP_PROTECTED_DIR_PATH . '/endpoints',
                APP_CMS_DIR_PATH . '/endpoints',
                APP_FRAMEWORK_DIR_PATH . '/endpoints'
            ],

            'middlewares' => [
                APP_PROTECTED_DIR_PATH . '/middlewares',
                APP_CMS_DIR_PATH . '/middlewares',
                APP_FRAMEWORK_DIR_PATH . '/middlewares'
            ],

            'controllers' => [
                APP_PROTECTED_DIR_PATH . '/controllers',
                APP_CMS_DIR_PATH . '/controllers',
                APP_FRAMEWORK_DIR_PATH . '/controllers'
            ],

            'models' => [
                APP_PROTECTED_DIR_PATH . '/models',
                APP_CMS_DIR_PATH . '/models',
                APP_FRAMEWORK_DIR_PATH . '/models'
            ],

            'plugins' => [
                APP_PROTECTED_DIR_PATH . '/plugins',
                APP_CMS_DIR_PATH . '/plugins',
                APP_FRAMEWORK_DIR_PATH . '/plugins'
            ],

            'hooks' => [
                APP_PROTECTED_DIR_PATH . '/hooks',
                APP_CMS_DIR_PATH . '/hooks',
                APP_FRAMEWORK_DIR_PATH . '/hooks'
            ],

            'config' => [
                APP_PROTECTED_DIR_PATH . '/config',
                APP_CMS_DIR_PATH . '/config'
            ],

            'lang' => [
                APP_PROTECTED_DIR_PATH . '/lang',
                APP_CMS_DIR_PATH . '/lang'
            ],

            'themes' => [
                APP_PROTECTED_DIR_PATH . '/themes',
                APP_CMS_DIR_PATH . '/themes'
            ],

            'pages' => [
                APP_PROTECTED_DIR_PATH . '/pages',
                APP_CMS_DIR_PATH . '/pages'
            ]
        ]
    );
}

require_once __DIR__ . '/framework/init.php';
