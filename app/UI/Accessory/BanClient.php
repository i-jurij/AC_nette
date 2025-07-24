<?php
namespace App\UI\Accessory;

trait BanClient
{
    public function isBanned(): bool
    {
        $roles = $this->getUser()->getRoles();
        if (in_array('banned', $roles, true)) {
            return true;
        }

        return false;
    }
}
