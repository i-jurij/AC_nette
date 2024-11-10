<?php

declare(strict_types=1);

namespace App\UI\Home;

// use App\Model\PageFacade;
use Nette;

/**
 * @property HomeTemplate $template
 */
final class HomePresenter extends \App\UI\BasePresenter
{
    // use \App\UI\Accessory\LinkFromFileSystem;
    /*
        public function __construct(private PageFacade $db_pages)
        {
        }
    */
    public function renderDefault()
    {
        /*
        $db_data = $this->db_pages->getPagesData();
        if (count($db_data) > 0) {
            $this->template->pages_data = $db_data;
        }
            */
        // $this->template->menuList = $this->linkFromFileSystem(__DIR__);
    }
}
class HomeTemplate extends \App\UI\BaseTemplate
{
    public Nette\Security\User $user;
    public string $basePath;
    public string $baseUrl;
    public array $flashes;
    public object $presenter;
    public object $control;
    public array $pages_data;
    // public array $menuList;
}
