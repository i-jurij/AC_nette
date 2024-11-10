<?php

declare(strict_types=1);

function start()
{
    echo '
1. Create table "user", "role", "permission" etc, run:
"php start.php migrate"

2. Create user (with role "admin"), run:
"php start.php useradd <username> <password>"

';
    exit(1);
}

function migrate($db)
{
    try {
        $db->transaction(function ($db) {
            $path_create = APPDIR.'/../bin/create_sql.php';
            $path_insert = APPDIR.'/../bin/insert_sql.php';
            $path_trigger = APPDIR.'/../bin/trigger_sql.php';
            if (include $path_create) {
                foreach ($create_sqls as $key => $sql) {
                    $check_table = $db->fetch('SELECT count(*) FROM sqlite_master WHERE type=? AND name=?', 'table', $key);
                    if ($check_table['count(*)'] === 0) {
                        $db->query($sql);
                        if ((include $path_insert) && isset($insert_sqls[$key])) {
                            $db->query($insert_sqls[$key]);
                        }
                        if ((include $path_trigger) && isset($trigger_sqls[$key])) {
                            $db->query($trigger_sqls[$key]);
                        }
                    }
                }
            }
        });
        echo "Migrate was executed. Database and table was created.\n";
        exit(1);
    } catch (Exception $e) {
        echo "Error: '.$e.'.\n";
        exit(1);
    }
}

function userAdd($container, $argv)
{
    $user_add_to_db = $container->getByType(App\Model\UserFacade::class);
    $table = $user_add_to_db::TableName;
    $admin = $user_add_to_db->sqlite->table('role')->select('id')->where('role_name', 'admin')->fetch();

    // check if at least one users with admin role isset
    $admin_isset = $user_add_to_db->sqlite->table('role_user')->select('count(*)')->where('role_id', $admin['id'])->fetch();

    if (empty($admin_isset['count(*)'])) {
        try {
            [,, $username, $password] = $argv;
            $user_add_to_db->shortAdd($username, $password);
            echo "User $username was added.\n";
            exit(1);
        } catch (App\Model\DuplicateNameException $e) {
            echo "Error: duplicate username.\n";
            exit(1);
        }
    } else {
        echo "Warning: users already isset. Try UI.\n";
        exit(1);
    }
}

if (PHP_SAPI === 'cli') {
    require __DIR__.'/../vendor/autoload.php';

    $container = App\Bootstrap::boot()
        ->createContainer();
    // var_dump($container);
    $db = $container->getByName('database.sqlite.connection');

    if (!isset($argv[1])) {
        start();
    } elseif ($argc == 2 && !empty($argv[1]) && $argv[1] === 'migrate') {
        migrate($db);
    } elseif ($argc == 4 && $argv[1] === 'useradd') {
        userAdd($container, $argv);
    } else {
        echo "Somethig wrong :( \n";
        exit(1);
    }
}
