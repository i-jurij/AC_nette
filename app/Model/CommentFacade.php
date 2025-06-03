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

    /**
     * @param array $offers - array of active row with id field (after fetchAll() eg)
     */
    public function commentsCount(array $offers): array
    {
        $offer_ids = array_column($offers, 'id');
        $cc = $this->db->table('comment')
            ->select('offer_id, COUNT(offer_id) AS count')
            ->where('offer_id IN', $offer_ids)
            ->group('offer_id');
        foreach ($cc as $value) {
            $res[$value->offer_id] = $value->count;
        }
        return $res ?? [];
    }

    public function getByOffer(int $offer_id): \Nette\Database\Table\Selection
    {
        $cc = $this->db->table('comment')
            ->where('offer_id', $offer_id)
            ->order('created_at ASC');
        return $cc;
    }

    public function getByClient(int $client_id): \Nette\Database\Table\Selection
    {
        $cc = $this->db->table('comment')
            ->where('client_id', $client_id)
            ->order('offer_id, created_at ASC');
        return $cc;
    }

    public function get(int $id)
    {
        return $this->db->table('comment')->get($id);
    }

    public function getRequest(int $id)
    {
        return $this->db->table('comment')->get($id)->request_data;
    }

    public function update(int $id, string $comment_text)
    {
        $c = $this->db->table('comment')->get($id);
        return $c->update([
            'comment_text' => $comment_text,
        ]);
    }


    public function delete(int $id)
    {
        $res = $this->db->table('comment')
            ->get($id)
            ->delete();
        return $res;
    }
}
