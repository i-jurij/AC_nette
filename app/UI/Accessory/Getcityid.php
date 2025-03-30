<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Nette\Database\Connection;

trait Getcityid
{
    private function getCityId(array $location)
    {
        $res_id = false;
        $path_to_geo_db = realpath(APPDIR
            .DIRECTORY_SEPARATOR
            .'..'
            .DIRECTORY_SEPARATOR
            .'vendor'
            .DIRECTORY_SEPARATOR
            .'i-jurij'
            .DIRECTORY_SEPARATOR
            .'geolocation'
            .DIRECTORY_SEPARATOR
            .'src'
            .DIRECTORY_SEPARATOR
            .'sqlite'
            .DIRECTORY_SEPARATOR
            .'geolocation.db');

        $dsn = "sqlite:$path_to_geo_db";

        $sqlite = new Connection($dsn);

        if (!empty($location['city']) && !empty($location['region'])) {
            $city = $location['city'];
            $region = $location['region'];

            $reg_sql = 'SELECT (`id`) FROM `geo_regions` WHERE `name` LIKE ?';
            $region_id = $sqlite->query($reg_sql, "%$region%")->fetchField();

            $res_id = $sqlite
                ->query('SELECT (`id`) FROM `geo_city` WHERE', [
                    'name LIKE' => "%$city%",
                    'region_id' => $region_id,
                ])
                ->fetchField();
        }

        if (!empty($location['city']) && empty($location['region'])) {
            $city = $location['city'];
            $loc_sql = 'SELECT (`id`) FROM `geo_city` WHERE `name` LIKE ?';
            $res_id = $sqlite->query($loc_sql, "%$city%")->fetchField();
        }

        return $res_id;
    }
}
