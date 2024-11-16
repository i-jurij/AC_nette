<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Customers;

final class CustomersPresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Customers', '')) {
            $this->error('Forbidden', 403);
        }
    }
}
