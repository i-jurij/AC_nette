<?php

namespace App\UI\Home\Sign;

use App\Model\UserFacade;
use App\UI\Accessory\FormFactory;
use App\UI\Accessory\PhoneNumber;
use Nette;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;
use Nette\Utils\Html;

final class SignPresenter extends \App\UI\BasePresenter
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
        $form = $this->formFactory->createHomeLoginForm();
        $form->setHtmlAttribute('id', 'log_in_app')
            ->setHtmlAttribute('class', 'form center');

        $form->addGroup('');
        $form->addCaptcha('captcha', 'Ошибка в капче. Повторите ввод.');

        $form->addGroup('');
        $form->addSubmit('send', 'Войти');

        $form->addGroup('--- 👤 ---');
        $url_reg = $this->link(':Home:Sign:up');
        $form->addButton('register', Html::el('div')
            ->setHtml('<a href="'.$url_reg.'">Зарегистрироваться</a>'));

        $form->addGroup('--- 🔓 ---');
        $url_restore = $this->link(':Home:Sign:restore');
        $form->addButton('restore', Html::el('div')
            ->setHtml('<a href="'.$url_restore.'">Забыли пароль?</a>'));

        $form->onSuccess[] = $this->userLogin(...);

        return $form;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    private function userLogin(Form $form, \stdClass $data): void
    {
        try {
            if (!empty($data->username)) {
                $this->getUser()->login($data->username, $data->password);
            }
            if (!empty($data->phone)) {
                $this->getUser()->login(PhoneNumber::toDb($data->phone), $data->password);
            }

            $this->restoreRequest($this->backlink);

            $this->redirect(':Home:');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Wrong login or password.');
        }
    }

    public function createComponentSignUpForm()
    {
        $form = (new Signupform($this->formFactory))->get();

        $form->onSuccess[] = $this->processSignUpForm(...);

        return $form;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    private function processSignUpForm(Form $form, \stdClass $data): void
    {
        $data->roles = 'client';
        $res = $this->userfacade->add($data);
        if ($res === 'ok') {
            $this->flashMessage('Вы зарегистрированы', 'success');
            $this->redirect(':Home:Sign:in');
        } else {
            $form->addError($res);
        }
    }

    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        // $this->flashMessage('Log out');
        $this->redirect(':Home:');
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function actionCheckPhoneInDb(): void
    {
        $httpRequest = $this->getHttpRequest();
        $phone = $httpRequest->getPost('phone');
        $res = $this->userfacade->searchBy('phone', $phone);
        if (!empty($res->id)) {
            $this->sendJson(1);
        }
        $this->sendJson(0);
    }
}

class SignTemplate extends \App\UI\Home\BaseTemplate
{
    // public array $menuList;
}
