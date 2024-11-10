<?php
/*
$role = 'INSERT INTO "role" ("role_name") VALUES
        ("client"),
        ("guest"),
        ("user"),
        ("moder"),
        ("admin")';
$permission = 'INSERT INTO "permission" ("resource", "action") VALUES
        ("User", "getUserData"),
        ("User", "add"),
        ("User", "update"),
        ("User", "deleteUserData")';
$roles_permissions = 'INSERT INTO "role_permission" ("role_id", "permission_id") VALUES
        ((SELECT "id" from "role" WHERE "role_name"="admin" LIMIT 1), 1),
        ((SELECT "id" from "role" WHERE "role_name"="admin" LIMIT 1), 2),
        ((SELECT "id" from "role" WHERE "role_name"="admin" LIMIT 1), 3),
        ((SELECT "id" from "role" WHERE "role_name"="moder" LIMIT 1), 1)';

$insert_sqls = [
    'role' => $role,
    'permission' => $permission,
    'role_permission' => $role_permission,
];
*/
$role = "INSERT INTO `role` SET role_name = 'admin'";
$insert_sqls = ['role' => $role];
