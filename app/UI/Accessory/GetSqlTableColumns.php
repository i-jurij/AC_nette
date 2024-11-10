<?php

namespace App\UI\Accessory;

class GetSqlTableColumns
{
    public function getColumns($connection, $table)
    {
        $q = $connection->query("PRAGMA table_info($table)");
        while ($column = $q->fetch()) {
            $res[$column['name']] = $column['type'];
        }

        return $res;
    }

    public function getMysqlColumns($connection, $table)
    {
        $q = $connection->query("DESCRIBE $table");
        $result = $q->fetchAll();
        foreach ($result as $column) {
            $res[$column['Field']] = $column['Type'];
        }

        return $res;
    }
}
