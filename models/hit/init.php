<?php

use function Sonder\Core\Utils\loadDirectory;

loadDirectory(__DIR__ . '/interfaces');
loadDirectory(__DIR__ . '/enums');
loadDirectory(__DIR__ . '/exceptions');
loadDirectory(__DIR__ . '/vo');
loadDirectory(__DIR__ . '/forms');

require_once __DIR__ . '/HitStore.php';
require_once __DIR__ . '/HitApi.php';
require_once __DIR__ . '/HitModel.php';
