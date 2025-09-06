<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Nette\Application\UI\Form;
// use Nette\Localization\Translator;
use Nette\Forms\Container;
use Nette\Security\User;

/**
 * Factory for creating general forms with optional CSRF protection.
 */
final class FormFactory
{
    protected $form;

    /**
     * Dependency injection of the current user session.
     */
    public function __construct(
        // private Translator $translator,
        private User $user
    ) {
        $this->form = new Form();
    }

    private function phoneAdd()
    {
        Container::extensionMethod('addPhone', function (Container $form, string $name, ?string $label = null) {
            return $form->addText($name, $label)
                ->addRule($form::Pattern, 'Введен неправильный номер', PhoneNumber::PHONE_REGEX);
        });
        $this->form->addPhone('phone', '')
            //->setRequired('Телефон обязателен.')
            ->setHtmlType('tel')
            ->setHtmlAttribute('placeholder', '☎ +7 999 333 22 22') // 📱
            ->setHtmlAttribute('id', 'user_phone_input');
    }

    private function nameAdd()
    {
        $this->form->addText('username', '')
            ->setHtmlAttribute('placeholder', '👤 Имя:')
            // ->setRequired('Имя обязательно.')
            ->addRule($this->form::MinLength, 'Имя длиной не менее %d символов', 3)
            ->addRule($this->form::Pattern, 'Имя только из букв, цифр, пробелов, дефисов и подчеркиваний', '^[a-zA-Zа-яА-ЯёЁ0-9\-_ ]{3,125}$')
            ->setMaxLength(125);
    }

    private function emailAdd()
    {
        $this->form->addEmail('email', '')
            ->setHtmlAttribute('placeholder', '📧 Email:')
            ->addRule($this->form::Email, 'Введите правильный адрес электронной почты.')
            ->addFilter(function ($value) {
                return filter_var($value, FILTER_SANITIZE_EMAIL);
            });
        ;
    }

    private function passwordAdd()
    {
        $this->form->addPassword('password', '')
            ->setHtmlAttribute('placeholder', '🔒 Пароль:')
            ->setRequired('Пароль обязателен.')
            ->addRule($this->form::MinLength, 'Пароль длиной не менее %d символов', PASSWORD_MIN_LENGTH)
            ->setMaxLength(120);
    }

    /**
     * Create a new form instance. If user is logged in, add CSRF protection.
     */
    public function create(): Form
    {
        // $form->setTranslator($this->translator);
        $renderer = $this->form->getRenderer();
        $renderer->wrappers['group']['container'] = 'div class="my1 mx-auto pb2 px2"';
        $renderer->wrappers['controls']['container'] = 'div';
        $renderer->wrappers['pair']['container'] = 'div';
        $renderer->wrappers['label']['container'] = null;
        $renderer->wrappers['control']['container'] = null;

        return $this->form;
    }

    public function createLoginForm(): Form
    {
        $form = $this->create();
        $form->addProtection('Csrf error');
        // $form->addGroup('--- 👥 ---');
        $form->addGroup('');
        $this->nameAdd();
        $form['username']->setRequired();
        // $this->phoneAdd();
        $this->passwordAdd();

        return $form;
    }

    public function createHomeLoginForm(): Form
    {
        $form = $this->create();
        $form->addProtection('Ошибка. Почистите куки, закройте вкладку и откройте ее снова');
        $form->addGroup('');
        $this->nameAdd();
        $this->phoneAdd();
        $this->emailAdd();
        $this->passwordAdd();

        return $form;
    }

    public function createClientRatingForm()
    {
        $form = $this->create();
        $form->addProtection('Ошибка. Почистите куки, закройте вкладку и откройте ее снова');

        $form->setHtmlAttribute('id', 'client_rating_form')
            ->setHtmlAttribute('class', 'center mb2 mr2');

        $form->addGroup('');
        $form->addRadioList('rating_value', null, [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5])
            ->setRequired('Пожалуйста, выберите оценку');

        $form->addGroup('');
        $form->addHidden('client_id_who', null);
        $form->addHidden('client_id_to_whom', null);
        $form->addSubmit('client_rating_form_submit', 'Оценить ');

        return $form;
    }
}