<?php

declare(strict_types=1);

namespace App\UI;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{
    use Accessory\Breadcrumb;
    use Accessory\LinkFromFileSystem;
    use Accessory\GetKeyValueRecursive;

    protected array $dirList;

    public function __construct()
    {
        parent::__construct();
        $this->dirList = $this->linkFromFileSystem(APPDIR.DIRECTORY_SEPARATOR.'UI');
    }

    public function beforeRender()
    {
        $this->template->breadcrumb = $this->getBC();
        $this->template->dirList = $this->dirList;
        $this->template->currentPageMenu = $this->getKeyValueRec(end($this->template->breadcrumb)['full'], $this->dirList);
        $this->template->shared_templates = APPDIR.DIRECTORY_SEPARATOR.'UI'.DIRECTORY_SEPARATOR.'shared_templates'.DIRECTORY_SEPARATOR;
    }
}

class BaseTemplate extends \Nette\Bridges\ApplicationLatte\Template
{
    public array $breadcrumb;
    public array $dirList;
    public array $currentPageMenu;
    public string $shared_templates;
}
