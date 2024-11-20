<?php

declare(strict_types=1);

namespace App\UI\Home;

use App\UI\Accessory\GeoPlugin;
use App\UI\Accessory\IsBot;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    public function beforeRender()
    {
        parent::beforeRender();
        // get location of page visitor
        // Фильтруем ботов, чтобы не было лишних запросов к API
        if (!IsBot::check()) {
            // //////////////////////////////////////
            // city from GeoPlugin.com //
            /*
            $geoplugin = new GeoPlugin();
            $geoplugin->lang = 'ru';
            // locate the IP (get country, city and many other things)
            // $geoplugin->locate(ip: '77.51.199.15');
            $geoplugin->locate();
            $city_name = $geoplugin->city ?? null;
            */
            // //////////////////////////////////////

            // /////////////////////////////////////
            // city from SypexGeo.com //
            $geo = json_decode(file_get_contents('http://api.sypexgeo.net/json/'.$_SERVER['REMOTE_ADDR']), true);
            $city_name = $geo['city']['name_ru'] ?? null;
            // ////////////////////////////////////
            if (isset($city_name)) {
                $this->template->city_from_back = $city_name;
            }
        }
    }
}

class BaseTemplate extends \App\UI\BaseTemplate
{
    public string $city_from_back;
}
