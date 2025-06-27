-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июн 27 2025 г., 15:43
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
CREATE DATABASE IF NOT EXISTS `AC` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

USE `AC`;

-- --------------------------------------------------------
--
-- Структура таблицы `category`
--
-- Создание: Июн 27 2025 г., 13:11
-- Последнее обновление: Июн 27 2025 г., 13:19
--
DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(10) UNSIGNED NOT NULL,
  `image` varchar(1500) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `category`:
--
--
-- Дамп данных таблицы `category`
--
INSERT INTO
  `category` (
    `id`,
    `image`,
    `name`,
    `description`,
    `created_at`,
    `updated_at`
  )
VALUES
  (
    1,
    NULL,
    'Строительство',
    NULL,
    '2025-06-18 13:09:10',
    '2025-06-18 13:09:10'
  ),
  (
    2,
    NULL,
    'Инженерные системы и оборудование',
    NULL,
    '2025-06-23 15:52:59',
    '2025-06-23 15:52:59'
  ),
  (
    3,
    NULL,
    'Заборы',
    NULL,
    '2025-06-23 16:02:41',
    '2025-06-23 16:02:41'
  ),
  (
    4,
    NULL,
    'Металл',
    NULL,
    '2025-06-23 16:08:29',
    '2025-06-23 16:08:29'
  ),
  (
    5,
    NULL,
    'Двери, калитки, замки',
    NULL,
    '2025-06-23 16:30:06',
    '2025-06-23 16:33:20'
  ),
  (
    6,
    NULL,
    'Окна',
    NULL,
    '2025-06-23 16:31:47',
    '2025-06-23 16:31:47'
  ),
  (
    7,
    NULL,
    'Электромонтажные работы',
    NULL,
    '2025-06-23 16:47:42',
    '2025-06-23 16:47:42'
  ),
  (
    8,
    NULL,
    'Сантехника',
    NULL,
    '2025-06-23 17:03:28',
    '2025-06-23 17:03:28'
  ),
  (
    9,
    NULL,
    'Автомобиль',
    NULL,
    '2025-06-23 17:24:34',
    '2025-06-23 17:24:34'
  ),
  (
    10,
    NULL,
    'Мотоцикл',
    NULL,
    '2025-06-23 17:28:35',
    '2025-06-23 17:28:35'
  ),
  (
    11,
    NULL,
    'Доставка, погрузка',
    NULL,
    '2025-06-23 17:33:04',
    '2025-06-23 17:33:04'
  ),
  (
    12,
    NULL,
    'Сад, огород',
    NULL,
    '2025-06-23 17:35:32',
    '2025-06-23 17:35:32'
  ),
  (
    13,
    NULL,
    'Красота',
    NULL,
    '2025-06-23 17:41:22',
    '2025-06-23 17:41:42'
  ),
  (
    14,
    NULL,
    'Медицина',
    NULL,
    '2025-06-23 17:44:06',
    '2025-06-23 17:44:06'
  ),
  (
    15,
    NULL,
    'Учеба',
    NULL,
    '2025-06-23 17:44:54',
    '2025-06-23 17:44:54'
  ),
  (
    16,
    NULL,
    'Развлечения, отдых',
    NULL,
    '2025-06-23 17:50:39',
    '2025-06-23 17:50:39'
  ),
  (
    17,
    NULL,
    'Туризм',
    NULL,
    '2025-06-23 17:55:42',
    '2025-06-23 17:55:42'
  ),
  (
    18,
    NULL,
    'Вычислительная техника и сети',
    NULL,
    '2025-06-23 18:03:46',
    '2025-06-23 18:03:46'
  ),
  (
    19,
    NULL,
    'Программирование',
    NULL,
    '2025-06-23 18:08:19',
    '2025-06-23 18:08:19'
  ),
  (
    20,
    NULL,
    'Фото, видео, 3d, CAD',
    NULL,
    '2025-06-23 18:10:49',
    '2025-06-24 16:53:00'
  ),
  (
    21,
    NULL,
    'Реклама, СЕО',
    NULL,
    '2025-06-23 18:17:18',
    '2025-06-23 18:17:18'
  ),
  (
    22,
    NULL,
    'Животные',
    NULL,
    '2025-06-23 18:21:21',
    '2025-06-23 18:21:21'
  ),
  (
    23,
    NULL,
    'Еда, напитки',
    NULL,
    '2025-06-24 16:49:45',
    '2025-06-24 16:50:18'
  ),
  (
    24,
    NULL,
    'Одежда, обувь',
    NULL,
    '2025-06-25 17:07:25',
    '2025-06-25 17:07:25'
  ),
  (
    25,
    NULL,
    'Мебель',
    NULL,
    '2025-06-25 17:10:37',
    '2025-06-25 17:10:37'
  );

-- --------------------------------------------------------
--
-- Структура таблицы `chat`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `chat`;

CREATE TABLE `chat` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `offer_id` int(10) UNSIGNED NOT NULL,
  `client_id_who` int(10) UNSIGNED NOT NULL,
  `client_id_to_whom` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `moderated` tinyint(1) DEFAULT 0,
  `read` tinyint(1) DEFAULT 0,
  `request_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `chat`:
--   `parent_id`
--       `chat` -> `id`
--   `client_id_who`
--       `client` -> `id`
--   `client_id_to_whom`
--       `client` -> `id`
--   `offer_id`
--       `offer` -> `id`
--
-- --------------------------------------------------------
--
-- Структура таблицы `client`
--
-- Создание: Июн 27 2025 г., 13:11
-- Последнее обновление: Июн 27 2025 г., 13:23
--
DROP TABLE IF EXISTS `client`;

CREATE TABLE `client` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(512) NOT NULL,
  `image` text DEFAULT NULL,
  `password` text NOT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `phone_verified` tinyint(4) DEFAULT NULL,
  `email` text DEFAULT NULL,
  `email_verified` tinyint(4) DEFAULT NULL,
  `auth_token` text NOT NULL,
  `rating` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `client`:
--
-- --------------------------------------------------------
--
-- Структура таблицы `comment`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `comment`;

CREATE TABLE `comment` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `offer_id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `comment_text` text NOT NULL,
  `moderated` tinyint(1) DEFAULT 0,
  `request_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `comment`:
--   `parent_id`
--       `comment` -> `id`
--   `client_id`
--       `client` -> `id`
--   `offer_id`
--       `offer` -> `id`
--
-- --------------------------------------------------------
--
-- Структура таблицы `grievance`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `grievance`;

CREATE TABLE `grievance` (
  `id` int(10) UNSIGNED NOT NULL,
  `offer_id` int(10) UNSIGNED NOT NULL,
  `comment_id` int(10) UNSIGNED DEFAULT NULL,
  `client_id_who` int(10) UNSIGNED NOT NULL,
  `message` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `resolve` tinyint(1) DEFAULT 0,
  `resolve_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `grievance`:
