<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use App\Model\UserFacade;
use Nette\Utils\Random;

class UserFact
{
    private array $array = [
        'username' => '',
        'password' => '',
        'phone' => '',
        'email' => '',
    ];
    private $roles_ids;
    public $uf;

    public function __construct(UserFacade $userfacade)
    {
        $this->uf = $userfacade;
        //$this->roles_ids = $this->uf->db->table('role')->select('id')->fetchPairs('id');
    }

    public function seedUser()
    {
        $this->array['username'] = Random::generate(7, 'a-z');
        $this->array['password'] = 'password';
        $this->array['phone'] = Random::generate(10, '0-9');
        $this->array['email'] = $this->array['username'] . '@' . $this->array['username'] . '.com';
        $this->array['auth_token'] = Random::generate(10, '0-9');
        //$role_id = array_rand($this->roles_ids) ?? '';
        //$this->array['roles'] = [$role_id];

        return $this->array;
    }

    public function saveUser($user)
    {
        //$this->uf->add($user);
        $this->uf->db->table('user')->insert($user);
    }
}
