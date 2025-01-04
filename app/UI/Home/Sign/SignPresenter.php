<?php

namespace App\UI\Home\Sign;

use App\UI\Accessory\FormFactory;
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
        private FormFactory $formFactory
    ) {
    }

    protected function createComponentSignInForm(): Form
    {
        $form = $this->formFactory->createLoginForm();
        $form->setHtmlAttribute('id', 'log_in_app')
            ->setHtmlAttribute('class', 'form');

        $form->addGroup('');
        $form->addCaptcha('captcha', 'Ошибка в капче. Повторите ввод.');

        $form->addGroup('');
        $form->addSubmit('send', 'Войти');

        $form->addGroup('--- ✍ ---');
        $url_reg = $this->link('Sign:up');
        $form->addButton('register', Html::el('div')
            ->setHtml('<a href="'.$url_reg.'">Зарегистрироваться</a>'));

        $form->onSuccess[] = $this->userLogin(...);

        return $form;
    }

    public function createComponentSignUpForm()
    {
        $form = $this->formFactory->create();

        $form->setHtmlAttribute('id', 'signup')
            ->setHtmlAttribute('class', 'form');

        $form->addGroup('');
        $form->addEmail('email', '')
            ->setHtmlAttribute('placeholder', 'Email:');

        $form->addGroup('');
        $form->addCaptcha('captcha', 'Ошибка в капче. Повторите ввод.');

        $form->addGroup('');
        $form->addSubmit('send', 'Зарегистрироваться');

        $form->addGroup('--- 🔓 ---');
        $url_reg = $this->link('Sign:in');
        $form->addButton('register', Html::el('div')
            ->setHtml('<a href="'.$url_reg.'">Войти</a>'));

        $form->addGroup('--- § ---');
        $url_politic = $this->link(':Politic:');
        $form->addButton('politic', Html::el('div')
            ->setHtml('<a href="'.$url_politic.'">Политика обработки персональных данных</a>'))
            ->setHtmlAttribute('class', 'pseudo');

        $form->onSuccess[] = $this->processSignUpForm(...);

        return $form;
    }

    private function userLogin(Form $form, \stdClass $data): void
    {
        try {
            $this->getUser()->login($data->username, $data->password);
            $this->restoreRequest($this->backlink);

            $this->redirect(':Home:');
        } catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Wrong login or password.');
        }
    }

    private function processSignUpForm(Form $form, \stdClass $data): void
    {
        try {
            // register user
            $this->flashMessage('На ваш электронный адрес выслано письмо. Для завершения регистрации следуйте инструкции в письме.', 'info');
            $this->redirect(':Home:Sign:in');
        } catch (Exception $e) {
            $form->addError('Unknown error.');
        }
    }

    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        // $this->flashMessage('Log out');
        $this->redirect(':Home:');
    }
}

class SignTemplate extends \App\UI\Home\BaseTemplate
{
    // public array $menuList;
}
