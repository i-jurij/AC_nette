<?php

declare(strict_types=1);

$path_to_sql_file = 'mysql';

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

function migrate(object $container, string $path_to_sql_file)
{
    $begin_path_to_sql_files = APPDIR.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.$path_to_sql_file.DIRECTORY_SEPARATOR;
    $path_db_create = \realpath($begin_path_to_sql_files.'create_db.php');
    $path_create = \realpath($begin_path_to_sql_files.'create_sql.php');
    $path_insert = \realpath($begin_path_to_sql_files.'insert_sql.php');
    $path_trigger = \realpath($begin_path_to_sql_files.'trigger_sql.php');

    $db = $container->getByName('database.'.$path_to_sql_file.'.connection');
    $reflection = $db->getReflection();

    try {
        $db->beginTransaction();

        if (include $path_create) {
            foreach ($create_sqls as $key => $sql) {
                $check_table = $reflection->hasTable($key);

                if ($check_table == false) {
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
        // });
        $db->commit();
        echo "Migrate was executed. Database and table was created.\n";
        exit(1);
    } catch (Exception $e) {
        $db->rollback();
        echo "Error: '.$e.'.\n";
        exit(1);
    }
}

function userAdd(object $container, array $argv)
{
    $userFacade = $container->getByType(App\Model\UserFacade::class);
    $admin = $userFacade->db->table('role')->select('id')->where('role_name', 'admin')->fetch();

    // check if at least one users with admin role isset
    $admin_isset = $userFacade->db->table('role_user')->select('count(*)')->where('role_id', $admin['id'])->fetch();

    if (empty($admin_isset['count(*)'])) {
        try {
            [,, $username, $password] = $argv;
            $userFacade->shortAdd($username, $password, 'user');
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

    if (!isset($argv[1])) {
        start();
    } elseif ($argc == 2 && !empty($argv[1]) && $argv[1] === 'migrate') {
        migrate($container, $path_to_sql_file);
    } elseif ($argc == 4 && $argv[1] === 'useradd') {
        userAdd($container, $argv);
    } else {
        echo "Somethig wrong :( \n";
        exit(1);
    }
}
