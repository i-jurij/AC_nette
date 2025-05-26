<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Grievance;

use App\Model\GrievanceFacade;

final class GrievancePresenter extends \App\UI\Admin\BasePresenter
{
    public function __construct(
        protected GrievanceFacade $gf,
    ) {
        parent::__construct();
    }
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Grievance', '')) {
            $this->error('Forbidden', 403);
        }
        $this->template->grievance_not_resolve = $this->gf->getNotResolve();
    }

    public function renderByClient(int $id)
    {
        if (!$this->getUser()->isAllowed('Grievance', '')) {
            $this->error('Forbidden', 403);
        }
    }
}
