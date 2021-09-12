<?php

namespace App;

class Application extends BaseApplication
{
    const BR = "<br/>";
    const USER_VIEW_LIMIT = 2;
    const USER_ID_COOKIE = 'uid';

    public function consoleDefault()
    {
        //$uid = md5(time());
        //$usedId = 'b9fd8975a2d50949313f0f2a829f325a';

        $this->consoleConsumer();
    }

    public function consoleConsumer()
    {
        $counters = new Counters(self::USER_VIEW_LIMIT);
        $bannerCounters = $counters->getBannerCounterList();

        $db = new Database();
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

    protected function routing()
    {
        if ($_SERVER['REQUEST_URI'] === '/') {
            $this->index();
        }
    }

    public function index()
    {
        // sets user id cookie
        if (isset($_COOKIE[self::USER_ID_COOKIE])) {
            $usedId = $_COOKIE[self::USER_ID_COOKIE];
        } else {
            $usedId = md5(time());
            setcookie('uid', $usedId);
        }

        // response
        echo "User ID: $usedId" . self::BR;
        echo "Url Banner List:" . self::BR;

        $urls = $this->process($usedId);
        foreach ($urls as $url) {
            echo $url . self::BR;
        }
    }

    // main domain logic
    private function process($usedId): array
    {
        $db = new Database();
        $counters = new Counters(self::USER_VIEW_LIMIT);

        $bannerListIds = $counters->getUserBannerList($usedId);
        $bannerList = $db->getBannerListByUser($usedId, $bannerListIds);

        $response = [];
        foreach ($bannerList as $bannerId => $banner) {
            $response[] = $banner['url'];
            $counters->userBannerCount($usedId, $bannerId); // user-banner counter
            $counters->bannerCountInc("BID:$bannerId"); // banner counter
        }

        $response[] = $counters->getUsedMemory();

        return $response;
    }
}
