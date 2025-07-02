<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Ijurij\Geolocation\Lib\Csrf;
use Ijurij\Geolocation\Lib\Session;
use Nette\Utils\Random;

trait YandexLogin
{
    public function yandexLoginUrl(): string
    {
        $params = [
            'client_id' => YALOGIN_ID,
            'redirect_uri' => "https://" . SITE_NAME . $this->link(':Home:Sign:yandexLogin'),
            'response_type' => 'code',
            'state' => Csrf::getToken()
        ];

        $url = 'https://oauth.yandex.ru/authorize?' . urldecode(http_build_query($params));

        return $url;
    }

    public function getUserDataYandex(): array
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
                    'client_id' => YALOGIN_ID,
                    'client_secret' => YA_CLIENT_SECRET
                ];

                $ch = curl_init('https://oauth.yandex.ru/token');

                if ($ch === false) {
                    $url = 'https://oauth.yandex.ru/token';

                    // use key 'http' even if you send the request to https://...
                    $options = [
                        'http' => [
                            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                            'method' => 'POST',
                            'content' => http_build_query($params),
                        ],
                    ];

                    $context = stream_context_create($options);
                    $result = file_get_contents($url, false, $context);
                    if ($result === false) {
                        //$errors[] = 'Access token not received by fgc';
                        $errors[] = 'Ошибка! Зарегистрируйтесь с помощью формы на сайте';
                    } else {
                        $d = json_decode($result, true);
                        if (!empty($d['access_token'])) {
                            $url2 = 'https://oauth.yandex.ru/token';
                            $params2 = ['format' => 'json'];
                            $options2 = [
                                'http' => [
                                    'header' => 'Authorization: OAuth ' . $d['access_token'],
                                    'method' => 'POST',
                                    'content' => http_build_query($params2),
                                ],
                            ];
                            $context2 = stream_context_create($options2);
                            $result2 = file_get_contents($url2, false, $context2);
                            $data = json_decode($result2, true);
                        } else {
                            //$errors[] = 'Access token is empty';
                            $errors[] = 'Ошибка! Попробуйте позже или зарегистрируйтесь с помощью формы на сайте';
                        }
                    }
                } else {
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    $d = curl_exec($ch);
                    curl_close($ch);

                    if ($d !== false) {
                        $d = json_decode($d, true);
                    } else {
                        $errors[] = 'Ошибка! Попробуйте позже';
                    }

                    if (!empty($d['access_token'])) {
                        // Токен получили, получаем данные пользователя.
                        $ch = curl_init('https://login.yandex.ru/info');
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, ['format' => 'json']);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: OAuth ' . $d['access_token']]);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        // curl_setopt($ch, CURLOPT_HEADER, false);
                        $info = curl_exec($ch);
                        curl_close($ch);

                        $data = json_decode($info, true);
                    } else {
                        /*
                        $er = (!empty($d['error'])) ? $d['error'] : '';
                        $erd = (!empty($d['error_description'])) ? $d['error_description'] : '';
                        $mes = $er . $erd;
                        $errors[] = 'Access token not received by curl' . $mes;
                        */
                        $errors[] = 'Ошибка! Попробуйте позже или зарегистрируйтесь с помощью формы на сайте';
                    }
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
                // 'username' => $data['login'] . '_id_' . $data['id'],
                'email' => $data['default_email'] ?? '',
                'phone' => $data['default_phone']['number'] ?? '',
            ],
        ];
    }
}
