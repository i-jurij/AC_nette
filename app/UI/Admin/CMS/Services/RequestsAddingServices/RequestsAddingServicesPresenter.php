<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Services\RequestsAddingServices;

use Nette\Database\Explorer;

final class RequestsAddingServicesPresenter extends \App\UI\Admin\BasePresenter
{
    public function __construct(
        protected Explorer $db
    ) {
        parent::__construct();
    }

    public function renderDefault()
    {
        $this->template->services = $this->db->table('requestforaddingservice')->fetchAll();
    }

    public function handleAddServ($cat, $serv, $rec)
    {
        $res = $this->db->table('service')->insert([
            'category_id' => (int) $cat,
            'name' => htmlspecialchars(strip_tags($serv)),
        ]);
        if (!empty($res->id)) {
            $this->db->table('requestforaddingservice')->where('id', (int) $rec)->delete();
            $this->flashMessage('Service was added', 'success');
        }
        $this->redirect('default');
    }
}
