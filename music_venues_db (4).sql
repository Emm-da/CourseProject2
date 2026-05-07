-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 07 2026 г., 09:47
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
-- База данных: `music_venues_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `attendees_count` int(11) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `venue_id`, `booking_date`, `start_time`, `end_time`, `purpose`, `attendees_count`, `contact_phone`, `contact_email`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 60, '2026-01-11', '18:00:00', '22:00:00', 'other', 50, '+7 (966) 342-84-82', 'ann4zhgutova@yandex.ru', 'cancelled', '', '2026-01-11 17:14:52', '2026-01-11 17:38:12'),
(2, 3, 60, '2026-01-11', '18:00:00', '22:00:00', 'rehearsal', 50, '+7 (966) 342-84-82', 'ann4zhgutova@yandex.ru', 'pending', '', '2026-01-11 18:01:36', '2026-01-11 18:01:36'),
(3, 4, 60, '2026-01-24', '18:00:00', '22:00:00', 'concert', 50, '+7 (966) 342-84-82', '123@mail.com', 'pending', '', '2026-01-24 16:51:58', '2026-01-24 16:51:58');

-- --------------------------------------------------------

--
-- Структура таблицы `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `venue_id`, `created_at`) VALUES
(6, 4, 60, '2026-01-24 16:47:46');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `username`, `created_at`) VALUES
(1, 'test@example.com', 'password123', 'Тестовый пользователь', '2026-01-07 07:10:19'),
(2, 'admin@music-venues.ru', 'admin123', 'Администратор', '2026-01-07 07:10:19'),
(3, 'ann4zhgutova@yandex.ru', '$2y$10$YZFU3D4flqzzyA2V55VtcuEi2hAJ99UH7jEA6NQF0kllNAkHrDRdG', 'ann4zhgutova@yandex.ru', '2026-01-11 16:51:07'),
(4, '123@mail.com', '$2y$10$0gtISeyJ0LjdTg3/TmQKNOPwDA.vczZYUNjq5FNjbrgX06oInxi5.', '123', '2026-01-24 16:25:42');

-- --------------------------------------------------------

