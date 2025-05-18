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
        $sql = 'SELECT `chat`.`id`,
                        `chat`.`parent_id`,
                        `chat`.`offer_id`,
                        `chat`.`client_id_who`,
                        `chat`.`client_id_to_whom`,
                        `chat`.`message`,
                        `chat`.`created_at`,
                        `client`.`username` 
                        FROM `chat` 
                INNER JOIN `client` ON `chat`.`client_id_who` = `client`.`id`
                WHERE 
                `chat`.`offer_id` = ? 
                AND (`chat`.`client_id_who` = ? OR `chat`.`client_id_to_whom` = ?)
                AND `chat`.`moderated` = true';
        //$message = $this->db->query($sql, $data->offer_id, $data->client_id_who, $data->client_id_who)->fetchAll();
        $m = $this->db->query($sql, $data->offer_id, $data->client_id_who, $data->client_id_who);
        $message = [];
        foreach ($m as $row_message) {
            $message[] = \get_object_vars($row_message);
        }

        return $message;
    }
    public function getByClient(ArrayHash $data): array
    {
        return [];
    }

    public function markRead(array $ids)
    {
        $this->db->query('UPDATE `chat` SET `read` = 1 WHERE id IN ?', $ids);
    }
    public function create(ArrayHash $data)
    {
        $this->db->query('INSERT INTO `chat` ?', $data);
        $resp = $this->db->query('SELECT    `chat`.`id`,
                                        `chat`.`parent_id`,
                                        `chat`.`offer_id`,
                                        `chat`.`client_id_who`,
                                        `chat`.`client_id_to_whom`,
                                        `chat`.`message`,
                                        `chat`.`created_at`
                                FROM `chat` 
                                WHERE `chat`.`id` = ?', $this->db->getInsertId())->fetch();
        return \get_object_vars($resp);
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
