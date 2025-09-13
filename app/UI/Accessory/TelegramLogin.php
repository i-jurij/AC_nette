<?php

namespace App\UI\Accessory;

trait TelegramLogin
{
    /*
    data from bot throw data-auth-url:
    id, first_name, last_name, username, photo_url, auth_date and hash
*/
    public function getUserDataTelegram(): array
    {
        $errors = [];
        $d = [];

        $data = $this->checkTelegramAuthorization($_GET);
        if (!empty($data) && is_array($data)) {
            $d = [
                "id" => $data["id"] ?? "",
                "first_name" => htmlspecialchars($data["first_name"]) ?? "",
                "last_name" => htmlspecialchars($data["last_name"]) ?? "",
                "username" => htmlspecialchars($data["username"]) ?? "",
                "email" => htmlspecialchars($data["default_email"]) ?? "",
                "phone" => htmlspecialchars($data["default_phone"]["number"]) ?? "",
            ];
        } elseif (is_string($data)) {
            $errors[] = $data;
        }

        return [
            "error" => $errors,
            "data" => $d,
        ];
    }

    protected function checkTelegramAuthorization($get_arr): array|string
    {
        $check_hash = $get_arr["hash"];
        unset($get_arr["hash"]);
        $data_check_arr = [];
        foreach ($get_arr as $key => $value) {
            $data_check_arr[] = $key . "=" . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash("sha256", TELEGRAM_BOT_TOKEN, true);
        $hash = hash_hmac("sha256", $data_check_string, $secret_key);

        if (strcmp($hash, $check_hash) !== 0) {
            //return 'Data is NOT from Telegram';
            return 'Данные НЕ из Telegram';
        }
        if ((time() - $get_arr['auth_date']) > 86400) {
            // return 'Data is outdated';
            return 'Данные устарели. Попробуйте еще раз.';
        }

        return $get_arr;
    }
}
