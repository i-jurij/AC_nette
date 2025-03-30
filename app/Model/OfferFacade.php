<?php

declare(strict_types=1);

namespace App\Model;

use App\UI\Accessory\Getcityid;
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
        $this->sql_params = ['end_time > CURRENT_TIMESTAMP'];
        $this->limit_sql = '';
        $this->order_sql = '';
    }

    private function setSqlParams(string $type = '', array $location = [], ?int $limit = null, ?int $offset = null, ?object $form_data = null)
    {
        $city_id = $this->getCityId($location);
        if ($city_id != false && is_integer($city_id)) {
            $this->sql_params[] = "location = {$city_id}";
        }
        if (!empty($form_data->client_id) && is_integer($form_data->client_id)) {
            $this->sql_params[] = "client_id = {$form_data->client_id}";
        }

        if (!empty($type) && is_string($type)) {
            $this->sql_params[] = "offers_type = '{$type}offer'";
        }

        if (!empty($form_data->price_min) && !empty($form_data->price_max)) {
            $this->sql_params[] = $this->db::literal('price > ? AND price < ?', $form_data->price_min, $form_data->price_max);
        }

        if (!empty($limit) && !empty($offset)) {
            $this->limit_sql = "LIMIT $limit OFFSET $offset";
        }
        if (!empty($limit) && empty($offset)) {
            $this->limit_sql = "LIMIT $limit";
        }

        if (
            !empty($form_data->order_by) && !empty($form_data->order_type)
            && is_string($form_data->order_by) && is_string($form_data->order_type)
            && ($form_data->order_type === 'ASC' || $form_data->order_type === 'DESC')
            && array_key_exists($form_data->order_by, $this->allowed_columns)
        ) {
            $this->order_sql = "ORDER BY {$form_data->order_by} {$form_data->order_type}";
        }
    }

    public function offersCount($type = '', array $location = [], ?object $form_data = null): int
    {
        $this->setSqlParams(type: $type, location: $location, form_data: $form_data);
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

    public function getOffers(string $type = '', array $location = [], int $limit = 1000, ?int $offset = null, ?object $form_data = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE";
        $this->setSqlParams(type: $type, location: $location, limit: $limit, offset: $offset, form_data: $form_data);

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

        return $this->db->query($sql);
    }

    public function add()
    {
    }

    public function remove()
    {
    }

    public function update()
    {
    }
}
