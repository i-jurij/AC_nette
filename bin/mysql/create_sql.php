<?php

$role = 'CREATE TABLE IF NOT EXISTS `role`
	(	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL, 
		`role_name` CHAR(255) UNIQUE NOT NULL
	)';
$permission = 'CREATE TABLE IF NOT EXISTS `permission`
	(	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
		`resource` CHAR(255) NOT NULL, 
		`action` CHAR(255) DEFAULT NULL
	)';
$role_permission = 'CREATE TABLE IF NOT EXISTS `role_permission`
	(	`role_id` INTEGER NOT NULL, 
		`permission_id` INTEGER NOT NULL,
		PRIMARY KEY (`role_id`, `permission_id`)
	)';
$user = 'CREATE TABLE IF NOT EXISTS `user`
	(	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL, 
		`username` VARCHAR(512) NOT NULL UNIQUE, 
		`image` TEXT,
		`password` TEXT NOT NULL, 
		`phone` VARCHAR(12) UNIQUE DEFAULT NULL, 
		`phone_verified` TINYINT DEFAULT 0,
		`email` TEXT UNIQUE DEFAULT null, 
		`email_verified` TINYINT DEFAULT 0, 
		`auth_token` TEXT, 
		`created_at` TIMESTAMP NOT NULL
                           DEFAULT CURRENT_TIMESTAMP,
		`updated_at` TIMESTAMP NOT NULL
                           DEFAULT CURRENT_TIMESTAMP 
                           ON UPDATE CURRENT_TIMESTAMP
	)';
$role_user = 'CREATE TABLE IF NOT EXISTS `role_user`
	(	
		`role_id` INTEGER NOT NULL, 
		`user_id` INTEGER NOT NULL,
		PRIMARY KEY (`role_id`, `user_id`)
	)';

$create_sqls = [
    'role' => $role,
    'permission' => $permission,
    'role_permission' => $role_permission,
    'user' => $user,
    'role_user' => $role_user,
];
