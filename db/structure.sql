-- Adminer 4.3.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DELIMITER ;;

DROP FUNCTION IF EXISTS `SPLIT_STR`;;
CREATE FUNCTION `SPLIT_STR`(
  x VARCHAR(255),
  delim VARCHAR(12),
  pos INT
) RETURNS varchar(255) CHARSET utf8 COLLATE utf8_czech_ci
    DETERMINISTIC
BEGIN 
    RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(x, delim, pos),
       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) + 1),
       delim, '');
END;;

DELIMITER ;

CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Block ID',
  `type` int(11) NOT NULL COMMENT 'Type of article',
  `validity` int(11) NOT NULL COMMENT 'Free/paid/top',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Is active',
  `background_color` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Content background color',
  `color` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Font color',
  `width` varchar(50) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Width of block',
  `inserted_by` int(11) DEFAULT NULL COMMENT 'Record inserted by',
  `inserted_timestamp` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Timestamp',
  `pic_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Picture URL',
  `pic_id` int(11) DEFAULT NULL COMMENT 'ID to PIC',
  `url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Action url direct',
  `fb_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Facebook url',
  `yt_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Youtube link',
  `address` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Address of event',
  `location` int(11) DEFAULT NULL COMMENT 'Location of action - part',
  `sublocation` int(11) DEFAULT NULL COMMENT 'Sublocation - city',
  `show_counter` int(11) DEFAULT '0' COMMENT 'Count of shows',
  `click_counter` int(11) DEFAULT '0' COMMENT 'Count of views',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `validity` (`validity`),
  KEY `pic_id` (`pic_url`),
  KEY `inserted_by` (`inserted_by`),
  KEY `location` (`location`),
  KEY `sublocation` (`sublocation`),
  KEY `pic_id_2` (`pic_id`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`type`) REFERENCES `enum_item` (`order`),
  CONSTRAINT `article_ibfk_10` FOREIGN KEY (`pic_id`) REFERENCES `shared_pic` (`id`),
  CONSTRAINT `article_ibfk_3` FOREIGN KEY (`validity`) REFERENCES `enum_item` (`order`),
  CONSTRAINT `article_ibfk_7` FOREIGN KEY (`inserted_by`) REFERENCES `user` (`id`),
  CONSTRAINT `article_ibfk_8` FOREIGN KEY (`location`) REFERENCES `enum_item` (`order`),
  CONSTRAINT `article_ibfk_9` FOREIGN KEY (`sublocation`) REFERENCES `enum_item` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `article_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
  `article_id` int(11) NOT NULL COMMENT 'Article ID',
  `menu_order` int(11) NOT NULL COMMENT 'Category ID',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `menu_order` (`menu_order`),
  CONSTRAINT `article_category_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`),
  CONSTRAINT `article_category_ibfk_2` FOREIGN KEY (`menu_order`) REFERENCES `menu_item` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `article_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of record',
  `article_id` int(11) NOT NULL COMMENT 'ID of article',
  `header` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Header title',
  `seo` longtext COLLATE utf8_czech_ci NOT NULL COMMENT 'seo string',
  `lang` varchar(5) COLLATE utf8_czech_ci NOT NULL COMMENT 'Lang of content',
  `content` longtext COLLATE utf8_czech_ci NOT NULL COMMENT 'Text content',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `article_content_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `article_timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
  `article_id` int(11) NOT NULL COMMENT 'Article ID',
  `date_from` date NOT NULL COMMENT 'Article start date',
  `date_to` date NOT NULL COMMENT 'Article end date',
  `time` time NOT NULL COMMENT 'Article show time',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `article_timetable_ibfk_2` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of banner',
  `title` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Title of banner',
  `img` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Path to image',
  `url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Target URL ',
  `banner_type` int(11) NOT NULL COMMENT 'Type of banner',
  `date_start` date NOT NULL COMMENT 'Date start of banner',
  `date_end` date DEFAULT NULL COMMENT 'Date end of banner',
  `show_on_main_page` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Show banner on main page',
  `show_counter` int(11) NOT NULL DEFAULT '0' COMMENT 'Counter of banner shows',
  `click_counter` int(11) NOT NULL DEFAULT '0' COMMENT 'Counter of banner clicks',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID of user who inserted the record',
  PRIMARY KEY (`id`),
  KEY `banner_type` (`banner_type`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `banner_ibfk_1` FOREIGN KEY (`banner_type`) REFERENCES `enum_item` (`order`),
  CONSTRAINT `banner_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `banner_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of record',
  `banner_id` int(11) NOT NULL COMMENT 'ID of banner',
  `menu_order` int(11) NOT NULL COMMENT 'Order to menu',
  PRIMARY KEY (`id`),
  KEY `banner_id` (`banner_id`),
  KEY `menu_order` (`menu_order`),
  CONSTRAINT `banner_category_ibfk_1` FOREIGN KEY (`banner_id`) REFERENCES `banner` (`id`),
  CONSTRAINT `banner_category_ibfk_2` FOREIGN KEY (`menu_order`) REFERENCES `menu_item` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `block` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Block ID',
  `background_color` varchar(50) COLLATE utf8_czech_ci NOT NULL COMMENT 'Content background color',
  `color` varchar(50) COLLATE utf8_czech_ci NOT NULL COMMENT 'Font color',
  `width` varchar(50) COLLATE utf8_czech_ci NOT NULL COMMENT 'Width of block',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `block_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of record',
  `block_id` int(11) NOT NULL COMMENT 'ID of block',
  `lang` varchar(5) COLLATE utf8_czech_ci NOT NULL COMMENT 'Lang of content',
  `content` longtext COLLATE utf8_czech_ci NOT NULL COMMENT 'Text content',
  PRIMARY KEY (`id`),
  UNIQUE KEY `block_id_lang` (`block_id`,`lang`),
  CONSTRAINT `block_content_ibfk_1` FOREIGN KEY (`block_id`) REFERENCES `block` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `enum_header` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID záznamu',
  `description` varchar(255) COLLATE utf8_czech_ci NOT NULL DEFAULT 'USER ENUM' COMMENT 'Popis (nevyužíváno)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `enum_header` (`id`, `description`) VALUES
