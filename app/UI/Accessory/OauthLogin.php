<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Nette\Utils\Random;
use Nette\Security\SimpleIdentity;

trait OauthLogin
{
    public function oauthLogin(array $user_data): void
    {
        if (!empty($user_data['error'][0])) {
            foreach ($user_data['error'] as $error) {
                $this->flashMessage($error, 'error');
            }
        } else {
            if (!empty($user_data['data'])) {
                // check if user not isset in db
                $phone = !empty($user_data['data']['phone']) ? PhoneNumber::toDb($user_data['data']['phone']) : '';
                $email = !empty($user_data['data']['email']) ? $user_data['data']['email'] : '';

                $user_isset = $this->cf->db->table($this->cf->table)->where([
                    'phone' => $phone,
                    'email' => $email
                ])->fetch();

                $data = (object) $user_data['data'];

                $user = $this->getUser();

                if (empty($user_isset->id)) {
                    // then add user to db: $res = $this->cf->add($data);
                    $data->roles = 'client';
                    $data->password = Random::generate(10);
                    $res = $this->cf->add($data);

                    $this->flashMessage("Вы можете входить на сайт используя ваш номер телефона и пароль '$data->password' или дальше использовать вход через сторонние сервисы.", 'success');
                    if (!empty($data->username)) {
                        $user->login($data->username, $data->password);
                    }
                    if (!empty($data->phone)) {
                        $user->login(PhoneNumber::toDb($data->phone), $data->password);
                    }

                } else {
                    $arr = $user_isset->toArray();
                    unset($arr[$this->cf::ColumnPasswordHash]);
                    $roles = $this->cf->getRoless($user_isset[$this->cf::ColumnId]);
                    $identity = new SimpleIdentity($user_isset[$this->cf::ColumnId], $roles, $arr);
                    $user->login($identity);
                }
            } else {
                $this->flashMessage('Не получены данные пользователя', 'error');
            }
        }
    }
}
