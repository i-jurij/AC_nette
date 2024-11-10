<?php

declare(strict_types=1);

namespace App\UI\Admin\PageCreator\Update;

use App\UI\Accessory\RequireLoggedUser;
use Nette\Security\User;

final class UpdatePresenter extends \App\UI\BasePresenter
{
    // Incorporates methods to check user login status
    use RequireLoggedUser;

    public function renderDefault()
    {
    }
}
