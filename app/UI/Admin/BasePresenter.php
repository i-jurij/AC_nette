<?php

declare(strict_types=1);

namespace App\UI\Admin;

use Nette\Security\User;

abstract class BasePresenter extends \App\UI\BasePresenter
{
    use \App\UI\Accessory\LinkFromFileSystem;
    use \App\UI\Accessory\GetKeyValueRecursive;
    // Incorporates methods to check user login status
    use \App\UI\Accessory\RequireLoggedUser;

    protected array $dirList;

    public function __construct()
    {
        parent::__construct();
        $this->dirList = $this->linkFromFileSystem(APPDIR . DIRECTORY_SEPARATOR . 'UI');
    }

    public function beforeRender()
    {
        parent::beforeRender();
        $this->template->dirList = $this->dirList;
        $this->template->currentPageMenu = $this->getKeyValueRec(end($this->template->breadcrumb)['full'], $this->dirList);
    }
}

class BaseTemplate extends \App\UI\BaseTemplate
{
    public array $dirList;
    public array $currentPageMenu;
}
