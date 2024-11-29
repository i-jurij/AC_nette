CREATE TABLE IF NOT EXISTS `geo_district` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Федеральные округа';

INSERT INTO `geo_district` (`id`, `name`) VALUES
(2, 'Центральный федеральный округ'),
(3, 'Южный федеральный округ'),
(4, 'Северо-западный федеральный округ'),
(5, 'Дальневосточный федеральный округ'),
(6, 'Сибирский федеральный округ'),
(7, 'Уральский федеральный округ'),
(8, 'Приволжский федеральный округ'),
(9, 'Северо-Кавказский федеральный округ');