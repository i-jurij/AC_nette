<?php

declare(strict_types=1);

namespace App\UI\Admin\PageCreator\Delete;

final class DeletePresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('PageCreator:delete')) {
            $this->error('Forbidden', 403);
        }
    }
}
