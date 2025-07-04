<?php

namespace App\UI\Home\Sign;

use App\Model\ClientFacade;
use App\UI\Accessory\Email;
use App\UI\Accessory\FormFactory;
use App\UI\Accessory\IsBot;
use App\UI\Accessory\PhoneNumber;
use Ijurij\Geolocation\Lib\Csrf;
use Nette;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Form;

final class SignPresenter extends \App\UI\BasePresenter
{
    use \App\UI\Accessory\YandexLogin;
    use \App\UI\Accessory\TelegramLogin;
    use \App\UI\Accessory\VKLogin;
    use \App\UI\Accessory\OauthLogin;
    /**
     * Stores the previous page hash to redirect back after successful login.
     */
    #[Persistent]
    public string $backlink = '';

    // Dependency injection of form factory and user management facade
    public function __construct(
        private FormFactory $formFactory,
        protected ClientFacade $cf,
    ) {
        $this->onStartup[] = function () {
            if (IsBot::check()) {
                $this->redirect(':Home:');
            }
        };
    }

    protected function createComponentSignInForm(): Form
    {
        $form = $this->formFactory->createHomeLoginForm();
        $form->setHtmlAttribute('id', 'log_in_app')
            ->setHtmlAttribute('class', 'form center mb2 mr2');

        $form->addGroup('');
        $form->addCaptcha('captcha', 'Ошибка в капче. Повторите ввод.');

        $form->addGroup('');
        $form->addSubmit('send', 'Войти');

        $form->onSuccess[] = $this->userLogin(...);

        return $form;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    private function userLogin(Form $form, \stdClass $data): void
    {
        usleep(200000);
        try {
            $user = $this->getUser();
            if (!empty($data->username)) {
                $user->login($data->username, $data->password);
            }
            if (!empty($data->phone)) {
                $username = $this->cf->searchBy('phone', PhoneNumber::toDb($data->phone), true)->username;
                $user->login($username, $data->password);
            }

            $this->restoreRequest($this->backlink);

            $this->redirect(':Home:');
        } catch (Nette\Security\AuthenticationException $e) {
            sleep(1);
            // save failed login data to db for processing (name, phone, password, ip, user agent, referer etc, time)
            $form->addError('Wrong login or password.');
        }
    }

    /*
    public function actionYLTest(): void
    {
        $user_data['data'] = [
            'email' => 'vvvvv@gmail.com',
            'phone' => '+7 (944) 509 14 56',
        ];
        $this->oauthLogin($user_data);
        $this->restoreRequest($this->backlink);
        $this->redirect(':Home:');
    }
*/

    public function actionVklogin(): void
    {
        $user_data = $this->getUserDataVK();
        $this->oauthLogin($user_data);
        $this->restoreRequest($this->backlink);
        $this->redirect(':Home:');
    }

    public function actionYandexLogin(): void
    {
        $user_data = $this->getUserDataYandex();
        $this->oauthLogin($user_data);
        $this->restoreRequest($this->backlink);
        $this->redirect(':Home:');
    }

    public function actionTelegramLogin(): void
    {
        $user_data = $this->getUserDataTelegram();
        $this->oauthLogin($user_data);
        $this->restoreRequest($this->backlink);
        $this->redirect(':Home:');
    }

    public function renderIn()
    {
        $this->template->yandexLoginUrl = $this->yandexLoginUrl();
        $this->template->vkLoginUrl = $this->vkLoginUrl();
    }

    public function renderUp()
    {
        $this->template->yandexLoginUrl = $this->yandexLoginUrl();
        $this->template->vkLoginUrl = $this->vkLoginUrl();
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
        $res = $this->cf->add($data);
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
        $res = $this->cf->searchBy('phone', $phone);
        if (!empty($res->id)) {
            $this->sendJson(1);
        }
        $this->sendJson(0);
    }

    protected function createComponentRestoreForm(): Form
    {
        $form = $this->formFactory->create();
        $form->setHtmlAttribute('id', 'restore_password_form')
            ->setHtmlAttribute('class', 'form center mx-auto');

        $form->addGroup('');
        $form->addEmail('email', '')
            ->setHtmlAttribute('placeholder', '📧 Email:')
            ->setRequired('Введите адрес электронной почты.')
            ->addRule($form::Email, 'Введите правильный адрес электронной почты.');

        $form->addGroup('');
        $form->addCaptcha('captcha', 'Ошибка в капче. Повторите ввод.');

        $form->addGroup('');
        $form->addSubmit('send', 'Отправить');

        $form->onSuccess[] = $this->postRestore(...);

        return $form;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function postRestore(Form $form, \stdClass $data): void
    {
        $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $res = $this->cf->searchBy('email', $email);
            if (!empty($res->auth_token)) {
                // create or get and receive passsword or url with token to email;
                $this->absoluteUrls = true;
                $redirect_url = $this->link(':Home:Sign:restorelink') . '?token=' . $res->auth_token . '&' . Csrf::$token_name . '=' . Csrf::getToken();

                $mail = new Email();
                $mail->from = 'webmaster@' . SITE_NAME;
                $mail->to = $email;
                $mail->subject = 'Restore password';
                $mail->body = $redirect_url;
                try {
                    $mail->sendEmail();

                    $this->flashMessage('На указанный вами адрес электронной почты отправлено письмо.', 'success');

                } catch (\Throwable $th) {
                    // $this->flashMessage($th->getMessage() . PHP_EOL . 'Trace: ' . $th->getTraceAsString() . PHP_EOL, 'error');
                }
                $this->redirect(':Home:Sign:in');
            } else {
                $this->flashMessage('Пользователь с таким адресом электронной почты не зарегистрирован.', 'info');
                $this->redirect(':Home:Sign:up');
            }
        } else {
            $this->flashMessage('Адрес электронной почты указан неверно.', 'error');
            $this->redirect(':Home:Sign:restore');
        }
    }

    public function actionRestorelink(): void
    {
        if (Csrf::isValid() && Csrf::isRecent()) {
            $token = \filter_input(INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->redirect(':Home:Client:Profile:restorePassword', $token);
        } else {
            $this->error();
        }
    }
}

class SignTemplate extends \App\UI\Home\BaseTemplate
{
    public string $yandexLoginUrl;
    public string $vkLoginUrl;
}
