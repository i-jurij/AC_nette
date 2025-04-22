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

    // public function __construct(public Connection $db)
    public function __construct(public Explorer $db)
    {
        $this->table = 'comment';
    }

    public function create($data)
    {
        $this->db->table($this->table)->insert($data);
    }

    public function edit()
    {
    }

    public function delete()
    {
    }
}
