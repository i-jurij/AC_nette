<?php

declare(strict_types=1);

namespace App\UI\Admin;

use App\UI\Accessory\IsBot;

/**
 * @property AdminTemplate $template
 */
final class AdminPresenter extends BasePresenter
{
    use \App\UI\Accessory\CacheCleaner;
    public function __construct()
    {
        parent::__construct();
        $this->onStartup[] = function () {
            if (IsBot::check()) {
                $this->redirect(':Home:');
            }
        };
    }
}

class AdminTemplate extends BaseTemplate
{
    public string $basePath;
    public string $baseUrl;
    public array $flashes;
    public object $presenter;
    public object $control;
}
