<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Orders;

final class OrdersPresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Orders', '')) {
            $this->error('Forbidden', 403);
        }
    }
}
