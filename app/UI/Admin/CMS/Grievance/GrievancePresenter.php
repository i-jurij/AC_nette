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

    public function renderResolved()
    {
        if (!$this->getUser()->isAllowed('Grievance', '')) {
            $this->error('Forbidden', 403);
        }
        $this->template->grievance = $this->gf->getResolved();
    }

    public function handleMarkResolved(int $id)
    {
        if (!$this->getUser()->isAllowed('Grievance', 'markResolved')) {
            $this->error('Forbidden', 403);
        }
        if ($this->gf->markResolved($id)) {
            $this->flashMessage('Grievance mark as resolved', 'success');
        }
        $this->redirect('this');
    }

    public function handleRemoveOld()
    {
        if (!$this->getUser()->isAllowed('Grievance', 'removeOld')) {
            $this->error('Forbidden', 403);
        }
        if ($this->gf->removeOld()) {
            $this->flashMessage('Old grievance is deleted', 'success');
        }
        $this->redirect('this');
    }

    public function renderByClient(int $id, string $username)
    {
        if (!$this->getUser()->isAllowed('Grievance', '')) {
            $this->error('Forbidden', 403);
        }
        $this->template->grievance = $this->gf->getByClient($id);
        $this->template->client_name = htmlspecialchars(strip_tags($username));
    }
}
