DROP TABLE IF EXISTS `#__anodos_contractor` ;
DROP TABLE IF EXISTS `#__anodos_product` ;

CREATE  TABLE IF NOT EXISTS `#__anodos_contractor` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'Идентификатор' ,
  `name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Имя' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Псевдоним (служебное поле)' ,
  `category_id` BIGINT NOT NULL DEFAULT '0' COMMENT 'Категория' ,
  `vendor` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Производитель?' ,
  `distributor` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Поставщик?' ,
  `client` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Заказчик?' ,
  `competitor` TINYINT(1) NOT NULL DEFAULT '0' COMMENT 'Конкурент?' ,
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
  INDEX `anodos_contractor_category_idx` (`category_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = 'Контрагент';

INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`, `version`) VALUES
(0, 0, 1, 1, 2, 1, 'uncategorised', 'com_anodos.contractor', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{"target":"","image":""}', '', '', '{"page_title":"","author":"","robots":""}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1);

CREATE  TABLE IF NOT EXISTS `#__anodos_product` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(255) NOT NULL COMMENT 'Наименование продукции.' ,
  `alias` VARCHAR(255) NOT NULL DEFAULT '' ,
  `article` VARCHAR(225) NOT NULL DEFAULT '' COMMENT 'Артикул производителя (p/n).' ,
  `full_name` TEXT NOT NULL DEFAULT '' ,
  `category_id` BIGINT UNSIGNED NOT NULL DEFAULT '0' ,
  `vendor_id` BIGINT UNSIGNED NOT NULL ,
  `vat` DECIMAL(5,2) NOT NULL DEFAULT '18.00' ,
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
  INDEX `anodos_product_vendor_idx` (`vendor_id` ASC) )
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COMMENT = 'Продукт';

INSERT INTO `#__categories` (`id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`, `version`) VALUES
(0, 0, 1, 1, 2, 1, 'uncategorised', 'com_anodos.product', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{"target":"","image":""}', '', '', '{"page_title":"","author":"","robots":""}', 42, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1);
