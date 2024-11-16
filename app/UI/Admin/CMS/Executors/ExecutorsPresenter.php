<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Executors;

final class ExecutorsPresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Executors', '')) {
            $this->error('Forbidden', 403);
        }
    }
}
