<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

/**
 * Manages user-related operations such as authentication and adding new users.
 */
final class RatingFacade
{
    private Selection $table;

    public function __construct(public Explorer $db)
    {
        $this->table = $this->db->table('rating');
    }

    public function add($data)
    {
        foreach ($data as $value) {
            if (!is_int($value) && !ctype_digit(strval($value))) {
                return;
            }
        }
        $this->table->insert($data);
    }

    public function update(int $rating_value)
    {
    }

    public function delete(int $rating_value)
    {
    }
}
