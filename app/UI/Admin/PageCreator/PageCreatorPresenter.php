<?php

declare(strict_types=1);

namespace App\UI\Admin\PageCreator;

final class PageCreatorPresenter extends \App\UI\BasePresenter
{
    use \App\UI\Accessory\RequireLoggedUser;
    use \App\UI\Accessory\LinkFromFileSystem;

    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('PageCreator')) {
            $this->error('Forbidden', 403);
        }
    }
}
