<?php

declare(strict_types=1);

namespace App\UI\Accessory;

use Nette\Application\UI\Form;
// use Nette\Localization\Translator;
use Nette\Security\User;

/**
 * Factory for creating general forms with optional CSRF protection.
 */
final class FormFactory
{
    /**
     * Dependency injection of the current user session.
     */
    public function __construct(
        // private Translator $translator,
        private User $user
    ) {
    }

    /**
     * Create a new form instance. If user is logged in, add CSRF protection.
     */
    public function create(): Form
    {
        $form = new Form();
        $form->addProtection();
        // $form->setTranslator($this->translator);

        return $form;
    }

    public function createLoginForm(): Form
    {
        $form = $this->create();
        $renderer = $form->getRenderer();
        $renderer->wrappers['group']['container'] = 'div class="my1 mx-auto pb2 px2"';
        $renderer->wrappers['controls']['container'] = 'div';
        $renderer->wrappers['pair']['container'] = 'div';
        $renderer->wrappers['label']['container'] = null;
        $renderer->wrappers['control']['container'] = null;

        // $form->addGroup('--- 👥 ---');
        $form->addGroup('');
        $form->addText('username', 'Username:')
            ->setHtmlAttribute('placeholder', 'Username:')
            ->setRequired('Username is required.')
            ->addRule($form::MinLength, 'Имя длиной не менее %d символов', 3)
            ->addRule($form::Pattern, 'Имя только из букв, цифр, дефисов и подчеркиваний', '^[a-zA-Zа-яА-ЯёЁ0-9\-_]{3,25}$')
            ->setMaxLength(25);

        $form->addPassword('password', 'Password:')
            ->setHtmlAttribute('placeholder', 'Password:')
            ->setRequired('Password is required.')
            ->addRule($form::MinLength, 'Пароль длиной не менее %d символов', PASSWORD_MIN_LENGTH)
            ->setMaxLength(120);

        return $form;
    }
}