(1,	'USER ENUM'),
(2,	'USER ENUM'),
(3,	'USER ENUM'),
(4,	'USER ENUM'),
(5,	'USER ENUM');

CREATE TABLE `enum_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID záznamu',
  `enum_header_id` int(11) NOT NULL COMMENT 'ID číselníku',
  `lang` varchar(5) COLLATE utf8_czech_ci NOT NULL COMMENT 'Jazyk položky',
  `item` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Hodnota položky číselníku',
  `order` int(11) NOT NULL COMMENT 'ID společných položek',
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang_order` (`lang`,`order`),
  KEY `enum_header_id` (`enum_header_id`),
  KEY `order` (`order`),
  CONSTRAINT `enum_item_ibfk_1` FOREIGN KEY (`enum_header_id`) REFERENCES `enum_header` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `enum_item` (`id`, `enum_header_id`, `lang`, `item`, `order`) VALUES
(1,	1,	'cs',	'Akce',	1),
(2,	1,	'en',	'Action',	1),
(3,	1,	'cs',	'Článek',	2),
(4,	1,	'en',	'Text',	2),
(5,	2,	'cs',	'free',	3),
(6,	2,	'en',	'free',	3),
(7,	2,	'cs',	'placená',	4),
(8,	2,	'en',	'paid',	4),
(9,	2,	'cs',	'top',	5),
(10,	2,	'en',	'top',	5),
(11,	3,	'cs',	'Jihočeský',	6),
(12,	3,	'en',	'Southbohemia',	6),
(21,	4,	'cs',	'České Budějovice',	7),
(22,	4,	'en',	'Budweis',	7),
(23,	1,	'cs',	'Místo',	8),
(24,	1,	'en',	'Place',	8),
(25,	5,	'cs',	'Big banner (660x438)',	9),
(26,	5,	'en',	'Big banner (660x438)',	9),
(27,	5,	'cs',	'Large rectangle (336x280)',	10),
(28,	5,	'en',	'Large rectangle (336x280)',	10),
(29,	5,	'cs',	'Middle rectangle (336x157)',	11),
(30,	5,	'en',	'Middle rectangle (336x157)',	11),
(31,	5,	'cs',	'Full banner (468x60)',	12),
(32,	5,	'en',	'Full banner (468x60)',	12),
(33,	5,	'cs',	'Wallpaper (2000x1250)',	13),
(34,	5,	'en',	'Wallpaper (2000x1250)',	13),
(35,	5,	'cs',	'Halfbanner (290x60)',	14),
(36,	5,	'en',	'Halfbanner (290x60)',	14);

