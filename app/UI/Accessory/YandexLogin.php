<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Ijurij\Geolocation\Lib\Csrf;
use Ijurij\Geolocation\Lib\Session;

trait YandexLogin
{
    public function yandexLoginUrl(): string
    {
        $code_verifier = Csrf::token(256);
        $code_challenge = rtrim(strtr(base64_encode(hash('sha256', $code_verifier, true)), '+/', '-_'), '=');
        Session::set('code_verifier', $code_verifier);

        $params = [
            'client_id' => YALOGIN_ID,
            'redirect_uri' => $this->link(':Home:Sign:yandexLogin'),
            'response_type' => 'code',
            'state' => Csrf::getToken(),
            'code_challenge' => $code_challenge,
            'code_challenge_method' => 'S256',
        ];

        $url = 'https://oauth.yandex.ru/authorize?'.urldecode(http_build_query($params));

        return $url;
    }

    public function getUserDataYandex(): array
    {
        $errors = [];
        $data = [];

        // check csrf and then execute code
        if (hash_equals(Session::get('token'), $_GET['state'])) {
            if (!empty($_GET['code'])) {
                // Отправляем код для получения токена (POST-запрос).
                $params = [
                    'grant_type' => 'authorization_code',
                    'code' => $_GET['code'],
                    'client_id' => YALOGIN_ID,
                    'client_secret' => YA_CLIENT_SECRET,
                    'code_verifier' => Session::get('code_verifier'),
                ];

                $ch = curl_init('https://oauth.yandex.ru/token');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $d = curl_exec($ch);
                curl_close($ch);

                $d = json_decode($d, true);
                if (!empty($d['access_token'])) {
                    // Токен получили, получаем данные пользователя.
                    $ch = curl_init('https://login.yandex.ru/info');
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, ['format' => 'json']);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: OAuth '.$d['access_token']]);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    // curl_setopt($ch, CURLOPT_HEADER, false);
                    $info = curl_exec($ch);
                    curl_close($ch);

                    $data = json_decode($info, true);
                } else {
                    $errors[] = 'Access token not received';
                }
            } else {
                $errors[] = 'Authorization code not received';
            }
        } else {
            $errors[] = 'State is invalid (csrf not valid)';
        }

        return [
            'error' => $errors,
            'data' => [
                'username' => $data['login'].'_id_'.$data['id'],
                'email' => $data['default_email'] ?? '',
                'phone' => $data['default_phone']['number'] ?? '',
            ],
        ];
    }
}
