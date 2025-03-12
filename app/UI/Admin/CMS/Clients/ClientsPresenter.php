<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Clients;

use App\Model\ClientFacade;
use App\UI\Accessory\FormFactory;
use Nette\Application\UI\Form;

final class ClientsPresenter extends \App\UI\Admin\BasePresenter
{
    protected $user_data;
    public $postsearch;

    public function __construct(
        protected ClientFacade $clientfacade,
        private FormFactory $formFactory)
    {
        parent::__construct();
    }

    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Clients', '')) {
            $this->error('Forbidden', 403);
        }
    }

    public function renderList(int $page = 1): void
    {
        if (!$this->getUser()->isAllowed('Clients', 'getAllUsersData')) {
            $this->error('Forbidden', 403);
        }
        $users_data = $this->clientfacade->getAllUsersData();
        $this->template->count = count($users_data);

        $lastPage = 0;
        $this->template->users_data = $users_data->page($page, 8, $lastPage);
        $this->template->page = $page;
        $this->template->lastPage = $lastPage;

        foreach ($users_data as $user) {
            // $roles[$user->id] = $this->roleWithUserId($this->clientfacade->db, $user->id);
            $roles[$user->id] = $this->clientfacade->roleWithUserId($user->id);
        }
        $this->template->users_roles = $roles;
    }

    public function createComponentClientUpdateForm()
    {
        $form = new Form();
        $form->addProtection();
        $renderer = $form->getRenderer();
        $renderer->wrappers['group']['container'] = 'div class="my1 mx-auto pb2 px2"';
        $renderer->wrappers['controls']['container'] = 'div';
        $renderer->wrappers['pair']['container'] = 'div';
        $renderer->wrappers['label']['container'] = null;
        $renderer->wrappers['control']['container'] = null;

        $form->setHtmlAttribute('id', 'clientUpdateForm')
            ->setHtmlAttribute('class', 'form');
        // ->setAction($this->link(':Admin:Users:update'));

        $form->addGroup('');

        $form->addHidden('id');

        $form->addText('username', 'Username:')
            ->setHtmlAttribute('placeholder', 'Name:')
            ->addRule($form::MinLength, 'Имя длиной не менее %d символов', 3)
            ->addRule($form::Pattern, 'Имя только из букв, цифр, дефисов и подчеркиваний', '^[a-zA-Zа-яА-ЯёЁ0-9\-_]{3,25}$')
            ->setMaxLength(25);

        $form->addPassword('password', 'Password:')
            ->setHtmlAttribute('placeholder', 'Password:')
            ->addRule($form::MinLength, 'Пароль длиной не менее %d символов', PASSWORD_MIN_LENGTH)
            ->setMaxLength(120);

        $form->addPassword('passwordVerify', 'PasswordVerify')
            ->setHtmlAttribute('placeholder', 'Confirm password:')
            ->addRule($form::Equal, 'Несоответствие пароля', $form['password'])
            ->addRule($form::MinLength, 'Пароль длиной не менее %d символов', PASSWORD_MIN_LENGTH)
            ->setMaxLength(120)
            ->setOmitted();

        $form->addText('phone', 'Phone:')
            ->setHtmlType('tel')
            ->setHtmlAttribute('placeholder', 'Phone:')
            ->addRule($form::Pattern, '+7 000 111 22 33', '(\+?7|8)?\s?[\(]{0,1}?\d{3}[\)]{0,1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?');
        // ->setEmptyValue('+7');

        $form->addEmail('email', 'Email:')
            ->setHtmlAttribute('placeholder', 'Email:');

        $roles = $this->clientfacade->db->table('role');
        foreach ($roles as $role) {
            $roles_array[$role['id']] = $role['role_name'];
        }

        $form->addCheckboxList('roles', 'Roles:', $roles_array);

        $form->addGroup('');
        $form->addSubmit('send', 'Update client');

        $form->onSuccess[] = [$this, 'update'];

        return $form;
    }

    public function renderEdit(int $id): void
    {
        if (($this->getUser()->getId() === $id) || $this->getUser()->isAllowed('Clients', 'update')) {
            $this->template->user_data = $this->clientfacade->getUserData($id);
            $this->template->user_roles = $this->clientfacade->roleWithUserId($id);
        } else {
            $this->flashMessage('You don\'t have permission for this', 'text-warning');
            $this->redirect(':Admin:');
        }
    }

    #[Requires(methods: 'POST')]
    public function update(Form $form, $data): void
    {
        if (($this->getUser()->getId() == $data->id) || $this->getUser()->isAllowed('Clients', 'update')) {
            // update profile throw clientfacade? and show profile again with updated data;
            try {
                $id = $data->id;
                unset($data->id);
                $update = array_filter((array) $data);
                if (!empty($update)) {
                    $this->clientfacade->update($id, $update);
                    $this->flashMessage(\json_encode($update).' Client updated', 'text-success');
                } else {
                    $this->flashMessage('Nothing was updated', 'text-success');
                }
            } catch (\Exception $e) {
                $this->flashMessage('Caught Exception!'.PHP_EOL
                    .'Error message: '.$e->getMessage().PHP_EOL
                    .'File: '.$e->getFile().PHP_EOL
                    .'Line: '.$e->getLine().PHP_EOL
                    .'Error code: '.$e->getCode().PHP_EOL
                    .'Trace: '.$e->getTraceAsString().PHP_EOL, 'text-danger');
            }
        } else {
            $this->error('Forbidden', 403);
        }

        $this->redirect(':Admin:');
    }

    public function actionDelete(int $id): void
    {
        if (!$this->getUser()->isAllowed('Clients', ' deleteUserData')) {
            $this->error('Forbidden', 403);
        }
        try {
            $this->clientfacade->deleteUserData($id);
            $this->flashMessage('Client deleted.');
        } catch (\Throwable $th) {
            $this->flashMessage($th);
        }
        // $this->redirect(':Admin:Users:');
        $this->redirect(':Admin:CMS:Clients:list');
    }

    public function createComponentClientSearchForm(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'clientSearchForm')
            ->setHtmlAttribute('class', 'form');
        $form->addText('username', 'Name:')
            ->setHtmlAttribute('placeholder', 'Name:')
            ->addRule($form::MinLength, 'Имя длиной не менее %d символов', 3)
            ->addRule($form::Pattern, 'Имя только из букв, цифр, дефисов и подчеркиваний', '^[a-zA-Zа-яА-ЯёЁ0-9\-_]{3,25}$')
            ->setMaxLength(25);
        $form->addText('phone', 'Phone:')
            ->setHtmlAttribute('placeholder', 'Phone:')
            ->addRule($form::Pattern, 'Only +, digits, underscore, spaces and hyphens', '^[+0-9\-_]{3,30}$')
            ->setMaxLength(30);
        $form->addText('email', 'Email:')
            ->setHtmlAttribute('placeholder', 'Email:')
            ->setMaxLength(125);
        $roles = $this->clientfacade->db->table('role');
        foreach ($roles as $role) {
            $roles_array[$role['id']] = $role['role_name'];
        }
        $form->addCheckboxList('roles', 'Roles:', $roles_array);
        $form->addSubmit('searchClient', 'Search');
        $form->onSuccess[] = [$this, 'postSearch'];

        return $form;
    }

    #[Requires(methods: 'POST')]
    public function postSearch(?Form $form = null): void
    {
        if (!$this->getUser()->isAllowed('Clients', 'search')) {
            $this->error('Forbidden', 403);
        }

        $httpRequest = $this->getHttpRequest();

        if ($httpRequest->isMethod('POST') && !empty($form)) {
            try {
                $this->template->show = $this->clientfacade->search($form->getValues());
                if (empty($this->template->show)) {
                    $this->flashMessage("Client NOT found.\n", 'text-warning');
                    $this->redirect('this');
                }
            } catch (\Exception $e) {
                $this->flashMessage("\n".$e->getMessage(), 'text-danger');
                $this->redirect('this');
            }
        }
    }
}
