<?php
declare(strict_types=1);

namespace App\Model;

use App\UI\Accessory\RequireLoggedUser;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

class OfferFacade
{
    use RequireLoggedUser;
    public string $table = 'offer';

    public function __construct(public Explorer $db)
    {
    }

    public function list()
    {
    }
    public function get(int $limit = 20, int $offset = 0)
    {
        return $this->db->table($this->table)
            ->where('offer.end_time >', 'CURRENT_TIMESTAMP')
            ->limit($limit, $offset)
            ->order('offer.price DESC, offer.updated_at DESC');
    }

    public function getWorks()
    {
        return $this->db->table($this->table)
            ->where('offer.offers_type', 'workoffer')
            ->where('offer.end_time >', 'CURRENT_TIMESTAMP')
            ->order('offer.price DESC, offer.updated_at DESC');
    }
    public function getServices()
    {
        return $this->db->table($this->table)
            ->where('offer.offers_type', 'serviceoffer')
            ->where('offer.end_time >', 'CURRENT_TIMESTAMP')
            ->order('offer.price DESC, offer.updated_at DESC');
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
