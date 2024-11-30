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
}
