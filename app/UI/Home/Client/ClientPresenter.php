<?php

declare(strict_types=1);

namespace App\UI\Home\Client;

/**
 * @property ClientTemplate $template
 */
final class ClientPresenter extends \App\UI\Home\BasePresenter
{
    //use \App\UI\Accessory\LinkFromFileSystem;
    //use \App\UI\Accessory\GetKeyValueRecursive;
    public function renderDefault()
    {
        /*
        $dirList = $this->linkFromFileSystem(APPDIR . DIRECTORY_SEPARATOR . 'UI');
        $this->template->data = $this->getKeyValueRec(end($this->template->breadcrumb)['full'], $dirList);
        */
        $this->forward(':Home:Client:Profile:');
    }
}

class ClientTemplate extends \App\UI\Home\BaseTemplate
{
    public array $data;
}