--
-- Структура таблицы `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `global_id` bigint(20) DEFAULT NULL,
  `adm_area` varchar(100) DEFAULT NULL,
  `district` varchar(100) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `location_clarification` varchar(500) DEFAULT NULL,
  `working_hours` varchar(100) DEFAULT NULL,
  `working_hours_clarification` varchar(500) DEFAULT NULL,
  `balance_holder` varchar(255) DEFAULT NULL,
  `balance_holder_phone` varchar(50) DEFAULT NULL,
  `balance_holder_fax` varchar(50) DEFAULT NULL,
  `balance_holder_email` varchar(100) DEFAULT NULL,
  `balance_holder_website` varchar(255) DEFAULT NULL,
  `geo_data` text DEFAULT NULL,
  `geodata_center` text DEFAULT NULL,
  `latitude` decimal(11,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `venues`
--

INSERT INTO `venues` (`id`, `name`, `description`, `global_id`, `adm_area`, `district`, `lat`, `lng`, `address`, `location_clarification`, `working_hours`, `working_hours_clarification`, `balance_holder`, `balance_holder_phone`, `balance_holder_fax`, `balance_holder_email`, `balance_holder_website`, `geo_data`, `geodata_center`, `latitude`, `longitude`, `created_at`) VALUES
(2, 'Эстрада «Центральная»', NULL, 61274174, 'Восточный административный округ', 'район Сокольники', NULL, NULL, 'город Москва, 1-й Лучевой просек, дом 1, строение 2А', '1-й Лучевой просек', '', 'май-октябрь', 'ГАУК г. Москвы «ПКиО «Сокольники»', '(499) 268-54-30', '', 'parksokolniki@culture.mos.ru', 'parksokolniki.mos.ru', '{coordinates=[37.673708995, 55.795444616], type=Point}', '', 0.00000000, 37.67370900, '2026-01-07 07:10:18'),
(3, 'Симфоническая эстрада', NULL, 61274175, 'Восточный административный округ', 'район Сокольники', NULL, NULL, 'город Москва, Майский просек, дом 1Б', 'Майский просек,1Б', '', 'площадка закрыта на ремонт', 'ГАУК г. Москвы «ПКиО «Сокольники»', '(499) 268-54-30', '', 'parksokolniki@culture.mos.ru', 'parksokolniki.mos.ru', '{coordinates=[37.679258591, 55.79835022], type=Point}', '', 0.00000000, 37.67925859, '2026-01-07 07:10:18'),
(4, 'Сцена Таганского парка', NULL, 61274176, 'Центральный административный округ', 'Таганский район', NULL, NULL, 'город Москва, Таганская улица, дом 40-42', 'Сцена на главной площади', '07:00-23:00', 'Расписание мероприятий уточняется в летний период', 'ГАУК г. Москвы ПКиО «Таганский»', '(495) 912-27-17', '(495) 912-27-17', 'parktaganskiy@culture.mos.ru', 'parktaganskiy.ru', '{coordinates=[37.667530301, 55.738455805], type=Point}', '', 0.00000000, 37.66753030, '2026-01-07 07:10:18'),
(5, 'Главная сцена парка Фили', NULL, 61274177, 'Западный административный округ', 'район Филёвский Парк', NULL, NULL, 'город Москва, улица Барклая, дом 22', 'От центрального входа вверх по Главной аллее', 'Согласно таймингу проводимых мероприятий', 'Круглогодично', 'ГАУК г. Москвы ПКиО «Фили»', '(499) 145-45-05', '(499) 145-45-05', 'parkfili@culture.mos.ru', 'parkfili.ru', '{coordinates=[37.498831777, 55.667519487], type=Point}', '', 0.00000000, 37.49883178, '2026-01-07 07:10:18'),
(6, 'Главная сцена парка 50-летия Октября', NULL, 61274178, 'Западный административный округ', 'район Проспект Вернадского', NULL, NULL, 'город Москва, улица Удальцова, дом 22', 'Площадь Семьи в центральной части парка', 'Круглосуточно', 'Круглогодично', 'ГАУК г. Москвы ПКиО «Фили»', '(499) 145-45-05', '(499) 145-45-05', 'parkfili@culture.mos.ru', 'parkfili.ru', '{coordinates=[37.498832067, 55.667518884], type=Point}', '', 0.00000000, 37.49883207, '2026-01-07 07:10:18'),
(7, 'Деревянная терраса', NULL, 61274251, 'Центральный административный округ', 'район Якиманка', NULL, NULL, 'город Москва, улица Крымский Вал, дом 9', 'Крымская набережная', '07:00-23:00', 'Круглогодично', 'ЦПКиО имени М. Горького', '(495) 995-00-20', '(495) 237-35-24', 'official@park-gorkogo.com', 'park-gorkogo.com', '{coordinates=[37.604837578, 55.735450353], type=Point}', '', 0.00000000, 37.60483758, '2026-01-07 07:10:18'),
(8, 'Главная сцена парка в Кузьминках', NULL, 61274252, 'Юго-Восточный административный округ', 'район Кузьминки', NULL, NULL, 'город Москва, Кузьминский парк, дом 1, строение 2', 'Около детской площадки, в центральной части парка', 'Круглосуточно', '', 'ГАУК г. Москвы «ГМЗ «Кузьминки-Люблино»', '(499) 175-33-69', '(499) 175-33-69', 'info@park-kuzminki.ru', 'park-kuzminki.ru', '{coordinates=[37.770013803, 55.69490623], type=Point}', '', 0.00000000, 37.77001380, '2026-01-07 07:10:18'),
(9, 'Лианозовский парк', NULL, 61274181, 'Северо-Восточный административный округ', 'район Лианозово', NULL, NULL, 'город Москва, Угличская улица, дом 13', '', '07:00-23:00', '', 'ГАУК г. Москвы ПКиО «Лианозовский»', '(499) 908-35-00', '(499) 908-35-00', 'liapark@ya.ru', 'liapark.ru', '{coordinates=[37.570668267, 55.900488124], type=Point}', '', 0.00000000, 37.57066827, '2026-01-07 07:10:18'),
(13, 'Парк Красная Пресня (основная территория)', NULL, 61274223, 'Центральный административный округ', 'Пресненский район', NULL, NULL, 'город Москва, Мантулинская улица, дом 5, строение 2', 'Главная сцена в парке', '09:00-23:00', 'Работа площадки по графику проводимых мероприятий. В сотальное время к свободному посещению доступна прилегающая площадка с деревянным настилом.', 'ГАУК г. Москвы ПКиО «Красная Пресня»', '(499) 256-13-02', '(499) 256-13-02', 'info@p-kp.ru', 'p-kp.ru', '{coordinates=[37.551529605, 55.75454526], type=Point}', '', 0.00000000, 37.55152961, '2026-01-07 07:10:18'),
(14, 'Эстрада «Солнечная» Измайловского парка', NULL, 61274224, 'Восточный административный округ', 'район Измайлово', NULL, NULL, 'город Москва, аллея Большого Круга, дом 7', 'Майская аллея', '', 'В рамках проведения мероприятий', 'ГАУК г. Москвы «Измайловский ПКиО»', '(499) 530-16-67', '', 'parkizmailovskiy@culture.mos.ru', 'izmailovsky-park.ru', '{coordinates=[37.498839695, 55.667510696], type=Point}', '', 0.00000000, 37.49883970, '2026-01-07 07:10:18'),
(15, 'Центральная площадь Измайловского парка', NULL, 61274225, 'Восточный административный округ', 'район Измайлово', NULL, NULL, 'город Москва, аллея Большого Круга, дом 7', 'Народный проспект', '', 'В рамках проведения мероприятий', 'ГАУК г. Москвы «Измайловский ПКиО»', '(499) 530-16-67', '', 'parkizmailovskiy@culture.mos.ru', 'izmailovsky-park.ru', '{coordinates=[37.498839712, 55.667510679], type=Point}', '', 0.00000000, 37.49883971, '2026-01-07 07:10:18'),
(16, 'Музыкальная площадка у Дирекции Измайловского парка', NULL, 61274226, 'Восточный административный округ', 'район Измайлово', NULL, NULL, 'город Москва, аллея Большого Круга, дом 7', '', '17:00-18:00', 'Каждое воскресенье, июнь - август', 'ГАУК г. Москвы «Измайловский ПКиО»', '(499) 530-16-67', '', 'parkizmailovskiy@culture.mos.ru', 'izmailovsky-park.ru', '{coordinates=[37.498839665, 55.667510645], type=Point}', '', 0.00000000, 37.49883967, '2026-01-07 07:10:18'),
(17, 'Центральная площадь музейно-паркового комплекса «Северное Тушино»', NULL, 61274189, 'Северо-Западный административный округ', 'район Северное Тушино', NULL, NULL, 'город Москва, улица Свободы, дом 52', 'Центральная площадь.', 'В соответствии с графиком работы парка', 'В соответствии с графиком работы парка', 'ГАУК г. Москвы «МПК «Северное Тушино»', '(495) 640-73-55', '(495) 640-73-54', 'info@mosparks.ru', 'mosparks.ru', '{coordinates=[37.451335492, 55.861081295], type=Point}', '', 0.00000000, 37.45133549, '2026-01-07 07:10:18'),
(19, 'Центральная сцена', NULL, 61274191, 'Северо-Западный административный округ', 'район Северное Тушино', NULL, NULL, 'город Москва, улица Свободы, дом 52', 'Сцена на центральной площади', 'В соответствии с графиком работы парка', 'В соответствии с графиком работы парка', 'ГАУК г. Москвы «МПК «Северное Тушино»', '(495) 640-73-55', '(495) 640-73-54', 'info@mosparks.ru', 'mosparks.ru', '{coordinates=[37.451477565, 55.861127722], type=Point}', '', 0.00000000, 37.45147757, '2026-01-07 07:10:18'),
(21, 'Открытая сцена усадьбы Воронцово', NULL, 61274227, 'Юго-Западный административный округ', 'Обручевский район', NULL, NULL, 'город Москва, улица Воронцовские Пруды, дом 3', 'В центре парка', 'Ежедневно', 'Круглый год', 'ГАУК г. Москвы «Усадьба Воронцово»', '(495) 580-26-78', '(495) 580-26-78', 'info@usadba-vorontsovo.ru', 'usadba-vorontsovo.ru', '{coordinates=[37.533064807, 55.667139578], type=Point}', '', 0.00000000, 37.53306481, '2026-01-07 07:10:18'),
(22, 'Эстрада в Саду им. Н.Э. Баумана', NULL, 61274194, 'Центральный административный округ', 'Басманный район', NULL, NULL, 'город Москва, Старая Басманная улица, дом 15', 'Сцена-ракушка у входа со стороны Новой Басманной улицы', '06:00-24:00', 'Расписание мероприятий уточняется в летний период', 'ГАУК г. Москвы «Сад КиО им. Н. Э. Баумана»', '(499) 261-58-83', '(499) 261-58-83', 'sadbaumana@culture.mos.ru', 'sadbaumana.ru', '{coordinates=[37.659379386, 55.768794543], type=Point}', '', 0.00000000, 37.65937939, '2026-01-07 07:10:18'),
(23, 'Открытая сцена в Саду им. Н.Э. Баумана', NULL, 61274195, 'Центральный административный округ', 'Басманный район', NULL, NULL, 'город Москва, Старая Басманная улица, дом 15', 'Напротив входа со стороны Старой Басманной улицы', '06:00-24:00', 'Расписание мероприятий уточняется в летний период', 'ГАУК г. Москвы «Сад КиО им. Н. Э. Баумана»', '(499) 261-58-83', '(499) 261-58-83', 'sadbaumana@culture.mos.ru', 'sadbaumana.ru', '{coordinates=[37.659727653, 55.766213729], type=Point}', '', 0.00000000, 37.65972765, '2026-01-07 07:10:18'),
(26, 'Сцена Летнего кинотеатра', NULL, 339887675, 'Центральный административный округ', 'район Якиманка', NULL, NULL, 'город Москва, улица Крымский Вал, дом 9', 'Музеон. Возле летнего кинотеатра', '07:00-23:00', 'Расписание мероприятий уточняется в летний период', 'ЦПКиО имени М. Горького', '(495) 995-00-20', '(495) 237-35-24', 'official@park-gorkogo.com', 'park-gorkogo.com', '{coordinates=[37.60981801, 55.73729146], type=Point}', '', 0.00000000, 37.60981801, '2026-01-07 07:10:18'),
(27, 'Деревянная сцена - Треугольник у Крымского моста', NULL, 339887676, 'Центральный административный округ', 'район Якиманка', NULL, NULL, 'город Москва, улица Крымский Вал, дом 9', 'Музеон. Возле Крымского моста', '07:00-23:00', 'Расписание мероприятий уточняется в летний период', 'ЦПКиО имени М. Горького', '(495) 995-00-20', '(495) 237-35-24', 'official@park-gorkogo.com', 'park-gorkogo.com', '{coordinates=[37.600876872, 55.73383466], type=Point}', '', 0.00000000, 37.60087687, '2026-01-07 07:10:18'),
(28, 'Купол ALPBAU', NULL, 339887677, 'Центральный административный округ', 'район Якиманка', NULL, NULL, 'город Москва, улица Крымский Вал, дом 9', 'Музеон. Недалеко от памятников Ф.Э. Дзержинскому и М. Горькому', '07:00-23:00', 'Расписание мероприятий уточняется в летний период', 'ЦПКиО имени М. Горького', '(495) 995-00-20', '(495) 237-35-24', 'official@park-gorkogo.com', 'park-gorkogo.com', '{coordinates=[37.609773197, 55.735270656], type=Point}', '', 0.00000000, 37.60977320, '2026-01-07 07:10:18'),
(29, 'Большое массовое поле', NULL, 339887678, 'Центральный административный округ', 'район Якиманка', NULL, NULL, 'город Москва, улица Крымский Вал, дом 9', 'Партер. Большое массовое поле (сцена устанавливается при необходимости)', '07:00-23:00', 'Расписание мероприятий уточняется в летний период', 'ЦПКиО имени М. Горького', '(495) 995-00-20', '(495) 237-35-24', 'official@park-gorkogo.com', 'park-gorkogo.com', '{coordinates=[37.597175344, 55.727715661], type=Point}', '', 0.00000000, 37.59717534, '2026-01-07 07:10:18'),
(30, 'Пушкинская набережная', NULL, 339887679, 'Центральный административный округ', 'район Якиманка', NULL, NULL, 'город Москва, улица Крымский Вал, дом 9', 'Партер. Пушкинская набережная (сцена устанавливается при необходимости)', '07:00-23:00', 'Расписание мероприятий уточняется в летний период', 'ЦПКиО имени М. Горького', '(495) 995-00-20', '(495) 237-35-24', 'official@park-gorkogo.com', 'park-gorkogo.com', '{coordinates=[37.598546231, 55.731525162], type=Point}', '', 0.00000000, 37.59854623, '2026-01-07 07:10:18'),
(31, 'Главный вход', NULL, 339887680, 'Центральный административный округ', 'район Якиманка', NULL, NULL, 'город Москва, улица Крымский Вал, дом 9', 'Главный вход (сцена устанавливается при необходимости)', '07:00-23:00', 'Расписание мероприятий уточняется в летний период', 'ЦПКиО имени М. Горького', '(495) 995-00-20', '(495) 237-35-24', 'official@park-gorkogo.com', 'park-gorkogo.com', '{coordinates=[37.603716902, 55.731733004], type=Point}', '', 0.00000000, 37.60371690, '2026-01-07 07:10:18'),
(32, 'Фонтанная площадь', NULL, 339887681, 'Центральный административный округ', 'район Якиманка', NULL, NULL, 'город Москва, улица Крымский Вал, дом 9', 'Партер. Фонтанная площадь (сцена устанавливается при необходимости)', '07:00-23:00', 'Расписание мероприятий уточняется в летний период', 'ЦПКиО имени М. Горького', '(495) 995-00-20', '(495) 237-35-24', 'official@park-gorkogo.com', 'park-gorkogo.com', '{coordinates=[37.601816189, 55.729994185], type=Point}', '', 0.00000000, 37.60181619, '2026-01-07 07:10:18'),
(33, 'Музыкальная площадка (сцена) в парке «Олимпийская деревня»', NULL, 639712067, 'Западный административный округ', 'район Тропарёво-Никулино', NULL, NULL, 'город Москва, улица Лобачевского, дом 27', 'Сцена на воде', 'Во время проведения мероприятий', 'В летний период', 'ГАУК г. Москвы ПКиО «Фили»', '(499) 145-45-05', '(499) 145-45-05', 'parkfili@culture.mos.ru', 'parkfili.ru', '{coordinates=[37.498831589, 55.667518857], type=Point}', '', 0.00000000, 37.49883159, '2026-01-07 07:10:18'),
(45, 'Сцена Большого амфитеатра', NULL, 840225919, 'Центральный административный округ', 'Тверской район', NULL, NULL, 'город Москва, улица Варварка, владение 6/5', 'метро Китай-город, Площадь революции, Охотный ряд, Театральная', 'Круглосуточно', '', 'ГАУК г.Москвы «Парк «Зарядье»', '(495) 531-05-00', '', 'parkzaryadye@culture.mos.ru', 'zaryadyepark.ru', '{coordinates=[37.630130705, 55.751292246], type=Point}', '', 0.00000000, 37.63013071, '2026-01-07 07:10:18'),
(47, 'Северная площадь Измайловского парка', NULL, 909038947, 'Восточный административный округ', 'район Измайлово', NULL, NULL, 'город Москва, аллея Большого Круга, дом 7', 'Майская аллея', '', 'В период проведения мероприятий', 'ГАУК г. Москвы «Измайловский ПКиО»', '(499) 530-16-67', '', 'parkizmailovskiy@culture.mos.ru', 'izmailovsky-park.ru', '{coordinates=[37.498839682, 55.667510786], type=Point}', '', 0.00000000, 37.49883968, '2026-01-07 07:10:18'),
(48, 'Плавучая сцена', NULL, 918678779, 'Центральный административный округ', 'Пресненский район', NULL, NULL, 'город Москва, Мантулинская улица, дом 24', '70 метров от здания, акватория Красногвардейских прудов', 'Ежедневно с 10:00-22:00', 'Работа площадки по графику проводимых мероприятий. В остальное время свободное использование и посещение.', 'ГАУК г. Москвы ПКиО «Красная Пресня»', '(499) 256-13-02', '(499) 256-13-02', 'info@p-kp.ru', 'p-kp.ru', '{coordinates=[37.547049165, 55.756463075], type=Point}', '', 0.00000000, 37.54704917, '2026-01-07 07:10:18'),
(52, 'Холл павильона «Медиацентр»', NULL, 918678781, 'Центральный административный округ', 'Тверской район', NULL, NULL, 'город Москва, улица Варварка, домовладение 6, строение 1', 'метро Китай-город, Площадь революции, Охотный ряд, Театральная', 'По графику работы павильона', 'В летний период: пн. - 14.00 до 21.00, вт.-вс. - 10.00 до 21.00, в зимний период: пн. - 14.00 до 20.00, вт.-вс. - 10.00 до 20.00', 'ГАУК г.Москвы «Парк «Зарядье»', '(495) 531-05-00', '', 'parkzaryadye@culture.mos.ru', 'zaryadyepark.ru', '{coordinates=[37.627073202, 55.751475444], type=Point}', '', 0.00000000, 37.62707320, '2026-01-07 07:10:18'),
(53, 'Малый амфитеатр', NULL, 918678782, 'Центральный административный округ', 'Тверской район', NULL, NULL, 'город Москва, улица Варварка, владение 6/5', 'метро Китай-город, Площадь революции, Охотный ряд, Театральная', 'Круглосуточно', '', 'ГАУК г.Москвы «Парк «Зарядье»', '(495) 531-05-00', '', 'parkzaryadye@culture.mos.ru', 'zaryadyepark.ru', '{coordinates=[37.630941994, 55.750662995], type=Point}', '', 0.00000000, 37.63094199, '2026-01-07 07:10:18'),
(55, 'Круглая сцена в Ландшафтном парке «Митино»', NULL, 1028733357, 'Северо-Западный административный округ', 'район Митино', NULL, NULL, 'город Москва, Новотушинский проезд, дом 5, строение 3Б/Н', 'Недалеко от станции метро Волоколамская', '', 'Круглогодично', 'ГАУК г. Москвы «ПКиО «Бабушкинский»', '(499) 184-34-22', '', 'babkapark@mail.ru', 'bapark.ru', '{coordinates=[37.378504911, 55.835821913], type=Point}', '', 0.00000000, 37.37850491, '2026-01-07 07:10:18'),
(56, 'Эстрада «Ротонда»', NULL, 1029327169, 'Восточный административный округ', 'район Сокольники', NULL, NULL, 'город Москва, улица Сокольнический Вал, дом 1А, строение 5', 'Главная аллея', 'Ср, Чт, Пт, Сб, Вс 13:00 - 19:00', 'Круглогодично', 'ГАУК г. Москвы «ПКиО «Сокольники»', '(499) 268-54-30', '', 'parksokolniki@culture.mos.ru', 'parksokolniki.mos.ru', '{coordinates=[37.676745194, 55.793514576], type=Point}', '', 0.00000000, 37.67674519, '2026-01-07 07:10:18'),
(57, 'Эстрада «Веранда танцев»', NULL, 1029327171, 'Восточный административный округ', 'район Сокольники', NULL, NULL, 'город Москва, Майский просек, владение 4, сооружение 1', 'Майский просек', 'с 10:00 до 20:00', 'круглогодично', 'ГАУК г. Москвы «ПКиО «Сокольники»', '(499) 268-54-30', '', 'parksokolniki@culture.mos.ru', 'parksokolniki.mos.ru', '{coordinates=[37.679405977, 55.795915006], type=Point}', '', 0.00000000, 37.67940598, '2026-01-07 07:10:18'),
(59, 'Холл Научно-познавательного центра «Заповедное посольство»', NULL, 1129448295, 'Центральный административный округ', 'Тверской район', NULL, NULL, 'город Москва, улица Варварка, домовладение 6, строение 1', 'метро Китай-город, Площадь революции, Охотный ряд, Театральная', 'По графику работы павильона', 'В летний период: пн. - 14.00 до 21.00, вт.-вс. - 10.00 до 21.00, в зимний период: пн. - 14.00 до 20.00, вт.-вс. - 10.00 до 20.00', 'ГАУК г.Москвы «Парк «Зарядье»', '(495) 531-05-00', '', 'parkzaryadye@culture.mos.ru', 'www.zaryadyepark.ru', '{coordinates=[37.629069258, 55.751714066], type=Point}', '', 0.00000000, 37.62906926, '2026-01-07 07:10:18'),
(60, '«Северный тоннель»', NULL, 1129448296, 'Центральный административный округ', 'Тверской район', NULL, NULL, 'город Москва, улица Варварка, домовладение 6, строение 1', 'метро Китай-город, Площадь революции, Охотный ряд, Театральная', 'По графику работы павильона', 'В летний период: пн. - 14.00 до 21.00, вт.-вс. - 10.00 до 21.00, в зимний период: пн. - 14.00 до 20.00, вт.-вс. - 10.00 до 20.00', 'ГАУК г.Москвы «Парк «Зарядье»', '(495) 531-05-00', '', 'parkzaryadye@culture.mos.ru', 'www.zaryadyepark.ru', '{coordinates=[37.627821418, 55.752031006], type=Point}', '', 0.00000000, 37.62782142, '2026-01-07 07:10:18'),
(61, 'Сцена с помещением для артистов', NULL, 2755790775, 'Юго-Восточный административный округ', 'район Люблино', NULL, NULL, 'недалеко от здания, расположенного по адресу: Тихая улица, д.23, с8', '55.687185, 37.740404', '', '', 'ГАУК г. Москва, \"ГМЗ \"Кузьминки-Люблино\"', '(499) 175-33-69', '', 'parkkuzminki@culture.mos.ru', 'park-kuzminki.ru', '{coordinates=[37.499124707, 55.667348717], type=Point}', '', 0.00000000, 37.49912471, '2026-01-07 07:10:18'),
(62, 'Музыкальная беседка', NULL, 2755790774, 'Восточный административный округ', 'район Измайлово', NULL, NULL, 'аллея Большого Круга, вл. 4', 'Рядом с Площадью Мужества', '', 'В период проведения мероприятий', 'ГАУК г. Москвы \"Измайловский ПКиО\"', '(499) 530-16-67', '', 'parkizmailovskiy@culture.mos.ru', 'izmailovsky-park.ru', '{coordinates=[37.49912607, 55.667348836], type=Point}', '', 0.00000000, 37.49912607, '2026-01-07 07:10:18'),
(63, 'Ротонда (малая сцена)', NULL, 2755790772, 'Центральный административный округ', 'Таганский район', NULL, NULL, 'ул. Таганская, д. 40-42', 'Площадка перед фонтаном', '7:00-22:00', '', 'ГАУК г. Москвы \"ПКиО \"Таганский\"', '(495) 912-27-17', '(495) 912-27-17', 'parktaganskiy@culture.mos.ru', 'parktaganskiy.ru', '{coordinates=[37.499125512, 55.667348071], type=Point}', '', 0.00000000, 37.49912551, '2026-01-07 07:10:18'),
(64, 'Сцена Детского парка', NULL, 2755790771, 'Центральный административный округ', 'Таганский район', NULL, NULL, 'ул. Таганская, д. 15А, стр. 1', 'На центральной площади перед фонтаном', '7:00 - 22:00', '', 'ГАУК г. Москвы \"ПКиО \"Таганский\"', '(495) 912-27-17', '(495) 912-27-17', 'parktaganskiy@culture.mos.ru', 'parktaganskiy.ru', '{coordinates=[37.499125562, 55.667348015], type=Point}', '', 0.00000000, 37.49912556, '2026-01-07 07:10:18'),
(65, 'Малая сцена на площадке \"Лес чудес\"', NULL, 2755790770, 'Восточный административный округ', 'район Измайлово', NULL, NULL, 'Народный проспект, 17к1с10', 'Со стороны метро Партизанская', '', 'В период проведения мероприятий', 'ГАУК г. Москвы \"Измайловский ПКиО\"', '(499) 530-16-67', '', 'parkizmailovskiy@culture.mos.ru', 'izmailovsky-park.ru', '{coordinates=[37.499126279, 55.667348801], type=Point}', '', 0.00000000, 37.49912628, '2026-01-07 07:10:18'),
(66, 'Летняя эстрада', NULL, 2755790769, 'Центральный административный округ', 'Тверской район', NULL, NULL, 'город Москва, улица Каретный Ряд, дом 3, строение 1', 'Теневая часть сада \"Эрмитаж\"', '', '', 'ГАУК г. Москвы \"МГС \"Эрмитаж\"', '(499) 699-04-32', '', 'sadermitazh@culture.mos.ru', 'mosgorsad.ru', '{coordinates=[37.498837388, 55.667510665], type=Point}', '', 0.00000000, 37.49883739, '2026-01-07 07:10:18'),
(67, 'Сцена', NULL, 2755790768, 'Центральный административный округ', 'Тверской район', NULL, NULL, 'город Москва, улица Каретный Ряд, дом 3, строение 1', 'Партерная часть сада \"Эрмитаж\"', '', '', 'ГАУК г. Москвы \"МГС \"Эрмитаж\"', '(499) 699-04-32', '', 'sadermitazh@culture.mos.ru', '', '{coordinates=[37.498837425, 55.667510657], type=Point}', '', 0.00000000, 37.49883743, '2026-01-07 07:10:18');

-- --------------------------------------------------------

--
-- Структура таблицы `venue_features`
--

CREATE TABLE `venue_features` (
  `id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `feature_name` varchar(100) NOT NULL,
  `feature_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `venue_photos`
--

CREATE TABLE `venue_photos` (
  `id` int(11) NOT NULL,
  `venue_id` int(11) NOT NULL,
  `photo_url` varchar(500) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `venue_photos`
--

INSERT INTO `venue_photos` (`id`, `venue_id`, `photo_url`, `description`, `is_main`, `uploaded_at`) VALUES
(4, 2, 'https://avatars.mds.yandex.net/get-afishanew/34116/ccd0e4d0ceafd4f777dc83c2d39e0cef/960x690_noncrop', 'Основная сцена в парке Сокольники', 1, '2026-01-09 08:24:47'),
(5, 3, 'https://www.vao-mos.info/wp-content/uploads/2016/06/simfonicheskaya_estrada.jpg', 'Классическая сцена-ракушка', 1, '2026-01-09 08:24:47'),
(6, 4, 'https://media-cdn.tripadvisor.com/media/photo-s/10/5d/57/e0/caption.jpg', 'Главная сцена Таганского парка', 1, '2026-01-09 08:24:47'),
(7, 5, 'https://avatars.mds.yandex.net/get-altay/774756/2a0000015f248da28396d3f91e47f6bdd5ee/XXL_height', 'Современная концертная площадка', 1, '2026-01-09 08:24:47'),
(8, 6, 'https://icmos-s3.aif.ru/entity/001/270/gallery_largeimage_5c315f47dc3873c6795fbd36287073b8.jpg', 'Площадка для массовых мероприятий', 1, '2026-01-09 08:24:47'),
(9, 7, 'https://www.vremena-goda.ru/upload/iblock/8b0/bsd_15.jpg', 'Терраса с видом на Москву-реку', 1, '2026-01-09 08:24:47'),
(10, 8, 'https://kudamoscow.ru/uploads/753b8bd2a8cf70316126646d0c800249.jpg', 'Сцена в историческом парке', 1, '2026-01-09 08:24:47'),
(11, 9, 'https://api.parkseason.ru/images/styles/690_388/c8/7a/f3ccdd27d2000e3f9255a7e3e2c48800565c66876be79877658279.jpg', 'Музыкальная площадка в парке', 1, '2026-01-09 08:24:47'),
(12, 13, 'https://resizer.mail.ru/p/5af72917-1043-5c80-99fd-f9458844f2e4/AAACB2-JAQ1waLAZ_vpNXNFzUZXw2r_n-rqIjqGEkfAtvlITDHDttQPN8-qf3tzv6uXVNMh_YAzLnfsTdkKKfmCEj_s.jpg', 'Главная сцена парка', 1, '2026-01-09 08:24:47'),
(13, 14, 'https://img-fotki.yandex.ru/get/42385/35994105.7f8/0_f2a81_1d4acbf1_XL.jpg', 'Эстрада Солнечная', 1, '2026-01-09 08:24:47'),
(14, 15, 'https://avatars.mds.yandex.net/i?id=599bb8428cb9c956ad263da422178add_l-4570529-images-thumbs&n=13', 'Центральная площадь', 1, '2026-01-09 08:24:47'),
(15, 16, 'https://avatars.mds.yandex.net/get-altay/979642/2a00000188b66c846cc7da9ae26bba824a44/XXL_height', 'Музыкальная площадка', 1, '2026-01-09 08:24:47'),
(16, 47, 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/Измайловский_парк_культуры_и_отдыха._Фото_8.jpg/1200px-Измайловский_парк_культуры_и_отдыха._Фото_8.jpg', 'Северная площадь', 1, '2026-01-09 08:24:47'),
(17, 17, 'https://avatars.mds.yandex.net/i?id=e4ceb76ac0388412e11e154cb40841e160c8287f-4486195-images-thumbs&n=13', 'Центральная площадь', 1, '2026-01-09 08:24:47'),
(18, 19, 'https://irecommend.ru/sites/default/files/imagecache/copyright1/user-images/2990141/V001GoP5ybT5tY56ZXOtkg.jpg', 'Профессиональная сцена', 1, '2026-01-09 08:24:47'),
(19, 21, 'https://avatars.mds.yandex.net/i?id=2211a6fe0288489e116e5ac26a4695e78d9d79f4-3940630-images-thumbs&n=13', 'Сцена в исторической усадьбе', 1, '2026-01-09 08:24:47'),
(20, 22, 'https://avatars.mds.yandex.net/i?id=830a40c3c6e73ae42d5b4f0369136c371310f856-3690429-images-thumbs&n=13', 'Эстрада-ракушка', 1, '2026-01-09 08:24:47'),
(21, 23, 'https://avatars.mds.yandex.net/i?id=130682fc2d852b6f41d1a87de561a4ac6224ef78-6426292-images-thumbs&n=13', 'Открытая сцена', 1, '2026-01-09 08:24:47'),
(22, 26, 'https://avatars.mds.yandex.net/i?id=57e0d207cc1c3ec3a3725dd9f98fe64c-5300391-images-thumbs&n=13', 'Летний кинотеатр', 1, '2026-01-09 08:24:47'),
(23, 27, 'https://avatars.mds.yandex.net/i?id=45e908a304355676b48176c6a645b48b-4136742-images-thumbs&n=13', 'Деревянная сцена', 1, '2026-01-09 08:24:47'),
(24, 28, 'https://avatars.mds.yandex.net/i?id=c81cc692d1c7b6c36124769b508ed5f51ac278f7-13222423-images-thumbs&n=13', 'Купол ALPBAU', 1, '2026-01-09 08:24:47'),
(25, 29, 'https://avatars.mds.yandex.net/i?id=88081732394b97d93850a3c1a29249a1-5220389-images-thumbs&n=13', 'Большое поле для мероприятий', 1, '2026-01-09 08:24:47'),
(26, 33, 'https://avatars.mds.yandex.net/i?id=03546bb93ae637be1a7116253d4e9616cad1176c-5460185-images-thumbs&n=13', 'Сцена на воде', 1, '2026-01-09 08:24:47'),
(27, 45, 'https://avatars.mds.yandex.net/i?id=8c02a795dcd67eb11a925ea36080d2e75363dd54-12722406-images-thumbs&n=13', 'Большой амфитеатр', 1, '2026-01-09 08:24:47'),
(28, 53, 'https://avatars.mds.yandex.net/i?id=101a0f78f3923afb63244872edd899eb15ff3e56-5344211-images-thumbs&n=13', 'Малый амфитеатр', 1, '2026-01-09 08:24:47'),
(29, 52, 'https://avatars.mds.yandex.net/i?id=e7622c316dca9cdfc17345ac1091563c5c0ca0c6-5232623-images-thumbs&n=13', 'Холл Медиацентра', 1, '2026-01-09 08:24:47'),
(30, 48, 'https://avatars.mds.yandex.net/i?id=724765805bdc0059ee84c4dca58bf494-4623308-images-thumbs&n=13', 'Уникальная плавучая платформа', 1, '2026-01-09 08:24:47'),
(31, 55, 'https://avatars.mds.yandex.net/i?id=daf74e5faa07ab40e506fde92d48c8b26e8bf002-10638478-images-thumbs&n=13', 'Круглая сцена в ландшафтном парке', 1, '2026-01-09 08:24:47'),
(32, 56, 'https://avatars.mds.yandex.net/i?id=87447267d6218543d5985460624a1521396e0ee6-5255429-images-thumbs&n=13', 'Эстрада Ротонда', 1, '2026-01-09 08:24:47'),
(33, 57, 'https://avatars.mds.yandex.net/i?id=ec3b1efd33714c2689d2ad3720dfda2476098566-15428023-images-thumbs&n=13', 'Веранда танцев', 1, '2026-01-09 08:24:47'),
(34, 67, 'https://avatars.mds.yandex.net/i?id=e58e99b5818de09ea338d8c7cf59ebb17892b9ce-4101207-images-thumbs&n=13', 'Сцена в саду', 1, '2026-01-09 08:24:47'),
(35, 66, 'https://avatars.mds.yandex.net/i?id=1245f9d4021b02e9ccbceb1ba9bb64deb9bdfad9-5235028-images-thumbs&n=13', 'Летняя эстрада', 1, '2026-01-09 08:24:47'),
(36, 64, 'https://avatars.mds.yandex.net/get-altay/239474/2a0000015e5345d7b008d1648a030cca430f/L', 'Сцена детского парка', 1, '2026-01-09 08:24:47'),
(37, 63, 'https://avatars.mds.yandex.net/i?id=a0a44f530e837b3b332c2b2786c024fa718b7716-12421994-images-thumbs&n=13', 'Ротонда', 1, '2026-01-09 08:24:47'),
(38, 65, 'https://avatars.mds.yandex.net/i?id=c7a4d762edbc7553ffffa547b557ffb69e8ea809-5288772-images-thumbs&n=13', 'Малая сцена Лес чудес', 1, '2026-01-09 08:24:47'),
(39, 62, 'https://avatars.mds.yandex.net/i?id=1fd733526a778f975ce3510f92a5e1461124f8d1-4987773-images-thumbs&n=13', 'Музыкальная беседка', 1, '2026-01-09 08:24:47'),
(40, 61, 'https://avatars.mds.yandex.net/i?id=48f4ebb6f6791346126b77a5d8da23975d3727d8-12391133-images-thumbs&n=13', 'Сцена с помещениями для артистов', 1, '2026-01-09 08:24:47'),
(41, 60, 'https://avatars.mds.yandex.net/i?id=f7a68731e0af5430ed8292e0f4c6fa179c9ea191-8608462-images-thumbs&n=13', '«Северный тоннель»', 1, '2026-01-09 08:39:37'),
(43, 31, 'https://avatars.mds.yandex.net/i?id=954612c86657ae2ff67ca2cf87ca69605001cfbb-16113309-images-thumbs&n=13', 'Главный вход', 1, '2026-01-09 08:42:04'),
(44, 30, 'https://avatars.mds.yandex.net/get-entity_search/1634327/846504597/SUx182_2x', 'Пушкинская набережная', 1, '2026-01-09 08:43:31'),
(45, 32, 'https://avatars.mds.yandex.net/i?id=926282aea74565e5097c8e5ed1f44b23-5874305-images-thumbs&n=13', 'Фонтанная площадь', 1, '2026-01-09 08:45:41'),
(46, 59, 'https://avatars.mds.yandex.net/get-altay/13668123/2a00000194d60a44d6dc0940384841da7b67/h220', 'Холл Научно-познавательного центра «Заповедное посольство»', 1, '2026-01-09 08:46:37');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `venue_id` (`venue_id`);

--
-- Индексы таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favorite` (`user_id`,`venue_id`),
  ADD KEY `venue_id` (`venue_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venue_id` (`venue_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venues_name` (`name`),
  ADD KEY `idx_venues_district` (`district`),
  ADD KEY `idx_venues_adm_area` (`adm_area`),
  ADD KEY `idx_venues_coordinates` (`latitude`,`longitude`);

--
-- Индексы таблицы `venue_features`
--
ALTER TABLE `venue_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venue_id` (`venue_id`);

--
-- Индексы таблицы `venue_photos`
--
ALTER TABLE `venue_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venue_id` (`venue_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `venue_features`
--
ALTER TABLE `venue_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `venue_photos`
--
ALTER TABLE `venue_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `venue_features`
--
ALTER TABLE `venue_features`
  ADD CONSTRAINT `venue_features_ibfk_1` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `venue_photos`
--
ALTER TABLE `venue_photos`
  ADD CONSTRAINT `venue_photos_ibfk_1` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
