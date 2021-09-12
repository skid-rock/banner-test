<?php

namespace App;

use DateTime;

class Logger
{
    public static function timeStamp($stdout = true): ?string
    {
        $output = (new DateTime)->format('Y-m-d H:i:s u') . PHP_EOL;

        if ($stdout) {
            echo $output;

            return null;
        } else {
            return $output;
        }
    }
}