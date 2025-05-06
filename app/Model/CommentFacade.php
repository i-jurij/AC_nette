<?php

declare(strict_types=1);

namespace App\Model;

use App\UI\Accessory\RequireLoggedUser;
// use Nette\Database\Connection;
use Nette\Database\Explorer;

class CommentFacade
{
    use RequireLoggedUser;

    private string $table;
    private int $numberOfComments = 100;

    // public function __construct(public Connection $db)
    public function __construct(public Explorer $db)
    {
        $this->table = 'comment';
    }

    public function create($data)
    {
        $row = false;
        if ($this->checkCount(offer_id: $data->offer_id) < $this->numberOfComments) {
            $row = $this->db->table($this->table)->insert($data);
        }
        return $row;
    }

    private function checkCount(int $offer_id): int
    {
        return $this->db->table($this->table)->where('offer_id', $offer_id)->count();
    }

    public function edit()
    {
    }

    public function delete()
    {
    }
}
