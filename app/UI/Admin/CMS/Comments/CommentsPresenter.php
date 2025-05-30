<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Comments;

final class CommentsPresenter extends \App\UI\Admin\BasePresenter
{
    public function renderDefault()
    {
        if (!$this->getUser()->isAllowed('Comments', '')) {
            $this->error('Forbidden', 403);
        }
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function renderByClient(int $id): void
    {

    }

    #[Requires(sameOrigin: true)]
    public function renderEdit(int $id): void
    {

    }
    #[Requires(sameOrigin: true)]
    public function actionDelete(int $id): void
    {

    }
}
