<?php

declare(strict_types=1);

namespace App\UI\Admin\Users;

use App\Model\UserFacade;
use App\UI\Accessory\Email;
use App\UI\Accessory\FormFactory;
use Ijurij\Geolocation\Lib\Csrf;
use Nette\Application\UI\Form;

/**
 * @property UsersTemplate $template
 */
final class UsersPresenter extends \App\UI\Admin\BasePresenter
{
    protected $user_data;
    public $postsearch;

    public function __construct(
        protected UserFacade $userfacade,
        private FormFactory $formFactory)
    {
        parent::__construct();
    }

    public function renderDefault()
    {
    }

    public function renderList(int $page = 1): void
    {
        if (!$this->getUser()->isAllowed('User', 'getAllUsersData')) {
            $this->error('Forbidden', 403);
        }
        $users_data = $this->userfacade->getAllUsersData();
        $this->template->count = count($users_data);

        $lastPage = 0;
        $this->template->users_data = $users_data->page($page, 8, $lastPage);
        $this->template->page = $page;
        $this->template->lastPage = $lastPage;

        foreach ($users_data as $user) {
            // $roles[$user->id] = $this->roleWithUserId($this->userfacade->db, $user->id);
            $roles[$user->id] = $this->userfacade->roleWithUserId($user->id);
        }
        $this->template->users_roles = $roles;
    }

    public function renderProfile(): void
    {
        $identity = $this->getUser()->getIdentity();
        $this->template->user_data = $identity->getData();
        $this->template->user_data['roles'] = $identity->getRoles();
    }

    public function createComponentUserUpdateForm()
    {
        $form = new Form();
        $form->addProtection();
        $renderer = $form->getRenderer();
        $renderer->wrappers['group']['container'] = 'div class="my1 mx-auto pb2 px2"';
        $renderer->wrappers['controls']['container'] = 'div';
        $renderer->wrappers['pair']['container'] = 'div';
        $renderer->wrappers['label']['container'] = null;
        $renderer->wrappers['control']['container'] = null;

        $form->setHtmlAttribute('id', 'userUpdateForm')
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

        $roles = $this->userfacade->db->table('role');
        foreach ($roles as $role) {
            $roles_array[$role['id']] = $role['role_name'];
        }

        $form->addCheckboxList('roles', 'Roles:', $roles_array);

        $form->addGroup('');
        $form->addSubmit('send', 'Update user');

        $form->onSuccess[] = [$this, 'update'];

        return $form;
    }

    public function renderEdit(int $id): void
    {
        if (($this->getUser()->getId() === $id) || $this->getUser()->isAllowed('User', 'update')) {
            $this->template->user_data = $this->userfacade->getUserData($id);
            $this->template->user_roles = $this->userfacade->roleWithUserId($id);
        } else {
            $this->flashMessage('You don\'t have permission for this', 'text-warning');
            $this->redirect(':Admin:');
        }
    }

