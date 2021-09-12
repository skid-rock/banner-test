<?php

namespace App;

class Application
{
    const BR = "<br/>";

    public function runConsole()
    {
        //$uid = md5(time());
        $uid = 'b9fd8975a2d50949313f0f2a829f325a';

        $this->process($uid);
    }

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
        if (isset($_COOKIE['uid'])) {
            $uid = $_COOKIE['uid'];
        } else {
            $uid = md5(time());
            setcookie('uid', $uid);
        }

        // response
        echo "User_id: $uid" . self::BR;
        echo "Url Banner List:" . self::BR;

        $urls = $this->process($uid);
        foreach ($urls as $url) {
            echo $url . self::BR;
        }
    }

    // main domain logic
    private function process($uid): array
    {
        $db = new Database();

        $bannerList = $db->getBannerListByUser($uid);
        $response = [];
        foreach ($bannerList as $id => $banner) {
            $response[] = $banner['url'];
            $db->incrementUserBannerCounter($id, $uid);
            $db->incrementBannerCounter($id);
        }

        return $response;
    }
}
