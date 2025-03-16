<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Offers\Services;

final class ServicesPresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Services', '')) {
            $this->error('Forbidden', 403);
        }
    }
}
