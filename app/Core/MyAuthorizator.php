<?php

declare(strict_types=1);

namespace App\Core;

use Nette;
use Nette\Database\Connection;

class MyAuthorizator implements Nette\Security\Authorizator
{
    public function __construct(private Connection $db)
    {
    }

    public function isAllowed($role, $resource, $action): bool
    {
        if ($role === 'admin') {
            return true;
        }
        // check if role has resource and empty action (allowed all for this resource)
        $res = [
            'role_name' => $role,
            'resource' => $resource,
            'action' => null,
        ];
        if (\in_array($res, $this->getRolePermissionData())) {
            return true;
        }
        $res_act = [
            'role_name' => $role,
            'resource' => $resource,
            'action' => $action,
        ];

        if (\in_array($res_act, $this->getRolePermissionData())) {
            return true;
        }

        return false;
    }

    private function getRolePermissionData(): array
    {
        $result = [];
        $query = 'SELECT
                        r.role_name,
                        p.resource,
                        p.action
                    FROM
                        `role` AS r
                    INNER JOIN
                        `role_permission` AS rp
                        ON r.id = rp.role_id
                    INNER JOIN
                        `permission` AS p
                        ON p.id = rp.permission_id';

        foreach ($this->db->query($query) as $row) {
            $result[] = [
                'role_name' => $row->role_name,
                'resource' => $row->resource,
                'action' => $row->action,
            ];
        }

        return $result;
    }
}
