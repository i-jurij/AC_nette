<?php

declare(strict_types=1);

namespace App\UI\Accessory;

trait YandexLogin
{
    public function yandexLoginUrl(): string
    {
        $params = [
            'client_id' => 'ID_ПРИЛОЖЕНИЯ',
            'redirect_uri' => 'https://example.com/login_ya.php',
            'response_type' => 'code',
            'state' => '123',
            'code_challenge' => 'преобразованная верcия верификатора code_verifier',
            'code_challenge_method' => 'S256',
        ];

        $url = 'https://oauth.yandex.ru/authorize?'.urldecode(http_build_query($params));

        return $url;
    }

    public function yandexLogin()
    {
        $state = $_GET['state']; // 123

        if (!empty($_GET['code'])) {
            // Отправляем код для получения токена (POST-запрос).
            $params = [
                'grant_type' => 'authorization_code',
                'code' => $_GET['code'],
                'client_id' => 'ID_ПРИЛОЖЕНИЯ',
                'client_secret' => 'ПАРОЛЬ_ПРИЛОЖЕНИЯ',
            ];

            $ch = curl_init('https://oauth.yandex.ru/token');
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $data = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($data, true);
            if (!empty($data['access_token'])) {
                // Токен получили, получаем данные пользователя.
                $ch = curl_init('https://login.yandex.ru/info');
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, ['format' => 'json']);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: OAuth '.$data['access_token']]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HEADER, false);
                $info = curl_exec($ch);
                curl_close($ch);

                $info = json_decode($info, true);
                print_r($info);
            }
        }
    }
}
