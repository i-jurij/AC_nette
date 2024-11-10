<?php

declare(strict_types=1);

namespace App\Model;

use App\UI\Accessory\RequireLoggedUser;
use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

/**
 * Manages user-related operations such as authentication and adding new users.
 */
final class RoleFacade
{
    use RequireLoggedUser;
    public Selection $table;
    public Selection $role;
    public Selection $role_permission;
    private Selection $role_user;

    public function __construct(public Explorer $sqlite)
    {
        $this->role = $this->sqlite->table('role');
        $this->role_permission = $this->sqlite->table('role_permission');
        $this->role_user = $this->sqlite->table('role_user');
    }

    public function add(array $data): string
    {
        $role = $this->role->insert(['role_name' => $data['rolename']]) or throw new \Exception('Data NOT inserted');

        if (!empty($data['permissions'][0]) && isset($role->id)) {
            foreach ($data['permissions'] as $key => $permission_id) {
                $role_permission_insert[] = ['role_id' => $role->id, 'permission_id' => $permission_id];
            }
            $role_permission = $this->role_permission->insert($role_permission_insert) or throw new \Exception('Data NOT inserted');
        }

        return $role->role_name;
    }

    public function delete(array $data): int
    {
        $role = $this->role->where('id', $data['role'])->delete() or throw new \Exception('Role(s) NOT deleted');

        if ($role > 0) {
            $roles_permissions = $this->role_permission->where('role_id', $data['role'])->delete(); // or throw new \Exception('Permissions for roles NOT deleted');
            if ($roles_permissions > 0) {
                $roles_user = $this->role_user->where('role_id', $data['role'])->delete(); // or throw new \Exception('Users roles NOT deleted');
                if ($roles_user > 0) {
                    return 3; // All deleted
                } else {
                    return 2; // 'Roles deleted. Permissions deleted. Users roles NOT deleted (or users have not been assigned these roles).'
                }
            } else {
                return 1; // 'Roles deleted. Permissions NOT deleted (or role(s) have not been permissions';
            }
        } else {
            return 0; // 'Roles NOT deleted';
        }
    }

    public function permissionsAdd(array $data): void
    {
        if (!empty($data['permissions'][0]) && !empty($data['role'])) {
            foreach ($data['permissions'] as $key => $permission_id) {
                $role_permission_insert[] = ['role_id' => $data['role'], 'permission_id' => $permission_id];
            }
            $this->role_permission->insert($role_permission_insert) or throw new \Exception('Permissions for role NOT inserted');
        }
    }

    public function permissionsDelete(array $data): void
    {
        if (!empty($data[0])) {
            foreach ($data as $value) {
                list($role_id, $permission_id) = explode('_', $value);
                $roles[] = $role_id;
                $permissions[] = $permission_id;
                // $delete[$role_id][] = $permission_id;
            }

            try {
                foreach ($this->role->where('id', $roles) as $role) {
                    $role->related('role_permission.role_id')
                        ->where('permission_id', $permissions)
                        ->delete();
                }
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }
}
