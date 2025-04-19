<?php

declare(strict_types=1);

namespace App\Model;

// use Nette\Database\Explorer;
use App\UI\Accessory\RequireLoggedUser;
use Nette\Database\Connection;
use Nette\Database\Table\Selection;

/**
 * Manages user-related operations such as authentication and adding new users.
 */
final class RatingFacade
{
    use RequireLoggedUser;
    private Selection $table;

    public function __construct(public Connection $db) // public Explorer $db)
    {
        // $this->table = $this->db->table('rating');
    }

    public function get(int $id)
    {
        return $this->db->query('SELECT `rating` FROM `client` WHERE `id` = ?', $id)->fetchField();
    }

    public function add($data)
    {
        $client_id_max = 'SELECT MAX(`id`) FROM `client`';
        if (is_numeric($data->client_id_who) && intval($data->client_id_who) <= $client_id_max
            && is_numeric($data->client_id_to_whom) && intval($data->client_id_to_whom) <= $client_id_max
            && is_numeric($data->rating_value) && intval($data->rating_value) > 0 && intval($data->rating_value) < 6
        ) {
            // $this->table->insert($data);
            $this->db->query('INSERT INTO `rating` ? ON DUPLICATE KEY UPDATE `rating_value` = ?;', $data, $data->rating_value);

            return true;
        } else {
            return false;
        }
    }

    public function update(int $rating_value)
    {
    }

    public function delete(int $rating_value)
    {
    }
}
