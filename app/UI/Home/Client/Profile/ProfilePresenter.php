<?php

declare(strict_types=1);

namespace App\UI\Home\Client\Profile;

use App\Model\ClientFacade;
use \App\Model\ChatFacade;
use App\UI\Accessory\IsBot;
use Ijurij\Geolocation\Lib\Csrf;
use Nette\Application\UI\Form;
use App\UI\Accessory\PhoneNumber;
use Nette\Utils\Validators;

/**
 * @property ProfileTemplate $template
 */
final class ProfilePresenter extends \App\UI\Home\BasePresenter
{
    // use \App\UI\Accessory\RequireLoggedUser;

    // Dependency injection of form factory and user management facade
    public function __construct(
        protected ClientFacade $cf,
        protected ChatFacade $chat
    ) {
        parent::__construct($this->chat);
        $this->onStartup[] = function () {
            if (IsBot::check()) {
                $this->redirect(':Home:');
            }
        };
    }

    public function renderDefault()
    {
        $user = $this->getUser();
        if ($user->isLoggedIn()) {
            $identity = $user->getIdentity();
            $this->template->user_data = $identity->getData();
        } elseif ($user->getLogoutReason() === $user::LogoutInactivity) {
            $this->flashMessage('You have been signed out due to inactivity. Please sign in again.');
            $this->redirect(':Home:Sign:in', ['backlink' => $this->storeRequest()]);
        } else {
            $this->redirect(':Home:Sign:in');
        }
    }

    public function createComponentClientUpdateUsernameForm()
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'clientUpdateUsernameForm');

        $form->addText('username', 'Введите новое имя:')
            ->setHtmlAttribute('id', 'clientUpdateUsernameInput')
            ->setHtmlAttribute('placeholder', 'Буквы, цифры, дефис')
            ->addRule($form::MinLength, 'Имя длиной не менее %d символов', 3)
            ->addRule($form::MaxLength, 'Имя длиной до 250 символов', 250)
            ->addRule($form::Pattern, 'Имя только из букв, цифр, дефисов и подчеркиваний', '^[a-zA-Zа-яА-ЯёЁ0-9_\-]+$')
            ->setMaxLength(250)
            ->setRequired();

        $form->addHidden('client_id', $this->getUser()->getId());

        $form->addSubmit('clientUpdateUsernameFormSubmit', 'Update');

        $form->onSuccess[] = [$this, 'edit'];

        return $form;
    }

    public function createComponentClientUpdatePhoneForm()
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'clientUpdatePhoneForm');

        $form->addText('phone', 'Phone:')
            ->setHtmlType('tel')
            ->setHtmlAttribute('placeholder', '+7 000 111 22 33')
            ->setHtmlAttribute('id', 'user_phone_input')
            ->addRule($form::Pattern, 'Формат номера: +7 000 111 22 33', PhoneNumber::PHONE_REGEX)
            ->setRequired();
        // ->setEmptyValue('+7');

        $form->addHidden('client_id', $this->getUser()->getId());

        $form->addSubmit('clientUpdatePhoneFormSubmit', 'Update');

        $form->onSuccess[] = [$this, 'edit'];

        return $form;
    }

    public function createComponentClientUpdateEmailForm()
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'clientUpdateEmailForm');

        $form->addEmail('email', 'Email:')
            ->setHtmlAttribute('id', 'clientUpdateEmailInput')
            ->setHtmlAttribute('placeholder', 'name@mail.com')
            ->addRule($form::Email, 'Введите правильный адрес электронной почты.')
            ->addFilter(fn($value) => filter_var($value, FILTER_SANITIZE_EMAIL))
            ->setRequired();

        $form->addHidden('client_id', $this->getUser()->getId());

        $form->addSubmit('clientUpdateEmailFormSubmit', 'Update');

        $form->onSuccess[] = [$this, 'edit'];

        return $form;
    }

    public function createComponentClientUpdatePasswordForm()
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'clientUpdatePasswordForm');

        $form->addPassword('password', 'Password:')
            ->setHtmlAttribute('id', 'clientUpdatePasswordInput')
            ->addRule($form::MinLength, 'Пароль длиной не менее %d символов', PASSWORD_MIN_LENGTH)
            ->setMaxLength(120);

        $form->addPassword('passwordVerify', 'PasswordVerify')
            ->setHtmlAttribute('id', 'clientUpdatePasswordVerifyInput')
            ->addRule($form::Equal, "Пароли не совпадают", $form['password'])
            ->addRule($form::MinLength, 'Пароль длиной не менее %d символов', PASSWORD_MIN_LENGTH)
            ->setMaxLength(120)
            ->setOmitted();

        $form->addHidden('client_id', $this->getUser()->getId());

        $form->addSubmit('clientUpdatePasswordFormSubmit', 'Update');

        $form->onSuccess[] = [$this, 'edit'];

        return $form;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function edit(Form $form, \stdClass $data)
    {
        $id = null;
        $d = [];
        if (!empty($data->client_id)) {
            $id = (int) $data->client_id;
            unset($data->client_id);
        }
        if (!empty($data->username) && is_string($data->username)) {
            $d['username'] = mb_substr(htmlspecialchars(strip_tags($data->username)), 0, 250);
        }
        if (!empty($data->phone)) {
            $d['phone'] = PhoneNumber::toDb($data->phone);
        }
        if (!empty($data->email) && Validators::isEmail($data->email)) {
            $d['email'] = $data->email;
        }
        if (!empty($data->password)) {
            $d['password'] = $data->password;
        }

        $current_client_id = $this->getUser()->getId();
        if ($this->getUser()->isLoggedIn() && $id === $current_client_id) {
            if (!empty($d)) {
                $this->cf->update($id, $d);
                $this->getUser()->logout(clearIdentity: true);
                // $this->flashMessage('Изменения сохранены.', 'success');
                $this->redirect(':Home:Sign:in');
            }
            $this->flashMessage('Отправлена пустая форма.', 'info');
            $this->redirect('this');
        } else {
            $this->error();
        }
    }

    #[Requires(sameOrigin: true)]
    public function actionDelete(int $id)
    {
        if ($this->getUser()->isLoggedIn() && $id === $this->getUser()->getId()) {
            $this->cf->deleteClientData($id);
            $this->getUser()->logout(clearIdentity: true);
            $this->redirect(':Home:default');
        } else {
            $this->error();
        }
    }
    public function renderRestorePassword($token) // waiting for auth_token on enter
    {
        $this->template->user_token = $token;
        $this->template->csrf_name = Csrf::$token_name;
        $this->template->csrf = Csrf::getToken();
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function actionPostRestorePassword(): void
    {
        if (Csrf::isValid() && Csrf::isRecent()) {
            $httpRequest = $this->getHttpRequest();
            $auth_token = $httpRequest->getPost('user_token');
            $id = $this->cf->searchBy('auth_token', $auth_token)->id;
            $data['id'] = $id;
            $data['password'] = $httpRequest->getPost('password');
            $this->cf->update($id, $data);

            $this->flashMessage('Теперь введите ваш новый пароль для входа.', 'info');
            $this->redirect(':Home:Sign:in');
        } else {
            $this->error();
        }
    }
}

class ProfileTemplate extends \App\UI\Home\BaseTemplate
{
    public array $user_data;
    public string $user_token;
    public string $csrf_name;
    public string $csrf;
}
