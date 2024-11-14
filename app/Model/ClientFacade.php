<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Security\Passwords;

/**
 * Manages client-related operations such as authentication and adding new clients.
 */
class ClientFacade extends UserFacade
{
    public function __construct(
        public Explorer $db,
        private Passwords $passwords,
        public string $table = 'client',
        private string $table_role_user = 'role_client'
    ) {
        $this->selection = $this->db->table($this->table);
    }

    public function shortAdd(string $username, string $password, string $table): void
    {
    }
}

/**
 * Custom exception for duplicate usernames.
 */
class DuplicateClientNameException extends \Exception
{
}
