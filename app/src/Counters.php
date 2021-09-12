<?php

namespace App;

use Exception;
use Redis;

class Counters
{
    private Redis $redis;
    private int $userViewLimit;

    public function __construct(int $userViewLimit)
    {
        $this->userViewLimit = $userViewLimit;
        $this->redis = new Redis();

        if (!$this->redis->connect('redis')) {
            throw new Exception('Redis connection error');
        }
    }

    public function bannerCountInc($name): int
    {
        return $this->redis->incr($name);
    }

    public function bannerCountDecBy($name, $by = 1): int
    {
        return $this->redis->decrBy($name, $by);
    }

    public function userBannerCount($userId, $bannerId)
    {
        if ($this->redis->incr("$userId:$bannerId") >= $this->userViewLimit) {
            $this->appendUserBannerList($userId, $bannerId);
        }
    }

    // get list of viewed banners as comma-separated string
    public function getUserBannerList($name): string
    {
        return $this->redis->get("VIEWED:$name");
    }

    // save list of viewed banners as comma-separated string
    public function appendUserBannerList($name, $item): string
    {
        return $this->redis->append("VIEWED:$name", "$item,");
    }

    public function getBannerCounterList(): array
    {
        $bannerKeys = $this->redis->keys('BID:*');
        $bannerValues = $this->redis->mget($bannerKeys);

        return array_combine($bannerKeys, $bannerValues);
    }

    //
    public function getUsedMemory(): string
    {
        return "Used Memory: " . $this->redis->info('MEMORY')['used_memory_human'];
    }

    // tmp
    public function redis()
    {
        return $this->redis;
    }

}