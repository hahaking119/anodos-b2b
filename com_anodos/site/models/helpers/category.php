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
 * Category helper.
 */
class Category {

	// Определяем id категории
	public function getSynonym($synonym, $contractorId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM `#__anodos_category_synonym`
			WHERE '{$synonym}' <=> `name`
			AND '{$contractorId}' = `contractor_id`;";
		$db->setQuery($query);
		$synonym = $db->loadObject();

		// Возвращаем результат
		return $synonym;
	}

	// Добавляет синоним категории в базу
	public function addSynonym($synonym, $contractorId, $createdBy = 0) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// TODO перед добавлением провести проверку на уникальность

		// Выполняем запрос вставки
		$query = "
			INSERT INTO `#__anodos_category_synonym` (
				`name`,
				`category_id`,
				`contractor_id`,
				`created`,
				`created_by`)
			VALUES (
				'{$synonym}',
				'0',
				'{$contractorId}',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM `#__anodos_category_synonym`
			WHERE '{$synonym}' <=> `name`
			AND '{$contractorId}' = `contractor_id`;";
		$db->setQuery($query);
		$synonym = $db->loadObject();

		// Возвращаем результат
		return $synonym;
	}
}
