-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Мар 01 2025 г., 18:39
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12
SET
  SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
  time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;

/*!40101 SET NAMES utf8mb4 */
;

--
-- База данных: `AC`
--
CREATE DATABASE IF NOT EXISTS AC CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';

USE AC;

-- --------------------------------------------------------
--
-- Структура таблицы `client`
--
CREATE TABLE IF NOT EXISTS `client` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(512) NOT NULL,
  `image` text DEFAULT NULL,
  `password` text NOT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `phone_verified` tinyint(4) DEFAULT 0,
  `email` text DEFAULT NULL,
  `email_verified` tinyint(4) DEFAULT 0,
  `auth_token` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Дамп данных таблицы `client`
--
INSERT INTO
  `client` (
    `id`,
    `username`,
    `image`,
    `password`,
    `phone`,
    `phone_verified`,
    `email`,
    `email_verified`,
    `auth_token`,
    `created_at`,
    `updated_at`
  )
VALUES
  (
    111,
    '79888787878',
    NULL,
    '$2y$10$bnC1Wehmga04w0x6zU.k6emDyiIM.TrUgVLu4j.COHQ5sVKI7joKC',
    '79888787878',
    0,
    'gmail@gmail.com',
    0,
    'f6busjw4lg9rdtt',
    '2025-01-08 11:00:48',
    '2025-02-23 14:15:12'
  ),
  (
    112,
    '77878787878',
    NULL,
    '$2y$10$MN31QBxeu2q1PproRZHSsuM4INyjwFaELEC0NAwmFakzWnnADAZFm',
    '77878787878',
    0,
    NULL,
    0,
    '0z5dztfu1a23gdg',
    '2025-01-09 09:41:28',
    '2025-01-09 09:41:28'
  ),
  (
    113,
    '77654677667',
    NULL,
    '$2y$10$qHGpCnzPJpFnsbLKU2xuZ.COh9OHoDC0ZmWkoSTScfHzE6ABZ91MO',
    '77654677667',
    0,
    NULL,
    0,
    'lebqqhmhejn0j4c',
    '2025-01-09 09:49:09',
    '2025-01-09 09:49:09'
  ),
  (
    114,
    '78786876876',
    NULL,
    '$2y$10$2O0ABFmQl8Bba3HiCH2fWuAjNWnZ6.olpi2EzqnQlJHW//IXjLt0O',
    '78786876876',
    0,
    NULL,
    0,
    'zstiko44bgyk7my',
    '2025-01-09 09:50:49',
    '2025-01-09 09:50:49'
  ),
  (
    115,
    '78897687687',
    NULL,
    '$2y$10$.e8WTrgv5qdUrQEZQJI9euvYSGWDDWi.kLCZDedIeloOb1TL.1.C2',
    '78897687687',
    0,
    NULL,
    0,
    'mbf40vv8ad2i742',
    '2025-01-09 22:58:15',
    '2025-01-09 22:58:15'
  );

-- --------------------------------------------------------
--
-- Структура таблицы `permission`
--
CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(10) UNSIGNED NOT NULL,
  `resource` char(255) NOT NULL,
  `action` char(255) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Дамп данных таблицы `permission`
--
INSERT INTO
  `permission` (`id`, `resource`, `action`)
VALUES
  (1, 'User', 'getAllUsersData'),
  (2, 'User', 'getUserData'),
  (3, 'User', 'getRoless');

-- --------------------------------------------------------
--
-- Структура таблицы `role`
--
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_name` char(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Дамп данных таблицы `role`
--
INSERT INTO
  `role` (`id`, `role_name`)
VALUES
  (1, 'admin'),
  (3, 'client'),
  (2, 'moder');

-- --------------------------------------------------------
--
-- Структура таблицы `role_client`
--
CREATE TABLE IF NOT EXISTS `role_client` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Дамп данных таблицы `role_client`
--
INSERT INTO
  `role_client` (`role_id`, `user_id`)
VALUES
  (3, 111),
  (3, 112),
  (3, 113),
  (3, 114),
  (3, 115);

-- --------------------------------------------------------
--
-- Структура таблицы `role_permission`
--
CREATE TABLE IF NOT EXISTS `role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Дамп данных таблицы `role_permission`
--
INSERT INTO
  `role_permission` (`role_id`, `permission_id`)