CREATE TABLE `enum_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID záznamu',
  `enum_header_id` int(11) NOT NULL COMMENT 'ID číselníku',
  `lang` varchar(5) COLLATE utf8_czech_ci NOT NULL COMMENT 'Jazyk ',
  `description` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Popis číselníku v odpovídající jazyce',
  PRIMARY KEY (`id`),
  KEY `enum_header_id` (`enum_header_id`),
  CONSTRAINT `enum_translation_ibfk_1` FOREIGN KEY (`enum_header_id`) REFERENCES `enum_header` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `enum_translation` (`id`, `enum_header_id`, `lang`, `description`) VALUES
(1,	1,	'cs',	'Typ příspěvku'),
(2,	1,	'en',	'Type of contribution'),
(3,	2,	'cs',	'Platnost přípěvku'),
(4,	2,	'en',	'Article validity'),
(5,	3,	'cs',	'Lokace'),
(6,	3,	'en',	'Location'),
(13,	4,	'cs',	'Sublokace'),
(14,	4,	'en',	'Sublocation'),
(15,	5,	'cs',	'Typ banneru'),
(16,	5,	'en',	'Banner type');

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID record',
  `active` tinyint(1) NOT NULL COMMENT 'Is active',
  `inserted_timestamp` datetime NOT NULL COMMENT 'Date and time inserted',
  `user_id` int(11) NOT NULL COMMENT 'Inserted by',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `gallery_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
  `gallery_id` int(11) NOT NULL COMMENT 'ID of gallery',
  `header` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Gallery title',
  `desc` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Description in lang',
  `lang` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gallery_id` (`gallery_id`),
  CONSTRAINT `gallery_content_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `gallery_pic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Record ID',
  `gallery_id` int(11) NOT NULL COMMENT 'ID fo gallery',
  `shared_pic_id` int(11) NOT NULL COMMENT 'ID of pic in gallery',
  PRIMARY KEY (`id`),
  KEY `gallery_id` (`gallery_id`),
  KEY `shared_pic_id` (`shared_pic_id`),
  CONSTRAINT `gallery_pic_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`id`),
  CONSTRAINT `gallery_pic_ibfk_2` FOREIGN KEY (`shared_pic_id`) REFERENCES `shared_pic` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID of record (needed in subitems)',
  `lang` varchar(5) COLLATE utf8_czech_ci NOT NULL COMMENT 'Language shortcut',
  `link` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Link to web',
  `title` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Frontend title',
  `alt` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Alt on hover',
  `level` int(11) NOT NULL COMMENT 'Level nesting',
  `order` int(11) NOT NULL COMMENT 'Order in menu',
  `submenu` int(11) NOT NULL COMMENT 'ID of this menu item',
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang_link` (`lang`,`link`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `menu_item` (`id`, `lang`, `link`, `title`, `alt`, `level`, `order`, `submenu`) VALUES
(1,	'cs',	'akce',	'Akce',	'Akce',	1,	1,	0),
(2,	'en',	'action',	'Action',	'Action',	1,	1,	0),
(3,	'cs',	'hudebni-akce',	'Hudební akce',	'hudební akce',	2,	2,	1),
(4,	'en',	'music-events',	'Music events',	'Music events',	2,	2,	1),
(9,	'cs',	'kulturni-akce',	'Kulturní akce',	'Kulturní akce',	2,	3,	1),
(10,	'en',	'culture-events',	'Culture events',	'Culture events',	2,	3,	1),
(11,	'cs',	'zabava',	'Zábava',	'Zábava',	2,	4,	1),
(12,	'en',	'entertainment',	'Entertainment',	'Enterteiment',	2,	4,	1),
(13,	'cs',	'sportovni-akce',	'Sportovní akce',	'Sportovní akce',	2,	5,	1),
(14,	'en',	'sport-events',	'Sport events',	'Sport events',	2,	5,	1),
(15,	'cs',	'kulinarske-akce',	'Kulinářské akce',	'Kulinářské akce',	2,	6,	1),
(16,	'en',	'cooking-events',	'Cooking events',	'Cooking events',	2,	6,	1),
(17,	'cs',	'vystavy-prednasky',	'Výstavy / Přednášky',	'Výstavy / přednášky',	2,	7,	1),
(18,	'en',	'shows-speaks',	'Shows / Speaks',	'Shows / speaks',	2,	7,	1),
(19,	'cs',	'akce-pro-deti',	'Akce pro děti',	'Akce pro děti',	2,	8,	1),
(20,	'en',	'kids-actions',	'Kids actions',	'Kids actions',	2,	8,	1),
(21,	'cs',	'festivaly',	'Festivaly',	'Festivaly',	2,	9,	1),
(22,	'en',	'festivals',	'Festivals',	'Festivals',	2,	9,	1),
(23,	'cs',	'mista-sluzby',	'Místa / Služby',	'Místa / služby',	1,	10,	0),
(24,	'en',	'places-services',	'Places / Services',	'Places / services',	1,	10,	0),
(25,	'cs',	'ubytovani',	'Ubytování',	'Ubytování',	2,	11,	23),
(26,	'en',	'accommodation',	'Accommodation',	'Accommodation',	2,	11,	23),
(27,	'cs',	'pohostinstvi',	'Pohostinství',	'Pohostinství',	2,	12,	23),
(28,	'en',	'hospitality',	'Hospitality',	'Hospitality',	2,	12,	23),
(29,	'cs',	'restaurace',	'Restaurace',	'Restaurtace',	3,	13,	27),
(30,	'en',	'restaurants',	'Restaurants',	'Restaurants',	3,	13,	27),
(31,	'cs',	'pivovary',	'Pivovary',	'Pivovary',	3,	14,	27),
(32,	'en',	'breweries',	'Breweries',	'Breweries',	3,	14,	27),
(33,	'cs',	'kavarny',	'Kavárny',	'Kavárny',	3,	15,	27),
(34,	'en',	'cafes',	'Cafes',	'Cafes',	3,	15,	27),
(35,	'cs',	'bary',	'Bary',	'Bary',	3,	16,	27),
(36,	'en',	'bars',	'Bars',	'Bars',	3,	16,	27),
(37,	'cs',	'pizzerie',	'Pizzerie',	'Pizzerie',	3,	17,	27),
(38,	'en',	'pizzerias',	'Pizzerias',	'Pizzerias',	3,	17,	27),
(39,	'cs',	'zahradky',	'Zahrádky',	'Zahrádky',	3,	18,	27),
(40,	'en',	'gardens',	'Gardens',	'Gardens',	3,	18,	27);

CREATE TABLE `page_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `menu_item_id` int(11) NOT NULL COMMENT 'ID of men item',
  `block_id` int(11) NOT NULL COMMENT 'ID of block',
  `order` int(11) NOT NULL COMMENT 'Order of item in',
  PRIMARY KEY (`id`),
  KEY `menu_item_id` (`menu_item_id`),
  KEY `block_id` (`block_id`),
  CONSTRAINT `page_content_ibfk_1` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_item` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `shared_pic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Id',
  `path` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Cesta k souboru',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `slider_pic` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `path` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Cesta k souboru',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `slider_setting` (
  `id` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'ID položky (inputu)',
  `value` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Uložená hodnota',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `slider_setting` (`id`, `value`) VALUES
('SLIDER_CONTROLS',	'1'),
('SLIDER_ON',	'1'),
('SLIDER_SLIDE_SHOW',	'1'),
('SLIDER_TIMING',	'3'),
('SLIDER_WIDTH',	'WIDTH_10');

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `email` varchar(255) NOT NULL COMMENT 'Přihlašovací jméno (email)',
  `password` char(255) NOT NULL COMMENT 'Heslo',
  `real_name` varchar(255) NOT NULL COMMENT 'Reálné jméno',
  `phone` varchar(255) DEFAULT NULL COMMENT 'Telefon',
  `role` int(2) NOT NULL COMMENT 'Role v číselném vyjádření',
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Je uživatel aktivní?',
  `register_timestamp` datetime NOT NULL COMMENT 'Kdy by uživatel registrován',
  `last_login` datetime NOT NULL COMMENT 'Poslední přihlášení',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `user` (`id`, `email`, `password`, `real_name`, `phone`, `role`, `active`, `register_timestamp`, `last_login`) VALUES
(7,	'cizi@email.cz',	'$2y$10$bfwt2RK0OnC0U5/q94Q2q.lLh.1NXP5KaW4tGt4XZ3cttPjJH.EIa',	'Jan Cimler',	'777652670',	99,	1,	'0000-00-00 00:00:00',	'2017-09-17 11:49:05'),
(12,	'jan.cimler@gmail.com',	'$2y$10$NMCFOMDd2wHXJGIj3tnKDuui4lZYY0bwm/uTW/NJJF3bhxHbKHDVa',	'',	NULL,	99,	1,	'2017-08-23 14:27:33',	'0000-00-00 00:00:00'),
(13,	'frantisek.drobil@greenconsulting.cz',	'$2y$10$65zIVrE9IVlHCrt.s7wvfeyZKM6FTXxs0VG3Xtyd/UUFqTLtK2Q4.',	'František Drobil',	'',	99,	1,	'2017-08-25 11:31:32',	'0000-00-00 00:00:00'),
(14,	'cizister@yahoo.com',	'$2y$10$mJC2fzG6YWnAEwlyP50ab.4GDuexPj8IhwcCSRigjqaNzf5xzotkm',	'Je Ce',	'',	30,	1,	'2017-08-25 13:35:59',	'2017-08-25 13:36:19');

CREATE TABLE `web_config` (
  `id` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Identifikace položky (název inputu)',
  `lang` varchar(5) COLLATE utf8_czech_ci NOT NULL COMMENT 'Identifikace jazyka',
  `value` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Uložená hodnota',
  UNIQUE KEY `lang_id` (`lang`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `web_config` (`id`, `lang`, `value`) VALUES
('CONTACT_FORM_ATTACHMENT',	'',	'1'),
('CONTACT_FORM_BACKGROUND_COLOR',	'',	'#20262f'),
('CONTACT_FORM_COLOR',	'',	'#ffffff'),
('CONTACT_FORM_RECIPIENT',	'',	'cizi@email.cz'),
('FOOTER_BACKGROUND_COLOR',	'',	'#20262f'),
('FOOTER_COLOR',	'',	'#ffffff'),
('FOOTER_WIDTH',	'',	'WIDTH_10'),
('HEADER_BACKGROUND_COLOR',	'',	'#000000'),
('HEADER_COLOR',	'',	'#ffffff'),
('HEADER_HEIGHT',	'',	'130'),
('HEADER_WIDTH',	'',	'WIDTH_8'),
('LANG_BG_COLOR',	'',	'#000000'),
('LANG_FONT_COLOR',	'',	'#ffffff'),
('LANG_WIDTH',	'',	'WIDTH_10'),
('SHOW_CONTACT_FORM_IN_FOOTER',	'',	''),
('SHOW_FOOTER',	'',	'1'),
('SHOW_HEADER',	'',	'1'),
('WEB_CONFIG_BACKGROUND_COLOR',	'',	'#ffffff'),
('WEB_FAVICON',	'',	'/upload/20160519-095912-20160516-184539-favicon.ico'),
('WEB_HOME_BLOCK',	'',	'1'),
('WEB_MENU_BG',	'',	'#2b2b2b'),
('WEB_MENU_LINK_COLOR',	'',	'#ffffff'),
('WEB_SHOW_HOME',	'',	'1'),
('WEB_SHOW_MENU',	'',	'1'),
('WEB_WIDTH',	'',	'WIDTH_8'),
('CONTACT_FORM_CONTENT',	'cs',	''),
('CONTACT_FORM_TITLE',	'cs',	'Kontaktujte nás'),
('FOOTER_CONTENT',	'cs',	''),
('HEADER_CONTENT',	'cs',	'<table style=\"width: 100%; height: 100%;\">\n<tbody>\n<tr>\n<td style=\"width: 50%;\" align=\"center\">\n<h1 style=\"font-family: \'InterstateRegularCompressed\',sans-serif; font-size: 52px; line-height: 44px; color: white; white-space: nowrap; letter-spacing: 0; font-weight: normal;\">JIZNICECHY<span style=\"color: #e04b00;\">EVENTS</span>CALENDAR</h1>\n</td>\n<td style=\"width: 50%;\" align=\"center\">\n<h1 style=\"font-family: font-size: 54px; font-weight: bold; line-height: 44px; color: white; white-space: nowrap; letter-spacing: 0;\">JIHO<span style=\"color: #e04b00;\">ČESK&Yacute;</span>KALEND&Aacute;Ř.CZ</h1>\n</td>\n</tr>\n</tbody>\n</table>'),
('WEB_KEYWORDS',	'cs',	'České Budějovice akce, kultura, koncerty, festivaly, pro děti, společenské, sport, zábava, výlety, památky, hotely, restaurace, cestování, ubytování, nákupy v jižní Čechy, Lipensko, Český Krumlov, Třeboň, Tábor'),
('WEB_TITLE',	'cs',	'Jihočeský kalendář akcí -  kultura, koncerty, zábava, sport, ubytování'),
('WEG_GOOGLE_ANALYTICS',	'cs',	'<script>\n</script>'),
('CONTACT_FORM_CONTENT',	'en',	'<p>Contact form contact content dude</p>'),
('CONTACT_FORM_TITLE',	'en',	'Title of english version of contact form'),
('FOOTER_CONTENT',	'en',	'<p>English version of footer content</p>'),
('HEADER_CONTENT',	'en',	'<table style=\"width: 100%; height: 100%;\">\n<tbody>\n<tr>\n<td style=\"width: 50%;\" align=\"center\">\n<h1 style=\"font-family: \'InterstateRegularCompressed\',sans-serif; font-size: 52px; line-height: 44px; color: white; white-space: nowrap; letter-spacing: 0; font-weight: normal;\">JIZNICECHY<span style=\"color: #e04b00;\">EVENTS</span>CALENDAR</h1>\n</td>\n<td style=\"width: 50%;\" align=\"center\">\n<h1 style=\"font-family: font-size: 54px; font-weight: bold; line-height: 44px; color: white; white-space: nowrap; letter-spacing: 0;\">JIHO<span style=\"color: #e04b00;\">ČESK&Yacute;</span>KALEND&Aacute;Ř.CZ</h1>\n</td>\n</tr>\n</tbody>\n</table>'),
('WEB_KEYWORDS',	'en',	'Keyword'),
('WEB_TITLE',	'en',	'My web title'),
('WEG_GOOGLE_ANALYTICS',	'en',	'');

-- 2017-09-17 10:31:21
