<?php

namespace App\UI\Accessory;

trait TelegramLogin
{
    public function getUserDataTelegram(): array
    {
        $errors = [];
        $d = [];

        function checkTelegramAuthorization($auth_data)
        {
            $check_hash = $auth_data['hash'];
            unset($auth_data['hash']);
            $data_check_arr = [];
            foreach ($auth_data as $key => $value) {
                $data_check_arr[] = $key . '=' . $value;
            }
            sort($data_check_arr);
            $data_check_string = implode("\n", $data_check_arr);
            $secret_key = hash('sha256', TELEGRAM_BOT_TOKEN, true);
            $hash = hash_hmac('sha256', $data_check_string, $secret_key);

            if (strcmp($hash, $check_hash) === 0 && ((time() - $auth_data['auth_date']) < 86400)) {
                return $auth_data;
            } else {
                return null;
            }
        }

        $data = checkTelegramAuthorization($_GET);
        if (!empty($data)) {
            $d = [
                'username' => $data['username'] . '_id_' . $data['id'],
                // 'email' => $data['default_email'] ?? '',
                // 'phone' => $data['default_phone']['number'] ?? '',
            ];
        }

        return [
            'error' => $errors,
            'data' => $d,
        ];
    }
}
