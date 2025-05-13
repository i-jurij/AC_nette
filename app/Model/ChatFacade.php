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

    public function search(ArrayHash $data)
    {
        // get chats message from this client to offers owner 
        // (search by offer_id client_id and parent_id = chat.id where offer_id and client_id)
        // get chats message to this client (search by offer_id with him client_id)
    }
    public function create(ArrayHash $data)
    {
        return 1;
    }

    /**
     * return int number of all replies to clients messages
     */
    public function countChat(int $client_id, int $offer_id = null): int
    {
        if (!empty($offer_id)) {
            $sql = "SELECT COUNT(*) FROM `chat` WHERE `chat`.`parent_id` IN 
                            (SELECT `chat`.`id` FROM `chat` WHERE `chat`.`offer_id` = ? AND `chat`.`client_id` = ?)
                            AND `chat`.`client_id` = (
                                SELECT `offer`.`client_id` FROM `offer` WHERE `offer`.`id` = ?
                            )
                            AND `chat`.`read` = FALSE";
            $res = $this->db->query($sql, $offer_id, $client_id, $offer_id)->fetchField();
        } else {
            $sql = "SELECT COUNT(*) FROM `chat` WHERE `chat`.`offer_id` IN
                (SELECT `offer`.`id` FROM `offer` WHERE `offer`.`client_id` = ?)
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
