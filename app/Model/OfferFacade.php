<?php

declare(strict_types=1);

namespace App\Model;

use App\UI\Accessory\Location\Getcityid;
use App\UI\Accessory\RequireLoggedUser;
use Nette\Database\Connection;

class OfferFacade
{
    use RequireLoggedUser;
    use Getcityid;
    private string $table;
    private array $sql_params;
    private array $allowed_columns;
    private string $limit_sql;
    private string $order_sql;

    public function __construct(public Connection $db)
    {
        $this->table = 'offer';
        $this->db = $db;

        $reflection = $this->db->getReflection();
        if ($reflection->hasTable($this->table)) {
            $table = $reflection->getTable($this->table);
        }
        $this->allowed_columns = $table->columns;
    }

    private function setSqlParams(array $location = [], ?int $limit = null, ?int $offset = null, ?object $form_data = null)
    {
        $this->sql_params[] = 'end_time > CURRENT_TIMESTAMP';
        $this->sql_params['nobanned'] = "{$this->table}.`client_id` NOT IN (SELECT `role_client`.`user_id` FROM `role_client` 
                INNER JOIN `role` ON `role_client`.`role_id` = `role`.`id`
                WHERE `role`.`role_name` = 'banned')";
        $this->limit_sql = '';
        $this->order_sql = 'ORDER BY end_time DESC';

        if (!empty($form_data->with_banned)) {
            unset($this->sql_params['nobanned']);
        }
        // offer id from request "Home:offer $offer_id" for page of offer by user case
        if (!empty($form_data->id) && is_int($form_data->id)) {
            $this->sql_params = ["`{$this->table}`.`id` = $form_data->id"];
            $this->order_sql = '';

            return true;
        }

        // get by updated_at > $form_data->created_time (datetime object)
        if (!empty($form_data->created_time)) {
            $this->sql_params[] = "`{$this->table}`.`updated_at` > '$form_data->created_time '";
        }
        // service
        if (!empty($form_data->service)) {
            $ids = \unserialize($form_data->service, ['allowed_classes' => false]);
            $offer_ids = [];
            if (is_array($ids) && $this->ifEachArrayValueInt($ids)) {
                $offer_ids = $this->db->query('SELECT (offer_id) FROM offer_service WHERE service_id IN ?', $ids);
            } else {
                if (ctype_digit(strval($ids))) {
                    $offer_ids = $this->db->query('SELECT (offer_id) FROM offer_service WHERE service_id = ?', $ids);
                }
            }
            foreach ($offer_ids as $row) {
                // for similar search 
                if (empty($form_data->not_id)) {
                    $res1[] = $row->offer_id;
                } elseif (!empty($form_data->not_id) && $row->offer_id != $form_data->not_id) {
                    $res1[] = $row->offer_id;
                }
            }
            if (!empty($res1)) {
                $res = '(' . \implode(',', \array_values(\array_unique($res1, SORT_REGULAR))) . ')';
                $this->sql_params[] = "`{$this->table}`.`id` IN {$res}";
            } else {
                $this->sql_params[] = "`{$this->table}`.`id` = -1";
            }
        }

        // LOCATION //
        /*
        $city_id = $this->getCityId($location);
        if ($city_id != false && is_integer($city_id)) {
            $this->sql_params[] = "city_id = {$city_id}";
        }
        */
        if (!empty($location)) {
            if (!empty($location['city']) && is_string($location['city'])) {
                /*$city = htmlspecialchars($location['city']);
                $sql_city = "city_name = '{$city}'";
                */
                if (str_contains($location['city'], '(')) {
                    $city = \trim(\explode('(', \htmlspecialchars($location['city']))[0]);
                } else {
                    $city = \htmlspecialchars($location['city']);
                }
                $sql_city = "city_name LIKE '{$city}%'";
            }
            if (!empty($location['region']) && is_string($location['region'])) {
                /*
                 $region = \htmlspecialchars($location['region']);
                $sql_region = "region_name = '{$region}'";
                */
                $region = \explode(' ', \htmlspecialchars($location['region']))[0];
                $sql_region = "region_name LIKE '{$region}%'";
            }

            if (!empty($sql_city)) {
                // $this->sql_params[] = (!empty($sql_region)) ? "($sql_city OR $sql_region)" : $sql_city;
                $this->sql_params[] = (!empty($sql_region)) ? "(($sql_city AND $sql_region) OR $sql_city)" : $sql_city;
            }
        }
        // END LOCATION

