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
        $this->form->addPhone('phone', 'Телефон:')
            ->setHtmlType('tel')
            ->setHtmlAttribute('placeholder', '+7 999 333 22 22')
            ->setHtmlAttribute('id', 'user_phone_input')
            ->setRequired('Телефон обязателен.');
    }

    private function nameAdd()
    {
        $this->form->addText('username', 'Имя:')
            ->setHtmlAttribute('placeholder', 'Имя:')
            ->setRequired('Имя обязательно.')
            ->addRule($this->form::MinLength, 'Имя длиной не менее %d символов', 3)
            ->addRule($this->form::Pattern, 'Имя только из букв, цифр, дефисов и подчеркиваний', '^[a-zA-Zа-яА-ЯёЁ0-9\-_]{3,25}$')
            ->setMaxLength(25);
    }

    private function passwordAdd()
    {
        $this->form->addPassword('password', 'Пароль:')
            ->setHtmlAttribute('placeholder', 'Пароль:')
            ->setRequired('Пароль обязателен.')
            ->addRule($this->form::MinLength, 'Пароль длиной не менее %d символов', PASSWORD_MIN_LENGTH)
            ->setMaxLength(120);
    }

    /**
     * Create a new form instance. If user is logged in, add CSRF protection.
     */
    public function create(): Form
    {
        $this->form->addProtection();
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
        // $form->addGroup('--- 👥 ---');
        $form->addGroup('');
        $this->nameAdd();
        // $this->phoneAdd();
        $this->passwordAdd();

        return $form;
    }

    public function createHomeLoginForm(): Form
    {
        $form = $this->create();
        $form->addGroup('');
        $this->phoneAdd();
        $this->passwordAdd();

        return $form;
    }
}
