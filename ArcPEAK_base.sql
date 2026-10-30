-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Хост: MySQL-8.4
-- Время создания: Окт 30 2026 г., 21:46
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
  `sort_order` int NOT NULL DEFAULT '0',
  `is_key` tinyint(1) NOT NULL DEFAULT '0',
  `is_radio` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `catalog_tags`
--

INSERT INTO `catalog_tags` (`id`, `name`, `slug`, `sort_order`, `is_key`, `is_radio`) VALUES
(1, 'Тип постройки', 'type', 1, 1, 1),
(2, 'Площадь', 'area', 2, 1, 1),
(3, 'Этажность', 'floors', 3, 1, 1),
(4, 'Стили', 'styles', 4, 0, 0),
(5, 'Материалы стен', 'materials', 5, 0, 0),
(6, 'Кровля', 'roof', 6, 0, 0),
(7, 'Фундамент', 'foundation', 7, 0, 0),
(8, 'Окна', 'windows', 8, 0, 0),
(9, 'Фасад', 'facade', 9, 0, 0),
(10, 'Инженерия', 'engineering', 10, 0, 0),
(11, 'Доп. помещения', 'extra', 11, 0, 0),
(12, 'Умный дом', 'smart', 12, 0, 0),
(13, 'Ландшафт', 'landscape', 13, 0, 0),
(14, 'Отделка', 'interior', 14, 0, 0);

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
(162, 6, 'sheet', 20, '2026-10-29 21:27:45'),
(163, 6, 'sheet', 23, '2026-10-29 21:27:46'),
(164, 6, 'sheet', 27, '2026-10-29 21:27:47'),
(165, 6, 'sheet', 30, '2026-10-29 21:27:48'),
(166, 6, 'sheet', 33, '2026-10-29 21:27:49'),
(167, 6, 'sheet', 37, '2026-10-29 21:27:49'),
(168, 6, 'sheet', 40, '2026-10-29 21:27:50'),
(169, 6, 'sheet', 41, '2026-10-29 21:27:51'),
(170, 6, 'sheet', 45, '2026-10-29 21:27:52'),
(171, 6, 'sheet', 2, '2026-10-29 21:27:53'),
(172, 6, 'sheet', 7, '2026-10-29 21:27:54'),
(173, 6, 'sheet', 14, '2026-10-29 21:27:55');

-- --------------------------------------------------------

--
-- Структура таблицы `portfolio_projects`
--

