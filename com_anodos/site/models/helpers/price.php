<?php
/**
 * @version     0.0.1
 * @package     com_anodosupdater
 * @copyright   Copyright (C) 2012. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Andrey J Bezpalov <abezpalov@ya.ru> - http://anodos.ru
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Price helper.
 */
class Price {

	// Возвращает последнюю цену на продукт указанного поставщика
	public function getPrice($contractorId, $productId, $state = 1) {
		//TODO
	}

	// Заносит цену в базу
	public function addPrice($contractorId, $productId, $price, $currencyId, $priceTypeId, $addDate = 3, $createdBy = 0) {

		// Определяем переменные
		$version = 0;

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// TODO проверем наличие цены c теми же ключами
		$query = "
			SELECT MAX(version)
			FROM `#__anodos_price`
			WHERE `#__anodos_price`.`contractor_id` = '{$contractorId}'
			AND   `#__anodos_price`.`product_id`    = '{$productId}'
			AND   `#__anodos_price`.`created`       = NOW();";
		$db->setQuery($query);
		$version =  $db->loadResult();

		//Получаем последнюю версию
		if (NULL != $version) {
			$version++;
		} else {
			$version = 0;
		}

		// Заносим информацию в базу
		$query="
			INSERT INTO `#__anodos_price` (
				`contractor_id`,
				`product_id`,
				`created`,
				`version`,
				`price`,
				`currency_id`,
				`price_type_id`,
				`created_by`,
				`publish_up`,
				`publish_down`)
			VALUES (
				'{$contractorId}',
				'{$productId}',
				NOW(),
				'{$version}',
				'{$price}',
				'{$currencyId}',
				'{$priceTypeId}',
				'{$createdBy}',
				NOW(),
				ADDDATE(NOW(), {$addDate})
			);
		";
		$db->setQuery($query);
		$db->query();
		return true;
	}

	// Возвращает id типа цены по псевдониму
	public function getPriceTypeFromAlias($alias) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT * 
			FROM `#__anodos_price_type`
			WHERE `alias` = '{$alias}'
		;";
		$db->setQuery($query);
		$priceType = $db->loadObject();

		// Возвращаем результат
		return $priceType;
	}

	// Добавляет тип цен
	public function addPriceType($name, $alias, $type = 0, $fixed = 0, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			INSERT INTO #__anodos_price_type (
				`name`,
				`alias`,
				`type`,
				`fixed`,
				`state`,
				`created`,
				`created_by`
				)
			VALUES (
				'{$name}',
				'{$alias}',
				'{$type}',
				'{$fixed}',
				'1',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос
		$query = "
			SELECT * 
			FROM `#__anodos_price_type`
			WHERE `alias` = '{$alias}'
		;";
		$db->setQuery($query);
		$priceType = $db->loadObject();

		// Возвращаем результат
		return $priceType;
	}

	// Снятие с публикации устаревшей информации о ценах
	public function clearSQL($contractorId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE `#__anodos_price`
			SET state = '0'
			WHERE `contractor_id` = '{$contractorId}';";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}
}
