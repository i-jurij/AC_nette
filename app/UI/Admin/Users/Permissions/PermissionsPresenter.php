<?php

declare(strict_types=1);

namespace App\UI\Admin\Users\Permissions;

use App\Model\PermissionFacade;
use Nette\Application\UI\Form;

/**
 * @property PermissionsTemplate $template
 */
final class PermissionsPresenter extends \App\UI\Admin\BasePresenter
{
    public string $backlink;
    protected $user_data;

    public function __construct(
        private PermissionFacade $pf)
    {
        parent::__construct();
    }

    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isAllowed('Permission')) {
            $this->error('Forbidden', 403);
        }
    }

    public function actionAddAuto()
    {
        $actions = $this->pf->actionListFromModelDir();
        // $actions = $this->pf->presenterList() ?? null;
        $existed_permissions = $this->pf->list();
        if (is_array($actions) && !empty($actions)) {
            foreach ($actions as $resource => $values) {
                foreach ($values as $key => $action) {
                    if (isset($existed_permissions[$resource]) && \in_array($action, $existed_permissions[$resource])) {
                        unset($actions[$resource][$key]);
                    }
                }
            }
        }
        unset($actions['Permission'], $actions['Role']);

        $this->template->actions = $actions;
    }

    public function createComponentFormPermissionsAddAuto(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'formPermissionsAddAuto')
           ->setHtmlAttribute('class', 'form');

        $form->addSubmit('addPermissionsAuto', 'Add permissions');

        $form->onSuccess[] = [$this, 'add'];

        return $form;
    }

    public function createComponentFormPermissionsAddManual(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'formPermissionsAddManual')
           ->setHtmlAttribute('class', 'form');

        $form->addText('resource', 'Resource:')
            ->setHtmlAttribute('placeholder', 'Resource with a capital letter:')
            ->addRule($form::MinLength, 'Length > %d', 2)
            ->addRule($form::Pattern, 'Only 2 < letters < 25', '^[A-Z][a-zA-Z]{2,25}$')
            ->setMaxLength(25)
            ->setRequired();
        $form->addText('actionn', 'Action:')
            ->setHtmlAttribute('placeholder', 'Action in lowercase:')
            ->addRule($form::MinLength, 'Length > %d', 2)
            ->addRule($form::Pattern, 'Only 2 < lowercase letters < 25', '^[a-z]{2,25}$')
            ->setMaxLength(25);
        // ->setRequired();

        $form->addSubmit('addPermissionsManual', 'Add permissions');

        $form->onSuccess[] = [$this, 'add'];

        return $form;
    }

    #[Requires(methods: 'POST')]
    public function add(Form $form): void
    {
        $data['resource'] = $form->getHttpData($form::DataText, 'resource');
        // $data['action'] = $form->getHttpData($form::DataText, 'action[]');
        $action_array = $form->getHttpData($form::DataText, 'action[]') ?? null;
        $action = $form->getHttpData($form::DataText, 'actionn') ?? null;
        if (!empty($action_array)) {
            $data['action'] = $action_array;
        // } elseif (!empty($action)) {
        } else {
            $data['action'] = $action;
        }
        try {
            // if (!empty($data['resource']) && !empty($data['action'])) {
            if (!empty($data['resource'])) {
                $this->pf->add($data);

                $this->flashMessage(json_encode($data).'Permission added', 'text-success');
            } else {
                $this->flashMessage(json_encode($data).'An empty form was received');
                $this->redirect(':Admin:Users:Permissions:add');
            }
        } catch (\Throwable $e) {
            $this->flashMessage('Caught Exception!'.PHP_EOL
                .'Error message: '.$e->getMessage().PHP_EOL
                .'File: '.$e->getFile().PHP_EOL
                .'Line: '.$e->getLine().PHP_EOL
                .'Error code: '.$e->getCode().PHP_EOL
                .'Trace: '.$e->getTraceAsString().PHP_EOL, 'text-danger');
        }

        $this->redirect(':Admin:');
    }

    public function actionDelete()
    {
        $existed_permissions = [];
        foreach ($this->pf->table as $row) {
            $existed_permissions[$row->resource][] = [
                'id' => $row->id,
                'action' => $row->action,
            ];
        }

        $this->template->existed_permissions = (!empty($existed_permissions)) ? $existed_permissions : false;
    }

    public function createComponentFormPermissionsDelete(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'formPermissionsDelete')
           ->setHtmlAttribute('class', 'form');

        $form->addSubmit('deletePermissions', 'Delete permissions');

        $form->onSuccess[] = [$this, 'delete'];

        return $form;
    }

    public function delete(Form $form): void
    {
        $data = $form->getHttpData($form::DataText, 'permission[]');
        try {
            $this->pf->delete($data);
            $this->flashMessage('Permissions was deleted', 'text-success');
        } catch (\Throwable $e) {
            $this->flashMessage('Caught Exception!'.PHP_EOL
                    .'Error message: '.$e->getMessage().PHP_EOL
                    .'File: '.$e->getFile().PHP_EOL
                    .'Line: '.$e->getLine().PHP_EOL
                    .'Error code: '.$e->getCode().PHP_EOL
                    .'Trace: '.$e->getTraceAsString().PHP_EOL, 'text-danger');
        }

        $this->redirect(':Admin:');
    }
}