CREATE TABLE `portfolio_projects` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `project_number` int NOT NULL,
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Проект',
  `project_kind` enum('ready','custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ready',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `portfolio_projects`
--

INSERT INTO `portfolio_projects` (`id`, `user_id`, `project_number`, `title`, `project_kind`, `created_at`, `updated_at`) VALUES
(6, 1, 1, 'Проект', 'custom', '2026-10-29 19:21:10', '2026-10-29 21:27:55');

-- --------------------------------------------------------

--
-- Структура таблицы `project_params`
--

CREATE TABLE `project_params` (
  `project_id` int NOT NULL,
  `region_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `project_params`
--

INSERT INTO `project_params` (`project_id`, `region_id`, `created_at`, `updated_at`) VALUES
(6, 17, '2026-10-29 19:21:10', '2026-10-29 21:28:24');

-- --------------------------------------------------------

--
-- Структура таблицы `project_sheet_overrides`
--

CREATE TABLE `project_sheet_overrides` (
  `id` int NOT NULL,
  `project_id` int NOT NULL,
  `sheet_id` int NOT NULL,
  `override_value` decimal(12,4) NOT NULL,
  `curator_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `regions`
--

CREATE TABLE `regions` (
  `id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `factor` decimal(6,4) NOT NULL DEFAULT '1.0000',
  `sort_order` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `regions`
--

INSERT INTO `regions` (`id`, `name`, `factor`, `sort_order`) VALUES
(1, 'Алтайский край', 1.0000, 1),
(2, 'Амурская область', 1.0000, 2),
(3, 'Архангельская область', 1.0000, 3),
(4, 'Астраханская область', 1.0000, 4),
(5, 'Белгородская область', 1.0000, 5),
(6, 'Брянская область', 1.0000, 6),
(7, 'Владимирская область', 1.0000, 7),
(8, 'Волгоградская область', 1.0000, 8),
(9, 'Вологодская область', 1.0000, 9),
(10, 'Воронежская область', 1.0000, 10),
(11, 'Еврейская автономная область', 0.9000, 11),
(12, 'Забайкальский край', 0.9000, 12),
(13, 'Ивановская область', 1.0000, 13),
(14, 'Иркутская область', 1.0000, 14),
(15, 'Кабардино-Балкарская Республика', 0.9000, 15),
(16, 'Калининградская область', 1.1000, 16),
(17, 'Калужская область', 1.0000, 17),
(18, 'Камчатский край', 1.0000, 18),
(19, 'Карачаево-Черкесская Республика', 0.9000, 19),
(20, 'Кемеровская область — Кузбасс', 0.9000, 20),
(21, 'Кировская область', 0.9000, 21),
(22, 'Костромская область', 1.0000, 22),
(23, 'Краснодарский край', 1.3000, 23),
(24, 'Красноярский край', 1.0000, 24),
(25, 'Курганская область', 0.9000, 25),
(26, 'Курская область', 1.0000, 26),
(27, 'Ленинградская область', 1.2000, 27),
(28, 'Липецкая область', 1.0000, 28),
(29, 'Магаданская область', 1.0000, 29),
(30, 'Москва', 1.5000, 30),
(31, 'Московская область', 1.4000, 31),
(32, 'Мурманская область', 1.0000, 32),
(33, 'Ненецкий автономный округ', 1.0000, 33),
(34, 'Нижегородская область', 1.1000, 34),
(35, 'Новгородская область', 1.0000, 35),
(36, 'Новосибирская область', 1.0000, 36),
(37, 'Омская область', 1.0000, 37),
(38, 'Оренбургская область', 1.0000, 38),
(39, 'Орловская область', 1.0000, 39),
(40, 'Пензенская область', 1.0000, 40),
(41, 'Пермский край', 1.0000, 41),
(42, 'Приморский край', 1.1000, 42),
(43, 'Псковская область', 1.0000, 43),
(44, 'Республика Адыгея', 1.0000, 44),
(45, 'Республика Алтай', 0.9000, 45),
(46, 'Республика Башкортостан', 1.1000, 46),
(47, 'Республика Бурятия', 0.9000, 47),
(48, 'Республика Дагестан', 0.9000, 48),
(49, 'Республика Ингушетия', 0.9000, 49),
(50, 'Республика Калмыкия', 0.9000, 50),
(51, 'Республика Карелия', 1.0000, 51),
(52, 'Республика Коми', 1.0000, 52),
(53, 'Республика Крым', 1.2000, 53),
(54, 'Республика Марий Эл', 0.9000, 54),
(55, 'Республика Мордовия', 0.9000, 55),
(56, 'Республика Саха (Якутия)', 1.0000, 56),
(57, 'Республика Северная Осетия — Алания', 0.9000, 57),
(58, 'Республика Татарстан', 1.1000, 58),
(59, 'Республика Тыва', 0.9000, 59),
(60, 'Республика Хакасия', 0.9000, 60),
(61, 'Ростовская область', 1.1000, 61),
(62, 'Рязанская область', 1.0000, 62),
(63, 'Самарская область', 1.1000, 63),
(64, 'Санкт-Петербург', 1.3000, 64),
(65, 'Саратовская область', 1.0000, 65),
(66, 'Сахалинская область', 1.0000, 66),
(67, 'Свердловская область', 1.1000, 67),
(68, 'Севастополь', 1.2000, 68),
(69, 'Смоленская область', 1.0000, 69),
(70, 'Ставропольский край', 1.0000, 70),
(71, 'Тамбовская область', 1.0000, 71),
(72, 'Тверская область', 1.0000, 72),
(73, 'Томская область', 1.0000, 73),
(74, 'Тульская область', 1.0000, 74),
(75, 'Тюменская область', 1.0000, 75),
(76, 'Удмуртская Республика', 1.0000, 76),
(77, 'Ульяновская область', 1.0000, 77),
(78, 'Хабаровский край', 1.1000, 78),
(79, 'Ханты-Мансийский автономный округ — Югра', 1.1000, 79),
(80, 'Челябинская область', 1.1000, 80),
(81, 'Чеченская Республика', 0.9000, 81),
(82, 'Чувашская Республика', 0.9000, 82),
(83, 'Чукотский автономный округ', 1.0000, 83),
(84, 'Ямало-Ненецкий автономный округ', 1.1000, 84),
(85, 'Ярославская область', 1.0000, 85);

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
  `modifier_type` enum('per_m2_add','percent_add','percent_discount','flat_add','multiplier','base_area','base_type_factor','base_floors') COLLATE utf8mb4_unicode_ci NOT NULL,
  `modifier_value` decimal(12,4) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `is_custom` tinyint(1) NOT NULL DEFAULT '0',
  `owner_user_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `sheets`
--

INSERT INTO `sheets` (`id`, `tag_id`, `name`, `description`, `image_path`, `modifier_type`, `modifier_value`, `sort_order`, `is_custom`, `owner_user_id`, `project_id`) VALUES
(1, 1, 'Квартира', 'Городская квартира в многоквартирном доме.', NULL, 'base_type_factor', 0.9000, 1, 0, NULL, NULL),
(2, 1, 'Частный дом', 'Отдельно стоящий дом с участком.', NULL, 'base_type_factor', 1.0000, 2, 0, NULL, NULL),
(3, 1, 'Таунхаус', 'Блокированный дом на несколько секций.', NULL, 'base_type_factor', 1.1000, 3, 0, NULL, NULL),
(4, 1, 'Вилла', 'Премиальная загородная резиденция.', NULL, 'base_type_factor', 1.5000, 4, 0, NULL, NULL),
(5, 1, 'Пентхаус', 'Верхний этаж с панорамным остеклением.', NULL, 'base_type_factor', 1.8000, 5, 0, NULL, NULL),
(6, 2, '50 м²', 'Студия или компактная квартира.', NULL, 'base_area', 50.0000, 1, 0, NULL, NULL),
(7, 2, '80 м²', 'Двухкомнатная квартира или дом.', NULL, 'base_area', 80.0000, 2, 0, NULL, NULL),
(8, 2, '100 м²', 'Просторная квартира или коттедж.', NULL, 'base_area', 100.0000, 3, 0, NULL, NULL),
(9, 2, '150 м²', 'Семейный дом или большая квартира.', NULL, 'base_area', 150.0000, 4, 0, NULL, NULL),
(10, 2, '200 м²', 'Загородный дом для семьи.', NULL, 'base_area', 200.0000, 5, 0, NULL, NULL),
(11, 2, '300 м²', 'Резиденция или крупный коттедж.', NULL, 'base_area', 300.0000, 6, 0, NULL, NULL),
(12, 2, '500 м²', 'Большая резиденция / вилла.', NULL, 'base_area', 500.0000, 7, 0, NULL, NULL),
(13, 3, '1 этаж', 'Одноэтажный дом.', NULL, 'base_floors', 1.0000, 1, 0, NULL, NULL),
(14, 3, '2 этажа', 'Двухэтажный дом.', NULL, 'base_floors', 2.0000, 2, 0, NULL, NULL),
(15, 3, '3 этажа', 'Трёхэтажный дом.', NULL, 'base_floors', 3.0000, 3, 0, NULL, NULL),
(16, 3, '4 этажа', 'Четырёхэтажный дом.', NULL, 'base_floors', 4.0000, 4, 0, NULL, NULL),
(17, 3, '5+ этажей', 'Пять и более этажей.', NULL, 'base_floors', 5.0000, 5, 0, NULL, NULL),
(18, 4, 'Лофт', 'Индустриальный стиль: кирпич, металл, открытые коммуникации.', NULL, 'percent_add', 8.0000, 1, 0, NULL, NULL),
(19, 4, 'Минимализм', 'Чистые линии, минимум деталей, максимум воздуха.', NULL, 'percent_discount', 5.0000, 2, 0, NULL, NULL),
(20, 4, 'Скандинавский', 'Светлые тона, дерево, натуральные материалы.', NULL, 'percent_add', 6.0000, 3, 0, NULL, NULL),
(21, 4, 'Барнхаус', 'Современный взгляд на классический амбар.', NULL, 'percent_add', 7.0000, 4, 0, NULL, NULL),
(22, 4, 'Классический', 'Классические пропорции, натуральные материалы, декор.', NULL, 'percent_add', 15.0000, 5, 0, NULL, NULL),
(23, 4, 'Хай-тек', 'Стекло, металл, технологии, прямые линии.', NULL, 'percent_add', 10.0000, 6, 0, NULL, NULL),
(24, 4, 'Шале', 'Альпийский стиль: дерево, камень, камин.', NULL, 'percent_add', 12.0000, 7, 0, NULL, NULL),
(25, 4, 'Фахверк', 'Немецкий фахверк, деревянный каркас, ручная работа.', NULL, 'percent_add', 18.0000, 8, 0, NULL, NULL),
(26, 4, 'Современный', 'Сбалансированный современный стиль.', NULL, 'percent_add', 5.0000, 9, 0, NULL, NULL),
(27, 4, 'Европейский', 'Классика с современным прочтением.', NULL, 'percent_add', 12.0000, 10, 0, NULL, NULL),
(28, 5, 'Газобетон', 'Тёплый и недорогой стеновой материал.', NULL, 'per_m2_add', 0.0000, 1, 0, NULL, NULL),
(29, 5, 'Кирпич керамический', 'Классика, долговечность, теплоёмкость.', NULL, 'per_m2_add', 4500.0000, 2, 0, NULL, NULL),
(30, 5, 'Кирпич силикатный', 'Более бюджетный вариант кирпича.', NULL, 'per_m2_add', 2800.0000, 3, 0, NULL, NULL),
(31, 5, 'Дерево (каркас)', 'Быстрый монтаж, экологичность.', NULL, 'per_m2_add', 2200.0000, 4, 0, NULL, NULL),
(32, 5, 'Керамический блок', 'Тёплая керамика, высокая прочность.', NULL, 'per_m2_add', 5200.0000, 5, 0, NULL, NULL),
(33, 5, 'Монолит', 'Монолитный железобетон, для многоэтажек.', NULL, 'per_m2_add', 6800.0000, 6, 0, NULL, NULL),
(34, 5, 'Брус профилированный', 'Деревянный дом из профилированного бруса.', NULL, 'per_m2_add', 3200.0000, 7, 0, NULL, NULL),
(35, 6, 'Плоская', 'Современная плоская кровля с эксплуатируемой зоной.', NULL, 'flat_add', 350000.0000, 1, 0, NULL, NULL),
(36, 6, 'Двускатная', 'Классическая двускатная кровля.', NULL, 'flat_add', 250000.0000, 2, 0, NULL, NULL),
(37, 6, 'Четырёхскатная', 'Вальмовая кровля, сложнее в монтаже.', NULL, 'flat_add', 480000.0000, 3, 0, NULL, NULL),
(38, 6, 'Односкатная', 'Простая односкатная, часто в хай-теке.', NULL, 'flat_add', 180000.0000, 4, 0, NULL, NULL),
(39, 6, 'Мансардная', 'Двускатная с мансардным этажом.', NULL, 'flat_add', 420000.0000, 5, 0, NULL, NULL),
(40, 6, 'Купольная', 'Сложная купольная кровля.', NULL, 'flat_add', 950000.0000, 6, 0, NULL, NULL),
(41, 7, 'Ленточный', 'Классический ленточный фундамент.', NULL, 'flat_add', 650000.0000, 1, 0, NULL, NULL),
(42, 7, 'Свайно-ростверковый', 'Для слабых грунтов.', NULL, 'flat_add', 780000.0000, 2, 0, NULL, NULL),
(43, 7, 'Плитный', 'Монолитная плита, для тяжёлых домов.', NULL, 'flat_add', 950000.0000, 3, 0, NULL, NULL),
(44, 7, 'Свайный', 'Винтовые или ЖБ сваи.', NULL, 'flat_add', 420000.0000, 4, 0, NULL, NULL),
(45, 7, 'Утеплённая шведская плита (УШП)', 'Готовое основание с тёплым полом.', NULL, 'flat_add', 1250000.0000, 5, 0, NULL, NULL),
(46, 8, 'ПВХ стандарт', 'Белый профиль, двухкамерный стеклопакет.', NULL, 'flat_add', 280000.0000, 1, 0, NULL, NULL),
(47, 8, 'ПВХ премиум', 'Ламинация, энергосбережение, многокамерный.', NULL, 'flat_add', 480000.0000, 2, 0, NULL, NULL),
(48, 8, 'Алюминий', 'Алюминиевый профиль, для панорамного остекления.', NULL, 'flat_add', 750000.0000, 3, 0, NULL, NULL),
(49, 8, 'Дерево-алюминий', 'Комбинированные окна.', NULL, 'flat_add', 920000.0000, 4, 0, NULL, NULL),
(50, 8, 'Панорамное остекление', 'От пола до потолка.', NULL, 'flat_add', 1450000.0000, 5, 0, NULL, NULL),
(51, 9, 'Штукатурка', 'Декоративная штукатурка, окраска.', NULL, 'per_m2_add', 1800.0000, 1, 0, NULL, NULL),
(52, 9, 'Кирпич', 'Фасадный кирпич, ручная кладка.', NULL, 'per_m2_add', 4200.0000, 2, 0, NULL, NULL),
(53, 9, 'Дерево', 'Планкен, имитация бруса.', NULL, 'per_m2_add', 2800.0000, 3, 0, NULL, NULL),
(54, 9, 'Клинкерная плитка', 'Долговечная клинкерная плитка.', NULL, 'per_m2_add', 5200.0000, 4, 0, NULL, NULL),
(55, 9, 'Фиброцементные панели', 'Современные навесные панели.', NULL, 'per_m2_add', 3600.0000, 5, 0, NULL, NULL),
(56, 9, 'Стеклофибробетон', 'Архитектурный бетон, сложные формы.', NULL, 'per_m2_add', 6800.0000, 6, 0, NULL, NULL),
(57, 10, 'Отопление + водоснабжение', 'Базовый комплект инженерии.', NULL, 'flat_add', 850000.0000, 1, 0, NULL, NULL),
(58, 10, 'Вентиляция приточно-вытяжная', 'Качественный воздухообмен.', NULL, 'flat_add', 480000.0000, 2, 0, NULL, NULL),
(59, 10, 'Тёплые полы', 'По всей площади дома.', NULL, 'flat_add', 620000.0000, 3, 0, NULL, NULL),
(60, 10, 'Кондиционирование', 'Мультизональная система.', NULL, 'flat_add', 550000.0000, 4, 0, NULL, NULL),
(61, 10, 'Автономная канализация', 'Септик с фильтрацией.', NULL, 'flat_add', 320000.0000, 5, 0, NULL, NULL),
(62, 10, 'Солнечные панели', 'Автономное электроснабжение.', NULL, 'flat_add', 1800000.0000, 6, 0, NULL, NULL),
(63, 10, 'Генератор', 'Резервный источник питания.', NULL, 'flat_add', 280000.0000, 7, 0, NULL, NULL),
(64, 11, 'Кабинет', 'Отдельный кабинет с рабочей зоной.', NULL, 'flat_add', 320000.0000, 1, 0, NULL, NULL),
(65, 11, 'Гардеробная', 'Просторная гардеробная с системой хранения.', NULL, 'flat_add', 280000.0000, 2, 0, NULL, NULL),
(66, 11, 'Кладовая', 'Кладовая или постирочная.', NULL, 'flat_add', 150000.0000, 3, 0, NULL, NULL),
(67, 11, 'Сауна', 'Финская сауна с парилкой.', NULL, 'flat_add', 650000.0000, 4, 0, NULL, NULL),
(68, 11, 'Бассейн', 'Крытый бассейн 8×4 м с подогревом.', NULL, 'flat_add', 2400000.0000, 5, 0, NULL, NULL),
(69, 11, 'Терраса', 'Открытая терраса с настилом.', NULL, 'flat_add', 420000.0000, 6, 0, NULL, NULL),
(70, 11, 'Зимний сад', 'Отапливаемое помещение с растениями.', NULL, 'flat_add', 580000.0000, 7, 0, NULL, NULL),
(71, 11, 'Мастер-спальня', 'Большая спальня с гардеробной и с/у.', NULL, 'flat_add', 480000.0000, 8, 0, NULL, NULL),
(72, 11, 'Кинозал', 'Домашний кинотеатр.', NULL, 'flat_add', 750000.0000, 9, 0, NULL, NULL),
(73, 11, 'Винный погреб', 'Погреб для хранения вина.', NULL, 'flat_add', 520000.0000, 10, 0, NULL, NULL),
(74, 12, 'Умное освещение', 'Управление светом со смартфона.', NULL, 'flat_add', 380000.0000, 1, 0, NULL, NULL),
(75, 12, 'Умный климат', 'Автоматическое управление климатом.', NULL, 'flat_add', 420000.0000, 2, 0, NULL, NULL),
(76, 12, 'Умная безопасность', 'Датчики, камеры, сигнализация.', NULL, 'flat_add', 340000.0000, 3, 0, NULL, NULL),
(77, 12, 'Мультирум', 'Аудио по всему дому.', NULL, 'flat_add', 280000.0000, 4, 0, NULL, NULL),
(78, 12, 'Полная автоматизация', 'Комплексная система KNX или аналог.', NULL, 'flat_add', 1250000.0000, 5, 0, NULL, NULL),
(79, 13, 'Благоустройство участка', 'Газон, дорожки, малые формы.', NULL, 'flat_add', 480000.0000, 1, 0, NULL, NULL),
(80, 13, 'Автополив', 'Автоматическая система полива.', NULL, 'flat_add', 180000.0000, 2, 0, NULL, NULL),
(81, 13, 'Освещение участка', 'Ландшафтная подсветка.', NULL, 'flat_add', 220000.0000, 3, 0, NULL, NULL),
(82, 13, 'Патио / зона барбекю', 'Летняя кухня на участке.', NULL, 'flat_add', 360000.0000, 4, 0, NULL, NULL),
(83, 13, 'Пруд / фонтан', 'Декоративный водоём.', NULL, 'flat_add', 650000.0000, 5, 0, NULL, NULL),
(84, 14, 'Базовая отделка', 'Штукатурка, стяжка, без чистовой.', NULL, 'per_m2_add', 6000.0000, 1, 0, NULL, NULL),
(85, 14, 'Стандарт', 'Чистовая отделка среднего уровня.', NULL, 'per_m2_add', 12000.0000, 2, 0, NULL, NULL),
(86, 14, 'Премиум', 'Качественные материалы, дизайн.', NULL, 'per_m2_add', 22000.0000, 3, 0, NULL, NULL),
(87, 14, 'Элитная отделка', 'Натуральный камень, шпон, авторский декор.', NULL, 'per_m2_add', 38000.0000, 4, 0, NULL, NULL),
(88, 14, 'Дизайнерская мебель', 'Мебель под заказ у дизайнера.', NULL, 'per_m2_add', 15000.0000, 5, 0, NULL, NULL),
(89, 14, 'Авторский текстиль', 'Шторы, обивка, ковры.', NULL, 'per_m2_add', 4200.0000, 6, 0, NULL, NULL),
(90, 14, 'Декоративное освещение', 'Люстры, бра, скрытая подсветка.', NULL, 'per_m2_add', 3600.0000, 7, 0, NULL, NULL);

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
-- Индексы таблицы `project_params`
--
ALTER TABLE `project_params`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `fk_pp_region` (`region_id`);

--
-- Индексы таблицы `project_sheet_overrides`
--
ALTER TABLE `project_sheet_overrides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_proj_sheet` (`project_id`,`sheet_id`),
  ADD KEY `fk_pso_sheet` (`sheet_id`),
  ADD KEY `fk_pso_curator` (`curator_id`);

--
-- Индексы таблицы `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `sheets`
--
ALTER TABLE `sheets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sheet_tag` (`tag_id`),
  ADD KEY `fk_sheet_owner` (`owner_user_id`),
  ADD KEY `fk_sheet_project` (`project_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `portfolio_items`
--
ALTER TABLE `portfolio_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT для таблицы `portfolio_projects`
--
ALTER TABLE `portfolio_projects`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `project_sheet_overrides`
--
ALTER TABLE `project_sheet_overrides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT для таблицы `sheets`
--
ALTER TABLE `sheets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

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
-- Ограничения внешнего ключа таблицы `project_params`
--
ALTER TABLE `project_params`
  ADD CONSTRAINT `fk_pp_project` FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pp_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `project_sheet_overrides`
--
ALTER TABLE `project_sheet_overrides`
  ADD CONSTRAINT `fk_pso_curator` FOREIGN KEY (`curator_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pso_project` FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pso_sheet` FOREIGN KEY (`sheet_id`) REFERENCES `sheets` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `sheets`
--
ALTER TABLE `sheets`
  ADD CONSTRAINT `fk_sheet_owner` FOREIGN KEY (`owner_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sheet_project` FOREIGN KEY (`project_id`) REFERENCES `portfolio_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sheet_tag` FOREIGN KEY (`tag_id`) REFERENCES `catalog_tags` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
