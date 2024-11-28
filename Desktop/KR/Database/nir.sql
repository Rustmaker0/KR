-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Ноя 28 2024 г., 23:23
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `nir`
--

-- --------------------------------------------------------

--
-- Структура таблицы `department`
--

CREATE TABLE IF NOT EXISTS `department` (
  `id_dep` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name_dep` varchar(255) NOT NULL,
  `dep_head_name` varchar(255) NOT NULL,
  `dep_head_secname` varchar(255) NOT NULL,
  `dep_head_surname` varchar(255) NOT NULL,
  `id_faculty` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_dep`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=31 ;

--
-- Дамп данных таблицы `department`
--

INSERT INTO `department` (`id_dep`, `name_dep`, `dep_head_name`, `dep_head_secname`, `dep_head_surname`, `id_faculty`) VALUES
(1, 'Кафедра судебного дела', 'Пётр', 'Петрович', 'Судеев', 2),
(2, 'Кафедра информационных технологий', 'Алексей', 'Васильевич', 'Никитин', 1),
(24, 'Кафедра нефтедобывающей промышленности', 'Иван ', 'Сергеевич', 'Никифоров 	', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `employees`
--

CREATE TABLE IF NOT EXISTS `employees` (
  `id_empl` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `empl_name` varchar(255) NOT NULL,
  `empl_secname` varchar(255) NOT NULL,
  `empl_surname` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `post` varchar(255) NOT NULL,
  `academic_degree` varchar(255) DEFAULT NULL,
  `id_dep` int(11) unsigned NOT NULL,
  `passport_data` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_empl`,`passport_data`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `employees`
--

INSERT INTO `employees` (`id_empl`, `empl_name`, `empl_secname`, `empl_surname`, `status`, `post`, `academic_degree`, `id_dep`, `passport_data`) VALUES
(1, 'Никита', 'Олегович', 'Воронцов', 'Преподаватель', 'Главный преподаватель', 'Высшая', 1, 2532123456),
(2, 'Олег', 'Викторович', 'Воронцов', 'Аспирант', 'Аспирант', NULL, 1, 2532123126),
(3, 'Фёдор', 'Олегович', 'Кирков', 'Преподаватель', 'Главный преподаватель', 'Высшая', 2, 3532123456);

-- --------------------------------------------------------

--
-- Структура таблицы `faculty`
--

CREATE TABLE IF NOT EXISTS `faculty` (
  `id_faculty` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name_faculty` varchar(255) NOT NULL,
  PRIMARY KEY (`id_faculty`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Дамп данных таблицы `faculty`
--

INSERT INTO `faculty` (`id_faculty`, `name_faculty`) VALUES
(1, 'Факультет информатики и математики'),
(2, 'Факультет истории и права');

-- --------------------------------------------------------

--
-- Структура таблицы `nirs`
--

CREATE TABLE IF NOT EXISTS `nirs` (
  `id_nir` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nir_name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `place` varchar(255) NOT NULL,
  PRIMARY KEY (`id_nir`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Дамп данных таблицы `nirs`
--

INSERT INTO `nirs` (`id_nir`, `nir_name`, `type`, `date`, `place`) VALUES
(1, 'Олимпиада по математики 2022', 'Олимпиада', '2022-10-28', 'г. Иркутск'),
(2, 'Конференция по математики 2023', 'Конференция', '2023-11-18', 'г. Иркутск');

-- --------------------------------------------------------

--
-- Структура таблицы `organizing_committee`
--

CREATE TABLE IF NOT EXISTS `organizing_committee` (
  `id_nir` int(11) unsigned NOT NULL,
  `passport_data` int(11) unsigned NOT NULL,
  `status_chairman` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_nir`,`passport_data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `organizing_committee`
--

INSERT INTO `organizing_committee` (`id_nir`, `passport_data`, `status_chairman`) VALUES
(1, 2532123126, 0),
(1, 2532123456, 1),
(1, 3532123456, 0),
(2, 2532123126, 1),
(2, 3532123456, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `participants`
--

CREATE TABLE IF NOT EXISTS `participants` (
  `id_nir` int(11) unsigned NOT NULL,
  `passport_data` int(11) unsigned NOT NULL,
  `status` varchar(255) NOT NULL,
  PRIMARY KEY (`id_nir`,`passport_data`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `participants`
--

INSERT INTO `participants` (`id_nir`, `passport_data`, `status`) VALUES
(1, 12314123, 'Студент'),
(1, 1212321232, 'Студент');

-- --------------------------------------------------------

--
-- Структура таблицы `specialization`
--

CREATE TABLE IF NOT EXISTS `specialization` (
  `id_spec` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name_spec` varchar(255) NOT NULL,
  `id_faculty` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_spec`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `specialization`
--

INSERT INTO `specialization` (`id_spec`, `name_spec`, `id_faculty`) VALUES
(1, 'Автоматизированные системы управления', 1),
(4, 'Электронные вычислительные машины', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `students`
--

CREATE TABLE IF NOT EXISTS `students` (
  `id_stud` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `stud_name` varchar(255) NOT NULL,
  `stud_secname` varchar(255) NOT NULL,
  `stud_surname` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `id_spec` int(11) unsigned NOT NULL,
  `passport_data` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id_stud`,`passport_data`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп данных таблицы `students`
--

INSERT INTO `students` (`id_stud`, `stud_name`, `stud_secname`, `stud_surname`, `group`, `id_spec`, `passport_data`) VALUES
(1, 'Владимир', 'Викторович', 'Рушкин', 'АСУб-22-2', 1, 1212321232),
(2, 'Николай', 'Викторович', 'Рушкин', 'АСУб-22-1', 1, 1212324442),
(4, 'Олег', 'Васильевич', 'Рожков', 'ЭВМб-22-3', 4, 12314123);

-- --------------------------------------------------------

--
-- Структура таблицы `user_autentificate`
--

CREATE TABLE IF NOT EXISTS `user_autentificate` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `user_autentificate`
--

INSERT INTO `user_autentificate` (`userid`, `username`, `password`) VALUES
(1, 'admin', '698d51a19d8a121ce581499d7b701668');

-- --------------------------------------------------------

--
-- Структура таблицы `user_data`
--

CREATE TABLE IF NOT EXISTS `user_data` (
  `idadmin` int(11) NOT NULL AUTO_INCREMENT,
  `adminname` varchar(255) NOT NULL,
  `adminsecname` varchar(255) NOT NULL,
  `adminsurname` varchar(255) NOT NULL,
  `fuserid` int(11) NOT NULL,
  PRIMARY KEY (`idadmin`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `user_data`
--

INSERT INTO `user_data` (`idadmin`, `adminname`, `adminsecname`, `adminsurname`, `fuserid`) VALUES
(1, 'Иван', 'Иванович', 'Иванив', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
