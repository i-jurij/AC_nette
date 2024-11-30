<?php

declare(strict_types=1);

namespace App\UI\Home;

use App\UI\Accessory\Ip;
use App\UI\Accessory\IsBot;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    public function beforeRender()
    {
        parent::beforeRender();
        // get location of page visitor
        // Фильтруем ботов, чтобы не было лишних запросов к API
        if (!IsBot::check()) {
            // city from SypexGeo.com //
            $ip = (!empty(Ip::getIp()['ip'])) ? Ip::getIp()['ip'] : '';
            // $ip = '2.63.182.224';

            if ((bool) $ip && $ip != '127.0.0.1') {
                if (\filter_var(\ini_get('allow_url_fopen'), \FILTER_VALIDATE_BOOLEAN)) {
                    $json = file_get_contents('http://api.sypexgeo.net/json/'.$ip);
                    if ($json !== false) {
                        $geo = json_decode($json, true);
                        $city_name = $geo['city']['name_ru'] ?? null;
                        $region = $geo['region']['name_ru'] ?? null;
                    }
                }
            }

            // ////////////////////////////////////
            if (isset($city_name)) {
                $this->template->city_from_back = $city_name;
            }
            if (isset($region)) {
                $this->template->region = $region;
            }
        }
    }
}

class BaseTemplate extends \App\UI\BaseTemplate
{
    public string $city_from_back;
    public string $region;
}
