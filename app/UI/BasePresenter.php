<?php

declare(strict_types=1);

namespace App\UI;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    use Accessory\Breadcrumb;

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->breadcrumb = $this->getBC();
        $this->template->shared_templates = APPDIR.DIRECTORY_SEPARATOR.'UI'.DIRECTORY_SEPARATOR.'shared_templates'.DIRECTORY_SEPARATOR;
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
