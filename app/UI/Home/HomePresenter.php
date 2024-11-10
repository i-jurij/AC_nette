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
