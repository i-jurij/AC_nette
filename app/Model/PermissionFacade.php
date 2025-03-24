<?php

declare(strict_types=1);

namespace App\Model;

use App\UI\Accessory\RequireLoggedUser;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;
use Nette\Utils\Finder;
use Nette\Utils\Strings;

/**
 * Manages user-related operations such as authentication and adding new users.
 */
final class PermissionFacade
{
    use RequireLoggedUser;
    public Selection $table;

    public function __construct(public Explorer $db)
    {
        $this->table = $this->db->table('permission');
    }

    public function actionListFromModelDir(): array
    {
        foreach (Finder::findFiles('*Facade.php')->in(APPDIR . DIRECTORY_SEPARATOR . 'Model') as $name => $file) {
            $model = \pathinfo($name, PATHINFO_FILENAME);
            $resource = Strings::before($model, 'Facade', 1);
            if (\class_exists('App\Model\\' . $model)) {
                foreach (\get_class_methods('App\Model\\' . $model) as $key => $action) {
                    if (!in_array($action, ['__construct', 'token', 'getColumns', 'injectRequireLoggedUser', 'shortAdd'])) {
                        $actions[$resource][$key] = $action;
                    }
                }
            }
        }

        return $actions;
    }

    public function presenterList(): array
    {
        if (\class_exists('Nette\Application\UI\Presenter')) {
            foreach (\get_class_methods('Nette\Application\UI\Presenter') as $action) {
                $presenter[] = $action;
            }
        }

        foreach (Finder::findFiles('*Presenter.php')->from(APPDIR . DIRECTORY_SEPARATOR . 'UI') as $name => $file) {
            $path_parts = pathinfo($name);
            $name = $path_parts['filename'];
            $path = Strings::after($path_parts['dirname'], 'UI/', 1);

            $namespace = implode('\\', explode(DIRECTORY_SEPARATOR, $path)) . '\\';
            $resource = Strings::before($name, 'Presenter', 1);
            $class = 'App\UI\\' . $namespace . $name;
            if (\class_exists($class)) {
                foreach (\get_class_methods($class) as $action) {
                    if (!in_array($action, $presenter) && $action !== 'injectRequireLoggedUser') {
                        $actions[$resource][] = $action;
                    }
                }
            }
        }

        return $actions;
    }

    public function list()
    {
        $existed_permissions = [];
        foreach ($this->table as $data) {
            $existed_permissions[$data->resource][] = $data->action;
        }

        return $existed_permissions;
    }

    public function add($data): void
    {
        try {
            $insert = [];
            if (\is_array($data['action'])) {
                foreach ($data['action'] as $value) {
                    $insert[] = [
                        'resource' => $data['resource'],
                        'action' => $value,
                    ];
                }
            } elseif (is_string($data['action'])) {
                $insert = [
                    'resource' => $data['resource'],
                    'action' => $data['action'],
                ];
            }

            $this->table->insert($insert);
        } catch (\Exception $e) {
            throw new \Exception();
        }
    }

    public function delete($ids_array): void
    {
        try {
            $this->table->where('id', $ids_array)->delete();
        } catch (\Exception $e) {
            throw new \Exception();
        }
    }
}
