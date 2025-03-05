<?php

declare(strict_types=1);

namespace App\UI\Admin\Users\Roles;

use App\Model\PermissionFacade;
use App\Model\RoleFacade;
use App\UI\Accessory\FormFactory;
use Nette\Application\UI\Form;

final class RolesPresenter extends \App\UI\Admin\BasePresenter
{
    public string $backlink;
    protected $user_data;

    public function __construct(
        private FormFactory $formFactory,
        private PermissionFacade $pf,
        private RoleFacade $rf
    ) {
        parent::__construct();
    }

    protected function startup()
    {
        parent::startup();
        if (!$this->getUser()->isAllowed('Role', 'add') || !$this->getUser()->isAllowed('Role', 'delete')) {
            $this->error('Forbidden', 403);
        }
    }

    public function createComponentFormRoleAdd(): Form
    {
        $form = $this->formFactory->create();
        $form->setHtmlAttribute('id', 'formRoleAdd')
           ->setHtmlAttribute('class', 'form');

        $form->addText('rolename', 'Role name:')
            ->setHtmlAttribute('placeholder', 'Role name:')
            ->setRequired()
            ->addRule($form::Pattern, 'Role name only letters in the number from 3 to 25', '^[a-zA-Z]{3,25}$')
            ->setMaxLength(25);

        $permissions = $this->pf->table->fetchAll();
        array_multisort(array_column($permissions, 'resource'), SORT_DESC, $permissions);
        $form->addCheckboxList('permissions', 'Permissions:', $permissions);

        $form->addSubmit('addRole', 'Add role');

        $form->onSuccess[] = [$this, 'add'];

        return $form;
    }

    #[Requires(methods: 'POST')]
    public function add(Form $form, array $data): void
    {
        if (!empty($data['rolename'])) {
            try {
                $role = $this->rf->add($data);
                $this->flashMessage('Role "'.$role.'" was added', 'text-success');
            } catch (\Throwable $e) {
                $this->flashMessage('Caught Exception!'.PHP_EOL
                    .'Error message: '.$e->getMessage().PHP_EOL
                    .'File: '.$e->getFile().PHP_EOL
                    .'Line: '.$e->getLine().PHP_EOL
                    .'Error code: '.$e->getCode().PHP_EOL
                    .'Trace: '.$e->getTraceAsString().PHP_EOL, 'text-danger');
            }
        } else {
            $this->flashMessage('An empty form was received');
            $this->redirect(':Admin:Users:Roles:add');
        }
        $this->redirect(':Admin:');
    }

    public function createComponentFormRoleDelete(): Form
    {
        $form = $this->formFactory->create();
        $form->setHtmlAttribute('id', 'formRoleDelete')
           ->setHtmlAttribute('class', 'form');

        $roles = $this->rf->role->fetchAll();
        $form->addCheckboxList('role', 'Roles:', $roles);

        $form->addSubmit('deleteRole', 'Delete');

        $form->onSuccess[] = [$this, 'delete'];

        return $form;
    }

    #[Requires(methods: 'POST')]
    public function delete(Form $form, array $data): void
    {
        if (!empty($data['role'])) {
            try {
                $message = '';
                $type = 'text-info';

                switch ($this->rf->delete($data)) {
                    case 0:
                        $message = 'Roles NOT deleted';
                        break;
                    case 1:
                        $message = 'Role(s) was deleted. Permissions NOT deleted (or role(s) have not been permissions';
                        break;
                    case 2:
                        $message = 'Role(s) was deleted. Permissions was deleted. Users roles NOT deleted (or users have not been assigned these roles)';
                        break;
                    case 3:
                        $message = 'Role(s) was deleted';
                        $type = 'text-success';
                        break;
                }

                $this->flashMessage($message, $type);
            } catch (\Throwable $e) {
                $this->flashMessage($e->getMessage().PHP_EOL
                    .'Trace: '.$e->getTraceAsString().PHP_EOL, 'text-danger');
            }
        } else {
            $this->flashMessage('An empty form was received');
            $this->redirect(':Admin:Users:Roles:delete');
        }
        $this->redirect(':Admin:');
    }

