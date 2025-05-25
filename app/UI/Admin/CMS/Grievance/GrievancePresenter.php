<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Grievance;

final class GrievancePresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Grievance', '')) {
            $this->error('Forbidden', 403);
        }
    }
}
