<?php

declare(strict_types=1);

namespace App\UI\Admin\Cache;

use App\UI\Accessory\CacheCleaner;
use App\UI\Accessory\ErrorFlashMessage;
use App\UI\Accessory\RequireLoggedUser;
use Nette\Security\User;
use Nette\Utils\FileSystem;

/**
 * @property CacheTemplate $template
 */
final class CachePresenter extends \App\UI\BasePresenter
{
    // Incorporates methods to check user login status
    use RequireLoggedUser;
    use CacheCleaner;
    use ErrorFlashMessage;

    #[Requires(methods: 'GET')]
    public function renderDefault(): void
    {
        if ($this->getUser()->isAllowed('Cache', 'list')) {
            $csrf = random_int(PHP_INT_MIN, PHP_INT_MAX);

            $this->getHttpResponse()->setCookie('csrf', "$csrf", '30');

            $this->template->data = $this->getFileList(CACHE_DIR);
            $this->template->csrf = $csrf;
        } else {
            $this->flashMessage('You don\'t have permission for this', 'text-warning');
            $this->redirect(':Admin:');
        }
    }

    #[Requires(methods: 'POST')]
    public function actionClear(): void
    {
        if (!$this->getUser()->isAllowed('Cache', 'clear')) {
            $this->error('Forbidden', 403);
        }
        try {
            $data = $this->getHttpRequest()->getPost('clear_cache');
            $csrf = $this->getHttpRequest()->getPost('csrf');
            $cookie_csrf = $this->getHttpRequest()->getCookie('csrf');
            $this->getHttpResponse()->deleteCookie('csrf');
            $i = 0;

            if (!empty($data) && $csrf == $cookie_csrf) {
                foreach ($data as $file) {
                    FileSystem::delete($file);
                    ++$i;
                }
            }

            $this->flashMessage("Cache success remove. Remove {$i} files.");
        } catch (\Throwable $e) {
            $this->flashMessage($this->erMes($e), 'text-danger');
        }

        $this->redirect(':Admin:Cache:default');
    }

    #[Requires(methods: 'POST')]
    public function actionClearAll(): void
    {
        if (!$this->getUser()->isAllowed('Cache', 'clear')) {
            $this->error('Forbidden', 403);
        }
        $csrf = $this->getHttpRequest()->getPost('csrf');
        $cookie_csrf = $this->getHttpRequest()->getCookie('csrf');
        $this->getHttpResponse()->deleteCookie('csrf');

        if ($csrf == $cookie_csrf) {
            try {
                $count = $this->cleanCache(CACHE_DIR) ?? 0;
                // $this->flashMessage("All cached files was removed. Remove {$count} files.");
            } catch (\Throwable $th) {
                $this->flashMessage($this->erMes($th), 'text-danger');
            }
        }

        $this->redirect(':Admin:Cache:default');
    }
}
