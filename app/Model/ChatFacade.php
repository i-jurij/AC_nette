<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Connection;
// use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use \Nette\Utils\ArrayHash;

class ChatFacade
{
    private Selection $chat;
    private int $numberOfMessage = 10;

    public function __construct(public Connection $db)
    //public function __construct(public Explorer $db)
    {
        //$this->chat = $this->db->table('chat');
    }

    public function getByOffer(ArrayHash $data): array
    {
        $sql = 'SELECT * FROM `chat` 
                WHERE 
                `offer_id` = ? 
                AND (client_id_who = ? OR client_id_to_whom = ?)';
        return $this->db->query($sql, $data->offer_id, $data->client_id_who, $data->client_id_who)->fetchAll();
    }
    public function getByClient(ArrayHash $data): array
    {
        return [];
    }
    public function create(ArrayHash $data): int
    {
        $this->db->query('INSERT INTO `chat` ?', $data);
        return (int) $this->db->getInsertId();
    }

    /**
     * return int number of all replies to clients messages
     */
    public function countChat(int $client_id, int $offer_id = null): int
    {
        if (!empty($offer_id)) {
            $sql = "SELECT COUNT(*) 
                        FROM `chat` 
                        WHERE `chat`.`offer_id` = ?
                        AND `chat`.`client_id_to_whom` = ?
                        AND `chat`.`read` = FALSE";
            $res = $this->db->query($sql, $offer_id, $client_id)->fetchField();
        } else {
            $sql = "SELECT COUNT(*) 
                        FROM `chat` 
                        WHERE `chat`.`client_id_to_whom` = ?
                        AND `chat`.`read` = FALSE";
            $res = $this->db->query($sql, $client_id)->fetchField();
        }

        return $res;
    }

    public function edit()
    {
    }

    public function delete()
    {
    }
}