VALUES
  (2, 1),
  (2, 2);

-- --------------------------------------------------------
--
-- Структура таблицы `role_user`
--
CREATE TABLE IF NOT EXISTS `role_user` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Дамп данных таблицы `role_user`
--
INSERT INTO
  `role_user` (`role_id`, `user_id`)
VALUES
  (1, 1),
  (2, 2);

-- --------------------------------------------------------
--
-- Структура таблицы `user`
--
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(512) NOT NULL,
  `image` text DEFAULT NULL,
  `password` text NOT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `phone_verified` tinyint(4) DEFAULT 0,
  `email` text DEFAULT NULL,
  `email_verified` tinyint(4) DEFAULT 0,
  `auth_token` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Дамп данных таблицы `user`
--
INSERT INTO
  `user` (
    `id`,
    `username`,
    `image`,
    `password`,
    `phone`,
    `phone_verified`,
    `email`,
    `email_verified`,
    `auth_token`,
    `created_at`,
    `updated_at`
  )
VALUES
  (
    1,
    'admin',
    NULL,
    '$2y$10$OXI/zByxC5f.7aGx38kcu.IGCnefdrPY.Wg1nF5XXvzOY6I1YFY0O',
    NULL,
    0,
    NULL,
    0,
    'kxgc9gq3xvl95ks',
    '2024-11-28 20:21:54',
    '2024-11-28 20:21:54'
  ),
  (
    2,
    'moder',
    NULL,
    '$2y$10$zpz8PE/0PRj1Tj7JcIC6pe/nayVrVD1bOQeGFzjyBveXtX3iGaSqC',
    NULL,
    0,
    NULL,
    0,
    'grvgqnoqy3irl06',
    '2025-01-05 13:35:55',
    '2025-01-05 13:35:55'
  );

CREATE TABLE IF NOT EXISTS `userappliedforregistration` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(512) NOT NULL,
  `image` text DEFAULT NULL,
  `password` text NOT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `phone_verified` tinyint(4) DEFAULT NULL,
  `email` text DEFAULT NULL,
  `email_verified` tinyint(4) DEFAULT NULL,
  `auth_token` text NOT NULL,
  `csrf` text DEFAULT NULL,
  `roles` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Индексы сохранённых таблиц
--
--
-- Индексы таблицы `client`
--
ALTER TABLE
  `client`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `username` (`username`),
ADD
  UNIQUE KEY `phone` (`phone`),
ADD
  UNIQUE KEY `email` (`email`) USING HASH;

--
-- Индексы таблицы `permission`
--
ALTER TABLE
  `permission`
ADD
  PRIMARY KEY (`id`);

--
-- Индексы таблицы `role`
--
ALTER TABLE
  `role`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `role_name` (`role_name`);

--
-- Индексы таблицы `role_client`
--
ALTER TABLE
  `role_client`
ADD
  PRIMARY KEY (`role_id`, `user_id`);

--
-- Индексы таблицы `role_permission`
--
ALTER TABLE
  `role_permission`
ADD
  PRIMARY KEY (`role_id`, `permission_id`);

--
-- Индексы таблицы `role_user`
--
ALTER TABLE
  `role_user`
ADD
  PRIMARY KEY (`role_id`, `user_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE
  `user`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `username` (`username`),
ADD
  UNIQUE KEY `phone` (`phone`),
ADD
  UNIQUE KEY `email` (`email`) USING HASH;

--
-- AUTO_INCREMENT для сохранённых таблиц
--
--
-- AUTO_INCREMENT для таблицы `client`
--
ALTER TABLE
  `client`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 117;

--
-- AUTO_INCREMENT для таблицы `permission`
--
ALTER TABLE
  `permission`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 4;

--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE
  `role`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 29;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE
  `user`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 3;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;