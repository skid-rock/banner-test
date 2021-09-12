<?php

namespace App;

class Application
{
    const BR = "<br/>";
    const USER_VIEW_LIMIT = 2;
    const USER_ID_COOKIE = 'uid';

    public function run()
    {
        $this->routing();
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
