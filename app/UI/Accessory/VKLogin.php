<?php

namespace App\UI\Accessory;

use Ijurij\Geolocation\Lib\Csrf;
use Ijurij\Geolocation\Lib\Session;
use Nette\Utils\Random;

trait VKLogin
{
    public function VKLoginUrl(): string
    {
        $code_verifier = Random::generate(48, '0-9a-z');
        $code_challenge = base64_encode(hash('sha256', $code_verifier, true));
        if (!Session::has('code_verifier')) {
            Session::set('code_verifier', $code_verifier);
        }

        $params = [
            'client_id' => VKLOGIN_ID,
            'redirect_uri' => "https://" . SITE_NAME . $this->link(':Home:Sign:vklogin'),
            'response_type' => 'code',
            'scope' => 'email phone vkid.personal_info',
            'scheme' => 'dark',
            'state' => Csrf::getToken(),
            'code_challenge' => $code_challenge,
            'code_challenge_method' => 'S256',
        ];

        $url = 'https://id.vk.ru/authorize?' . urldecode(http_build_query($params));

        return $url;
    }

    public function getUserDataVK(): array
    {
        $errors = [];
        $data = [];

        // check csrf and then execute code
        if (hash_equals(Session::get(Csrf::$token_name), $_GET['state'])) {
            if (!empty($_GET['code'])) {
                // Отправляем код для получения токена (POST-запрос).
                $params = [
                    'grant_type' => 'authorization_code',
                    'code' => $_GET['code'],
                    'client_id' => VKLOGIN_ID,
                    'device_id' => $_GET['device_id'],
                    'state' => Csrf::getToken(),
                    'code_verifier' => Session::get('code_verifier'),
                ];

                $ch = curl_init('https://id.vk.ru/oauth2/auth');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                //curl_setopt($ch, CURLOPT_HEADER, false);
                $d = curl_exec($ch);
                curl_close($ch);

                if ($d !== false) {
                    $d = json_decode($d, true);
                } else {
                    $errors[] = 'Ошибка! Попробуйте позже';
                }

                if (!empty($d['state']) && hash_equals(Session::get(Csrf::$token_name), $d['state']) && !empty($d['access_token'])) {
                    // Токен получили, получаем данные пользователя.
                    $params = [
                        'client_id' => VKLOGIN_ID,
                        'access_token' => $d['access_token'],
                    ];
                    $ch = curl_init('https://id.vk.ru/oauth2/user_info');
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    // curl_setopt($ch, CURLOPT_HEADER, false);
                    $info = curl_exec($ch);
                    curl_close($ch);

                    $data = json_decode($info, true);
                    /*
                    {
                        "user": {
                            "user_id": "<идентификатор пользователя>",
                            "first_name": "<имя пользователя>",
                            "last_name": "<фамилия пользователя>",
                            "phone": "<телефон пользователя>",
                            "avatar": "<ссылка на фото профиля>",
                            "email": "<почта пользователя>",
                            "sex": <пол>,
                            "verified": <статус верификации пользователя>,
                            "birthday": "<дата рождения>"
                        }
                    }
                    */

                    $f = $data['user']['first_name'] ?? '';
                    $l = $data['user']['last_name'] ?? '';
                    $username = $f . ' ' . $l;
                    $email = $data['user']['email'] ?? '';
                    $phone = $data['user']['phone'] ?? '';

                    /*
                    {
                    "error": "<ошибка с одним из значений ниже>",
                    "error_description": "<описание ошибки. Передается в виде строки>",
                    "state": "<строка, которая передана в изначальном запросе>"
                    }
                    */
                    if (!empty($data['user']['error'])) {
                        $errors[] = $data['user']['error'] . '. ' . $data['user']['error_description'];
                    }
                } else {
                    //$errors[] = 'Access token not received or state is invalid';
                    $errors[] = 'Ошибка! Попробуйте позже или зарегистрируйтесь с помощью формы на сайте';
                }
            } else {
                //$errors[] = 'Authorization code not received';
                $errors[] = 'Ошибка! Попробуйте позже';
            }
        } else {
            //$errors[] = 'State is invalid (csrf not valid)';
            $errors[] = 'Ошибка! Закройте страницу, откройте снова и попробуйте еще раз';
        }

        return [
            'error' => $errors,
            'data' => [
                "user_id" => $data['user']['user_id'] ?? '',
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
            ],
        ];
    }
}