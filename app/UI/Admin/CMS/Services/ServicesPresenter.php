<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Services;

use App\Model\ServiceFacade;
use Nette\Application\UI\Form;

final class ServicesPresenter extends \App\UI\Admin\BasePresenter
{
    public function __construct(
        protected ServiceFacade $sf
    ) {
        parent::__construct();
    }

    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Services')) {
            $this->error('Forbidden', 403);
        }

        $this->template->services = $this->sf->getAllServices();
    }

    #[Requires(sameOrigin: true)]
    public function handleCatDelete($id)
    {
        if ($this->sf->deleteCategory((int) $id) > 0) {
            $this->flashMessage('Category was deleted', 'success');
        }
        $this->redirect('this');
    }

    #[Requires(sameOrigin: true)]
    public function handleServDelete($id)
    {
        if ($this->sf->deleteService((int) $id) > 0) {
            $this->flashMessage('Service was deleted', 'success');
        }
        $this->redirect('this');
    }

    public function createComponentEditForm($id)
    {
        $form = new Form();
        $form->addProtection('Reload the page');
        $form->addText('name', 'New name:')
            ->setRequired();
        $form->addHidden('id');

        $form->addSubmit('cat_serv_edit_submit', 'Save');

        return $form;
    }

    #[Requires(sameOrigin: true)]
    public function actionEdit(string $type, string $id)
    {
        $form = $this->getComponent('editForm');
        if ($this->getParameter('type') === 's') {
            $d = $this->sf->getServ((int) $id);
            $form->setDefaults($d);
            $form->onSuccess[] = [$this, 'servEditProc'];
        } elseif ($this->getParameter('type') === 'c') {
            $d = $this->sf->getCategory((int) $id);
            $form->setDefaults($d);
            $form->onSuccess[] = [$this, 'catEditProc'];
        } else {
            $this->error();
        }
    }

    public function catEditProc(Form $form, array $data)
    {
        if ($this->sf->upCategory((int) $data['id'], ['name' => $data['name']]) > 0) {
            $this->flashMessage('Category was updated', 'success');
        }
        $this->redirect('default');
    }

    public function servEditProc(Form $form, array $data)
    {
        if ($this->sf->upService((int) $data['id'], ['name' => $data['name']]) > 0) {
            $this->flashMessage('Service was updated', 'success');
        }
        $this->redirect('default');
    }

    public function createComponentCatServAddForm()
    {
        $form = new Form();
        $form->addProtection('Reload the page');
        $form->addText('category_name', 'Name of category:')
            ->setRequired();

        $form->addSubmit('cat_serv_add', 'Save');
        $form->onSuccess[] = [$this, 'catServAddProc'];
        return $form;
    }

    public function catServAddProc(Form $form, array $data)
    {
        $category_name = $data['category_name'];
        $resc = $this->sf->addCategory($category_name);
        if ($resc !== 0) {
            $serv_name_array = $form->getHttpData($form::DataText, 'service_name[]');
            foreach ($serv_name_array as $key => $service_name) {
                $serv_name_array[$key] = [
                    'category_id' => $resc,
                    'name' => $service_name
                ];
            }
            $ress = $this->sf->addServices($serv_name_array);
        }
        $this->redirect('default');
    }

    public function createComponentServiceAddForm()
    {
        $form = new Form();
        $form->addProtection('Reload the page');
        $form->addText('name', 'Name of service:')
            ->setRequired();
        $form->addHidden('category_id', null);
        $form->addSubmit('serv_to_cat_add', 'Save');

        return $form;

    }
    #[Requires(sameOrigin: true)]
    public function actionServiceAdd($category_id)
    {
        $form = $this->getComponent('serviceAddForm');
        $cid = $form->getComponent('category_id');
        $cid->setDefaultValue((int) $category_id);
        $form->onSuccess[] = [$this, 'serviceAddProc'];
    }

    public function serviceAddProc(Form $form, array $data)
    {
        if ($this->sf->addService($data) > 0) {
            $this->flashMessage('Service was added', 'success');
        }
        $this->redirect('default');
    }
}
