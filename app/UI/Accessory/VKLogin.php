<?php

namespace App\UI\Accessory;

use Ijurij\Geolocation\Lib\Csrf;
use Ijurij\Geolocation\Lib\Session;
use Nette\Utils\Random;

trait VKLogin
{
    public function VKLoginUrl(): string
    {
        /*
        $code_verifier = Random::generate(48, '0-9a-z');
        $code_challenge = base64_encode(hash('sha256', $code_verifier, true));
        */

        if (!function_exists('get_code_challenge')) {
            function get_code_challenge($code_verifier)
            {
                return str_replace(['%2B', '%2F'], ['-', '_'], urlencode(trim(base64_encode(hash('sha256', $code_verifier, true)), '=')));
            }
        }
        $code_verifier = bin2hex(random_bytes(32));
        $code_challenge = get_code_challenge($code_verifier);

        /*
                $code_verifier = '-O4wWM-HViMhB6RhcHe_0zocYSEqTYZSP6kFidNYMXg';
                $code_challenge = 'K_Qbg5YV-EH3VVisQeG6H6TvNZn7JB_4i8HY2V_hRrU';
        */
        Session::set('code_verifier', $code_verifier);

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

        if (Session::has(Csrf::$token_name) === false) {
            $errors[] = 'В сессии нет токена. Закройте страницу, откройте снова и попробуйте еще раз';
            return ['error' => $errors, 'data' => $data];
        }

        // check csrf and then execute code
        if (!hash_equals(Session::get(Csrf::$token_name), $_GET['state'])) {
            //$errors[] = 'State is invalid (csrf not valid)';
            $errors[] = 'Токены отличаются. Закройте страницу, откройте снова и попробуйте еще раз';
            return ['error' => $errors, 'data' => $data];
        }

        if (!empty($_GET['code'])) {
            $url = 'https://id.vk.ru/oauth2/auth';
            // Отправляем код для получения токена (POST-запрос).
            $params = [
                'grant_type' => 'authorization_code',
                'code' => $_GET['code'],
                'client_id' => VKLOGIN_ID,
                'device_id' => $_GET['device_id'],
                'state' => Csrf::getToken(),
                'code_verifier' => Session::get('code_verifier'),
                'redirect_uri' => "https://" . SITE_NAME . $this->link(':Home:Sign:vklogin'),
            ];

            $fields_params = http_build_query($params);

            $ch = curl_init($url);
            if ($ch === false) {
                $d = file_get_contents($url, false, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header' => 'Content-type: application/x-www-form-urlencoded',
                        'content' => $fields_params
                    ]
                ]));
            } else {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_params);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FAILONERROR, true);
                $d = curl_exec(handle: $ch);
                curl_close($ch);
            }
        } else {
            $errors[] = 'Ошибка. Не получен код подтверждения. Попробуйте позже';
            return ['error' => $errors, 'data' => $data];
        }

        if (isset($d) && $d !== false) {
            $res = json_decode($d, true);
        } else {
            $errors[] = 'Ошибка! Возможно сервер авторизации недоступен, попробуйте позже';
            return ['error' => $errors, 'data' => $data];
        }

        if (!empty($res['state']) && hash_equals(Session::get(Csrf::$token_name), $res['state']) && !empty($res['access_token'])) {
            // Токен получили, получаем данные пользователя.
            $params2 = [
                'client_id' => VKLOGIN_ID,
                'access_token' => $res['access_token'],
            ];
            $fields_params2 = http_build_query($params2);
            $ch = curl_init('https://id.vk.ru/oauth2/user_info');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_params2);
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
            if (!empty($data['error'])) {
                $errors[] = 'Error! ' . $data['error'] . ': ' . $data['error_description'];
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
        } else {
            //$errors[] = 'Access token not received or state is invalid';
            //$errors[] = 'Ошибка! Попробуйте позже или зарегистрируйтесь с помощью формы на сайте';
            $er = (!empty($res['error'])) ? $res['error'] : '';
            $erd = (!empty($res['error_description'])) ? $res['error_description'] : '';
            $mes = "$er:  $erd";
            $errors[] = "Ошибка при запросе токена.  $mes";
            return ['error' => $errors, 'data' => $data];
        }
    }
}