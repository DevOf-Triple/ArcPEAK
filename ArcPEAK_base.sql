-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.4
-- Время создания: Окт 24 2026 г., 02:44
-- Версия сервера: 8.4.6
-- Версия PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `ArcPEAK_base`
--

-- --------------------------------------------------------

--
-- Структура таблицы `buildings`
--

CREATE TABLE `buildings` (
  `id` int NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `base_price` decimal(12,2) NOT NULL,
  `area_m2` decimal(8,2) NOT NULL,
  `floors` tinyint NOT NULL DEFAULT '1',
  `bedrooms` tinyint NOT NULL DEFAULT '0',
  `bathrooms` tinyint NOT NULL DEFAULT '1',
  `garage` enum('none','canopy','1','2plus') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `style` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wall_material` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roof_type` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extra_features` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `buildings`
--

INSERT INTO `buildings` (`id`, `name`, `slug`, `image_path`, `description`, `base_price`, `area_m2`, `floors`, `bedrooms`, `bathrooms`, `garage`, `style`, `wall_material`, `roof_type`, `extra_features`, `created_at`) VALUES
(1, 'Резиденция «Сосновый бор»', 'sosnovy-bor', NULL, 'Двухэтажная резиденция с панорамным остеклением и террасой на кровле.', 18500000.00, 320.00, 2, 4, 3, '2plus', 'modern', 'gazobeton', 'flat', '[\"office\", \"wardrobe\", \"sauna\", \"terrace\"]', '2026-10-23 21:28:04'),
(2, 'Дом «Северный ветер»', 'severny-veter', NULL, 'Компактный дом для семьи из трёх человек. Скандинавский стиль.', 7800000.00, 145.00, 2, 3, 2, '1', 'scandi', 'tree', '2skat', '[\"wardrobe\", \"terrace\"]', '2026-10-23 21:28:04'),
(3, 'Усадьба «Тихий берег»', 'tihiy-bereg', NULL, 'Классическая усадьба с колоннами и садом. Три этажа, панорамный вид.', 24000000.00, 410.00, 3, 5, 4, '2plus', 'classic', 'kirpich', '4skat', '[\"office\", \"sauna\", \"pool\", \"terrace\"]', '2026-10-23 21:28:04'),
(4, 'Барнхаус «Поле»', 'barnhaus-pole', NULL, 'Барнхаус с двускатной кровлей и большими окнами на юг.', 9200000.00, 165.00, 1, 3, 2, 'canopy', 'barnhouse', 'tree', '2skat', '[\"wardrobe\", \"terrace\"]', '2026-10-23 21:28:04'),
(5, 'Минимализм «Куб»', 'minimal-kub', NULL, 'Одноэтажный дом-куб с плоской кровлей и внутренним двориком.', 11500000.00, 180.00, 1, 3, 2, '1', 'minimal', 'gazobeton', 'flat', '[\"office\", \"terrace\"]', '2026-10-23 21:28:04'),
(6, 'Шале «Альпы»', 'shale-alpy', NULL, 'Шале с камином и винтовой лестницей. Вид на горы.', 15200000.00, 210.00, 2, 4, 3, '1', 'shale', 'tree', '2skat', '[\"sauna\", \"wardrobe\", \"terrace\"]', '2026-10-23 21:28:04'),
(7, 'Фахверк «Классик»', 'fahverk-klassik', NULL, 'Немецкий фахверк, ручная работа по дереву.', 16800000.00, 240.00, 2, 4, 3, '2plus', 'fahverk', 'tree', '4skat', '[\"office\", \"sauna\"]', '2026-10-23 21:28:04'),
(8, 'Хай-тек «Стекло»', 'hitek-steklo', NULL, 'Дом из стекла и металла с плоской кровлей. Умный дом.', 21000000.00, 280.00, 2, 3, 3, '2plus', 'hitek', 'kirpich', 'flat', '[\"office\", \"pool\", \"terrace\"]', '2026-10-23 21:28:04');

-- --------------------------------------------------------

--
-- Структура таблицы `building_sheets`
--

CREATE TABLE `building_sheets` (
  `id` int NOT NULL,
  `building_id` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `building_sheets`
--

INSERT INTO `building_sheets` (`id`, `building_id`, `title`, `body`, `sort_order`) VALUES
(1, 1, 'Планировка', 'Первый этаж: гостиная-столовая 65 м², кухня 22 м², кабинет 18 м², санузел. Второй этаж: 4 спальни, 2 санузла, гардеробная.', 1),
(2, 1, 'Спецификация', 'Газобетон D500, кровля плоская с ПВХ-мембраной, окна — алюминиевый профиль с тройным стеклопакетом.', 2),
(3, 1, 'Описание комнат', 'Мастер-спальня 28 м² с выходом на террасу. Гостиная с двойным светом и камином.', 3),
(4, 2, 'Планировка', 'Компактная планировка: кухня-гостиная 32 м², 3 спальни, 2 санузла, терраса.', 1),
(5, 2, 'Спецификация', 'Каркас из сухой строганой доски, утепление 200 мм, кровля двускатная металлочерепица.', 2),
(6, 3, 'Планировка', 'Первый этаж: парадная гостиная, столовая, кухня, кабинет. Второй: 3 спальни, 2 санузла. Третий: мастер-спальня с гардеробной и выходом на балкон.', 1),
(7, 3, 'Спецификация', 'Кирпич керамический, кровля четырёхскатная натуральная черепица, окна — дерево-алюминий.', 2),
(8, 3, 'Описание комнат', 'Мастер-спальня 42 м², кабинет с библиотекой, зимний сад.', 3),
(9, 4, 'Планировка', 'Одноэтажный: кухня-гостиная 48 м², 3 спальни, 2 санузла, терраса 30 м².', 1),
(10, 4, 'Спецификация', 'Каркас, кровля двускатная, фасад — вертикальная деревянная рейка.', 2),
(11, 5, 'Планировка', 'Квадратный план: гостиная в центре, 3 спальни по углам, внутренний двор.', 1),
(12, 5, 'Спецификация', 'Газобетон, кровля плоская с зелёной эксплуатируемой кровлей.', 2),
(13, 6, 'Планировка', 'Первый этаж: гостиная с камином, кухня, сауна. Второй: 4 спальни, 3 санузла.', 1),
(14, 6, 'Спецификация', 'Каркас, кровля двускатная с широким свесом, фасад — дерево.', 2),
(15, 7, 'Планировка', 'Два этажа, классическая анфилада, 4 спальни, кабинет, сауна.', 1),
(16, 8, 'Планировка', 'Умный дом: панорамные окна, 3 спальни, кабинет, бассейн с подогревом.', 1),
(17, 8, 'Спецификация', 'Кирпич, кровля плоская, стеклянный фасад, инженерия — KNX.', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `catalog_tags`
--

CREATE TABLE `catalog_tags` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `catalog_tags`
--

INSERT INTO `catalog_tags` (`id`, `name`, `slug`, `sort_order`) VALUES
(1, 'Стили', 'styles', 1),
(2, 'Материалы', 'materials', 2),
(3, 'Кровля', 'roof', 3),
(4, 'Дополнительно', 'extra', 4);

-- --------------------------------------------------------

--
-- Структура таблицы `portfolio_items`
--

CREATE TABLE `portfolio_items` (
  `id` int NOT NULL,
  `project_id` int NOT NULL,
  `item_type` enum('building','sheet') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` int NOT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `portfolio_items`
--

INSERT INTO `portfolio_items` (`id`, `project_id`, `item_type`, `item_id`, `added_at`) VALUES
(49, 3, 'sheet', 7, '2026-10-23 22:56:18'),
(52, 3, 'sheet', 13, '2026-10-23 22:56:20');

-- --------------------------------------------------------

--
-- Структура таблицы `portfolio_projects`
--

CREATE TABLE `portfolio_projects` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `project_number` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Проект',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `portfolio_projects`
--

INSERT INTO `portfolio_projects` (`id`, `user_id`, `project_number`, `title`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Проект', '2026-10-23 21:49:27', '2026-10-23 22:50:16'),
(2, 1, 2, 'Вилла', '2026-10-23 21:49:34', '2026-10-23 22:57:18'),
(3, 1, 3, 'супер', '2026-10-23 22:32:01', '2026-10-23 22:57:24');

-- --------------------------------------------------------

--
-- Структура таблицы `sheets`
--

CREATE TABLE `sheets` (
  `id` int NOT NULL,
  `tag_id` int NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modifier_type` enum('per_m2_add','percent_add','percent_discount','flat_add','multiplier') COLLATE utf8mb4_unicode_ci NOT NULL,
  `modifier_value` decimal(12,4) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `sheets`
--

INSERT INTO `sheets` (`id`, `tag_id`, `name`, `description`, `image_path`, `modifier_type`, `modifier_value`, `sort_order`) VALUES
(1, 1, 'ЛОФТ', 'Индустриальный стиль: открытые коммуникации, кирпич, металл.', NULL, 'percent_add', 12.0000, 1),
(2, 1, 'МИНИМАЛИЗМ', 'Чистые линии, минимум деталей, максимум воздуха.', NULL, 'percent_discount', 5.0000, 2),
(3, 1, 'СКАНДИНАВСКИЙ', 'Светлые тона, дерево, натуральные материалы.', NULL, 'percent_add', 8.0000, 3),
(4, 1, 'БАРНХАУС', 'Современный взгляд на классический амбар.', NULL, 'percent_add', 10.0000, 4),
(5, 1, 'КЛАССИЧЕСКИЙ', 'Классические пропорции, натуральные материалы, декор.', NULL, 'percent_add', 25.0000, 5),
(6, 2, 'ГАЗОБЕТОН', 'Тёплый и недорогой стеновой материал.', NULL, 'per_m2_add', 0.0000, 1),
(7, 2, 'КИРПИЧ', 'Классика, долговечность, высокая теплоёмкость.', NULL, 'per_m2_add', 4500.0000, 2),
(8, 2, 'ДЕРЕВО (КАРКАС)', 'Быстрый монтаж, экологичность.', NULL, 'per_m2_add', 2200.0000, 3),
(9, 3, 'ПЛОСКАЯ', 'Современная плоская кровля с эксплуатируемой зоной.', NULL, 'flat_add', 350000.0000, 1),
(10, 3, 'ДВУСКАТНАЯ', 'Классическая двускатная кровля.', NULL, 'flat_add', 250000.0000, 2),
(11, 3, 'ЧЕТЫРЁХСКАТНАЯ', 'Четырёхскатная кровля, сложнее в монтаже.', NULL, 'flat_add', 480000.0000, 3),
(12, 4, 'КАБИНЕТ', 'Отдельный кабинет с рабочей зоной.', NULL, 'flat_add', 320000.0000, 1),
(13, 4, 'ГАРДЕРОБНАЯ', 'Просторная гардеробная с системой хранения.', NULL, 'flat_add', 280000.0000, 2),
(14, 4, 'САУНА', 'Финская сауна с парилкой и комнатой отдыха.', NULL, 'flat_add', 650000.0000, 3),
(15, 4, 'БАССЕЙН', 'Крытый бассейн 8×4 м с подогревом.', NULL, 'flat_add', 2400000.0000, 4),
(16, 4, 'ТЕРРАСА', 'Открытая терраса с настилом из лиственницы.', NULL, 'flat_add', 420000.0000, 5);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password_hash`, `full_name`, `email`, `created_at`) VALUES
(1, 'triple', '$2y$10$k3W95uyP13.KHtFmWXL0f.eC4.7G6aVheHWSyYaWlbuvaeXqeGlhS', 't r i', 'example@example.ru', '2026-10-16 20:20:15'),
(2, 'super', '$2y$10$ya8iDXXHjpGGlJ8xfC9NS.QWhlC1z4fiDUboUrPs8vtyiz9s66Ueq', 's u p', 'example@example.com', '2026-10-16 22:33:35');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Индексы таблицы `building_sheets`
--
ALTER TABLE `building_sheets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bs_building` (`building_id`);

--
-- Индексы таблицы `catalog_tags`
--
ALTER TABLE `catalog_tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Индексы таблицы `portfolio_items`
--
ALTER TABLE `portfolio_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_project_item` (`project_id`,`item_type`,`item_id`),
  ADD KEY `idx_project` (`project_id`);

--
-- Индексы таблицы `portfolio_projects`
--
ALTER TABLE `portfolio_projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_number` (`user_id`,`project_number`);

--
-- Индексы таблицы `sheets`
--
ALTER TABLE `sheets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sheet_tag` (`tag_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `login` (`login`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `building_sheets`
--
ALTER TABLE `building_sheets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT для таблицы `catalog_tags`
--
ALTER TABLE `catalog_tags`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `portfolio_items`
--
ALTER TABLE `portfolio_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT для таблицы `portfolio_projects`
--
ALTER TABLE `portfolio_projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `sheets`
--
ALTER TABLE `sheets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `building_sheets`
--
ALTER TABLE `building_sheets`
  ADD CONSTRAINT `fk_bs_building` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `portfolio_items`
--
ALTER TABLE `portfolio_items`
  ADD CONSTRAINT `fk_pi_project` FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `portfolio_projects`
--
ALTER TABLE `portfolio_projects`
  ADD CONSTRAINT `fk_pp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sheets`
--
ALTER TABLE `sheets`
  ADD CONSTRAINT `fk_sheet_tag` FOREIGN KEY (`tag_id`) REFERENCES `catalog_tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
