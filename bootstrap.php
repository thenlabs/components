<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/vendor/andaniel05/pyramidaltests/src/DSL/PHPUnit.php';

// Include macros files
foreach (glob(__DIR__ . "/tests/Macro/*Macro.php") as $macro) {
    require_once $macro;
}
