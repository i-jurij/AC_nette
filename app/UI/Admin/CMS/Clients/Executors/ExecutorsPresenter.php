<?php

declare (strict_types = 1);

namespace App\UI\Admin\CMS\Clients\Executors;

use Nette\Application\UI\Form;

final class ExecutorsPresenter extends \App\UI\Admin\CMS\Clients\ClientsPresenter
{
    public function renderDefault(int $page = 1): void
    {
        if (! $this->getUser()->isAllowed('Executors', 'getAllClientsData')) {
            $this->error('Forbidden', 403);
        }
        //$clients_data          = $this->clientfacade->getAllClientsData();
        $clients_data = $this->clientfacade->getCl('executor');

        $this->template->count = count($clients_data);

        $lastPage                     = 0;
        $this->template->clients_data = $clients_data->page($page, 6, $lastPage);
        $this->template->page         = $page;
        $this->template->lastPage     = $lastPage;

        $roles = [];
        foreach ($clients_data as $user) {
            // $roles[$user->id] = $this->roleWithClientId($this->clientfacade->db, $user->id);
            $roles[$user->id] = $this->clientfacade->roleWithClientId($user->id);
        }
        $this->template->users_roles = $roles;
    }

    public function createComponentExecutorSearchForm(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'executorSearchForm')
            ->setHtmlAttribute('class', 'form');
        $form->addText('username', 'Name:')
            ->setHtmlAttribute('placeholder', 'Name:')
            ->addRule($form::MinLength, 'Имя длиной не менее %d символов', 3)
            ->addRule($form::MaxLength, 'Имя длиной до 25 символов', 25)
            ->addRule($form::Pattern, 'Имя только из букв, цифр, дефисов и подчеркиваний', '^[a-zA-Zа-яА-ЯёЁ0-9_\-]+$')
            ->setMaxLength(25);
        $form->addText('phone', 'Phone:')
            ->setHtmlAttribute('placeholder', 'Phone:')
            ->addRule($form::Pattern, 'Only +, digits, underscore, spaces and hyphens', '^[+0-9\-_]$')
            ->setMaxLength(30);
        $form->addText('email', 'Email:')
            ->setHtmlAttribute('placeholder', 'Email:')
            ->setMaxLength(125);
        $roles = $this->clientfacade->db->table('role')
            ->where('role_name', ['banned', 'executor']);
        foreach ($roles as $role) {
            $roles_array[$role['id']] = $role['role_name'];
        }
        $form->addCheckboxList('roles', 'Roles:', $roles_array);
        $form->addSubmit('searchExecutor', 'Search');
        $form->onSuccess[] = [$this, 'postSearch'];

        return $form;
    }

    public function postSearch(?Form $form = null): void
    {
        if (! $this->getUser()->isAllowed('Executors', 'search')) {
            $this->error('Forbidden', 403);
        }

        $httpRequest = $this->getHttpRequest();

        if ($httpRequest->isMethod('POST') && ! empty($form)) {
            try {
                $data = $form->getValues();

                $executor_role_id = $this->clientfacade->db
                    ->table('role')
                    ->where('role_name', 'executor')
                    ->fetch()
                    ->id;
                array_push($data->roles, $executor_role_id);
                $this->template->show = $this->clientfacade->search($data);
                if (empty($this->template->show)) {
                    $this->flashMessage("Client NOT found.\n", 'text-warning');
                    $this->redirect('this');
                }
            } catch (\Exception $e) {
                $this->flashMessage("\n" . $e->getMessage(), 'text-danger');
                $this->redirect('this');
            }
        }
    }
}
