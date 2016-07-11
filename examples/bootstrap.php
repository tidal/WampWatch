<?php

ini_set('xdebug.max_nesting_level', '200');
if (file_exists($file = realpath(__DIR__ . "/..") . '/vendor/autoload.php')) {
    $loader = require_once $file;
} else {
    throw new RuntimeException("autoload file not found in vendor path.");
}
