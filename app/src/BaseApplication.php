<?php

namespace App;

abstract class BaseApplication
{
    public function runConsole()
    {
        $argv = $argv ?? $_SERVER['argv'] ?? [];
        array_shift($argv);
        if (empty($argv)) {
            $this->consoleDefault();
        } else {
            echo 'Command "' . array_shift($argv) . '" not found!' . PHP_EOL;
        }
    }

    public function run()
    {
        $this->routing();
    }

    abstract public function consoleDefault();

    abstract protected function routing();
}