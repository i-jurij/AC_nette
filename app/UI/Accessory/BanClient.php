<?php

declare(strict_types=1);

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
