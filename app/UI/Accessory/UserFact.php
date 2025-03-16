<?php

declare (strict_types = 1);

namespace App\UI\Accessory;

use App\Model\UserFacade;
use Nette\Utils\Random;

class UserFact
{
    private array $array = [
        'username' => '',
        'password' => '',
        'phone'    => '',
        'email'    => '',
    ];
    private $roles_ids;
    public $uf;

    public function __construct(UserFacade $userfacade)
    {
        $this->uf        = $userfacade;
        $this->roles_ids = $this->uf->db->table('role')->select('id')->fetchPairs('id');
    }

    public function seedUser()
    {
        $this->array['username'] = Random::generate(7, 'a-z');
        $this->array['password'] = password_hash(Random::generate(8, 'a-z0-9'), PASSWORD_DEFAULT);
        $this->array['phone']    = Random::generate(10, '0-9');
        $this->array['email']    = $this->array['username'] . '@' . $this->array['username'] . '.com';
        $role_id                 = array_rand(\array_flip($this->roles_ids));
        $this->array['roles']    = [$role_id];

        return (object) $this->array;
    }

    public function saveUser($user)
    {
        $this->uf->add($user);
    }
}
