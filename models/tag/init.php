<?php

use function Sonder\Core\Utils\loadDirectory;

loadDirectory(__DIR__ . '/interfaces');
loadDirectory(__DIR__ . '/vo');
loadDirectory(__DIR__ . '/forms');

require_once __DIR__ . '/TagStore.php';
require_once __DIR__ . '/TagApi.php';
require_once __DIR__ . '/TagModel.php';
