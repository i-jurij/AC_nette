<?php

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

        if (Session::has(Csrf::$token_name) === false) {
            $errors[] = 'В сессии нет токена. Закройте страницу, откройте снова и попробуйте еще раз';
            return ['error' => $errors, 'data' => []];
        }

        // check csrf and then execute code
        if (!hash_equals(Session::get(Csrf::$token_name), $_GET['state'])) {
            //$errors[] = 'State is invalid (csrf not valid)';
            $errors[] = 'Токены отличаются. Закройте страницу, откройте снова и попробуйте еще раз';
            return ['error' => $errors, 'data' => []];
        }

        if (!empty($_GET['code'])) {
            $res = $this->reqFirst();
        } else {
            //$errors[] = 'Authorization code not received';
            $errors[] = 'Не получен авторизационный код. Попробуйте еще раз';
            return ['error' => $errors, 'data' => []];
        }

        if (isset($res) && $res !== false) {
            $d = json_decode($res, true);
        } else {
            $errors[] = 'Ошибка при запросе токена. Попробуйте еще раз';
            return ['error' => $errors, 'data' => []];
        }

        if (isset($d) && !empty($d['access_token'])) {
            $info = $this->reqTwo($d);
        } else {
            $er = (!empty($d['error'])) ? $d['error'] : '';
            $erd = (!empty($d['error_description'])) ? $d['error_description'] : '';
            $mes = "$er. $erd";
            $errors[] = "Ошибка при запросе токена.  $mes";
            // $errors[] = 'Ошибка! Попробуйте позже или зарегистрируйтесь с помощью формы на сайте';
            return ['error' => $errors, 'data' => []];
        }

        if (isset($info) && $info !== false) {
            $data = json_decode($info, true);
            $id = $data['id'] ?? '';
            $username = $data['login'] ?? '';
            $psuid = $data['psuid'] ?? '';
            $phone = $data['default_phone']['number'] ?? '';
            $email = $data['default_email'] ?? '';
        } else {
            $errors[] = 'Ошибка! Попробуйте позже';
            return ['error' => $errors, 'data' => []];
        }

        if (empty($username) && empty($phone) && empty($email)) {
            $errors[] = 'Ошибка! Не получены данные пользователя';
            return ['error' => $errors, 'data' => []];
        } else {
            return [
                'error' => $errors,
                'data' => [
                    'id' => $id,
                    'username' => $username,
                    'psuid' => $psuid,
                    'email' => $email,
                    'phone' => $phone,
                ],
            ];
        }
    }

    protected function reqFirst(): bool|string
    {
        $url = 'https://oauth.yandex.ru/token';
        // Отправляем код для получения токена (POST-запрос).
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $_GET['code'],
            'client_id' => YALOGIN_ID,
            'client_secret' => YA_CLIENT_SECRET
        ];

        $ch = curl_init($url);
        $d = ($ch === false) ? $this->reqGfFirst($url, $params) : $this->reqCurlFirst($ch, $params);
        return $d;
    }

    protected function reqCurlFirst($ch, $params): bool|string
    {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $d = curl_exec($ch);
        curl_close($ch);
        return $d;
    }

    protected function reqGfFirst($url, $params): bool|string
    {
        // use key 'http' even if you send the request to https://...
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($params),
            ],
        ];
        $context = stream_context_create($options);
        return file_get_contents($url, false, $context);
    }

    protected function reqTwo($d): bool|string
    {
        $url2 = 'https://login.yandex.ru/info';
        $ch = curl_init($url2);
        $d = ($ch === false) ? $this->reqGfTwo($url2, $d) : $this->reqCurlTwo($ch, $d);
        return $d;
    }

    protected function reqCurlTwo($ch, $d): bool|string
    {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['format' => 'json']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: OAuth ' . $d['access_token']]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // curl_setopt($ch, CURLOPT_HEADER, false);
        $info = curl_exec($ch);
        curl_close($ch);
        return $info;
    }

    protected function reqGfTwo($url2, $d): bool|string
    {
        $options2 = [
            'http' => [
                'header' => 'Authorization: OAuth ' . $d['access_token'],
                'method' => 'POST',
                'content' => http_build_query(['format' => 'json']),
            ],
        ];
        $context2 = stream_context_create($options2);
        $info = file_get_contents($url2, false, $context2);
        return $info;
    }
}