--
-- --------------------------------------------------------
--
-- Структура таблицы `offer`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `offer`;

CREATE TABLE `offer` (
  `id` int(10) UNSIGNED NOT NULL,
  `offers_type` varchar(25) NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `city_name` varchar(512) NOT NULL,
  `region_id` int(11) DEFAULT NULL,
  `region_name` varchar(512) DEFAULT NULL,
  `district_id` int(11) DEFAULT NULL,
  `district_name` varchar(512) DEFAULT NULL,
  `price` decimal(10, 2) NOT NULL,
  `message` text DEFAULT NULL,
  `moderated` tinyint(1) DEFAULT 0,
  `request_data` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `offer`:
--   `client_id`
--       `client` -> `id`
--
--
-- Триггеры `offer`
--
DROP TRIGGER IF EXISTS `offer_end_time_insert`;

DELIMITER $ $ CREATE TRIGGER `offer_end_time_insert` BEFORE
INSERT
  ON `offer` FOR EACH ROW
SET
  NEW.`end_time` = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 30 DAY) $ $ DELIMITER;

DROP TRIGGER IF EXISTS `offer_end_time_update`;

DELIMITER $ $ CREATE TRIGGER `offer_end_time_update` BEFORE
UPDATE
  ON `offer` FOR EACH ROW
SET
  NEW.`end_time` = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 30 DAY) $ $ DELIMITER;

-- --------------------------------------------------------
--
-- Структура таблицы `offer_image_thumb`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `offer_image_thumb`;

