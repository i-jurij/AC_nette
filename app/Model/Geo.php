<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Connection;

/**
 * Manages user-related operations such as authentication and adding new users.
 */
final class Geo
{
    private $table_district;
    private $table_region;
    private $table_city;

    public function __construct(private Connection $db)
    {
    }

    public function district()
    {
        $query = 'SELECT `id`, `name` FROM `geo_district`';

        return $this->db->fetchAll($query);
    }

    public function region($id)
    {
        $query = 'SELECT `id`, `name` FROM `geo_regions` WHERE district_id = ?';

        return $this->db->fetchAll($query, $id);
    }

    public function city($id)
    {
        $query = 'SELECT `id`, `name` FROM `geo_city` WHERE region_id = ?';

        return $this->db->fetchAll($query, $id);
    }

    public function getAll(): array
    {
        $query = '  SELECT  d.id AS district_id, d.name AS district, 
                            r.id AS region_id, r.name AS region,
                            c.id AS city_id, c.name AS city
                    FROM geo_district AS d
                    INNER JOIN geo_regions AS r ON d.id = r.district_id
                        INNER JOIN geo_city AS c ON r.id = c.region_id
        ';

        $rows = $this->db->query($query);

        foreach ($rows as $row) {
            $res['district'][$row->district_id]['id'] = $row->district_id;
            $res['district'][$row->district_id]['name'] = $row->district;

            $res['district'][$row->district_id]['regions'][$row->region_id]['id'] = $row->region_id;
            $res['district'][$row->district_id]['regions'][$row->region_id]['name'] = $row->region;

            $res['district'][$row->district_id]['regions'][$row->region_id]['cities'][$row->city_id]['id'] = $row->city_id;
            $res['district'][$row->district_id]['regions'][$row->region_id]['cities'][$row->city_id]['name'] = $row->city;
        }

        return $res ?? [];
    }

    public function fromCoord($long_lat)
    {
        $locality = [];
        if (!empty($long_lat) && \is_string($long_lat)) {
            list($long, $lat) = explode('_', trim($long_lat));
            $area = (1 / 111) * 100; // ~100km (1° ~ 111 км, 1 км = 1 / 111 = 0,009009009009009°.)

            $lat_dist_minus = (float) $lat - $area;
            $lat_dist_plus = (float) $lat + $area;
            $long_dist_minus = (float) $long - $area;
            $long_dist_plus = (float) $long + $area;

            $params0 = [$lat_dist_minus, $lat_dist_plus, $long_dist_minus, $long_dist_plus];

            $query = 'SELECT `city`, `adress`, `id`
                                    FROM (
                                            SELECT `id`, `city`, `adress`, `distance`
                                                FROM (
                                                        SELECT `gc`.`id`, `gc`.`name` AS city, `r`.`name` AS adress,
                                                            ACOS(SIN(PI()*gc.latitude/180.0)*SIN(PI()*?/180.0)
                                                                +COS(PI()*gc.latitude/180.0)*COS(PI()*?/180.0)
                                                                *COS(PI()*?/180.0-PI()*gc.longitude/180.0))*6371 AS distance
                                                        FROM `geo_city` AS gc
                                                        INNER JOIN `geo_regions` AS r ON `r`.`id` = `gc`.`region_id`
                                                        WHERE gc.latitude BETWEEN ? AND ?
                                                        AND gc.longitude BETWEEN ? AND ?
                                                ) AS subquery
                                            ORDER BY distance
                                            LIMIT 5
                                    ) AS limited
                                    ORDER BY distance
                                    LIMIT 1;';
            $params = [(float) $lat, (float) $lat, (float) $long, ...$params0];

            $locality = $this->db->fetch($query, ...$params);
        }

        return $locality;
    }
}
