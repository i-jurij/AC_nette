<?php

namespace App\UI\Admin\Sign;

use App\Model\UserFacade;
use App\UI\Accessory\FormFactory;
use Ijurij\Geolocation\Lib\Csrf;
use Nette;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;
use Nette\Utils\Html;
use Nette\Utils\Validators;

final class SignPresenter extends Nette\Application\UI\Presenter
{
    /**
     * Stores the previous page hash to redirect back after successful login.
     */
    #[Persistent]
    public string $backlink = '';

    // Dependency injection of form factory and user management facade
    public function __construct(
        private FormFactory $formFactory,
        protected UserFacade $userfacade,
    ) {
    }

    protected function createComponentSignInForm(): Form
    {
        $form = $this->formFactory->createLoginForm();
        $form->setHtmlAttribute('id', 'log_in_app')
            ->setHtmlAttribute('class', 'form center mx-auto');

        $form->addGroup('');
        $form->addCaptcha('captcha', 'Captcha error. Re-enter captcha.');

        $form->addGroup('');
        $form->addSubmit('send', 'Signin');

        $form->onSuccess[] = $this->userLogin(...);

        return $form;
    }

    private function userLogin(Form $form, \stdClass $data): void
    {
        try {
            $this->getUser()->login($data->username, $data->password);
            $this->restoreRequest($this->backlink);

            $this->redirect(':Admin:');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Wrong login or password.');
        }
    }

    public function createComponentSignUpForm()
    {
        $form = $this->formFactory->createLoginForm();

        $form->setHtmlAttribute('id', 'signup')
            ->setHtmlAttribute('class', 'form center mx-auto');

        $form->addPassword('passwordVerify', '')
            ->setHtmlAttribute('placeholder', 'Confirm password:')
            ->setRequired('Enter password again')
            ->addRule($form::Equal, 'Password mismatch', $form['password'])
            ->addRule($form::MinLength, 'Minimum password length %d characters', PASSWORD_MIN_LENGTH)
            ->setMaxLength(120)
            ->setOmitted();

        $form->addEmail('email', '')
            ->setHtmlAttribute('placeholder', 'Email:')
            ->setRequired('Enter email');

        $form->addGroup('');
        $form->addCaptcha('captcha', 'Captcha error. Re-enter captcha.');

        $form->addGroup('');
        $form->addSubmit('send', 'Register');

        $form->addGroup('--- 🔓 ---');
        $url_reg = $this->link('Sign:in');
        $form->addButton('register', Html::el('div')
                ->setHtml('<a href="'.$url_reg.'">Login</a>'));

        $form->onSuccess[] = $this->processSignUpForm(...);

        return $form;
    }

    private function processSignUpForm(Form $form, \stdClass $data): void
    {
        // save data to table with the data of users who have applied for registration
        // $prepared_data = $this->userfacade->prepareAddFormData($data);
        if (!empty($data->email) && Validators::isEmail($data->email)) {
            $prepared_data['email'] = $data->email;
        }

        if (!empty($data->username) && \preg_match('/^[a-zA-Zа-яА-ЯёЁ0-9\-_]{3,25}$/', $data->username)) {
            $prepared_data['username'] = $data->username;
        }

        $data_for_search = (object) $prepared_data;
        $search = $this->userfacade->search($data_for_search);
        if (!empty($search)) {
            $this->flashMessage('User with same data (username or phone or email) already registered.', 'error');
            $this->redirect(':Admin:Sign:up');
        }

        $prepared_data['password'] = $data->password;
        $prepared_data['auth_token'] = $this->userfacade->token();
        $this->userfacade->db->beginTransaction();
        try {
            $res = $this->userfacade->db->table('userappliedforregistration')->insert($prepared_data);
            $this->userfacade->db->commit();
            $this->flashMessage('Your data is processed.', 'info');
            $this->redirect(':Home:');
        } catch (Nette\Database\UniqueConstraintViolationException $e) {
            $this->userfacade->db->rollBack();
            \Tracy\Debugger::log($e, \Tracy\Debugger::EXCEPTION);
            $form->addError('User with same data already applied for registration.');
        } catch (Nette\Database\DriverException $e) {
            $this->userfacade->db->rollBack();
            \Tracy\Debugger::log($e, \Tracy\Debugger::EXCEPTION);
            $form->addError('Database error.'.$e);
        }
    }

    public function actionVerifyEmail($token): void
    {
        $token = \filter_input(INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
        $csrf = \filter_input(INPUT_GET, Csrf::$token_name, FILTER_SANITIZE_SPECIAL_CHARS);
        $data = $this->userfacade->db->table('userappliedforregistration')
            ->where('auth_token', $token)
            ->where('csrf', $csrf)
            ->fetch();

        if (isset($data->csrf) && $csrf === $data->csrf) {
            $data_array = [
                UserFacade::ColumnName => $data->username,
                UserFacade::ColumnPasswordHash => $this->userfacade->passwords->hash($data->password),
                UserFacade::ColumnEmail => $data->email,
                UserFacade::ColumnEmailVerified => 1,
                UserFacade::ColumnAuthToken => $this->userfacade->token(),
            ];
            $data_array['roles'] = \unserialize($data->roles, ['allowed_classes' => false]);
            $res_user_add = $this->userfacade->add((object) $data_array);
            if ($res_user_add === 'ok') {
                // remove user data from userappliedforregistration
                $res_del = $this->userfacade->db->table('userappliedforregistration')
                            ->where('auth_token', $token)
                            ->delete();
                // send email to new user with him username and password

                $this->flashMessage('You can log in now.', 'text-success');
                $this->redirect(':Admin:Sign:in');
            } else {
                $this->flashMessage($res_user_add, 'text-warning');
                $this->redirect(':Admin:Sign:up');
            }
        } else {
            $this->error();
        }
    }

    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        $this->flashMessage('Log out');
        $this->redirect(':Admin:');
        // $this->forward('Home:');
    }
}

class SignTemplate extends \App\UI\Admin\BaseTemplate
{
}
