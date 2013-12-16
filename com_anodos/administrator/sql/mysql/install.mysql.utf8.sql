DROP TABLE IF EXISTS `#__anodos_order_line`;
DROP TABLE IF EXISTS `#__anodos_order`;
DROP TABLE IF EXISTS `#__anodos_order_stage`;
DROP TABLE IF EXISTS `#__anodos_currency_rate`;
DROP TABLE IF EXISTS `#__anodos_price`;
DROP TABLE IF EXISTS `#__anodos_currency`;
DROP TABLE IF EXISTS `#__anodos_price_type`;
DROP TABLE IF EXISTS `#__anodos_product_quantity`;
DROP TABLE IF EXISTS `#__anodos_stock`;
DROP TABLE IF EXISTS `#__anodos_vendor_synonym`;
DROP TABLE IF EXISTS `#__anodos_category_synonym`;
DROP TABLE IF EXISTS `#__anodos_updater`;
DROP TABLE IF EXISTS `#__anodos_product`;
DROP TABLE IF EXISTS `#__anodos_measure_unit`;
DROP TABLE IF EXISTS `#__anodos_product_vat`;
DROP TABLE IF EXISTS `#__anodos_partner`;

CREATE TABLE IF NOT EXISTS `#__anodos_partner` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `category_id` INT(11) NULL,
  `vendor` TINYINT(1) NOT NULL DEFAULT '0',
  `distributor` TINYINT(1) NOT NULL DEFAULT '0',
  `client` TINYINT(1) NOT NULL DEFAULT '0',
  `competitor` TINYINT(1) NOT NULL DEFAULT '0',
  `state` TINYINT(3) NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` BIGINT NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` BIGINT NOT NULL DEFAULT '0',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  INDEX `category_idx` (`category_id` ASC),
  INDEX `vendor_idx` (`vendor` ASC),
  INDEX `distributor_idx` (`distributor` ASC),
  INDEX `client_idx` (`client` ASC),
  INDEX `competitor_idx` (`competitor` ASC),
  INDEX `state_idx` (`state` ASC),
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC),
  CONSTRAINT `fk_partner_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `#__categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_product_vat` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `vat` DECIMAL(5,2) NOT NULL DEFAULT '18.00',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `vat_UNIQUE` (`vat` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `#__anodos_product_vat` (`id`, `vat`) VALUES (1, 18.00);
INSERT INTO `#__anodos_product_vat` (`id`, `vat`) VALUES (2, 0.00);

CREATE TABLE IF NOT EXISTS `#__anodos_measure_unit` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(8) NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `#__anodos_measure_unit` (`id`, `name`, `full_name`) VALUES (1, 'шт.', 'Штуки');
INSERT INTO `#__anodos_measure_unit` (`id`, `name`, `full_name`) VALUES (2, 'м.', 'Метры');
INSERT INTO `#__anodos_measure_unit` (`id`, `name`, `full_name`) VALUES (3, 'уп.', 'Упаковки');