        if (!empty($form_data->client_id) && is_integer($form_data->client_id)) {
            $this->sql_params[] = "client_id = {$form_data->client_id}";
        }

        if (!empty($form_data->offertype) && in_array($form_data->offertype, ['work', 'service'], true)) {
            //$this->sql_params[] = "offers_type = '{$form_data->offertype}offer'";
            //$this->sql_params[] = "`{$this->table}`.`offers_type` = '{$form_data->offertype}offer'";
            if ($form_data->offertype === 'work') {
                $this->sql_params[] = "`{$this->table}`.`offers_type` = 'workoffer'";
            }
            if ($form_data->offertype === 'service') {
                $this->sql_params[] = "`{$this->table}`.`offers_type` = 'serviceoffer'";
            }
        }

        if (!empty($form_data->price_min) && ctype_digit(strval($form_data->price_min))) {
            $this->sql_params[] = "price >= $form_data->price_min";
        }

        if (!empty($form_data->price_max) && ctype_digit(strval($form_data->price_max))) {
            $this->sql_params[] = "price <= $form_data->price_max";
        }

        if (!empty($form_data->moderated) && in_array($form_data->moderated, [0, 1], true)) {
            $this->sql_params[] = "moderated = $form_data->moderated";
        }

        if (!empty($limit) && !empty($offset)) {
            $this->limit_sql = "LIMIT $limit OFFSET $offset";
        }
        if (!empty($limit) && empty($offset)) {
            $this->limit_sql = "LIMIT $limit";
        }

