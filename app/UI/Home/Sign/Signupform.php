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
        //        unset($form['username']);
        $form->setHtmlAttribute('id', 'signUpForm')
            ->setHtmlAttribute('name', 'signUpForm')
            ->setHtmlAttribute('class', 'form mb2 mr2 center');
        $form->addGroup('');
        $form->addGroup('');
        $form->addCaptcha('captcha', 'Ошибка в капче. Повторите ввод.');
        $form->addGroup('');
        $form->addSubmit('send', 'Зарегистрироваться');

        return $form;
    }
}