<?php

namespace App\UI;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    use Accessory\Breadcrumb;
    use Accessory\BanClient;

    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->shared_templates = (string) APPDIR . DIRECTORY_SEPARATOR . 'UI' . DIRECTORY_SEPARATOR . 'shared_templates' . DIRECTORY_SEPARATOR;
        if ($this->isBanned()) {
            // $this->error();
            $this->template->setFile($this->template->shared_templates . 'banned.latte');
            $this->sendTemplate();
        }
        $this->template->breadcrumb = $this->getBC();
    }
}

class BaseTemplate extends \Nette\Bridges\ApplicationLatte\Template
{
    public \Nette\Security\User $user;
    public string $basePath;
    public string $baseUrl;
    public array $flashes;
    public object $presenter;
    public object $control;
    public array $breadcrumb;
    public string $shared_templates;
}
