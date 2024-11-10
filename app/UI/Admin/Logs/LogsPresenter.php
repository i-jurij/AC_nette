<?php

declare(strict_types=1);

namespace App\UI\Admin\Logs;

use Nette;
use Nette\Utils\FileSystem;
use Nette\Utils\Finder;

/**
 * @property LogsTemplate $template
 */
final class LogsPresenter extends \App\UI\BasePresenter
{
    use \App\UI\Accessory\RequireLoggedUser;
    use \App\UI\Accessory\HumanSize;
    use \App\UI\Accessory\ClearFile;

    private string $log_directory = APPDIR.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'log';

    public function renderDefault(int $page = 1)
    {
        if ($this->getUser()->isAllowed('Logs', 'list')) {
            foreach (Finder::findFiles(['*.log', '*.html'])->in($this->log_directory) as $log) {
                $size = $log->getSize();
                $message = '';
                if ($size > 1048576 * 10) {
                    $message = 'File is bigger then 10Mb. You can clear it before open (last ten entries will be left)';
                }

                if ($log->getExtension() == 'log') {
                    $this->template->logs[] = [
                        'name' => $log->getFilename(),
                        'realpath' => $log->getRealPath(),
                        'modification_time' => $log->getMTime(),
                        'size' => $this->formatBytes($size, 2),
                        'message' => $message,
                    ];
                }

                if ($log->getExtension() == 'html') {
                    $tracys[] = [
                        'name' => $log->getFilename(),
                        'realpath' => $log->getRealPath(),
                        'modification_time' => $log->getMTime(),
                        'size' => $this->formatBytes($size, 2),
                        'message' => $message,
                    ];
                }
            }
            if (!empty($tracys)) {
                $time = array_column($tracys, 'modification_time');
                array_multisort($time, SORT_DESC, $tracys);

                $paginator = new Nette\Utils\Paginator();
                $paginator->setItemCount(count($tracys));
                $paginator->setItemsPerPage(10);
                $paginator->setPage($page);

                $this->template->tracys = array_slice($tracys, $paginator->getOffset(), $paginator->getLength(), true);

                $this->template->paginator = $paginator;
            }
        } else {
            $this->flashMessage('You don\'t have permission for this', 'text-warning');
            $this->redirect(':Admin:');
        }
    }

    public function actionShow($name, int $page = 1)
    {
        if ($this->getUser()->isAllowed('Logs', 'show')) {
            $this->template->name = $name;

            if (\is_readable($this->log_directory.DIRECTORY_SEPARATOR.$name)) {
                if (pathinfo($this->log_directory.DIRECTORY_SEPARATOR.$name, PATHINFO_EXTENSION) == 'log') {
                    $lines = array_reverse(file($this->log_directory.DIRECTORY_SEPARATOR.$name), true);

                    $paginator = new Nette\Utils\Paginator();
                    $paginator->setItemCount(count($lines));
                    $paginator->setItemsPerPage(10);
                    $paginator->setPage($page);

                    $this->template->lines = array_slice($lines, $paginator->getOffset(), $paginator->getLength(), true);

                    $this->template->paginator = $paginator;
                } elseif (pathinfo($this->log_directory.DIRECTORY_SEPARATOR.$name, PATHINFO_EXTENSION) == 'html') {
                    $this->template->setFile($this->log_directory.DIRECTORY_SEPARATOR.$name);
                }
            } else {
                $this->flashMessage('You don\'t have permission for this', 'text-warning');
                $this->redirect(':Admin:Logs:');
            }
        } else {
            $this->flashMessage('The logs file is not readable or not exist');
            $this->redirect(':Admin:Logs:');
        }
    }

    public function actionClear($name)
    {
        if ($this->clearFile($this->log_directory.DIRECTORY_SEPARATOR.$name, 30)) {
            $this->flashMessage('Success! Log "'.$name.'" cleared', 'text-success');
            $this->redirectPermanent(':Admin:Logs:');
        } else {
            $this->flashMessage('Error! Log "'.$name.'" was NOT cleared.', 'text-danger');
            $this->redirectPermanent(':Admin:Logs:');
        }
    }

    public function actionDelete($name)
    {
        try {
            FileSystem::delete($this->log_directory.DIRECTORY_SEPARATOR.$name);
            $this->flashMessage('Success! Tracy log "'.$name.'" deleted', 'text-success');
        } catch (\Throwable $th) {
            $this->flashMessage($th->getMessage().PHP_EOL
                 .'Trace: '.$th->getTraceAsString().PHP_EOL, 'text-danger');
        }
        $this->redirectPermanent(':Admin:Logs:');
    }

    public function actionDeleteAll()
    {
        try {
            foreach (Finder::findFiles(['*.html'])->in($this->log_directory) as $tracy) {
                FileSystem::delete($tracy->getRealPath());
            }

            $this->flashMessage('Success! All tracy logs deleted', 'text-success');
        } catch (\Throwable $th) {
            $this->flashMessage($th->getMessage().PHP_EOL
                 .'Trace: '.$th->getTraceAsString().PHP_EOL, 'text-danger');
        }
        $this->redirectPermanent(':Admin:Logs:');
    }
}
/*
class LogsTemplate extends \App\Ui\BaseTemplate
{
    public Nette\Security\User $user;
    public string $basePath;
    public string $baseUrl;
    public array $flashes;
    public object $presenter;
    public object $control;
    public array $pages_data;
}
*/
