<?php

ini_set('xdebug.max_nesting_level', '200');
if (file_exists($file = __DIR__.'/../vendor/autoload.php')) {
    $loader = require_once $file;
} else {
    throw new RuntimeException('Install dependencies to run test suite.');
}
