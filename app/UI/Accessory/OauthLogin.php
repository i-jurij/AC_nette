<?php

namespace App\UI\Accessory;

use Nette\Utils\Random;
use Nette\Security\SimpleIdentity;
use Nette\Utils\Strings;

trait OauthLogin
{
    public function oauthLogin(array $user_data): void
    {
        if (!empty($user_data['error'])) {
            foreach ($user_data['error'] as $error) {
                $this->flashMessage($error, 'error');
            }
        }

        if (!empty($user_data['data'])) {
            /*
                        $this->flashMessage(json_encode($user_data));
                        return;
            */
            // check if user not isset in db
            $username = $user_data['data']['username'] ?? '';
            $phone = !empty($user_data['data']['phone']) ? PhoneNumber::toDb($user_data['data']['phone']) : '';
            $email = !empty($user_data['data']['email']) ? $user_data['data']['email'] : '';

            $condition = ['username' => $username, 'phone' => $phone, 'email' => $email];
            foreach ($condition as $key => $value) {
                if (empty($value)) {
                    unset($condition[$key]);
                }
            }

            if (!empty($condition['username'])) {
                $un = $condition['username'];
            } elseif (!empty($condition['phone'])) {
                $un = $condition['phone'];
            } elseif (!empty($condition['email'])) {
                // здесь нужно еще почистить первую часть имейла, чтобы оставить только буквы и цифры
                $un = Strings::before($condition['email'], '@', 1);
            }

            if (!empty($condition)) {
                // $user_isset = $this->cf->db->table($this->cf->table)->where($condition)->fetch();
                $user_isset = $this->cf->db->table($this->cf->table)->where('username', $un)->fetch();
                $data = (object) $condition;
                $user = $this->getUser();
                if (empty($user_isset->id)) {
                    // then add user to db: $res = $this->cf->add($data);user_data['data']
                    $data->roles = 'client';
                    $data->password = Random::generate(10);
                    $res = $this->cf->add($data);
                    if ($res === 'ok') {
                        $this->flashMessage("Вы можете входить на сайт используя имя $un и пароль $data->password или дальше использовать вход через сторонние сервисы.", 'success');

                        $user->login($un, $data->password);
                    } else {
                        $this->flashMessage("Ошибка. $res", 'error');
                    }
                } else {
                    $arr = $user_isset->toArray();
                    unset($arr[$this->cf::ColumnPasswordHash]);
                    $roles = $this->cf->getRoless($user_isset[$this->cf::ColumnId]);
                    $identity = new SimpleIdentity($user_isset[$this->cf::ColumnId], $roles, $arr);
                    $this->flashMessage('Вы вошли c использованием сторонних служб', 'info');
                    $user->login($identity);
                }
            } else {
                $this->flashMessage('Не получены данные пользователя', 'error');
            }
        } else {
            $this->flashMessage('Не получены данные пользователя', 'error');
        }
    }
}