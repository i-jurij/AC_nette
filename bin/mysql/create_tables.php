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
	(	`role_id` INTEGER UNSIGNED NOT NULL, 
		`permission_id` INTEGER UNSIGNED NOT NULL,
		PRIMARY KEY (`role_id`, `permission_id`),
		FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE CASCADE,
		FOREIGN KEY (`permission_id`) REFERENCES `permission`(`id`) ON DELETE CASCADE
	)';
$user = 'CREATE TABLE IF NOT EXISTS `user`
	(	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL, 
		`username` VARCHAR(512) NOT NULL UNIQUE, 
		`image` TEXT,
		`password` TEXT NOT NULL, 
		`phone` VARCHAR(12) UNIQUE DEFAULT NULL, 
		`phone_verified` TINYINT DEFAULT NULL,
		`email` TEXT UNIQUE DEFAULT NULL, 
		`email_verified` TINYINT DEFAULT NULL, 
		`auth_token` TEXT NOT NULL, 
		`created_at` TIMESTAMP NOT NULL
                           DEFAULT CURRENT_TIMESTAMP,
		`updated_at` TIMESTAMP NOT NULL
                           DEFAULT CURRENT_TIMESTAMP 
                           ON UPDATE CURRENT_TIMESTAMP
	)';

$role_user = 'CREATE TABLE IF NOT EXISTS `role_user`
	(	
		`role_id` INTEGER UNSIGNED NOT NULL, 
		`user_id` INTEGER UNSIGNED NOT NULL,
		PRIMARY KEY (`role_id`, `user_id`),
		FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
		FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE CASCADE
	)';

$client = 'CREATE TABLE IF NOT EXISTS `client`
	(	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL, 
		`username` VARCHAR(512) NOT NULL UNIQUE, 
		`image` TEXT,
		`password` TEXT NOT NULL, 
		`phone` VARCHAR(12) UNIQUE DEFAULT NULL, 
		`phone_verified` TINYINT DEFAULT NULL,
		`email` TEXT UNIQUE DEFAULT null, 
		`email_verified` TINYINT DEFAULT NULL, 
		`auth_token` TEXT NOT NULL, 
		`created_at` TIMESTAMP NOT NULL
                           DEFAULT CURRENT_TIMESTAMP,
		`updated_at` TIMESTAMP NOT NULL
                           DEFAULT CURRENT_TIMESTAMP 
                           ON UPDATE CURRENT_TIMESTAMP
	)';

$role_client = 'CREATE TABLE IF NOT EXISTS `role_client`
	(	
		`role_id` INTEGER UNSIGNED NOT NULL, 
		`user_id` INTEGER UNSIGNED NOT NULL,
		PRIMARY KEY (`role_id`, `user_id`),
		FOREIGN KEY (`user_id`) REFERENCES `client`(`id`) ON DELETE CASCADE,
		FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE CASCADE
	)';

$userappliedforregistration = 'CREATE TABLE `userappliedforregistration` (
  `id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL, 
  `username` varchar(512) NOT NULL,
  `image` text DEFAULT NULL,
  `password` text NOT NULL,
  `phone` varchar(12) UNIQUE DEFAULT NULL,
  `phone_verified` tinyint(4) DEFAULT NULL,
  `email` text UNIQUE DEFAULT NULL,
  `email_verified` tinyint(4) DEFAULT NULL,
  `auth_token` text NOT NULL,
  `csrf` text DEFAULT NULL,
  `roles` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
';

$create_sqls = [
    'role' => $role,
    'permission' => $permission,
    'role_permission' => $role_permission,
    'user' => $user,
    'role_user' => $role_user,
    'client' => $client,
    'role_client' => $role_client,
    'userappliedforregistration' => $userappliedforregistration,
];
