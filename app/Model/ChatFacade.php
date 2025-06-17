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

    public function getByOfferNoRead(ArrayHash $data): array
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
                AND `chat`.`client_id_to_whom` = ?
                AND `chat`.`moderated` = true
                AND `chat`.`read` = false';
        $m = $this->db->query($sql, $data->offer_id, $data->client_id_who)->fetchAll();
        $message = [];
        foreach ($m as $k => $row_message) {
            //$message[] = \get_object_vars($row_message);
            foreach ($row_message as $key => $value) {
                $message[$k][$key] = $value;
            }
        }
        return $message;
    }

    public function getByClient(int $client_id_to_whom): array
    {
        $sql = 'SELECT `chat`.`id`,
                        `chat`.`offer_id`,
                        `chat`.`client_id_who`
                FROM `chat`
                WHERE 
                    `chat`.`client_id_to_whom` = ?
                    AND `chat`.`moderated` = true
                    AND `chat`.`read` = false';

        return $this->db->query($sql, $client_id_to_whom)->fetchAll();
    }

    public function markRead(array $ids): int|null
    {
        $result = $this->db->query('UPDATE `chat` SET `read` = 1 WHERE id IN ?', $ids);
        return $result->getRowCount();
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
    public function countChat(int $client_id): int
    {
        $sql = "SELECT COUNT(*) 
                    FROM `chat` 
                    WHERE `chat`.`client_id_to_whom` = ?
                    AND `chat`.`read` = FALSE";
        $res = $this->db->query($sql, $client_id)->fetchField();

        return $res;
    }

    /**
     * return int number of replies to clients messages on the offers page
     */
    public function countChatOffer(int $client_id, int $offer_id): int
    {
        $sql = "SELECT COUNT(*) 
                    FROM `chat` 
                    WHERE `chat`.`offer_id` = ?
                    AND `chat`.`client_id_to_whom` = ?
                    AND `chat`.`read` = FALSE";
        $res = $this->db->query($sql, $offer_id, $client_id)->fetchField();

        return $res;
    }


    /**
     * return int number of replies to clients messages on the offers page
     */
    public function countByOffer(array $offers): array
    {
        $offer_ids = array_column($offers, 'id');
        if (!empty($offer_ids)) {
            $sql = "SELECT offer_id, COUNT(offer_id) AS count 
                    FROM `chat` 
                    WHERE `chat`.`offer_id` IN ?
                    GROUP BY offer_id";
            $cc = $this->db->query($sql, $offer_ids);
            foreach ($cc as $value) {
                $res[$value->offer_id] = $value->count;
            }
        }
        return $res ?? [];
    }

    public function edit()
    {
    }

    public function delete(int $id): int|null
    {
        return $this->db->query('DELETE FROM `chat` WHERE id = ?', $id)->getRowCount();
    }


    public function deleteByOffer(int $offer_id): int|null
    {
        return $this->db->query('DELETE FROM `chat` WHERE offer_id = ?', $offer_id)->getRowCount();
    }

    public function deleteByOfferClient(int $offer_id, int $client_id): int|null
    {
        return $this->db->query(
            'DELETE FROM `chat` WHERE offer_id = ? AND (client_id_who = ? OR client_id_to_whom = ?)',
            $offer_id,
            $client_id,
            $client_id
        )->getRowCount();
    }

    public function AdmGetByOffer(int $offer_id)
    {
        $sql = 'SELECT `chat`.`id`,
                        `chat`.`parent_id`,
                        `chat`.`offer_id`,
                        `chat`.`client_id_who`,
                        `chat`.`client_id_to_whom`,
                        `chat`.`message`,
                        `chat`.`request_data`,
                        `chat`.`created_at`,
                        `client`.`username` 
                        FROM `chat` 
                INNER JOIN `client` ON `chat`.`client_id_who` = `client`.`id`
                WHERE 
                `chat`.`offer_id` = ?';
        $m = $this->db->query($sql, $offer_id)->fetchAll();

        return $m;
    }
}
