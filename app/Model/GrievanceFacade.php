<?php

declare(strict_types=1);

namespace App\Model;

use App\UI\Accessory\RequireLoggedUser;
// use Nette\Database\Connection;
use Nette\Database\Explorer;

class GrievanceFacade
{
    use RequireLoggedUser;

    private $gr;
    private int $numberOfComments = 100;

    // public function __construct(public Connection $db)
    public function __construct(public Explorer $db)
    {
        $this->gr = $this->db->table('grievance');
    }

    public function create(array $data): int
    {
        $row = $this->gr->insert($data);
        return (int) $row->id;
    }

    public function update()
    {
    }

    public function delete()
    {
    }
}
