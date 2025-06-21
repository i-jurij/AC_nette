<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Services\RequestsAddingServices;

use App\Model\ServiceFacade;

final class RequestsAddingServicesPresenter extends \App\UI\Admin\BasePresenter
{
    public function __construct(
        protected ServiceFacade $sf
    ) {
        parent::__construct();
    }

    public function renderDefault()
    {
        $this->template->services = $this->sf->db->table('requestforaddingservice')->fetchAll();
    }

    public function handleAddCatServ($cat, $serv, $rec)
    {
        $category_name = htmlspecialchars(strip_tags($cat));
        $cat_id = $this->sf->addCategory($category_name);
        if (!empty($cat_id)) {
            $data = ['category_id' => $cat_id, 'name' => htmlspecialchars(strip_tags($serv))];
            $res = $this->sf->addServices(data: $data);
            if (!empty($res)) {
                $this->deleteRequestForAddingService((int) $rec);
                $this->flashMessage('Category and service was added', 'success');
            }
        }
    }
    public function handleAddServ($cat, $serv, $rec)
    {
        $res = $this->sf->addServices([
            'category_id' => (int) $cat,
            'name' => htmlspecialchars(strip_tags($serv)),
        ]);
        if (!empty($res)) {
            $this->deleteRequestForAddingService((int) $rec);
            $this->flashMessage('Service was added', 'success');
        }
    }

    public function handleDelete($rec)
    {
        $this->deleteRequestForAddingService((int) $rec);
    }

    protected function deleteRequestForAddingService(int $req_id)
    {
        $this->sf->db->table('requestforaddingservice')->where('id', $req_id)->delete();
    }
}
