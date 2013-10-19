DROP TABLE IF EXISTS `#__anodos_partner` ;
DROP TABLE IF EXISTS `#__anodos_product` ;
DROP TABLE IF EXISTS `#__anodos_product_vat` ;
DROP TABLE IF EXISTS `#__anodos_updater` ;
DROP TABLE IF EXISTS `#__anodos_category_synonym` ;
DROP TABLE IF EXISTS `#__anodos_vendor_synonym` ;
DROP TABLE IF EXISTS `#__anodos_price_type` ;
DROP TABLE IF EXISTS `#__anodos_currency` ;
DROP TABLE IF EXISTS `#__anodos_price` ;
DROP TABLE IF EXISTS `#__anodos_currency_rate` ;
DROP TABLE IF EXISTS `#__anodos_stock` ;
DROP TABLE IF EXISTS `#__anodos_product_quantity` ;

CREATE  TABLE IF NOT EXISTS `#__anodos_partner` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT '' ,
  `category_id` BIGINT NOT NULL DEFAULT '0' ,
  `vendor` TINYINT(1) NOT NULL DEFAULT '0' ,
  `distributor` TINYINT(1) NOT NULL DEFAULT '0' ,
  `client` TINYINT(1) NOT NULL DEFAULT '0' ,
  `competitor` TINYINT(1) NOT NULL DEFAULT '0' ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `ordering` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `description` TEXT NOT NULL DEFAULT '' ,
  `checked_out` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` BIGINT NOT NULL DEFAULT '0' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` BIGINT NOT NULL DEFAULT '0' ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `metadata` TEXT NOT NULL DEFAULT '' ,
  `hits` BIGINT NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `anodos_partner_category_idx` (`category_id` ASC) ,
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = 'Контрагент';

INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`, `version`) VALUES
(0, 0, 1, 1, 2, 1, 'uncategorised', 'com_anodos.partner', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{"target":"","image":""}', '', '', '{"page_title":"","author":"","robots":""}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1);

CREATE  TABLE IF NOT EXISTS `#__anodos_product` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `alias` VARCHAR(255) NOT NULL DEFAULT '' ,
  `article` VARCHAR(225) NOT NULL DEFAULT '' ,
  `full_name` TEXT NOT NULL DEFAULT '' ,
  `category_id` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `vendor_id` BIGINT UNSIGNED NOT NULL ,
  `vat_id` INT NOT NULL DEFAULT '1' ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `ordering` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `description` TEXT NOT NULL DEFAULT '' ,
  `checked_out` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '2100-01-01 00:00:00' ,
  `metadata` TEXT NOT NULL DEFAULT '' ,
  `hits` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `duble_of` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `abodos_product_category_idx` (`category_id` ASC) ,
  INDEX `anodos_product_duble_of_idx` (`duble_of` ASC) ,
  INDEX `anodos_product_vendor_idx` (`vendor_id` ASC) ,
  INDEX `anodos_product_vat_idx` (`vat_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = 'Продукт';

CREATE  TABLE IF NOT EXISTS `#__anodos_product_vat` (
	`id` INT NOT NULL AUTO_INCREMENT ,
	`vat` DECIMAL(5,2) NOT NULL DEFAULT '18.00' ,
	PRIMARY KEY (`id`) )
ENGINE = MyISAM;

INSERT INTO `#__anodos_product_vat` (`id`, `vat`) VALUES (1, 18.00);
INSERT INTO `#__anodos_product_vat` (`id`, `vat`) VALUES (2, 0.00);

INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`, `version`) VALUES
(0, 0, 1, 1, 2, 1, 'uncategorised', 'com_anodos.product', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{"target":"","image":""}', '', '', '{"page_title":"","author":"","robots":""}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1);

CREATE  TABLE IF NOT EXISTS `#__anodos_updater` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `partner_id` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `category_id` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT '' ,
  `state` TINYINT(3) NOT NULL DEFAULT '0' ,
  `ordering` BIGINT NOT NULL DEFAULT '0' ,
  `description` TEXT NOT NULL DEFAULT '' ,
  `checked_out` BIGINT NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `metadata` TEXT NOT NULL DEFAULT '' ,
  `hits` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `updated_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `client` VARCHAR(255) NOT NULL DEFAULT '' ,
  `login` VARCHAR(255) NOT NULL DEFAULT '' ,
  `pass` VARCHAR(255) NOT NULL DEFAULT '' ,
  `file` VARCHAR(255) NOT NULL DEFAULT '' ,
  `class` VARCHAR(255) NOT NULL DEFAULT '' ,
  `cookie` TEXT NOT NULL DEFAULT '' ,
  `key` VARCHAR(255) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `anodos_updater_partner_idx` (`partner_id` ASC) ,
  INDEX `anodos_updater_category_idx` (`category_id` ASC) ,
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`, `version`) VALUES
(0, 0, 1, 1, 2, 1, 'uncategorised', 'com_anodos.updater', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{"target":"","image":""}', '', '', '{"page_title":"","author":"","robots":""}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1);

