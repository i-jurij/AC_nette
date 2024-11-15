<?php

declare(strict_types=1);

namespace App\UI\Admin\PageCreator\Update;

final class UpdatePresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('PageCreator:update')) {
            $this->error('Forbidden', 403);
        }
    }
}
