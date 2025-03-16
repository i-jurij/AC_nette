<?php

declare (strict_types = 1);

namespace App\UI\Accessory;

use App\Model\ClientFacade;
use Nette\Utils\Random;

class ClientFact
{
    private array $array = [
        'username' => '',
        'password' => '',
        'phone'    => '',
        'email'    => '',
    ];
    private $client_role_id;
    private $customer_role_id;
    private $executor_role_id;
    public $cf;

    public function __construct(ClientFacade $clientFacade)
    {
        $this->cf = $clientFacade;

        $this->client_role_id = $this->cf->db
            ->table('role')
            ->where('role_name', 'client')
            ->fetch()
            ->id;

        $this->customer_role_id = $this->cf->db
            ->table('role')
            ->where('role_name', 'customer')
            ->fetch()
            ->id;

        $this->executor_role_id = $this->cf->db
            ->table('role')
            ->where('role_name', 'executor')
            ->fetch()
            ->id;
    }

    protected function prepare()
    {
        $this->array['username'] = Random::generate(7, 'a-z');
        $this->array['password'] = password_hash('password', PASSWORD_DEFAULT);
        $this->array['phone']    = Random::generate(10, '0-9');
        $this->array['email']    = $this->array['username'] . '@' . $this->array['username'] . '.com';
    }

    public function seedClient()
    {
        $this->prepare();
        $this->array['roles'] = [$this->client_role_id, array_rand(\array_flip([$this->customer_role_id, $this->executor_role_id]), 1)];

        return (object) $this->array;
    }

    public function saveClient($user)
    {
        $this->cf->add($user);
    }
}
