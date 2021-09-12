<?php

use App\Application;

set_time_limit(0);

require dirname(__DIR__) . '/vendor/autoload.php';

$application = new Application();
$application->run();
