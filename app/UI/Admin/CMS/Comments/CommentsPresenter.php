<?php

declare(strict_types=1);

namespace App\UI\Admin\CMS\Comments;

use \App\Model\CommentFacade;
use Nette\Application\UI\Form;

final class CommentsPresenter extends \App\UI\Admin\BasePresenter
{
    public function __construct(
        protected CommentFacade $comment
    ) {
        parent::__construct();
    }
    public function renderDefault($offer_id = null)
    {
        if (!$this->getUser()->isAllowed('Comments', '')) {
            $this->error('Forbidden', 403);
        }
        $comments = [];
        if (empty($offer_id)) {
            // $this->flashMessage('Объявление не выбрано. Зайдите на эту страницу через ссылки на странице Offers');
        } else {
            $res = $this->comment->getByOffer((int) $offer_id);
            foreach ($res as $comment) {
                if (empty($comment->parent_id)) {
                    $parent_id = 0;
                } else {
                    $parent_id = $comment->parent_id;
                }
                $comments[$parent_id][] = $comment;
            }
        }

        $this->template->comments = $comments;
    }


    #[Requires(methods: 'POST', sameOrigin: true)]
    public function renderByClient(int $id): void
    {

    }

    #[Requires(sameOrigin: true)]
    public function actionEdit(int $id): void
    {
        $this->template->comment = $this->comment->get((int) $id);
    }

    public function createComponentCommentEditForm(): Form
    {
        $form = new Form();
        $form->addProtection();
        $form->setHtmlAttribute('id', 'commentEditForm')
            ->setHtmlAttribute('class', 'form');

        $form->addTextArea('comment_text')
            ->setDefaultValue($this->template->comment->comment_text)
            ->setHtmlAttribute('rows', '4')
            ->setHtmlAttribute('cols', '60')
            ->addCondition($form::Length, [8, 500])
            ->addRule($form::Pattern, 'Только буквы, цифры и знаки препинания', '^[\p{L}\d ?!,.-_~\":;!]+$')
            ->setRequired();

        $form->addHidden('id')->setDefaultValue($this->template->comment->id)->setRequired();
        $form->addHidden('offer_id')->setDefaultValue($this->template->comment->offer_id)->setRequired();
        $form->addSubmit('updateComment', 'Update');
        $form->onSuccess[] = [$this, 'update'];

        return $form;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function update(Form $form, $data): void
    {
        if (!$this->getUser()->isAllowed('Comments', 'update')) {
            $this->error('Forbidden', 403);
        }
        $res = $this->comment->update((int) $data->id, htmlspecialchars(strip_tags($data->comment_text)));
        if ($res === true) {
            $this->flashMessage('Updated', 'success');
        } else {
            $this->flashMessage('Not updated. Something wrong...', 'error');
        }
        $this->redirect('default', (int) $data->offer_id);
    }

    #[Requires(sameOrigin: true)]
    public function handleDelete($id): void
    {
        if (!$this->getUser()->isAllowed('Comments', 'delete')) {
            $this->error('Forbidden', 403);
        }
        $res = $this->comment->delete((int) $id);
        if ($res === 1) {
            $this->flashMessage('Deleted', 'success');
        } else {
            $this->flashMessage('Not deleted. Something wrong...', 'error');
        }

        $this->redirect('this');
    }
}