INSERT INTO `#__anodos_updater` (`id`, `partner_id`, `category_id`, `name`, `alias`, `state`, `ordering`, `description`, `checked_out`, `checked_out_time`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `metadata`, `hits`, `updated`, `updated_by`, `client`, `login`, `pass`, `cookie`, `key`) VALUES (0, 0, 0, 'Обновление курсов валют ЦБР', 'CBR', 1, 0, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, '0000-00-00 00:00:00', 0, '', '', '', '', '61f3cff654657b5ba7c69cb949a22a5a');
INSERT INTO `#__anodos_updater` (`id`, `partner_id`, `category_id`, `name`, `alias`, `state`, `ordering`, `description`, `checked_out`, `checked_out_time`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `metadata`, `hits`, `updated`, `updated_by`, `client`, `login`, `pass`, `cookie`, `key`) VALUES (0, 0, 0, 'Обновление данных Merlion (Москва)', 'MerlionMsk', 1, 1, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, '0000-00-00 00:00:00', 0, '', '', '', '', 'e33193701a19d5fe636be0880b34f649');
INSERT INTO `#__anodos_updater` (`id`, `partner_id`, `category_id`, `name`, `alias`, `state`, `ordering`, `description`, `checked_out`, `checked_out_time`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `metadata`, `hits`, `updated`, `updated_by`, `client`, `login`, `pass`, `cookie`, `key`) VALUES (0, 0, 0, 'Обновление данных Treolan', 'Treolan', 1, 2, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, '0000-00-00 00:00:00', 0, '', '', '', '', '13c9666ea5dede5ca60a90a515d3762f');
INSERT INTO `#__anodos_updater` (`id`, `partner_id`, `category_id`, `name`, `alias`, `state`, `ordering`, `description`, `checked_out`, `checked_out_time`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `metadata`, `hits`, `updated`, `updated_by`, `client`, `login`, `pass`, `cookie`, `key`) VALUES (0, 0, 0, 'Обновление из конфигуратора Fujitsu', 'Fujitsu', 1, 3, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, '0000-00-00 00:00:00', 0, '', '', '', '', '0');
INSERT INTO `#__anodos_updater` (`id`, `partner_id`, `category_id`, `name`, `alias`, `state`, `ordering`, `description`, `checked_out`, `checked_out_time`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `metadata`, `hits`, `updated`, `updated_by`, `client`, `login`, `pass`, `cookie`, `key`) VALUES (0, 0, 0, 'Обновление данных Merlion (Самара)', 'MerlionSmr', 1, 4, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0, '0000-00-00 00:00:00', 0, '', '', '', '', '8411e9754b9b7e6d16be6f84c71a23cb');

CREATE  TABLE IF NOT EXISTS `#__anodos_category_synonym` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `partner_id` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `category_id` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `name` VARCHAR(255) NOT NULL DEFAULT '' ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `checked_out` BIGINT NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` BIGINT NOT NULL DEFAULT '0' ,
  `modified` DATETIME NOT NULL DEFAULT '000-00-00 00:00:00' ,
  `modified_by` BIGINT NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `anodos_category_synonym_partner_idx` (`partner_id` ASC) ,
  INDEX `anodos_category_synonym_category_idx` (`category_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

