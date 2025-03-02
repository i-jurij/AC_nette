<?php

namespace App\UI\Home\Sign;

use App\UI\Accessory\FormFactory;
use Nette\Application\UI\Form;

final class Signupform
{
    public function __construct(
        private FormFactory $formFactory
    ) {
    }

    public function get(): Form
    {
        $form = $this->formFactory->createHomeLoginForm();

        $form->setHtmlAttribute('id', 'signupform')
            ->setHtmlAttribute('class', 'form mb2 mr2 center');

        $form->addGroup('');
        $form->addEmail('email', '')
        ->setHtmlAttribute('placeholder', '📧 Email:')
        ->addRule($form::Email, 'Введите правильный адрес электронной почты.')
        ->addFilter(function ($value) {
            return filter_var($value, FILTER_SANITIZE_EMAIL);
        })
        ->setRequired('Введите адрес электронной почты.');

        $form->addGroup('');
        $form->addCaptcha('captcha', 'Ошибка в капче. Повторите ввод.');

        $form->addGroup('');
        $form->addSubmit('send', 'Зарегистрироваться');

        return $form;
    }
}