    #[Requires(methods: 'POST')]
    public function update(Form $form, $data): void
    {
        if (($this->getUser()->getId() == $data->id) || $this->getUser()->isAllowed('User', 'update')) {
            // update profile throw UserFacade? and show profile again with updated data;
            try {
                $id = $data->id;
                unset($data->id);
                $update = array_filter((array) $data);
                if (!empty($update)) {
                    if ($this->getUser()->isInRole('admin') || $this->getUser()->getIdentity()->getId() == $id) {
                        $this->userfacade->update($id, $update);
                        $this->flashMessage(\json_encode($update).' User updated', 'text-success');
                    } else {
                        $this->flashMessage($this->getUser()->getIdentity()->getId().'/'.$id.'/'.\json_encode($update).'You not permissions for user data updating');
                    }
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
        if (!$this->getUser()->isAllowed('User', ' deleteUserData')) {
            $this->error('Forbidden', 403);
        }
        try {
            $this->userfacade->deleteUserData($id);
            $this->flashMessage('User deleted.');
        } catch (\Throwable $th) {
            $this->flashMessage($th);
        }
        // $this->redirect(':Admin:Users:');
        $this->redirect(':Admin:Users:list');
    }

    public function createComponentUserAddForm(): Form
    {
        $form = $this->formFactory->createLoginForm();

        $form->setHtmlAttribute('class', 'form');
        // ->setHtmlAttribute('id', 'useradd')

        $form->addPassword('passwordVerify', 'PasswordVerify')
            ->setHtmlAttribute('placeholder', 'Confirm password:')
            ->setRequired('Введите пароль ещё раз, чтобы проверить опечатки')
            ->addRule($form::Equal, 'Несоответствие пароля', $form['password'])
            ->addRule($form::MinLength, 'Пароль длиной не менее %d символов', PASSWORD_MIN_LENGTH)
            ->setMaxLength(120)
            ->setOmitted();

        $form->addText('phone', 'Phone:')
            ->setHtmlType('tel')
            ->setHtmlAttribute('placeholder', 'Phone:')
            ->addRule($form::Pattern, '+7 000 111 22 33', '(\+?7|8)?\s?[\(]{0,1}?\d{3}[\)]{0,1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?\d{1}\s?[\-]{0,1}?');

        $form->addEmail('email', 'Email:')
            ->setHtmlAttribute('placeholder', 'Email:');

        $roles = $this->userfacade->db->table('role');
        foreach ($roles as $role) {
            $roles_array[$role['id']] = $role['role_name'];
        }

        $form->addCheckboxList('roles', 'Roles:', $roles_array);

        $form->addGroup('');
        $form->addSubmit('send', 'Add user');

        $form->onSuccess[] = [$this, 'useradd'];

        return $form;
    }

    #[Requires(methods: 'POST')]
    public function useradd(Form $form, $data): void
    {
        if (!$this->getUser()->isAllowed('User', ' add')) {
            $this->error('Forbidden', 403);
        }
        try {
            $new_user = $this->userfacade->add($data);
            $this->flashMessage('You have successfully user add.', 'text-success');
        } catch (\Exception $e) {
            $this->flashMessage("Such a name, email or number is already in the database.\nError: ".$e->getMessage(), 'text-danger');
        }

        $this->redirect(':Admin:');
    }

    public function actionAdd(): void
    {
        $this->template->applicationscount = count($this->userfacade->db->table('userappliedforregistration'));
    }

    public function actionApplicationsforregistration(): void
    {
        $this->template->applications = $this->userfacade->db->table('userappliedforregistration');
    }

    public function createComponentVerifyapplicationforregistrationForm()
    {
        $form = new Form();
        $form->addProtection();
        $form->addEmail('email');
        $form->addText('auth_token');
        $roles = $this->userfacade->db->table('role');
        foreach ($roles as $role) {
            $roles_array[$role['id']] = $role['role_name'];
        }
        $form->addCheckboxList('roles', '', $roles_array);
        $form->addSubmit('verifyFormSubmit', 'Verify');
        $form->onSuccess[] = [$this, 'verifyapplicationforregistration'];

        return $form;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function verifyapplicationforregistration(Form $form, $data): void
    {
        if (!$this->getUser()->isAllowed('User', ' add')) {
            $this->error('Forbidden', 403);
        }
        try {
            $this->absoluteUrls = true;
            $token = $data->auth_token;
            $url = $this->link(':Admin:Sign:verifyEmail', [
                'token' => $token,
                Csrf::$token_name => Csrf::getToken(),
            ]);

            if (!empty($data->roles)) {
                // send email for verification (url with auth_token)
                $mail = new Email();
                $mail->from = 'admin@'.SITE_NAME;
                $mail->to = $data->email;
                $mail->subject = 'Register on '.SITE_NAME;
                $mail->body = (string) $url;
                $mail->sendEmail();
                $message = 'Email for verify received.';
                // save serialized roles to table userappliedforregistration

                $roles = serialize($data->roles);
                $upd = $this->userfacade->db
                ->table('userappliedforregistration')
                ->where('auth_token', $token)
                ->update([
                    'roles' => $roles,
                    'csrf' => Csrf::getToken(),
                ]);
                if ($upd > 0) {
                    $this->flashMessage("$message Roles of user saved.", 'text-success');
                } else {
                    $this->flashMessage("$message Roles of user NOT saved.", 'text-warning'); // code...
                }
            } else {
                $this->flashMessage('Assign a user a role', 'text-warning');
            }
        } catch (\Exception $e) {
            $this->flashMessage('Error: '.$e->getMessage(), 'text-danger');
        }
        $this->redirect(':Admin:Users:applicationsforregistration');
    }

    public function createComponentUserSearchForm(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'userSearchForm')
            ->setHtmlAttribute('class', 'form');
        $form->addText('username', 'Username:')
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
        $roles = $this->userfacade->db->table('role');
        foreach ($roles as $role) {
            $roles_array[$role['id']] = $role['role_name'];
        }
        $form->addCheckboxList('roles', 'Roles:', $roles_array);
        $form->addSubmit('searchUser', 'Search');
        $form->onSuccess[] = [$this, 'postSearch'];

        return $form;
    }

    #[Requires(methods: 'POST')]
    public function postSearch(?Form $form = null): void
    {
        if (!$this->getUser()->isAllowed('User', 'search')) {
            $this->error('Forbidden', 403);
        }

        $httpRequest = $this->getHttpRequest();

        if ($httpRequest->isMethod('POST') && !empty($form)) {
            try {
                $this->template->show = $this->userfacade->search($form->getValues());
                if (empty($this->template->show)) {
                    $this->flashMessage("User NOT found.\n", 'text-warning');
                    $this->redirect('this');
                }
            } catch (\Exception $e) {
                $this->flashMessage("\n".$e->getMessage(), 'text-danger');
                $this->redirect('this');
            }
        }
    }
}
