<?php

namespace App;

class ConsoleApplication
{
    const USER_VIEW_LIMIT = 2;

    private bool $stopConsume = false;

    public function runConsole()
    {
        $argv = $argv ?? $_SERVER['argv'] ?? [];
        array_shift($argv);
        $command = array_shift($argv);

        if ($command === null) {
            $this->consoleDefault();
        } elseif ($command == 'consume')
            $this->consoleConsumer();
        else {
            echo 'Command "' . $command . '" not found!' . PHP_EOL;
        }
    }

    public function consoleDefault()
    {
        echo 'default';
    }

    public function sigHandle($signal)
    {
        switch ($signal) {
            case SIGTERM:
                echo "Got SIGTERM" . PHP_EOL;
                $this->stopConsume = true;
                break;
            case SIGKILL:
                echo "Got SIGKILL" . PHP_EOL;
                $this->stopConsume = true;
                break;
            case SIGINT:
                echo "User pressed Ctrl+C - Got SIGINT" . PHP_EOL;
                $this->stopConsume = true;
                break;
        }
    }

    // Warning! Infinite Loop
    public function consoleConsumer()
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGTERM, [$this, 'sigHandle']);
        pcntl_signal(SIGINT, [$this, 'sigHandle']);

        $counters = new Counters(self::USER_VIEW_LIMIT);
        $db = new Database();

        echo 'Start consumer' . PHP_EOL;

        while (true) {
            if ($this->stopConsume) {
                break;
            }

            $this->process($counters, $db);

            sleep(1);
        }

        echo "Stop consumer" . PHP_EOL;
    }

    private function process($counters, $db)
    {
        $bannerCounters = $counters->getBannerCounterList();

        $sql = '';
        foreach ($bannerCounters as $key => $value) {
            if ($value === "0") {
                continue;
            }

            $id = substr($key, 4); //remove "BID:"
            $sql .= "UPDATE banner SET view_count=view_count+$value WHERE id = $id;";
            $counters->bannerCountDecBy($key, $value);
        }

        $db->execute($sql);
    }
}