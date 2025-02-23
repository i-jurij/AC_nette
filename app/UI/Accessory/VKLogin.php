<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Ijurij\Geolocation\Lib\Csrf;
use Ijurij\Geolocation\Lib\Session;

trait VKLogin
{
    public function VKLoginUrl(): string
    {
        $code_verifier = Csrf::token(256);
        $code_challenge = rtrim(strtr(base64_encode(hash('sha256', $code_verifier, true)), '+/', '-_'), '=');
        Session::set('code_verifier', $code_verifier);

        $params = [
            'client_id' => VKLOGIN_ID,
            'redirect_uri' => $this->link(':Home:Sign:vkLogin'),
            'response_type' => 'code',
            'scope' => 'email phone vkid.personal_info',
            'scheme' => 'dark',
            'state' => Csrf::getToken(),
            'code_challenge' => $code_challenge,
            'code_challenge_method' => 'S256',
        ];

        $url = 'https://id.vk.com/authorize?'.urldecode(http_build_query($params));

        return $url;
    }

    public function getUserDataVK(): array
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
                    'client_id' => VKLOGIN_ID,
                    'device_id' => $_GET['device_id'],
                    'state' => Csrf::getToken(),
                    'code_verifier' => Session::get('code_verifier'),
                ];

                $ch = curl_init('https://id.vk.com/oauth2/auth');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $d = curl_exec($ch);
                curl_close($ch);

                $d = json_decode($d, true);
                if (hash_equals(Session::get('token'), $d['state']) && !empty($d['access_token'])) {
                    // Токен получили, получаем данные пользователя.
                    $params = [
                        'client_id' => VKLOGIN_ID,
                        'access_token' => $d['access_token'],
                    ];
                    $ch = curl_init('https://id.vk.com/oauth2/user_info');
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    // curl_setopt($ch, CURLOPT_HEADER, false);
                    $info = curl_exec($ch);
                    curl_close($ch);

                    $data = json_decode($info, true);
                } else {
                    $errors[] = 'Access token not received or state is invalid (csrf not valid)';
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
                'username' => $data['user']['first_name'].'_id_'.$data['user_id'],
                'email' => $data['user']['email'] ?? '',
                'phone' => $data['user']['phone'] ?? '',
            ],
        ];
    }
}
