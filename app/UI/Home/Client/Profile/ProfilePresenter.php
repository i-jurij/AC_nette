<?php

declare(strict_types=1);

namespace App\UI\Home\Client\Profile;

use App\Model\UserFacade;
use App\UI\Accessory\IsBot;
use Ijurij\Geolocation\Lib\Csrf;
use Nette\Application\UI\Form;

/**
 * @property ProfileTemplate $template
 */
final class ProfilePresenter extends \App\UI\Home\BasePresenter
{
    // use \App\UI\Accessory\RequireLoggedUser;

    // Dependency injection of form factory and user management facade
    public function __construct(
        protected UserFacade $userfacade,
    ) {
        parent::__construct();
        $this->onStartup[] = function () {
            if (IsBot::check()) {
                $this->redirect(':Home:');
            }
        };
    }

    public function renderDefault()
    {
        $this->template->data = 'Client profile';
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
            $id = $this->userfacade->searchBy('auth_token', $auth_token)->id;
            $data['id'] = $id;
            $data['password'] = $httpRequest->getPost('password');
            $this->userfacade->update($id, $data);

            $this->flashMessage('Теперь введите ваш новый пароль для входа.', 'info');
            $this->redirect(':Home:Sign:in');
        } else {
            $this->error();
        }
    }
}

class ProfileTemplate extends \App\UI\Home\BaseTemplate
{
    public string $data;
    public string $user_token;
    public string $csrf_name;
    public string $csrf;
}