CREATE TABLE IF NOT EXISTS `#__anodos_product` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `article` VARCHAR(225) NOT NULL,
  `full_name` TEXT NOT NULL,
  `category_id` INT(11) NOT NULL,
  `vendor_id` BIGINT UNSIGNED NOT NULL,
  `vat_id` INT UNSIGNED NOT NULL DEFAULT '1',
  `measure_unit_id` INT UNSIGNED NOT NULL DEFAULT '1',
  `state` TINYINT(3) NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `duble_of` BIGINT UNSIGNED NULL,
  PRIMARY KEY (`id`),
  INDEX `category_idx` (`category_id` ASC),
  INDEX `duble_of_idx` (`duble_of` ASC),
  INDEX `vendor_idx` (`vendor_id` ASC),
  INDEX `vat_idx` (`vat_id` ASC),
  INDEX `state_idx` (`state` ASC),
  INDEX `measure_unit_idx` (`measure_unit_id` ASC),
  CONSTRAINT `fk_product_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `#__categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_product_id`
    FOREIGN KEY (`duble_of`)
    REFERENCES `#__anodos_product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_vendor_id`
    FOREIGN KEY (`vendor_id`)
    REFERENCES `#__anodos_partner` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_product_vat_id`
    FOREIGN KEY (`vat_id`)
    REFERENCES `#__anodos_product_vat` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_anodos_product_measure_unit_id`
    FOREIGN KEY (`measure_unit_id`)
    REFERENCES `#__anodos_measure_unit` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_updater` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `partner_id` BIGINT UNSIGNED NULL,
  `category_id` INT(11) NULL,
  `name` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `state` TINYINT(3) NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `client` VARCHAR(255) NOT NULL DEFAULT '',
  `login` VARCHAR(255) NOT NULL DEFAULT '',
  `pass` VARCHAR(255) NOT NULL DEFAULT '',
  `key` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  INDEX `partner_idx` (`partner_id` ASC),
  INDEX `category_idx` (`category_id` ASC),
  INDEX `state_idx` (`state` ASC),
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC),
  CONSTRAINT `fk_updater_partner_id`
    FOREIGN KEY (`partner_id`)
    REFERENCES `#__anodos_partner` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_update_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `#__categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_vendor_synonym` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `partner_id` BIGINT UNSIGNED NOT NULL,
  `vendor_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL,
  `state` TINYINT(3) NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `vendor_idx` (`vendor_id` ASC),
  INDEX `partner_idx` (`partner_id` ASC),
  INDEX `state_idx` (`state` ASC),
  CONSTRAINT `fk_vendor_synonym_vendor_id`
    FOREIGN KEY (`vendor_id`)
    REFERENCES `#__anodos_partner` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_vendor_synonym_partner_id`
    FOREIGN KEY (`partner_id`)
    REFERENCES `#__anodos_partner` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_category_synonym` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `partner_id` BIGINT UNSIGNED NOT NULL,
  `category_id` INT(11) NULL,
  `name` VARCHAR(512) NOT NULL,
  `state` TINYINT(3) NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '000-00-00 00:00:00',
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `partner_idx` (`partner_id` ASC),
  INDEX `category_idx` (`category_id` ASC),
  INDEX `state_idx` (`state` ASC),
  CONSTRAINT `fk_category_synonim_partner_id`
    FOREIGN KEY (`partner_id`)
    REFERENCES `#__anodos_partner` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_category_synonim_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `#__categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_stock` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `delivery_time_min` INT UNSIGNED NOT NULL DEFAULT '5',
  `delivery_time_max` INT UNSIGNED NOT NULL DEFAULT '10',
  `partner_id` BIGINT UNSIGNED NOT NULL,
  `category_id` INT(11) NULL,
  `state` TINYINT(3) NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  INDEX `partner_idx` (`partner_id` ASC),
  INDEX `category_idx` (`category_id` ASC),
  INDEX `state_idx` (`state` ASC),
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC),
  CONSTRAINT `fk_stock_partner_id`
    FOREIGN KEY (`partner_id`)
    REFERENCES `#__anodos_partner` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_stock_category_id`
    FOREIGN KEY (`category_id`)
    REFERENCES `#__categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_product_quantity` (
  `product_id` BIGINT UNSIGNED NOT NULL,
  `stock_id` BIGINT UNSIGNED NOT NULL,
  `quantity` DECIMAL(13,2) NOT NULL,
  `measure_unit_id` INT UNSIGNED NOT NULL DEFAULT '1',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`product_id`, `stock_id`),
  INDEX `product_idx` (`product_id` ASC),
  INDEX `stock_idx` (`stock_id` ASC),
  INDEX `measure_unit_idx` (`measure_unit_id` ASC),
  CONSTRAINT `fk_product_quantity_product_id`
    FOREIGN KEY (`product_id`)
    REFERENCES `#__anodos_product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_quantity_stock_id`
    FOREIGN KEY (`stock_id`)
    REFERENCES `#__anodos_stock` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_product_quantity_measure_unit_id`
    FOREIGN KEY (`measure_unit_id`)
    REFERENCES `#__anodos_measure_unit` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_price_type` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  `state` TINYINT(3) NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` BIGINT UNSIGNED NOT NULL DEFAULT '0',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC),
  INDEX `state_idx` (`state` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_currency` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alias` CHAR(3) NOT NULL,
  `name_html` VARCHAR(255) NOT NULL,
  `state` TINYINT(3) NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` BIGINT NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` BIGINT NOT NULL DEFAULT '0',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC),
  INDEX `state_idx` (`state` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `#__anodos_currency` (`id`, `name`, `alias`, `name_html`, `state`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`) VALUES (0, 'Российский рубль', 'RUB', 'р.', 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `#__anodos_currency` (`id`, `name`, `alias`, `name_html`, `state`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`) VALUES (0, 'Американский доллар', 'USD', '&#36;', 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `#__anodos_currency` (`id`, `name`, `alias`, `name_html`, `state`, `created`, `created_by`, `modified`, `modified_by`, `publish_up`, `publish_down`) VALUES (0, 'Евро', 'EUR', '&#8364;', 1, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

CREATE TABLE IF NOT EXISTS `#__anodos_price` (
  `stock_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NOT NULL,
  `price` DECIMAL(13,2) NOT NULL,
  `currency_id` BIGINT UNSIGNED NOT NULL,
  `price_type_id` BIGINT UNSIGNED NOT NULL,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`stock_id`, `product_id`),
  INDEX `product_idx` (`product_id` ASC),
  INDEX `price_type_idx` (`price_type_id` ASC),
  INDEX `currency_idx` (`currency_id` ASC),
  INDEX `stock_idx` (`stock_id` ASC),
  CONSTRAINT `fk_price_product_id`
    FOREIGN KEY (`product_id`)
    REFERENCES `#__anodos_product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_price_price_type_id`
    FOREIGN KEY (`price_type_id`)
    REFERENCES `#__anodos_price_type` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_price_currency_id`
    FOREIGN KEY (`currency_id`)
    REFERENCES `#__anodos_currency` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_price_stock_id`
    FOREIGN KEY (`stock_id`)
    REFERENCES `#__anodos_stock` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_currency_rate` (
  `currency_id` BIGINT UNSIGNED NOT NULL,
  `date` DATE NOT NULL,
  `quantity` BIGINT NOT NULL,
  `rate` DECIMAL(12,4) NOT NULL,
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`currency_id`),
  INDEX `currency_idx` (`currency_id` ASC),
  INDEX `date_idx` (`date` ASC),
  CONSTRAINT `fk_currency_rate_currency_id`
    FOREIGN KEY (`currency_id`)
    REFERENCES `#__anodos_currency` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `#__anodos_currency_rate` (
	`currency_id`,
	`date`,
	`quantity`,
	`rate`,
	`publish_up`,
	`publish_down`)
VALUES (
	1,
	'0000-00-00',
	1,
	1,
	'2100-01-01 00:00:00',
	'2100-01-01 00:00:00');

CREATE TABLE IF NOT EXISTS `#__anodos_order_stage` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `alias` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `alias_UNIQUE` (`alias` ASC),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

INSERT INTO `#__anodos_order_stage` (`id`, `name`, `alias`) VALUES (1, 'Не согласован', 'Не согласован');
INSERT INTO `#__anodos_order_stage` (`id`, `name`, `alias`) VALUES (2, 'Согласован', 'Согласован');
INSERT INTO `#__anodos_order_stage` (`id`, `name`, `alias`) VALUES (3, 'Ожидание предоплаты', 'Ожидание предоплаты');
INSERT INTO `#__anodos_order_stage` (`id`, `name`, `alias`) VALUES (4, 'В работе', 'В работе');
INSERT INTO `#__anodos_order_stage` (`id`, `name`, `alias`) VALUES (5, 'Ожидание постоплаты', 'Ожидание постоплаты');
INSERT INTO `#__anodos_order_stage` (`id`, `name`, `alias`) VALUES (6, 'Закрыт', 'Закрыт');

CREATE TABLE IF NOT EXISTS `#__anodos_order` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `partner_id` BIGINT UNSIGNED NULL,
  `created` DATETIME NOT NULL,
  `created_by` BIGINT NOT NULL DEFAULT '0',
  `name` VARCHAR(255) NOT NULL DEFAULT 'Новый заказ',
  `state` TINYINT(3) NOT NULL DEFAULT '1' COMMENT 'Статус',
  `stage_id` INT UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Статус',
  `description` TEXT NULL,
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` BIGINT NOT NULL DEFAULT '0',
  `manager_id` BIGINT UNSIGNED NULL,
  `delivery_date` DATE NULL,
  `view_open_key` VARCHAR(32) NOT NULL,
  `edit_open_key` VARCHAR(32) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `stage_idx` (`stage_id` ASC),
  INDEX `partner_idx` (`partner_id` ASC),
  INDEX `created_by_idx` (`created_by` ASC),
  INDEX `state_idx` (`state` ASC),
  INDEX `modified_by_idx` (`modified_by` ASC),
  INDEX `manager_idx` (`manager_id` ASC),
  CONSTRAINT `fk_order_order_stage_id`
    FOREIGN KEY (`stage_id`)
    REFERENCES `#__anodos_order_stage` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_partner_id`
    FOREIGN KEY (`partner_id`)
    REFERENCES `#__anodos_partner` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS `#__anodos_order_line` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` BIGINT UNSIGNED NOT NULL,
  `product_id` BIGINT UNSIGNED NULL,
  `name` VARCHAR(255) NOT NULL DEFAULT '',
  `quantity` DECIMAL(13,2) NOT NULL DEFAULT '1.00',
  `measure_unit_id` INT UNSIGNED NOT NULL DEFAULT '1',
  `price_in` DECIMAL NULL,
  `currency_in_id` BIGINT UNSIGNED NULL,
  `price_out` DECIMAL NULL,
  `currency_out_id` BIGINT UNSIGNED NULL,
  `created` DATETIME NOT NULL,
  `created_by` BIGINT NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL,
  `modified_by` BIGINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  INDEX `order_idx` (`order_id` ASC),
  INDEX `product_idx` (`product_id` ASC),
  INDEX `currency_in_idx` (`currency_in_id` ASC),
  INDEX `currency_out_idx` (`currency_out_id` ASC),
  INDEX `measure_unit_idx` (`measure_unit_id` ASC),
  INDEX `created_idx` (`created` ASC),
  INDEX `modified_idx` (`modified` ASC),
  CONSTRAINT `fk_order_line_order_id`
    FOREIGN KEY (`order_id`)
    REFERENCES `#__anodos_order` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_line_product_id`
    FOREIGN KEY (`product_id`)
    REFERENCES `#__anodos_product` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_line_currency_in_id`
    FOREIGN KEY (`currency_in_id`)
    REFERENCES `#__anodos_currency` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_line_currency_out_id`
    FOREIGN KEY (`currency_out_id`)
    REFERENCES `#__anodos_currency` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_line_measure_unit_id`
    FOREIGN KEY (`measure_unit_id`)
    REFERENCES `#__anodos_measure_unit` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

ALTER TABLE `#__anodos_category_synonym` ADD COLUMN `original_id` VARCHAR(45) NULL AFTER `category_id`;
ALTER TABLE `#__anodos_vendor_synonym` ADD COLUMN `original_id` VARCHAR(45) NULL AFTER `vendor_id`;
