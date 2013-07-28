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
 * Vendor helper.
 */
class Vendor {

	// TODO TEST Возвращает информацию о производителе (по alias из базы)
	public function getVendorFromAlias($alias) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM `#__anodos_vendor`
			WHERE `alias` = '{$alias}';";
		$db->setQuery($query);
		$vendor = $db->loadObject();

		// Возвращаем результат
		return $vendor;
	}

	// TODO TEST Добавляет контрагента в базу, возвращает объект контрагента
	public function addVendor($name, $alias, $createdBy = 0) {

		// Инициализируем переменные и готовим запрос
		$db = JFactory::getDBO();

		// Выполняем запрос добавления
		$query = "
			INSERT INTO #__anodos_vendor (
				`name`,
				`alias`,
				`created`,
				`created_by`)
			VALUES (
				'{$name}',
				'{$alias}',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM `#__anodos_vendor`
			WHERE `alias` = '{$alias}';";
		$db->setQuery($query);
		$vendor = $db->loadObject();

		// Возвращаем результат
		return $vendor;
	}

	// Определяем id производителя
	public function getSynonym($synonym) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM `#__anodos_vendor_synonym`
			WHERE '{$synonym}' <=> `name`;";
		$db->setQuery($query);
		$synonym = $db->loadObject();

		// Возвращаем результат
		return $synonym;
	}

	// Добавляет синоним производителя
	public function addSynonym($synonym) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// TODO Провести проверку на уникальность перед добавлением

		// Выполняем запрос вставки
		$query = "
			INSERT INTO `#__anodos_vendor_synonym` (
				`name`,
				`vendor_id`,
				`created`)
			VALUES (
				'{$synonym}',
				'0',
				NOW());";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM `#__anodos_vendor_synonym`
			WHERE '{$synonym}' <=> `name`;";
		$db->setQuery($query);
		$synonym = $db->loadObject();

		// Возвращаем результат
		return $synonym;
	}
}
