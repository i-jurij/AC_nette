<?php

declare(strict_types=1);

namespace App\UI\Accessory;

final class Geo
{
    public static function geoPlugin()
    {
        if (!IsBot::check()) {
            $geoplugin = new GeoPlugin();
            $geoplugin->lang = 'ru';
            $geoplugin->locate();
            // $geoplugin->locate('2.63.182.224');

            $city_name = $geoplugin->city ?? null;
            $region = $geoplugin->region ?? null;

            return ['city' => $city_name, 'region' => $region];
        }
    }

    public static function sypexGeo()
    {
        // Фильтруем ботов, чтобы не было лишних запросов к API
        if (!IsBot::check()) {
            // city from SypexGeo.com //
            $ip = (!empty(Ip::getIp()['ip'])) ? Ip::getIp()['ip'] : '';
            // $ip = '2.63.182.224';

            if (!empty($ip) && $ip != '127.0.0.1') {
                if (\filter_var(\ini_get('allow_url_fopen'), \FILTER_VALIDATE_BOOLEAN)) {
                    $json = file_get_contents('http://api.sypexgeo.net/json/'.$ip);
                    if ($json !== false) {
                        $geo = json_decode($json, true);
                        $city_name = $geo['city']['name_ru'] ?? null;
                        $region = $geo['region']['name_ru'] ?? null;

                        return ['city' => $city_name, 'region' => $region];
                    }
                }
            }
        }
    }
}
