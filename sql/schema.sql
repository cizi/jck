-- Adminer 4.3.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';

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
  `inserted_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pic_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Picture URL',
  `pic_id` int(11) DEFAULT NULL COMMENT 'ID to PIC',
  `url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Action url direct',
  `fb_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Facebook url',
  `yt_url` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Youtube link',
  `place` int(11) DEFAULT NULL COMMENT 'Place ID',
  `place_text` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Place of event (club etc.)',
  `address` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Address of event',
  `location` int(11) DEFAULT NULL COMMENT 'Location of action - part',
  `sublocation` int(11) DEFAULT NULL COMMENT 'Sublocation - city',
  `contact` text COLLATE utf8_czech_ci COMMENT 'Contact especially for places',
  `show_counter` int(11) DEFAULT '0' COMMENT 'Count of shows',
  `click_counter` int(11) DEFAULT '0' COMMENT 'Count of views',
  `contact_email` varchar(255) COLLATE utf8_czech_ci DEFAULT NULL COMMENT 'Email who inserted',
  `gallery_id` int(11) DEFAULT NULL COMMENT 'ID galerie',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `validity` (`validity`),
  KEY `pic_id` (`pic_url`),
  KEY `inserted_by` (`inserted_by`),
  KEY `location` (`location`),
  KEY `sublocation` (`sublocation`),
  KEY `pic_id_2` (`pic_id`),
  KEY `place` (`place`),
  KEY `gallery_id` (`gallery_id`),
  CONSTRAINT `article_ibfk_1` FOREIGN KEY (`type`) REFERENCES `enum_item` (`order`),
  CONSTRAINT `article_ibfk_10` FOREIGN KEY (`pic_id`) REFERENCES `shared_pic` (`id`),
  CONSTRAINT `article_ibfk_11` FOREIGN KEY (`place`) REFERENCES `enum_item` (`order`),
  CONSTRAINT `article_ibfk_12` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`id`),
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


CREATE TABLE `enum_translation` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID záznamu',
  `enum_header_id` int(11) NOT NULL COMMENT 'ID číselníku',
  `lang` varchar(5) COLLATE utf8_czech_ci NOT NULL COMMENT 'Jazyk ',
  `description` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Popis číselníku v odpovídající jazyce',
  PRIMARY KEY (`id`),
  KEY `enum_header_id` (`enum_header_id`),
  CONSTRAINT `enum_translation_ibfk_1` FOREIGN KEY (`enum_header_id`) REFERENCES `enum_header` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


CREATE TABLE `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID record',
  `active` tinyint(1) NOT NULL COMMENT 'Is active',
  `inserted_timestamp` datetime NOT NULL COMMENT 'Date and time inserted',
  `user_id` int(11) NOT NULL COMMENT 'Inserted by',
  `on_main_page` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Show on on main page',
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
  `file_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'Typ souboru (0=obrázek; 1=soubor)',
  `article_id` int(11) DEFAULT NULL COMMENT 'ID příspěvku',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  CONSTRAINT `shared_pic_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `article` (`id`)
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


CREATE TABLE `v_emails_from_articles` (`contact_email` varchar(255));


CREATE TABLE `web_config` (
  `id` varchar(255) COLLATE utf8_czech_ci NOT NULL COMMENT 'Identifikace položky (název inputu)',
  `lang` varchar(5) COLLATE utf8_czech_ci NOT NULL COMMENT 'Identifikace jazyka',
  `value` text COLLATE utf8_czech_ci NOT NULL COMMENT 'Uložená hodnota',
  UNIQUE KEY `lang_id` (`lang`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


DROP TABLE IF EXISTS `v_emails_from_articles`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `v_emails_from_articles` AS select distinct `article`.`contact_email` AS `contact_email` from `article` where (`article`.`contact_email` is not null);

-- 2017-10-16 07:12:34
