<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Offers\Works;

final class WorksPresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Works', '')) {
            $this->error('Forbidden', 403);
        }
    }
}
