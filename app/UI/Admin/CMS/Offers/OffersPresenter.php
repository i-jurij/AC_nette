<?php

declare (strict_types = 1);

namespace App\UI\Admin\CMS\Offers;

final class OffersPresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (! $this->getUser()->isAllowed('Orders', '')) {
            $this->error('Forbidden', 403);
        }
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function renderByClient(int $id): void
    {

    }
}
