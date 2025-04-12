<?php

$role = 'CREATE TABLE IF NOT EXISTS `role`
	(	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
		`role_name` CHAR(255) UNIQUE NOT NULL
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';
$permission = 'CREATE TABLE IF NOT EXISTS `permission`
	(	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
		`resource` CHAR(255) NOT NULL,
		`action` CHAR(255) DEFAULT NULL
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';
$role_permission = 'CREATE TABLE IF NOT EXISTS `role_permission`
	(	`role_id` INTEGER UNSIGNED NOT NULL,
		`permission_id` INTEGER UNSIGNED NOT NULL,
		PRIMARY KEY (`role_id`, `permission_id`),
		FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE CASCADE,
		FOREIGN KEY (`permission_id`) REFERENCES `permission`(`id`) ON DELETE CASCADE
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';
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
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';

$role_user = 'CREATE TABLE IF NOT EXISTS `role_user`
	(
		`role_id` INTEGER UNSIGNED NOT NULL,
		`user_id` INTEGER UNSIGNED NOT NULL,
		PRIMARY KEY (`role_id`, `user_id`),
		FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE CASCADE,
		FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE CASCADE
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';

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
		`rating` INTEGER DEFAULT NULL,
		`created_at` TIMESTAMP NOT NULL
                           DEFAULT CURRENT_TIMESTAMP,
		`updated_at` TIMESTAMP NOT NULL
                           DEFAULT CURRENT_TIMESTAMP
                           ON UPDATE CURRENT_TIMESTAMP
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';

$role_client = 'CREATE TABLE IF NOT EXISTS `role_client`
	(
		`role_id` INTEGER UNSIGNED NOT NULL,
		`user_id` INTEGER UNSIGNED NOT NULL,
		PRIMARY KEY (`role_id`, `user_id`),
		FOREIGN KEY (`user_id`) REFERENCES `client`(`id`) ON DELETE CASCADE,
		FOREIGN KEY (`role_id`) REFERENCES `role`(`id`) ON DELETE CASCADE
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';

$userappliedforregistration = 'CREATE TABLE IF NOT EXISTS `userappliedforregistration` (
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci
';

$offer = 'CREATE TABLE IF NOT EXISTS `offer` (
	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
	`offers_type` varchar(25) NOT NULL,
	`client_id` INTEGER UNSIGNED NOT NULL,
	`city_id` INTEGER DEFAULT NULL,
	`city_name` varchar(512) NOT NULL,
	`region_id` INTEGER DEFAULT NULL,
	`region_name` varchar(512) DEFAULT NULL,
	`district_id` INTEGER DEFAULT NULL,
	`district_name` varchar(512) DEFAULT NULL,
	`price` DECIMAL(10,2) NOT NULL,
	`message` text DEFAULT NULL,
	`moderated` TINYINT(1) DEFAULT 0,
  	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`end_time` TIMESTAMP NOT NULL,
	FOREIGN KEY (`client_id`) REFERENCES `client`(`id`) ON DELETE CASCADE
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';

$offer_image = 'CREATE TABLE `offer_image_thumb` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`offer_id` INTEGER NOT NULL,
	`caption` VARCHAR(128) NOT NULL,
	`thumb` LONGBLOB NOT NULL,
	PRIMARY KEY(`id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';

$comment = 'CREATE TABLE IF NOT EXISTS `comment` (
	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
	`offer_id` INTEGER UNSIGNED NOT NULL,
	`client_id` INTEGER UNSIGNED NOT NULL,
	`comment_text` text NOT NULL,
	`moderated` TINYINT(1) DEFAULT 0,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	FOREIGN KEY (`client_id`) REFERENCES `client`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`offer_id`) REFERENCES `offer`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';

$rating = 'CREATE TABLE IF NOT EXISTS `rating` (
	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
	`client_id_who` INTEGER UNSIGNED NOT NULL,
	`client_id_to_whom` INTEGER UNSIGNED NOT NULL,
	`rating_value` INTEGER NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	FOREIGN KEY (`client_id_who`) REFERENCES `client`(`id`) ON DELETE CASCADE,
	FOREIGN KEY (`client_id_to_whom`) REFERENCES `client`(`id`) ON DELETE CASCADE
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';

$category = 'CREATE TABLE IF NOT EXISTS `category` (
	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
	`image` varchar(1500) DEFAULT NULL,
	`name` varchar(255) NOT NULL,
	`description` varchar(500) DEFAULT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  ';

$service = 'CREATE TABLE IF NOT EXISTS `service` (
	`id` INTEGER UNSIGNED PRIMARY KEY AUTO_INCREMENT NOT NULL,
	`category_id` INTEGER UNSIGNED DEFAULT NULL,
	`image` varchar(1500) DEFAULT NULL,
	`name` varchar(255) NOT NULL,
	`description` varchar(500) DEFAULT NULL,
	`price` decimal(9,2) DEFAULT NULL,
	`duration` int(11) DEFAULT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  	`updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) ON DELETE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  ';

$offer_service = 'CREATE TABLE IF NOT EXISTS `offer_service`
	(
		`offer_id` INTEGER UNSIGNED NOT NULL,
		`service_id` INTEGER UNSIGNED NOT NULL,
		PRIMARY KEY (`offer_id`, `service_id`),
		FOREIGN KEY (`offer_id`) REFERENCES `offer`(`id`) ON DELETE CASCADE,
		FOREIGN KEY (`service_id`) REFERENCES `service`(`id`) ON DELETE CASCADE
	) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci';

$create_sqls = [
    'role' => $role,
    'permission' => $permission,
    'role_permission' => $role_permission,
    'user' => $user,
    'role_user' => $role_user,
    'client' => $client,
    'role_client' => $role_client,
    'userappliedforregistration' => $userappliedforregistration,
    'offer' => $offer,
    'offer_image' => $offer_image,
    'comment' => $comment,
    'rating' => $rating,
    'category' => $category,
    'service' => $service,
    'offer_service' => $offer_service,
];
