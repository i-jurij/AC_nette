<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS;

final class CMSPresenter extends \App\UI\Admin\BasePresenter
{
    use \App\UI\Accessory\LinkFromFileSystem;

    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('CMS')) {
            $this->error('Forbidden', 403);
        }
    }
}
