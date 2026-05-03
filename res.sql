-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 03 2026 г., 17:22
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
-- База данных: `res`
--
CREATE DATABASE IF NOT EXISTS `res` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `res`;

-- --------------------------------------------------------

--
-- Структура таблицы `tovars`
--

CREATE TABLE `tovars` (
  `id_tovar` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `category` varchar(50) NOT NULL DEFAULT 'Еда' COMMENT 'Категория блюда (Еда, Напитки и т.д.)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `tovars`
--

INSERT INTO `tovars` (`id_tovar`, `name`, `description`, `price`, `image_path`, `category`) VALUES
(1, 'Завтрак по итальянски', 'Гренки с авокадо и яйцом', 499.00, 'img/eda/buter.jpg', 'Еда'),
(3, 'Клубничная мечта', 'Овсянка с клубникой, черносливом под йогуртом', 800.00, 'img/eda/ovsyanka.jpg', 'Еда'),
(4, 'Овсяные панкейки', 'Овсяные панкейки с нарезанными яблоком и корицей, политые сгущенкой 6 штук', 650.00, 'img/eda/pancake.jpg', 'Еда'),
(5, 'Пита', 'Необычное блюдо, где тартар завернут в питу  с обжаренной говядиной, свежими огурцами, авокадо, луком и соком лайма', 750.00, 'img/eda/pita.jpg', 'Еда'),
(6, 'Спринг ролл овощной', 'Овощи завернутые в рисовую бумагу, с специальным соусом том ям с кунжутом', 880.00, 'img/eda/salatrol.jpg', 'Еда'),
(7, 'Американский завтрак', 'Вафли с грецкими орехами и чашечкой американо', 300.00, 'img/eda/vafli.jpg', 'Еда'),
(8, 'Ужин аристократа', 'Лапша высшего сорта с кусочками говядины', 10000.00, 'img/eda/doshik.jpg', 'Еда'),
(9, 'Cалат Цезарь', 'Классика всей ресторанной кулинарии', 300.00, 'img/eda/685570272e1a9.jpg', 'Еда'),
(11, 'Ананасовый детокс', 'Фруктовый, травяной и тропический коктейль на основе сока, безалкогольный. ', 350.00, 'img/eda/6856c5198cdad.jpg', 'Напитки'),
(12, 'Мохито', 'Это освежающий сладкий лонг на основе рома с большим количеством мяты и лайма.', 100.00, 'img/eda/6856c59778a9a.jpg', 'Напитки'),
(13, 'Запретный плод', 'Ягодный, цветочный и сладкий коктейль на основе кальвадоса, алкогольный и крепкий. ', 400.00, 'img/eda/6856c5fd26afc.jpg', 'Напитки'),
(14, 'Глинтвейн Кардинал', 'Это крепкий ягодный, пряный и фруктовый коктейль на основе вина.', 250.00, 'img/eda/6856c64618a9e.jpg', 'Напитки'),
(15, 'Холодный латте с орео', 'Безалкогольный сливочный, кофейный, шоколадный и сладкий коктейль на основе молока.', 300.00, 'img/eda/6856c6ff26a81.jpg', 'Напитки'),
(16, 'Классический лимонад в кувшине', 'то безалкогольный цитрусовый, мятный и сладкий коктейль на основе содовой.', 400.00, 'img/eda/6856c77ac5704.jpg', 'Напитки'),
(17, 'Сникерс милкшейк ', 'Сливочный, шоколадный и сладкий коктейль на основе молока, безалкогольный. ', 250.00, 'img/eda/6856c7ce7266e.jpg', 'Напитки'),
(18, 'Черный чай', 'Классический черный чай с глубоким вкусом, легкой терпкостью и теплым древесным послевкусием.', 150.00, 'img/eda/6856c88e5b0e2.jpg', 'Напитки'),
(19, 'Зеленый чай ', 'Свежий, травянистый и легкий напиток с тонкой сладостью и освежающим послевкусием.', 150.00, 'img/eda/6856c8bd27cce.jpg', 'Напитки'),
(20, 'Матча', 'Ярко-зеленый, бархатистый чай с насыщенным сливочным вкусом и мягкой горчинкой.', 200.00, 'img/eda/6856ca2a6597c.jpg', 'Напитки'),
(21, 'Молочный улун', 'Благородный чай с цветочными нотами, медовой сладостью и долгим фруктовым послевкусием.', 200.00, 'img/eda/6856cb1281a2a.jpg', 'Напитки'),
(22, 'Чай каркаде', 'Ярко-рубиновый напиток с кисло-сладким вкусом, напоминающим спелую клюкву, подается горячим или со льдом.', 170.00, 'img/eda/6856cb7deda07.jpg', 'Напитки'),
(23, 'Чай Пуэр', 'Землистый, глубокий чай с древесными нотами и сложным бархатистым вкусом, который раскрывается с каждым глотком.', 300.00, 'img/eda/6856cbdb28ed4.jpg', 'Напитки');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL COMMENT 'Хранить только хэши паролей',
  `is_admin` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `email`, `pass`, `is_admin`) VALUES
(6, 'YTzver', '12345@mail.ru', '$2y$10$g.dqKS59g/ZMGqDyiysCdOE1UV44s7d.yqZM4lyD1uYJZXhgOhHBG', 1),
(7, 'Данил', '000@gmail.com', '$2y$10$2GdZa9Z7Qfx2oXkk2OV9h.mD1pmsPeYwfgS8Q5aE1EgP1IW1mdQL.', 0),
(8, 'YTzver', '01@mail.ru', '$2y$10$SxicmQYbqB0frPiK6lGfVO/26OvUdyfWKOeEqB/CJSNmOil3jxyvu', 0),
(9, 'Денис', '000@mail.ru', '$2y$10$gKI6jmqQESscGkWDubaWuuZ4Px3eUX99XhrcT2r6V4gjrhvS8Cq1O', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `tovars`
--
ALTER TABLE `tovars`
  ADD PRIMARY KEY (`id_tovar`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `tovars`
--
ALTER TABLE `tovars`
  MODIFY `id_tovar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
