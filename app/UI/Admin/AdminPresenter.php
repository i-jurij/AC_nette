<?php

declare(strict_types=1);

namespace App\UI\Admin;

/**
 * @property AdminTemplate $template
 */
final class AdminPresenter extends BasePresenter
{
    use \App\UI\Accessory\CacheCleaner;
}

class AdminTemplate extends BaseTemplate
{
    public string $basePath;
    public string $baseUrl;
    public array $flashes;
    public object $presenter;
    public object $control;
}
