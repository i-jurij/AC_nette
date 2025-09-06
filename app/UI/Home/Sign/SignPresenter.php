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
use Nette\Security\SimpleIdentity;

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

    private function userLogin(Form $form, \stdClass $data): void
    {
        usleep(200000);
        try {
            $user = $this->getUser();
            if (!empty($data->username)) {
                $user_pen = htmlspecialchars($data->username);
            } elseif (!empty($data->phone)) {
                $user_pen = $this->cf->searchBy('phone', PhoneNumber::toDb($data->phone), true)->username;
            } elseif (!empty($data->email)) {
                $user_pen = $this->cf->searchBy('email', htmlspecialchars($data->email))->username;
            }

            if (!empty($user_pen)) {
                $user->login($user_pen, $data->password);
                $this->restoreRequest($this->backlink);
                $this->redirect(':Home:');
            } else {
                $form->addError('Необходимо ввести имя, телефон или почту');
            }
        } catch (Nette\Security\AuthenticationException $e) {
            sleep(1);
            // save failed login data to db for processing (name, phone, password, ip, user agent, referer etc, time)
            $form->addError('Произошла ошибка. Проверьте правильность вводимых данных и попробуйте снова');
        }
    }

    public function actionVklogin(): void
    {
        $user_data = $this->getUserDataVK();
        $this->oauthLogin($user_data);
        $this->restoreRequest($this->backlink);
        $this->redirect(':Home:');
    }

    /*
        public function actionYLTest(): void
        {
            $user_data['data'] = [
                'email' => 'vvvvv@gmail.com',
                // 'phone' => '+7 (944) 509 14 56',
                'username' => 'bbbbbb'
            ];
            $this->oauthLogin($user_data);
            $this->restoreRequest($this->backlink);
            $this->redirect(':Home:');
        }
    */
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
        /*
        $form->onValidate[] = function (Form $form) {
            $values = $form->getValues();
            if (empty($values->username) && empty($values->phone) && empty($values->email)) {
                $form->addError('Укажите хотя бы одно: имя пользователя, телефон или email.');
            }
        };
        */
        $form->onSuccess[] = $this->processSignUpForm(...);

        return $form;
    }

    private function processSignUpForm(Form $form, \stdClass $data): void
    {
        $data->roles = 'client';
        $res = $this->cf->add($data);
        if (is_array($res)) {
            unset($res[1][$this->cf::ColumnPasswordHash]);
            $roles = $this->cf->getRoless($res[$this->cf::ColumnId]);
            $identity = new SimpleIdentity($res[$this->cf::ColumnId], $roles, $res);
            $this->flashMessage('Вы зарегистрированы: имя "' . $res['username'] . '", пароль "' . $data->password . '"', 'success');
            $this->getUser()->login($identity);
            $this->redirect(':Home:default');
        } elseif (is_string($res)) {
            $form->addError($res);
        }
    }

    public function actionOut(): void
    {
        $this->getUser()->logout(true);
        // $this->flashMessage('Log out');
        $this->redirect(':Home:');
    }

    #[Nette\Application\Attributes\Requires(methods: 'POST', sameOrigin: true)]
    public function actionCheckPhoneInDb(): void
    {
        $resp = '';
        $httpRequest = $this->getHttpRequest();
        $username = $httpRequest->getPost('username');
        $phone = $httpRequest->getPost('phone');
        $email = $httpRequest->getPost('email');

        if (!empty($username)) {
            $res_username = $this->cf->searchBy('username', $username, true);
            if (!empty($res_username->id)) {
                $resp = $resp . " Пользователь с таким именем уже существует.";
            }
        }

        if (!empty($phone)) {
            $res_phone = $this->cf->searchBy('phone', $phone, true);
            if (!empty($res_phone->id)) {
                $resp = $resp . " Пользователь с таким номером телефона уже существует.";
            }
        }

        if (!empty($email)) {
            $res_email = $this->cf->searchBy('email', $email, true);
            if (!empty($res_email->id)) {
                $resp = $resp . " Пользователь с таким адресом электронной почты уже существует.";
            }
        }


        $this->sendJson($resp);
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

    /**
     * The `postRestore` function in PHP filters and validates an email address, sends a password
     * restoration link via email if the email exists in the database, and handles error messages
     * accordingly.
     * 
     * @param Form form The `postRestore` function you provided seems to be handling a form submission for
     * restoring a password. The function takes two parameters: `` of type `Form` and `` of type
     * `\stdClass`.
     * @param \stdClass data The `data` parameter in the `postRestore` function is expected to be an object
     * of type `\stdClass`. It is used to retrieve the email address for password restoration. The email
     * address is then sanitized and validated before further processing. If the email is valid and
     * associated with an existing user, a
     */
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