--
-- База данных: `yatm`
--
CREATE DATABASE IF NOT EXISTS `yatm` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `yatm`;

-- --------------------------------------------------------

--
-- Структура таблицы `stations`
--

CREATE TABLE IF NOT EXISTS `stations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(16) NOT NULL,
  `port` int(11) NOT NULL,
  `name` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Структура таблицы `probes`
--

CREATE TABLE IF NOT EXISTS `probes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(8) NOT NULL,
  `location` varchar(8) NOT NULL,
  `description` varchar(8) NOT NULL,
  `station` varchar(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Структура таблицы `temp`
--

CREATE TABLE IF NOT EXISTS `data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `probe` varchar(8) NOT NULL,
  `time` datetime NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE USER 'yatm'@'localhost' IDENTIFIED BY  'zxcvbnm';

GRANT USAGE ON * . * TO  'yatm'@'localhost' IDENTIFIED BY  'zxcvbnm' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

GRANT ALL PRIVILEGES ON  `yatm` . * TO  'yatm'@'localhost';
