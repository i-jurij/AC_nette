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
            ->setHtmlAttribute('class', 'form');

        $form->addGroup('');
        $type_of_client = [
            'customer' => 'Заказчик',
            'executor' => 'Исполнитель',
        ];
        $input = $form->addRadioList('type_of_client', 'Вы регистрируетесь как:', $type_of_client);

        $form->addGroup('');
        $form->addCaptcha('captcha', 'Ошибка в капче. Повторите ввод.');

        $form->addGroup('');
        $form->addSubmit('send', 'Зарегистрироваться');

        return $form;
    }
}