        if (
            !empty($form_data->order_by) && !empty($form_data->order_type)
            && in_array($form_data->order_type, ['ASC', 'DESC', 'asc', 'desc'], true)
            && array_key_exists($form_data->order_by, $this->allowed_columns)
        ) {
            $this->order_sql = "ORDER BY {$form_data->order_by} {$form_data->order_type}";
        }
    }

    public function offersCount(array $location = [], ?object $form_data = null): int
    {
        unset($this->sql_params);
        $this->setSqlParams(location: $location, form_data: $form_data);
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE";
        $numItems = count($this->sql_params);
        $i = 0;
        foreach ($this->sql_params as $value) {
            $sql .= " {$value}";
            if (++$i !== $numItems) {
                $sql .= ' AND ';
            }
        }

        return $this->db->query($sql)->fetchField();
    }


    public function offersCountOld(): int
    {
        $sql = "SELECT COUNT(`id`) FROM {$this->table} WHERE `end_time` < CURRENT_TIMESTAMP";
        return $this->db->query($sql)->fetchField();
    }

    public function deleteOld(): array
    {
        //date('Y-m-d H:i:s','1299762201428')
        $ids_sql = "SELECT `id` FROM {$this->table} WHERE `end_time` < CURRENT_TIMESTAMP";
        $ids = $this->db->query($ids_sql)->fetchPairs(null, 'id');
        if (!empty($ids)) {
            // $sql = "DELETE FROM `{$this->table}` WHERE `end_time` < CURRENT_TIMESTAMP";
            // $res = $this->db->query($sql)->getRowCount();
            $sql = "DELETE FROM `{$this->table}` WHERE `id` IN ?";
            $res = $this->db->query($sql, $ids)->getRowCount();
            return ['ids' => $ids, 'countDeleted' => $res];
        } else {
            return [];
        }
    }

    public function getOffers(array $location = [], int $limit = 1000, ?int $offset = null, ?object $form_data = null): array
    {
        // $sql = "SELECT * FROM {$this->table} WHERE";
        // FORMAT(`{$this->table}`.`price`, 0, 'ru_RU') AS 'price',
        $sql = "SELECT 
                `{$this->table}`.`id`,
                `{$this->table}`.`offers_type`,
                `{$this->table}`.`city_id` ,
                `{$this->table}`.`city_name`,
                `{$this->table}`.`region_id`,
                `{$this->table}`.`region_name`,
                `{$this->table}`.`district_id`,
                `{$this->table}`.`district_name`,
                `{$this->table}`.`price`,
                `{$this->table}`.`message`,
                `{$this->table}`.`created_at`,
                `{$this->table}`.`updated_at`,
                `{$this->table}`.`end_time`,
                `client`.`id` AS client_id,
                `client`.`username` AS client_name,
                `client`.`image`  AS client_image,
                `client`.`phone`  AS client_phone,
                `client`.`phone_verified`  AS client_phone_verified,
                `client`.`email` AS client_email,
                `client`.`email_verified` AS client_email_verified,
                `client`.`rating` AS client_rating
                FROM {$this->table} 
                INNER JOIN `client` ON {$this->table}.client_id = `client`.id
                WHERE";
        unset($this->sql_params);
        $this->setSqlParams(location: $location, limit: $limit, offset: $offset, form_data: $form_data);

        $numItems = count($this->sql_params);
        $i = 0;
        foreach ($this->sql_params as $value) {
            $sql .= " {$value}";
            if (++$i !== $numItems) {
                $sql .= ' AND ';
            }
        }

        if (!empty($this->order_sql)) {
            $sql .= " {$this->order_sql}";
        }

        if (!empty($this->limit_sql)) {
            $sql .= " {$this->limit_sql}";
        }

        $offers = $this->db->query($sql)->fetchAll();

        // getting images and services with category
        if (!empty($offers)) {
            $offers_ids = array_column($offers, 'id');
            $sql_images = 'SELECT * FROM `offer_image_thumb` WHERE';
            $offer_images = $this->db->query($sql_images, ['offer_id' => $offers_ids])->fetchAll();

            $sql_services = 'SELECT 
            `offer_service`.`offer_id`,
            `service`.`id`,
            `service`.`image`,
            `service`.`name`,
            `service`.`description`,
            `category`.`id` AS category_id,
            `category`.`image` AS category_image,
            `category`.`name` AS category_name,
            `category`.`description` AS category_description
            FROM `offer_service` 
                INNER JOIN `service` ON `offer_service`.`service_id` = `service`.`id`
                INNER JOIN `category` ON `service`.`category_id` = `category`.`id`  WHERE';
            $offer_services = $this->db->query($sql_services, ['offer_service.offer_id' => $offers_ids])->fetchAll();

            $categories = [];
            foreach ($offer_services as $vos) {
                $categories[$vos->offer_id][$vos->category_id] = [
                    'category_id' => $vos->category_id,
                    'category_image' => $vos->category_image,
                    'category_name' => $vos->category_name,
                    'category_description' => $vos->category_description,
                    'services' => []
                ];
            }
            foreach ($offer_services as $vos) {
                $categories[$vos->offer_id][$vos->category_id]['services'][$vos->id] = [
                    'id' => $vos->id,
                    'image' => $vos->image,
                    'name' => $vos->name,
                    'description' => $vos->description,
                ];
            }

            //$fmt = new \NumberFormatter('ru_RU', \NumberFormatter::CURRENCY);
            //$fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 0);
            $res_array = [];
            foreach ($offers as $k => $offer) {
                $res_array[$k] = [];
                //$offer->price = $fmt->formatCurrency((float) $offer->price, 'RUR');
                foreach ($offer as $m => $valu) {
                    $res_array[$k] += [$m => $valu];
                }

                if (!empty($categories)) {
                    $res_array[$k] += ['services' => []];
                    foreach ($categories as $offer_id => $cat) {
                        if ($res_array[$k]['id'] == $offer_id) {
                            $res_array[$k]['services'] = $cat;
                        }
                    }
                }
                if (!empty($offer_images)) {
                    $res_array[$k] += ['thumbnails' => []];
                    foreach ($offer_images as $j => $img) {
                        if ($res_array[$k]['id'] == $img->offer_id) {
                            $res_array[$k]['thumbnails'] = $img;
                        }
                    }
                }
            }
        }

        return !empty($res_array) ? $res_array : [];
    }

    public function add(object $formdata): string
    {
        $this->db->query("INSERT INTO `{$this->table}` ?", $formdata);
        return $this->db->getInsertId();
    }

    public function update(int $id, object $formdata)
    {
        $this->db->query("UPDATE `{$this->table}` SET ?  WHERE id = ?", $formdata, $id);
        return $this->db->getInsertId();
    }

    public function remove(int $id): ?int
    {
        $sql = "DELETE FROM `{$this->table}` WHERE id = ?";

        return $this->db->query($sql, $id)->getRowCount();
    }

    public function getSimilar(array $location, int $limit, int $offset, object $form_data): array
    {
        return $this->getOffers(location: $location, limit: $limit, offset: $offset, form_data: $form_data);
    }

    public function priceMinMax()
    {
        return $this->db->query('SELECT MAX(`price`) AS price_max, MIN(`price`) AS price_min FROM `offer`')->fetch();
    }

    public function ifEachArrayValueInt($array)
    {
        foreach ($array as $value) {
            if (!is_int($value) && !ctype_digit(strval($value))) {
                return false;
            }
        }

        return true;
    }
}