CREATE  TABLE IF NOT EXISTS `#__anodos_vendor_synonym` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `partner_id` BIGINT UNSIGNED NOT NULL ,
  `vendor_id` BIGINT UNSIGNED NOT NULL ,
  `name` VARCHAR(255) NOT NULL ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `checked_out` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `anodos_vendor_synonym_vendor_idx` (`vendor_id` ASC) ,
  INDEX `anodos_vendor_synonym_partner_idx` (`partner_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

CREATE  TABLE IF NOT EXISTS `#__anodos_price_type` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `alias` VARCHAR(255) NOT NULL ,
  `input` TINYINT(1) NOT NULL DEFAULT '1' ,
  `output` TINYINT(1) NOT NULL DEFAULT '0' ,
  `fixed` TINYINT(1) NOT NULL DEFAULT '0' ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `ordering` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `description` TEXT NOT NULL DEFAULT '' ,
  `checked_out` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `metadata` TEXT NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;

CREATE  TABLE IF NOT EXISTS `#__anodos_currency` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `alias` CHAR(3) NOT NULL ,
  `name_html` VARCHAR(255) NOT NULL ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `ordering` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `description` TEXT NOT NULL DEFAULT '' ,
  `checked_out` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` BIGINT NOT NULL DEFAULT '0' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` BIGINT NOT NULL DEFAULT '0' ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `metadata` TEXT NOT NULL DEFAULT '' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = 'Валюты';

INSERT INTO `#__anodos_currency` (`id`, `name`, `alias`, `name_html`, `state`, `ordering`, `description`, `checked_out`, `checked_out_time`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `metadata`) VALUES (0, 'Российский рубль', 'RUB', 'р.', 1, 1, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '');
INSERT INTO `#__anodos_currency` (`id`, `name`, `alias`, `name_html`, `state`, `ordering`, `description`, `checked_out`, `checked_out_time`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `metadata`) VALUES (0, 'Американский доллар', 'USD', '&#36;', 1, 1, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '');
INSERT INTO `#__anodos_currency` (`id`, `name`, `alias`, `name_html`, `state`, `ordering`, `description`, `checked_out`, `checked_out_time`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`, `metadata`) VALUES (0, 'Евро', 'EUR', '&#8364;', 1, 1, '', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '');

CREATE  TABLE IF NOT EXISTS `#__anodos_price` (
  `stock_id` BIGINT UNSIGNED NOT NULL COMMENT 'Идентификатор склада.' ,
  `product_id` BIGINT UNSIGNED NOT NULL COMMENT 'Идентификатор продукта.' ,
  `created` DATETIME NOT NULL ,
  `version` INT UNSIGNED NOT NULL DEFAULT '0' ,
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `price` DECIMAL(13,2) NOT NULL COMMENT 'Цена.' ,
  `currency_id` BIGINT UNSIGNED NOT NULL ,
  `price_type_id` BIGINT UNSIGNED NOT NULL ,
  `discount` DECIMAL(5,2) NOT NULL DEFAULT '0.0' ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` INT UNSIGNED NOT NULL DEFAULT '0' ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  PRIMARY KEY (`product_id`, `created`, `version`, `stock_id`) ,
  INDEX `anodos_price_product_idx` (`product_id` ASC) ,
  INDEX `anodos_price_price_type_idx` (`price_type_id` ASC) ,
  INDEX `anodos_price_currency_idx` (`currency_id` ASC) ,
  INDEX `anodos_price_stock_idx` (`stock_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = 'Цены';

CREATE  TABLE IF NOT EXISTS `#__anodos_currency_rate` (
  `currency_id` BIGINT UNSIGNED NOT NULL ,
  `date` DATE NOT NULL ,
  `quantity` BIGINT NOT NULL DEFAULT '1' ,
  `rate` DECIMAL(12,4) NOT NULL ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` BIGINT NOT NULL DEFAULT '0' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` BIGINT NOT NULL DEFAULT '0' ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  PRIMARY KEY (`date`, `currency_id`) ,
  INDEX `anodos_currency_rate_currency_idx` (`currency_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = 'Курсы валют';

INSERT INTO `#__anodos_currency_rate` (`currency_id`, `date`, `quantity`, `rate`, `state`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`) VALUES (1, '0000-00-00', 1, 1, 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');


CREATE  TABLE IF NOT EXISTS `#__anodos_stock` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL ,
  `alias` VARCHAR(255) NOT NULL ,
  `delivery_time_min` DATETIME NOT NULL DEFAULT '0000-00-04 00:00:00' ,
  `delivery_time_max` DATETIME NOT NULL DEFAULT '0000-00-10 00:00:00' ,
  `partner_id` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `category_id` BIGINT NOT NULL DEFAULT '0' ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `ordering` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `description` TEXT NOT NULL DEFAULT '' ,
  `checked_out` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '2100-01-01 00:00:00' ,
  `metadata` TEXT NOT NULL DEFAULT '' ,
  `hits` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `anodos_stock_partner_idx` (`partner_id` ASC) ,
  INDEX `anodos_stock_category_idx` (`category_id` ASC) ,
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = 'Склады';

INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`, `version`) VALUES
(0, 0, 1, 1, 2, 1, 'uncategorised', 'com_anodos.stock', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{"target":"","image":""}', '', '', '{"page_title":"","author":"","robots":""}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1);

CREATE  TABLE IF NOT EXISTS `#__anodos_product_quantity` (
  `product_id` BIGINT NOT NULL ,
  `stock_id` BIGINT NOT NULL ,
  `created` DATETIME NOT NULL ,
  `version` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `quantity` DECIMAL(13,2) NOT NULL ,
  `state` TINYINT(3) NOT NULL DEFAULT '1' ,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `modified_by` BIGINT UNSIGNED NOT NULL ,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' ,
  `publish_down` DATETIME NOT NULL DEFAULT '2100-01-01 00:00:00' ,
  PRIMARY KEY (`product_id`, `stock_id`, `created`, `version`) ,
  INDEX `anodos_product_quantity_product_idx` (`product_id` ASC) ,
  INDEX `anodos_product_quantity_stock_idx` (`stock_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = 'Таблица с информацией о состоянии складов.';
