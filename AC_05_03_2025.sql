-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Мар 05 2025 г., 17:36
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `AC`
--
CREATE DATABASE IF NOT EXISTS AC CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';

USE AC;

-- --------------------------------------------------------

--
-- Структура таблицы `client`
--

CREATE TABLE `client` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `permission`
--

CREATE TABLE `permission` (
  `id` int(10) UNSIGNED NOT NULL,
  `resource` char(255) NOT NULL,
  `action` char(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `permission`
--

INSERT INTO `permission` (`id`, `resource`, `action`) VALUES
(1, 'User', 'getAllUsersData'),
(2, 'User', 'getUserData'),
(3, 'User', 'getRoless');

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

CREATE TABLE `role` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_name` char(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `role`
--

INSERT INTO `role` (`id`, `role_name`) VALUES
(1, 'admin'),
(3, 'client'),
(2, 'moder');

-- --------------------------------------------------------

--
-- Структура таблицы `role_client`
--

CREATE TABLE `role_client` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `role_permission`
--

CREATE TABLE `role_permission` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `role_permission`
--

INSERT INTO `role_permission` (`role_id`, `permission_id`) VALUES
(2, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `role_user`
--

CREATE TABLE `role_user` (
  `role_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `role_user`
--

INSERT INTO `role_user` (`role_id`, `user_id`) VALUES
(1, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE `user` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `username`, `image`, `password`, `phone`, `phone_verified`, `email`, `email_verified`, `auth_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, '$2y$10$OXI/zByxC5f.7aGx38kcu.IGCnefdrPY.Wg1nF5XXvzOY6I1YFY0O', NULL, 0, NULL, 0, 'kxgc9gq3xvl95ks', '2024-11-28 20:21:54', '2024-11-28 20:21:54'),
(2, 'moder', NULL, '$2y$10$zpz8PE/0PRj1Tj7JcIC6pe/nayVrVD1bOQeGFzjyBveXtX3iGaSqC', NULL, 0, NULL, 0, 'grvgqnoqy3irl06', '2025-01-05 13:35:55', '2025-01-05 13:35:55');

-- --------------------------------------------------------

--
-- Структура таблицы `userappliedforregistration`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`) USING HASH;

--
-- Индексы таблицы `permission`
--
ALTER TABLE `permission`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Индексы таблицы `role_client`
--
ALTER TABLE `role_client`
  ADD PRIMARY KEY (`role_id`,`user_id`);

--
-- Индексы таблицы `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`role_id`,`permission_id`);

--
-- Индексы таблицы `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`role_id`,`user_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `email` (`email`) USING HASH;

--
-- Индексы таблицы `userappliedforregistration`
--
ALTER TABLE `userappliedforregistration`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `client`
--
ALTER TABLE `client`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT для таблицы `permission`
--
ALTER TABLE `permission`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE `role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `userappliedforregistration`
--
ALTER TABLE `userappliedforregistration`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