    public function actionPermissionsAdd()
    {
        foreach ($this->rf->role_permission as $row) {
            $this->template->roles_isset_permissions['role_'.$row->role_id][] = $row->permission_id ?? [];
        }
    }

    public function createComponentFormPermissionsAdd(): Form
    {
        $form = $this->formFactory->create();
        $form->setHtmlAttribute('id', 'formPermissionsAdd')
           ->setHtmlAttribute('class', 'form');

        $roles = $this->rf->role->fetchAll();
        $form->addRadioList('role', 'Roles:', $roles);

        // $permissions = $this->pf->table->fetchAll();
        // \array_multisort(\array_column($permissions, 'resource'), SORT_DESC, $permissions);
        foreach ($this->pf->table as $value) {
            $permissions[$value['resource']][] = ['id' => $value['id'], 'action' => $value['action']];
        }

        $form->addCheckboxList('permissions', 'Permissions:', $permissions);

        $form->addSubmit('addPermissions', 'Add Permissions');

        $form->onSuccess[] = [$this, 'permissionsAdd'];

        return $form;
    }

    #[Requires(methods: 'POST')]
    public function permissionsAdd(Form $form): void
    {
        $data['role'] = $form->getHttpData($form::DataText, 'role');
        $data['permissions'] = $form->getHttpData($form::DataText, 'permissions[]');

        if (!empty($data['role']) && !empty($data['permissions'][0])) {
            try {
                $this->rf->permissionsAdd($data);
                $this->flashMessage('Permissions for role was inserted', 'text-success');
            } catch (\Throwable $e) {
                $this->flashMessage($e->getMessage().PHP_EOL
                    .'Trace: '.$e->getTraceAsString().PHP_EOL, 'text-danger');
            }
        }
        $this->redirect(':Admin:');
    }

    public function actionPermissionsDelete()
    {
        $roles = [];
        foreach ($this->rf->role as $role) {
            $permissions = [];
            foreach ($role->related('role_permission.role_id') as $role_permission) {
                $permission_id = $role_permission->ref('permission', 'permission_id')->id;
                $resource = $role_permission->ref('permission', 'permission_id')->resource;
                $action = $role_permission->ref('permission', 'permission_id')->action;

                $permissions[$resource][] = [
                    'permission_id' => $permission_id,
                    'action' => $action,
                ];
            }
            if (!empty($permissions)) {
                $roles[] = [
                    'role_id' => $role->id,
                    'role_name' => $role->role_name,
                    'permissions' => $permissions,
                ];
            }
        }
        $this->template->roles = $roles;
    }

    public function createComponentFormPermissionsDelete(): Form
    {
        $form = $this->formFactory->create();
        $form->setHtmlAttribute('id', 'formPermissionsDelete')
           ->setHtmlAttribute('class', 'form');

        $form->addSubmit('deletePermissions', 'Delete Permissions');

        $form->onSuccess[] = [$this, 'permissionsDelete'];

        return $form;
    }

    #[Requires(methods: 'POST')]
    public function permissionsDelete(Form $form): void
    {
        $data = $form->getHttpData($form::DataText, 'role_permissions[]');

        $this->flashMessage(\json_encode($data));
        if (!empty($data[0])) {
            try {
                $this->rf->permissionsDelete($data);
                $this->flashMessage('Permissions for roles was deleted', 'text-success');
            } catch (\Throwable $e) {
                $this->flashMessage($e->getMessage().PHP_EOL
                    .'Trace: '.$e->getTraceAsString().PHP_EOL, 'text-danger');
            }
        } else {
            $this->flashMessage('Empty form was received');
        }
        $this->redirect(':Admin:');
    }
}

class RolesTemplate extends \App\UI\Admin\BaseTemplate
{
    public array $roles_isset_permissions;
    public array $roles;
}
