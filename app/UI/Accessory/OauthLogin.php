<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Nette\Utils\Random;

trait OauthLogin
{
    public function oauthLogin(array $user_data): void
    {
        if (!empty($user_data['error'])) {
            foreach ($user_data['error'] as $error) {
                $this->flashMessage($error, 'text-danger');
            }
            $this->redirect(':Home:');
        }

        if (!empty($user_data['data'])) {
            // check if user not isset in db
            $check_array = [];
            foreach ($user_data['data'] as $key => $value) {
                if (!empty($value)) {
                    $check_array[$key] = $value;
                }
            }

            $user_isset = $this->userfacade->db->table($this->userfacade->table)->where($check_array)->fetch();

            $data = (object) $user_data['data'];
            if (empty($user_isset)) {
                // then add user to db: $res = $this->userfacade->add($data);
                $data->roles = 'client';
                $data->password = Random::generate(10);
                $res = $this->userfacade->add($data);
            } else {
                $data->password = $user_isset['password'];
            }
            // login
            $user = $this->getUser();
            if (!empty($data->username)) {
                $user->login($data->username, $data->password);
            }
            if (!empty($data->phone)) {
                $user->login(PhoneNumber::toDb($data->phone), $data->password);
            }

            $this->restoreRequest($this->backlink);

            $this->redirect(':Home:');
        }
    }
}