CREATE TABLE `offer_image_thumb` (
  `id` int(10) UNSIGNED NOT NULL,
  `offer_id` int(11) NOT NULL,
  `caption` varchar(128) NOT NULL,
  `thumb` longblob NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `offer_image_thumb`:
--
-- --------------------------------------------------------
--
-- Структура таблицы `offer_service`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `offer_service`;

CREATE TABLE `offer_service` (
  `offer_id` int(10) UNSIGNED NOT NULL,
  `service_id` int(10) UNSIGNED NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `offer_service`:
--   `offer_id`
--       `offer` -> `id`
--   `service_id`
--       `service` -> `id`
--
-- --------------------------------------------------------
--
-- Структура таблицы `permission`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `permission`;

CREATE TABLE `permission` (
  `id` int(10) UNSIGNED NOT NULL,
  `resource` char(255) NOT NULL,
  `action` char(255) DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `permission`:
--
-- --------------------------------------------------------
--
-- Структура таблицы `rating`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `rating`;

CREATE TABLE `rating` (
  `client_id_who` int(10) UNSIGNED NOT NULL,
  `client_id_to_whom` int(10) UNSIGNED NOT NULL,
  `rating_value` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `rating`:
--   `client_id_who`
--       `client` -> `id`
--   `client_id_to_whom`
--       `client` -> `id`
--
--
-- Триггеры `rating`
--
DROP TRIGGER IF EXISTS `rating_delete`;

DELIMITER $ $ CREATE TRIGGER `rating_delete`
AFTER
  DELETE ON `rating` FOR EACH ROW BEGIN DECLARE sum INT;

DECLARE row_count INT;

SELECT
  SUM(`rating`.`rating_value`) INTO sum
FROM
  `rating`
WHERE
  `rating`.`client_id_to_whom` = OLD.`client_id_to_whom`;

SELECT
  COUNT(*) INTO row_count
FROM
  `rating`
WHERE
  `rating`.`client_id_to_whom` = OLD.`client_id_to_whom`;

UPDATE
  `client`
SET
  `client`.`rating` = (ROUND(sum / row_count))
WHERE
  `client`.`id` = OLD.`client_id_to_whom`;

END $ $ DELIMITER;

DROP TRIGGER IF EXISTS `rating_insert`;

DELIMITER $ $ CREATE TRIGGER `rating_insert`
AFTER
INSERT
  ON `rating` FOR EACH ROW BEGIN DECLARE sum INT;

DECLARE row_count INT;

SELECT
  SUM(`rating`.`rating_value`) INTO sum
FROM
  `rating`
WHERE
  `rating`.`client_id_to_whom` = NEW.`client_id_to_whom`;

SELECT
  COUNT(*) INTO row_count
FROM
  `rating`
WHERE
  `rating`.`client_id_to_whom` = NEW.`client_id_to_whom`;

UPDATE
  `client`
SET
  `client`.`rating` = (ROUND(sum / row_count))
WHERE
  `client`.`id` = NEW.`client_id_to_whom`;

END $ $ DELIMITER;

DROP TRIGGER IF EXISTS `rating_update`;

DELIMITER $ $ CREATE TRIGGER `rating_update`
AFTER
UPDATE
  ON `rating` FOR EACH ROW BEGIN DECLARE sum INT;

DECLARE row_count INT;

SELECT
  SUM(`rating`.`rating_value`) INTO sum
FROM
  `rating`
WHERE
  `rating`.`client_id_to_whom` = NEW.`client_id_to_whom`;

SELECT
  COUNT(*) INTO row_count
FROM
  `rating`
WHERE
  `rating`.`client_id_to_whom` = NEW.`client_id_to_whom`;

UPDATE
  `client`
SET
  `client`.`rating` = (ROUND(sum / row_count))
WHERE
  `client`.`id` = NEW.`client_id_to_whom`;

END $ $ DELIMITER;

-- --------------------------------------------------------
--
-- Структура таблицы `requestforaddingservice`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `requestforaddingservice`;

CREATE TABLE `requestforaddingservice` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `new_category` varchar(255) DEFAULT NULL,
  `service` varchar(500) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `requestforaddingservice`:
--   `client_id`
--       `client` -> `id`
--   `category_id`
--       `category` -> `id`
--
-- --------------------------------------------------------
--
-- Структура таблицы `role`
--
-- Создание: Июн 27 2025 г., 13:11
-- Последнее обновление: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `role`;

CREATE TABLE `role` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_name` char(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `role`:
--
--
-- Дамп данных таблицы `role`
--
INSERT INTO
  `role` (`id`, `role_name`)
VALUES
  (1, 'admin'),
  (2, 'banned'),
  (3, 'client'),
  (4, 'customer'),
  (5, 'executor');

-- --------------------------------------------------------
--
-- Структура таблицы `role_client`
--
-- Создание: Июн 27 2025 г., 13:11
-- Последнее обновление: Июн 27 2025 г., 13:23
--
DROP TABLE IF EXISTS `role_client`;

CREATE TABLE `role_client` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `role_client`:
--   `user_id`
--       `client` -> `id`
--   `role_id`
--       `role` -> `id`
--
--
-- Дамп данных таблицы `role_client`
--
INSERT INTO
  `role_client` (`role_id`, `user_id`)
VALUES
  (3, 1),
  (5, 1);

-- --------------------------------------------------------
--
-- Структура таблицы `role_permission`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `role_permission`;

CREATE TABLE `role_permission` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `permission_id` int(10) UNSIGNED NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `role_permission`:
--   `role_id`
--       `role` -> `id`
--   `permission_id`
--       `permission` -> `id`
--
-- --------------------------------------------------------
--
-- Структура таблицы `role_user`
--
-- Создание: Июн 27 2025 г., 13:11
-- Последнее обновление: Июн 27 2025 г., 13:13
--
DROP TABLE IF EXISTS `role_user`;

CREATE TABLE `role_user` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `role_user`:
--   `user_id`
--       `user` -> `id`
--   `role_id`
--       `role` -> `id`
--
--
-- Дамп данных таблицы `role_user`
--
INSERT INTO
  `role_user` (`role_id`, `user_id`)
VALUES
  (1, 1);

-- --------------------------------------------------------
--
-- Структура таблицы `service`
--
-- Создание: Июн 27 2025 г., 13:11
-- Последнее обновление: Июн 27 2025 г., 13:23
--
DROP TABLE IF EXISTS `service`;

CREATE TABLE `service` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `image` varchar(1500) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `price` decimal(9, 2) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `service`:
--   `category_id`
--       `category` -> `id`
--
--
-- Дамп данных таблицы `service`
--
INSERT INTO
  `service` (
    `id`,
    `category_id`,
    `image`,
    `name`,
    `description`,
    `price`,
    `duration`,
    `created_at`,
    `updated_at`
  )
VALUES
  (
    1,
    1,
    NULL,
    'Бетонные работы',
    NULL,
    NULL,
    NULL,
    '2025-06-18 10:09:10',
    '2025-06-18 10:09:10'
  ),
  (
    2,
    1,
    NULL,
    'Обустройство крыши',
    NULL,
    NULL,
    NULL,
    '2025-06-18 10:09:10',
    '2025-06-18 10:09:10'
  ),
  (
    3,
    1,
    NULL,
    'Кладка кирпича, камня',
    NULL,
    NULL,
    NULL,
    '2025-06-18 10:09:10',
    '2025-06-18 10:09:10'
  ),
  (
    4,
    1,
    NULL,
    'Земляные работы',
    NULL,
    NULL,
    NULL,
    '2025-06-18 10:09:10',
    '2025-06-18 10:09:10'
  ),
  (
    5,
    1,
    NULL,
    'Фасадные работы',
    NULL,
    NULL,
    NULL,
    '2025-06-18 10:09:10',
    '2025-06-18 10:09:10'
  ),
  (
    6,
    1,
    NULL,
    'Штукатурка, шпаклевка',
    NULL,
    NULL,
    NULL,
    '2025-06-18 10:09:10',
    '2025-06-18 10:09:39'
  ),
  (
    7,
    1,
    NULL,
    'Укладка керамической плитки',
    NULL,
    NULL,
    NULL,
    '2025-06-18 10:09:10',
    '2025-06-18 10:09:10'
  ),
  (
    10,
    1,
    NULL,
    'Стяжка пола',
    NULL,
    NULL,
    NULL,
    '2025-06-18 10:11:48',
    '2025-06-18 10:11:48'
  ),
  (
    11,
    1,
    NULL,
    'Демонтаж',
    NULL,
    NULL,
    NULL,
    '2025-06-23 12:48:16',
    '2025-06-23 12:48:16'
  ),
  (
    12,
    2,
    NULL,
    'Водопровод',
    NULL,
    NULL,
    NULL,
    '2025-06-23 12:52:59',
    '2025-06-23 12:52:59'
  ),
  (
    13,
    2,
    NULL,
    'Канализация',
    NULL,
    NULL,
    NULL,
    '2025-06-23 12:52:59',
    '2025-06-23 12:52:59'
  ),
  (
    14,
    2,
    NULL,
    'Отопление',
    NULL,
    NULL,
    NULL,
    '2025-06-23 12:52:59',
    '2025-06-23 12:52:59'
  ),
  (
    15,
    2,
    NULL,
    'Газоснабжение',
    NULL,
    NULL,
    NULL,
    '2025-06-23 12:52:59',
    '2025-06-23 12:52:59'
  ),
  (
    16,
    2,
    NULL,
    'Вентиляция',
    NULL,
    NULL,
    NULL,
    '2025-06-23 12:52:59',
    '2025-06-23 12:52:59'
  ),
  (
    17,
    2,
    NULL,
    'Кондиционирование',
    NULL,
    NULL,
    NULL,
    '2025-06-23 12:52:59',
    '2025-06-23 12:52:59'
  ),
  (
    18,
    2,
    NULL,
    'Электроснабжение',
    NULL,
    NULL,
    NULL,
    '2025-06-23 12:52:59',
    '2025-06-23 12:52:59'
  ),
  (
    19,
    3,
    NULL,
    'Дикий камень',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:02:41',
    '2025-06-23 13:02:41'
  ),
  (
    20,
    3,
    NULL,
    'Блоки (француз и тп)',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:02:41',
    '2025-06-23 13:02:41'
  ),
  (
    21,
    3,
    NULL,
    'Кирпичные',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:02:41',
    '2025-06-23 13:02:41'
  ),
  (
    22,
    3,
    NULL,
    'Сварной секционный',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:02:41',
    '2025-06-23 13:02:41'
  ),
  (
    23,
    3,
    NULL,
    'Профнастил, штакетник',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:02:41',
    '2025-06-23 13:02:41'
  ),
  (
    24,
    3,
    NULL,
    'Жалюзи',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:02:41',
    '2025-06-23 13:02:41'
  ),
  (
    25,
    3,
    NULL,
    'Сетка 3д',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:02:41',
    '2025-06-23 13:02:41'
  ),
  (
    26,
    4,
    NULL,
    'Резка',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:08:29',
    '2025-06-23 13:08:29'
  ),
  (
    27,
    4,
    NULL,
    'Сварка электродуговая',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:08:29',
    '2025-06-23 13:18:58'
  ),
  (
    28,
    4,
    NULL,
    'Гнутье',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:08:29',
    '2025-06-23 13:08:29'
  ),
  (
    29,
    4,
    NULL,
    'Металлоконструкции',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:08:29',
    '2025-06-23 13:08:29'
  ),
  (
    33,
    4,
    NULL,
    'Покраска',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:15:07',
    '2025-06-23 13:15:07'
  ),
  (
    34,
    3,
    NULL,
    'Ворота',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:15:58',
    '2025-06-23 13:15:58'
  ),
  (
    35,
    3,
    NULL,
    'Калитки',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:16:13',
    '2025-06-23 13:16:13'
  ),
  (
    36,
    4,
    NULL,
    'Сварка газовая',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:19:21',
    '2025-06-23 13:19:21'
  ),
  (
    37,
    4,
    NULL,
    'Нарезка резьбы',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:25:11',
    '2025-06-23 13:25:11'
  ),
  (
    38,
    4,
    NULL,
    'Токарь',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:25:27',
    '2025-06-23 13:25:27'
  ),
  (
    39,
    4,
    NULL,
    'Слесарь',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:26:06',
    '2025-06-23 13:26:06'
  ),
  (
    40,
    5,
    NULL,
    'Дерево изготовление',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:30:06',
    '2025-06-23 13:30:06'
  ),
  (
    41,
    5,
    NULL,
    'Металл изготовление',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:30:06',
    '2025-06-23 13:30:06'
  ),
  (
    42,
    5,
    NULL,
    'Монтаж дверей',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:30:06',
    '2025-06-23 13:30:06'
  ),
  (
    43,
    5,
    NULL,
    'Установка замков',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:30:06',
    '2025-06-23 13:30:06'
  ),
  (
    44,
    5,
    NULL,
    'Обслуживание и ремонт дверей',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:30:06',
    '2025-06-23 13:30:06'
  ),
  (
    45,
    6,
    NULL,
    'Изготовление деревянных окон',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:31:47',
    '2025-06-23 13:31:47'
  ),
  (
    46,
    6,
    NULL,
    'Установка окон',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:31:47',
    '2025-06-23 13:31:47'
  ),
  (
    47,
    6,
    NULL,
    'Обслуживание и ремонт окон',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:31:47',
    '2025-06-23 13:31:47'
  ),
  (
    48,
    5,
    NULL,
    'Установка доводчиков',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:32:59',
    '2025-06-23 13:32:59'
  ),
  (
    49,
    5,
    NULL,
    'Электромеханические замки',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:34:24',
    '2025-06-23 13:34:57'
  ),
  (
    50,
    7,
    NULL,
    'Прокладка внутренней проводки',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:47:42',
    '2025-06-23 13:49:32'
  ),
  (
    51,
    7,
    NULL,
    'Установка и подключение оборудования, приборов',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:47:42',
    '2025-06-23 13:50:30'
  ),
  (
    52,
    7,
    NULL,
    'Установка розеток, выключателей, переключателей',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:47:42',
    '2025-06-23 13:47:42'
  ),
  (
    53,
    7,
    NULL,
    'Сигнализация',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:47:42',
    '2025-06-23 13:47:42'
  ),
  (
    54,
    7,
    NULL,
    'Видеонаблюдение',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:47:42',
    '2025-06-23 13:47:42'
  ),
  (
    55,
    7,
    NULL,
    'Расчеты и проекты',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:47:42',
    '2025-06-23 13:47:42'
  ),
  (
    56,
    7,
    NULL,
    'Автоматика, СКУД',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:47:42',
    '2025-06-23 13:47:42'
  ),
  (
    57,
    7,
    NULL,
    'Прокладка наружного кабеля',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:49:53',
    '2025-06-23 13:49:53'
  ),
  (
    58,
    3,
    NULL,
    'Автоматика ворот',
    NULL,
    NULL,
    NULL,
    '2025-06-23 13:51:39',
    '2025-06-23 13:51:39'
  ),
  (
    59,
    8,
    NULL,
    'Установка и замена смесителей, кранов, раковин, унитазов, ванн',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:03:28',
    '2025-06-23 14:03:28'
  ),
  (
    60,
    8,
    NULL,
    'Ликвидация протечек, замена труб и их разводка',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:03:28',
    '2025-06-23 14:03:28'
  ),
  (
    61,
    8,
    NULL,
    'Прочистка канализационной системы',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:03:28',
    '2025-06-23 14:03:28'
  ),
  (
    62,
    8,
    NULL,
    'Монтаж и демонтаж систем водоснабжения и отопления',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:03:28',
    '2025-06-23 14:03:28'
  ),
  (
    63,
    8,
    NULL,
    'Чистка бойлеров, устранение накипи',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:03:28',
    '2025-06-23 14:03:28'
  ),
  (
    64,
    8,
    NULL,
    'Установка и подключение оборудования',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:03:28',
    '2025-06-25 12:21:28'
  ),
  (
    65,
    1,
    NULL,
    'Гипсокартон монтаж',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:10:36',
    '2025-06-23 14:10:36'
  ),
  (
    66,
    1,
    NULL,
    'Плотник',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:11:12',
    '2025-06-23 14:11:12'
  ),
  (
    67,
    1,
    NULL,
    'Столяр',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:11:29',
    '2025-06-23 14:11:29'
  ),
  (
    68,
    1,
    NULL,
    'Маляр',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:12:06',
    '2025-06-23 14:12:06'
  ),
  (
    69,
    1,
    NULL,
    'Стекло резка, монтаж',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:13:41',
    '2025-06-23 14:13:41'
  ),
  (
    70,
    1,
    NULL,
    'Потолки монтаж',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:16:50',
    '2025-06-23 14:16:50'
  ),
  (
    71,
    1,
    NULL,
    'Полы монтаж покрытия',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:17:08',
    '2025-06-23 14:17:08'
  ),
  (
    72,
    1,
    NULL,
    'Обои поклейка',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:17:53',
    '2025-06-23 14:17:53'
  ),
  (
    73,
    9,
    NULL,
    'Ходовая, тормозная система ремонт',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:24:34',
    '2025-06-23 14:24:34'
  ),
  (
    74,
    9,
    NULL,
    'Рулевая ремонт',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:24:34',
    '2025-06-23 14:24:34'
  ),
  (
    75,
    9,
    NULL,
    'Электрика ремонт',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:24:34',
    '2025-06-23 14:24:34'
  ),
  (
    76,
    9,
    NULL,
    'Коробка передач, сцепление ремонт',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:24:34',
    '2025-06-23 14:24:34'
  ),
  (
    77,
    9,
    NULL,
    'Отопление, кондиционер ремонт',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:24:34',
    '2025-06-23 14:24:34'
  ),
  (
    78,
    9,
    NULL,
    'Чистка салона',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:24:34',
    '2025-06-23 14:24:34'
  ),
  (
    79,
    9,
    NULL,
    'Рихтовка, сварка кузова',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:24:34',
    '2025-06-23 14:24:34'
  ),
  (
    80,
    9,
    NULL,
    'Покраска деталей кузова',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:25:06',
    '2025-06-23 14:25:06'
  ),
  (
    81,
    10,
    NULL,
    'Двигатель ремонт',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:28:35',
    '2025-06-23 14:28:35'
  ),
  (
    82,
    10,
    NULL,
    'Электрика ремонт',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:28:35',
    '2025-06-23 14:28:35'
  ),
  (
    83,
    10,
    NULL,
    'Ходовая, тормозная система ремонт',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:28:35',
    '2025-06-23 14:28:35'
  ),
  (
    84,
    10,
    NULL,
    'Покраска',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:28:35',
    '2025-06-23 14:28:35'
  ),
  (
    85,
    11,
    NULL,
    'Малые объемы (вело, мото)',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:33:04',
    '2025-06-23 14:33:04'
  ),
  (
    86,
    11,
    NULL,
    'Легковой автомобиль',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:33:04',
    '2025-06-23 14:33:04'
  ),
  (
    87,
    11,
    NULL,
    'До 3,5 тонн грузовик',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:33:04',
    '2025-06-23 14:33:04'
  ),
  (
    88,
    11,
    NULL,
    'Больше 3,5 тонн грузовик',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:33:04',
    '2025-06-23 14:33:04'
  ),
  (
    89,
    11,
    NULL,
    'Трактор',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:33:04',
    '2025-06-23 14:33:04'
  ),
  (
    90,
    11,
    NULL,
    'Погрузчик',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:33:04',
    '2025-06-23 14:33:04'
  ),
  (
    91,
    11,
    NULL,
    'Эвакуатор',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:33:04',
    '2025-06-23 14:33:04'
  ),
  (
    92,
    12,
    NULL,
    'Садовник',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:35:32',
    '2025-06-23 14:35:32'
  ),
  (
    93,
    12,
    NULL,
    'Сезонный рабочий',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:35:32',
    '2025-06-23 14:35:32'
  ),
  (
    94,
    12,
    NULL,
    'Посадка деревьев',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:36:43',
    '2025-06-23 14:36:43'
  ),
  (
    95,
    12,
    NULL,
    'Устранение вредителей',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:37:35',
    '2025-06-23 14:37:35'
  ),
  (
    96,
    13,
    NULL,
    'Парикмахер',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:41:22',
    '2025-06-23 14:41:22'
  ),
  (
    97,
    13,
    NULL,
    'Маникюр, педикюр',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:41:22',
    '2025-06-23 14:41:22'
  ),
  (
    98,
    13,
    NULL,
    'Пилинг',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:41:22',
    '2025-06-23 14:41:22'
  ),
  (
    99,
    13,
    NULL,
    'СПА процедуры',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:41:22',
    '2025-06-23 14:41:22'
  ),
  (
    100,
    13,
    NULL,
    'Наращивание волос',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:41:22',
    '2025-06-23 14:41:22'
  ),
  (
    101,
    13,
    NULL,
    'Ресницы, брови',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:41:22',
    '2025-06-23 14:41:22'
  ),
  (
    102,
    14,
    NULL,
    'Массаж',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:44:06',
    '2025-06-23 14:44:06'
  ),
  (
    103,
    14,
    NULL,
    'Иглоукалывание',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:44:06',
    '2025-06-23 14:44:06'
  ),
  (
    104,
    14,
    NULL,
    'Уколы',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:44:06',
    '2025-06-23 14:44:06'
  ),
  (
    105,
    14,
    NULL,
    'Уход, сиделка',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:44:06',
    '2025-06-23 14:44:06'
  ),
  (
    106,
    15,
    NULL,
    'Репетитор',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:44:54',
    '2025-06-23 14:44:54'
  ),
  (
    107,
    13,
    NULL,
    'Фитнесс тренер',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:45:50',
    '2025-06-23 14:45:50'
  ),
  (
    108,
    11,
    NULL,
    'Грузчик',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:47:19',
    '2025-06-23 14:47:19'
  ),
  (
    109,
    1,
    NULL,
    'Разнорабочий',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:47:48',
    '2025-06-23 14:47:48'
  ),
  (
    110,
    16,
    NULL,
    'Диджей',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:50:39',
    '2025-06-23 14:50:39'
  ),
  (
    111,
    16,
    NULL,
    'Ведущий',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:50:39',
    '2025-06-23 14:50:39'
  ),
  (
    112,
    16,
    NULL,
    'Клоун',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:50:39',
    '2025-06-23 14:50:39'
  ),
  (
    113,
    16,
    NULL,
    'Актер',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:50:39',
    '2025-06-23 14:50:39'
  ),
  (
    114,
    16,
    NULL,
    'Танцор, танцовщица',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:50:39',
    '2025-06-23 14:50:39'
  ),
  (
    115,
    16,
    NULL,
    'Музыкант',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:50:39',
    '2025-06-23 14:50:39'
  ),
  (
    116,
    16,
    NULL,
    'Оркестр, группа',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:50:39',
    '2025-06-23 14:50:39'
  ),
  (
    117,
    17,
    NULL,
    'Гид',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:55:42',
    '2025-06-23 14:55:42'
  ),
  (
    118,
    17,
    NULL,
    'Автомобиль с водителем',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:55:42',
    '2025-06-23 14:55:42'
  ),
  (
    119,
    17,
    NULL,
    'Судно до 5 человек',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:55:42',
    '2025-06-23 14:55:42'
  ),
  (
    120,
    17,
    NULL,
    'Судно до 10 человек',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:55:42',
    '2025-06-23 14:55:42'
  ),
  (
    121,
    17,
    NULL,
    'Судно больше 10 человек',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:55:42',
    '2025-06-23 14:55:42'
  ),
  (
    122,
    17,
    NULL,
    'Автобус',
    NULL,
    NULL,
    NULL,
    '2025-06-23 14:55:42',
    '2025-06-23 14:55:42'
  ),
  (
    123,
    18,
    NULL,
    'Ремонт компьютеров',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:03:46',
    '2025-06-23 15:03:46'
  ),
  (
    124,
    18,
    NULL,
    'Ремонт телефонов',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:03:46',
    '2025-06-23 15:03:46'
  ),
  (
    125,
    18,
    NULL,
    'Сборка компьютера',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:03:46',
    '2025-06-25 12:22:37'
  ),
  (
    126,
    18,
    NULL,
    'Системный администратор',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:03:46',
    '2025-06-23 15:03:46'
  ),
  (
    127,
    18,
    NULL,
    'Техподдержка',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:03:46',
    '2025-06-23 15:03:46'
  ),
  (
    128,
    19,
    NULL,
    'Системное',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:08:19',
    '2025-06-23 15:08:19'
  ),
  (
    129,
    19,
    NULL,
    'Пользовательские приложения',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:08:19',
    '2025-06-23 15:08:19'
  ),
  (
    130,
    19,
    NULL,
    'Веб ',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:08:19',
    '2025-06-23 15:08:19'
  ),
  (
    131,
    19,
    NULL,
    'Встроенные системы',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:08:19',
    '2025-06-23 15:08:19'
  ),
  (
    132,
    19,
    NULL,
    'Промышленные станки',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:08:19',
    '2025-06-23 15:08:19'
  ),
  (
    133,
    20,
    NULL,
    'Фотограф',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:10:49',
    '2025-06-23 15:10:49'
  ),
  (
    134,
    20,
    NULL,
    'Оператор кино, видеосъемки',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:10:49',
    '2025-06-23 15:10:49'
  ),
  (
    135,
    20,
    NULL,
    'Обработка фото, видео',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:10:49',
    '2025-06-23 15:10:49'
  ),
  (
    136,
    12,
    NULL,
    'Срижка газонов, травы',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:12:20',
    '2025-06-23 15:12:20'
  ),
  (
    137,
    12,
    NULL,
    'Спил деревьев',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:12:46',
    '2025-06-23 15:12:46'
  ),
  (
    138,
    21,
    NULL,
    'Ведение блога, страниц в соцсетях',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:17:18',
    '2025-06-23 15:17:18'
  ),
  (
    139,
    21,
    NULL,
    'Рекомендации для сайтов по СЕО',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:17:18',
    '2025-06-23 15:17:18'
  ),
  (
    140,
    21,
    NULL,
    'Проведение рекламных кампаний',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:17:18',
    '2025-06-23 15:17:18'
  ),
  (
    141,
    22,
    NULL,
    'Выгул',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:21:21',
    '2025-06-23 15:21:21'
  ),
  (
    142,
    22,
    NULL,
    'Присмотр на время',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:21:21',
    '2025-06-23 15:21:21'
  ),
  (
    143,
    22,
    NULL,
    'Груминг',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:21:21',
    '2025-06-23 15:21:21'
  ),
  (
    144,
    22,
    NULL,
    'Обучение, тренировки',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:21:21',
    '2025-06-23 15:21:21'
  ),
  (
    145,
    22,
    NULL,
    'Прогулка на лошади',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:21:21',
    '2025-06-23 15:21:21'
  ),
  (
    146,
    22,
    NULL,
    'Аквариум, террариум изготовление и ремонт',
    NULL,
    NULL,
    NULL,
    '2025-06-23 15:22:13',
    '2025-06-23 15:22:13'
  ),
  (
    147,
    23,
    NULL,
    'Повар',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:49:45',
    '2025-06-24 13:49:45'
  ),
  (
    148,
    23,
    NULL,
    'Доставка',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:50:00',
    '2025-06-24 13:50:00'
  ),
  (
    149,
    9,
    NULL,
    'Водитель',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:51:02',
    '2025-06-24 13:51:02'
  ),
  (
    150,
    10,
    NULL,
    'Водитель',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:51:22',
    '2025-06-24 13:51:22'
  ),
  (
    151,
    20,
    NULL,
    'Визуализация интерьеров',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:52:34',
    '2025-06-24 13:52:34'
  ),
  (
    152,
    20,
    NULL,
    'Проектирование и чертежи',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:53:43',
    '2025-06-24 13:53:43'
  ),
  (
    153,
    1,
    NULL,
    'Документооборот',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:54:34',
    '2025-06-24 13:54:34'
  ),
  (
    154,
    1,
    NULL,
    'Сметчик',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:55:00',
    '2025-06-24 13:55:00'
  ),
  (
    155,
    19,
    NULL,
    '1С',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:56:06',
    '2025-06-24 13:56:06'
  ),
  (
    156,
    19,
    NULL,
    'Базы данных',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:56:30',
    '2025-06-24 13:56:30'
  ),
  (
    157,
    19,
    NULL,
    'Сайт создание',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:57:24',
    '2025-06-24 13:57:24'
  ),
  (
    158,
    21,
    NULL,
    'Промоутер',
    NULL,
    NULL,
    NULL,
    '2025-06-24 13:58:01',
    '2025-06-24 13:58:01'
  ),
  (
    159,
    18,
    NULL,
    'Установка, настройка ОС и программ',
    NULL,
    NULL,
    NULL,
    '2025-06-25 12:23:38',
    '2025-06-25 12:23:38'
  ),
  (
    160,
    18,
    NULL,
    'Подключение и настройка периферийных устройств',
    NULL,
    NULL,
    NULL,
    '2025-06-25 12:23:59',
    '2025-06-25 12:23:59'
  ),
  (
    161,
    24,
    NULL,
    'Пошив, ремонт одежды',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:07:25',
    '2025-06-25 14:07:25'
  ),
  (
    162,
    24,
    NULL,
    'Разработка дизайна',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:07:25',
    '2025-06-25 14:07:25'
  ),
  (
    163,
    24,
    NULL,
    ' Создание карт раскроя',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:07:25',
    '2025-06-25 14:07:25'
  ),
  (
    164,
    24,
    NULL,
    'Пошив обуви',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:07:25',
    '2025-06-25 14:07:25'
  ),
  (
    165,
    24,
    NULL,
    'Создание колодок',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:07:25',
    '2025-06-25 14:07:25'
  ),
  (
    166,
    24,
    NULL,
    'Швея',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:07:25',
    '2025-06-25 14:07:25'
  ),
  (
    167,
    24,
    NULL,
    'Ремонт обуви',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:07:25',
    '2025-06-25 14:07:25'
  ),
  (
    168,
    24,
    NULL,
    'Пошив головных уборов',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:08:01',
    '2025-06-25 14:08:01'
  ),
  (
    169,
    25,
    NULL,
    'Проектирование',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:10:37',
    '2025-06-25 14:10:37'
  ),
  (
    170,
    25,
    NULL,
    'Карты раскроя',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:10:37',
    '2025-06-25 14:10:37'
  ),
  (
    171,
    25,
    NULL,
    'Распил',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:10:37',
    '2025-06-25 14:10:37'
  ),
  (
    172,
    25,
    NULL,
    'Отделка кромки',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:10:37',
    '2025-06-25 14:10:37'
  ),
  (
    173,
    25,
    NULL,
    'Сборка мебели',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:10:37',
    '2025-06-25 14:10:37'
  ),
  (
    174,
    25,
    NULL,
    'Мебель из дерева',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:10:37',
    '2025-06-25 14:10:37'
  ),
  (
    175,
    25,
    NULL,
    'Ремонт мебели',
    NULL,
    NULL,
    NULL,
    '2025-06-25 14:10:37',
    '2025-06-25 14:10:37'
  );

-- --------------------------------------------------------
--
-- Структура таблицы `user`
--
-- Создание: Июн 27 2025 г., 13:11
-- Последнее обновление: Июн 27 2025 г., 13:14
--
DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(512) NOT NULL,
  `image` text DEFAULT NULL,
  `password` text NOT NULL,
  `phone` varchar(12) DEFAULT NULL,
  `phone_verified` tinyint(4) DEFAULT NULL,
  `email` text DEFAULT NULL,
  `email_verified` tinyint(4) DEFAULT NULL,
  `auth_token` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `user`:
--
-- --------------------------------------------------------
--
-- Структура таблицы `userappliedforregistration`
--
-- Создание: Июн 27 2025 г., 13:11
--
DROP TABLE IF EXISTS `userappliedforregistration`;

CREATE TABLE `userappliedforregistration` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- ССЫЛКИ ТАБЛИЦЫ `userappliedforregistration`:
--
--
-- Индексы сохранённых таблиц
--
--
-- Индексы таблицы `category`
--
ALTER TABLE
  `category`
ADD
  PRIMARY KEY (`id`);

--
-- Индексы таблицы `chat`
--
ALTER TABLE
  `chat`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `parent_id` (`parent_id`),
ADD
  KEY `client_id_who` (`client_id_who`),
ADD
  KEY `client_id_to_whom` (`client_id_to_whom`),
ADD
  KEY `offer_id` (`offer_id`);

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
-- Индексы таблицы `comment`
--
ALTER TABLE
  `comment`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `parent_id` (`parent_id`),
ADD
  KEY `client_id` (`client_id`),
ADD
  KEY `offer_id` (`offer_id`);

--
-- Индексы таблицы `grievance`
--
ALTER TABLE
  `grievance`
ADD
  PRIMARY KEY (`id`);

--
-- Индексы таблицы `offer`
--
ALTER TABLE
  `offer`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `client_id` (`client_id`);

--
-- Индексы таблицы `offer_image_thumb`
--
ALTER TABLE
  `offer_image_thumb`
ADD
  PRIMARY KEY (`id`);

--
-- Индексы таблицы `offer_service`
--
ALTER TABLE
  `offer_service`
ADD
  PRIMARY KEY (`offer_id`, `service_id`),
ADD
  KEY `service_id` (`service_id`);

--
-- Индексы таблицы `permission`
--
ALTER TABLE
  `permission`
ADD
  PRIMARY KEY (`id`);

--
-- Индексы таблицы `rating`
--
ALTER TABLE
  `rating`
ADD
  PRIMARY KEY (`client_id_who`, `client_id_to_whom`),
ADD
  KEY `client_id_to_whom` (`client_id_to_whom`);

--
-- Индексы таблицы `requestforaddingservice`
--
ALTER TABLE
  `requestforaddingservice`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `client_id` (`client_id`),
ADD
  KEY `category_id` (`category_id`);

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
  PRIMARY KEY (`role_id`, `user_id`),
ADD
  KEY `user_id` (`user_id`);

--
-- Индексы таблицы `role_permission`
--
ALTER TABLE
  `role_permission`
ADD
  PRIMARY KEY (`role_id`, `permission_id`),
ADD
  KEY `permission_id` (`permission_id`);

--
-- Индексы таблицы `role_user`
--
ALTER TABLE
  `role_user`
ADD
  PRIMARY KEY (`role_id`, `user_id`),
ADD
  KEY `user_id` (`user_id`);

--
-- Индексы таблицы `service`
--
ALTER TABLE
  `service`
ADD
  PRIMARY KEY (`id`),
ADD
  KEY `category_id` (`category_id`);

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
-- Индексы таблицы `userappliedforregistration`
--
ALTER TABLE
  `userappliedforregistration`
ADD
  PRIMARY KEY (`id`),
ADD
  UNIQUE KEY `phone` (`phone`),
ADD
  UNIQUE KEY `email` (`email`) USING HASH;

--
-- AUTO_INCREMENT для сохранённых таблиц
--
--
-- AUTO_INCREMENT для таблицы `category`
--
ALTER TABLE
  `category`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 26;

--
-- AUTO_INCREMENT для таблицы `chat`
--
ALTER TABLE
  `chat`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `client`
--
ALTER TABLE
  `client`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 2;

--
-- AUTO_INCREMENT для таблицы `comment`
--
ALTER TABLE
  `comment`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `grievance`
--
ALTER TABLE
  `grievance`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `offer`
--
ALTER TABLE
  `offer`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `offer_image_thumb`
--
ALTER TABLE
  `offer_image_thumb`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `permission`
--
ALTER TABLE
  `permission`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `requestforaddingservice`
--
ALTER TABLE
  `requestforaddingservice`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE
  `role`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 6;

--
-- AUTO_INCREMENT для таблицы `service`
--
ALTER TABLE
  `service`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 176;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE
  `user`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  AUTO_INCREMENT = 2;

--
-- AUTO_INCREMENT для таблицы `userappliedforregistration`
--
ALTER TABLE
  `userappliedforregistration`
MODIFY
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа сохраненных таблиц
--
--
-- Ограничения внешнего ключа таблицы `chat`
--
ALTER TABLE
  `chat`
ADD
  CONSTRAINT `chat_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `chat` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `chat_ibfk_2` FOREIGN KEY (`client_id_who`) REFERENCES `client` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `chat_ibfk_3` FOREIGN KEY (`client_id_to_whom`) REFERENCES `client` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `chat_ibfk_4` FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `comment`
--
ALTER TABLE
  `comment`
ADD
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `comment` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `comment_ibfk_3` FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `offer`
--
ALTER TABLE
  `offer`
ADD
  CONSTRAINT `offer_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `offer_service`
--
ALTER TABLE
  `offer_service`
ADD
  CONSTRAINT `offer_service_ibfk_1` FOREIGN KEY (`offer_id`) REFERENCES `offer` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `offer_service_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `rating`
--
ALTER TABLE
  `rating`
ADD
  CONSTRAINT `rating_ibfk_1` FOREIGN KEY (`client_id_who`) REFERENCES `client` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `rating_ibfk_2` FOREIGN KEY (`client_id_to_whom`) REFERENCES `client` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `requestforaddingservice`
--
ALTER TABLE
  `requestforaddingservice`
ADD
  CONSTRAINT `requestforaddingservice_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `requestforaddingservice_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `role_client`
--
ALTER TABLE
  `role_client`
ADD
  CONSTRAINT `role_client_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `client` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `role_client_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `role_permission`
--
ALTER TABLE
  `role_permission`
ADD
  CONSTRAINT `role_permission_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `role_permission_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permission` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `role_user`
--
ALTER TABLE
  `role_user`
ADD
  CONSTRAINT `role_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
ADD
  CONSTRAINT `role_user_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `service`
--
ALTER TABLE
  `service`
ADD
  CONSTRAINT `service_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;