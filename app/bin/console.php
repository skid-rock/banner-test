#!/usr/bin/env php
<?php

use App\ConsoleApplication;

if (!in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
    echo 'Warning: The console should be invoked via the CLI version of PHP, not the ' . PHP_SAPI . ' SAPI' . PHP_EOL;
}

set_time_limit(0);

require dirname(__DIR__) . '/vendor/autoload.php';

$application = new ConsoleApplication();
$application->runConsole();
