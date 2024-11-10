<?php

declare(strict_types=1);

namespace App\UI\Admin;

use Nette\Security\User;

/**
 * @property AdminTemplate $template
 */
final class AdminPresenter extends \App\UI\BasePresenter
{
    // Incorporates methods to check user login status
    use \App\UI\Accessory\RequireLoggedUser;
    use \App\UI\Accessory\CacheCleaner;
    use \App\UI\Accessory\LinkFromFileSystem;

    public function renderDefault()
    {
    }
}

class AdminTemplate extends \App\UI\BaseTemplate
{
    public User $user;
    public string $basePath;
    public string $baseUrl;
    public array $flashes;
    public object $presenter;
    public object $control;
    public array $sql;
    public array $cmsList;
}
