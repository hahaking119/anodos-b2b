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
 * Currency helper.
 */
class Currency {

	public function getCurrencyFromAlias($alias) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_currency
			WHERE '{$alias}' <=> alias;";
		$db->setQuery($query);
		$currency = $db->loadObject();

		// Возвращаем результат
		return $currency;
	}

	// Добавляет валюту, возвращает ее объект
	public function addCurrency($name, $alias, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос вставки
		$query = "
			INSERT
			INTO #__anodos_currency (
				`id`,
				`name`,
				`alias`,
				`name_html`,
				`state`,
				`description`,
				`created`,
				`created_by`)
			VALUES (
				0,
				'{$name}',
				'{$alias}',
				'{$alias}',
				'1',
				'',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_currency
			WHERE '{$alias}' <=> alias;";
		$db->setQuery($query);
		$currency = $db->loadObject();

		// Возвращаем результат
		return $currency;
	}

	// Возвращает курс валюты по ее id на указанную дату
	public function getRate($id, $date) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Проверяем, есть ли курс на текущую дату
		$query = "
			SELECT rate
			FROM #__anodos_currency_rate
			WHERE
				#__anodos_currency_rate.currency_id = '{$id}' AND
				#__anodos_currency_rate.date = '{$date}';";
		$db->setQuery($query);
		$rate = $db->loadResult();

		// Возвращаем результат
		return $rate;
	}

	// Добавляет курс валюты по ее коду на указанную дату 
	public function addRate($id, $date, $rate, $quantity, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Заносим информацию в базу
		$query = "
			INSERT INTO #__anodos_currency_rate (
				`currency_id`,
				`date`,
				`rate`,
				`quantity`,
				`state`,
				`created`,
				`created_by`)
			VALUES (
				'{$id}',
				'{$date}',
				'{$rate}',
				'{$quantity}',
				'1',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}

	// Снятие с публикации устаревших курсов валют
	public function setZeroState($id) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE `#__anodos_currency_rate`
			SET state = '0'
			WHERE `currency_id` = '{$id}';";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}
}
