<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Utils\DateTime;

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

    public function getNotResolve(): array
    {
        return $this->gr->where('resolve', false)->fetchAll();
    }

    public function getResolved(): array
    {
        return $this->gr->where('resolve', true)->fetchAll();
    }

    public function getByClient(int $id): array
    {
        return $this->gr
            ->where('resolve', false)
            ->where('client_id_who', $id)
            ->fetchAll();
    }

    public function markResolved(int $id): int
    {
        return $this->gr->where('id', $id)->update([
            'resolve' => 1,
            'resolve_time' => new DateTime,
        ]);

    }

    public function removeOld(): int
    {
        $original = new DateTime;
        $one_year_ago = $original->modifyClone('-1 year');
        return $this->gr
            ->where('resolve_time <', $one_year_ago)
            ->delete();
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
