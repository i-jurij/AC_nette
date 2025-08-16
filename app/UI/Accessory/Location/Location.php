<?php

namespace App\UI\Accessory\Location;
use Nette\Database\Connection;
class Location
{
    private Connection $db;
    public function __construct()
    {
        $path_to_geo_db = realpath(APPDIR
            . DIRECTORY_SEPARATOR
            . '..'
            . DIRECTORY_SEPARATOR
            . 'vendor'
            . DIRECTORY_SEPARATOR
            . 'i-jurij'
            . DIRECTORY_SEPARATOR
            . 'geolocation'
            . DIRECTORY_SEPARATOR
            . 'src'
            . DIRECTORY_SEPARATOR
            . 'sqlite'
            . DIRECTORY_SEPARATOR
            . 'geolocation.db');
        $dsn = "sqlite:$path_to_geo_db";

        $this->db = new Connection($dsn);
    }

    public function getDistricts()
    {
        $loc_sql = "SELECT `id`, `name` FROM `geo_district` ";
        return $this->db->query($loc_sql)->fetchAll();
    }
    public function getRegions(int $district_id)
    {
        $loc_sql = "SELECT `id`, `name`  FROM `geo_regions` WHERE `disrict_id` = ?";
        return $this->db->query($loc_sql, $district_id)->fetchAll();
    }

    public function getCities(int $region_id)
    {
        $loc_sql = "SELECT `id`, `name` FROM `geo_city`  WHERE `region_id` = ?";
        return $this->db->query($loc_sql, $region_id)->fetchAll();
    }

    public function list(): array
    {
        $res = [];
        $loc_sql = "SELECT  d.id AS district_id, d.name AS district, 
                            r.id AS region_id, r.name AS region,
                            c.id AS city_id, c.name AS city
                    FROM `geo_district` AS d
                    INNER JOIN `geo_regions` AS r ON d.id = r.district_id
                        INNER JOIN `geo_city` AS c ON r.id = c.region_id";
        $rows = $this->db->query($loc_sql);
        foreach ($rows as $row) {
            $res['district'][$row['district_id']]['id'] = $row['district_id'];
            $res['district'][$row['district_id']]['name'] = $row['district'];

            $res['district'][$row['district_id']]['regions'][$row['region_id']]['id'] = $row['region_id'];
            $res['district'][$row['district_id']]['regions'][$row['region_id']]['name'] = $row['region'];

            $res['district'][$row['district_id']]['regions'][$row['region_id']]['cities'][$row['city_id']]['id'] = $row['city_id'];
            $res['district'][$row['district_id']]['regions'][$row['region_id']]['cities'][$row['city_id']]['name'] = $row['city'];
        }

        return $res;
    }
}
