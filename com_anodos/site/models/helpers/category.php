<?php

defined('_JEXEC') or die;

class Category {

	// Определяем id категории
	public function getSynonym($synonym, $partnerId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM `#__anodos_category_synonym`
			WHERE '{$synonym}' <=> `name`
			AND '{$partnerId}' = `partner_id`;";
		$db->setQuery($query);
		$synonym = $db->loadObject();

		// Возвращаем результат
		return $synonym;
	}

	// Добавляет синоним категории в базу
	public function addSynonym($synonym, $partnerId, $createdBy = 0) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// TODO перед добавлением провести проверку на уникальность

		// Выполняем запрос вставки
		$query = "
			INSERT INTO `#__anodos_category_synonym` (
				`name`,
				`category_id`,
				`partner_id`,
				`created`,
				`created_by`)
			VALUES (
				'{$synonym}',
				'0',
				'{$partnerId}',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM `#__anodos_category_synonym`
			WHERE '{$synonym}' <=> `name`
			AND '{$partnerId}' = `partner_id`;";
		$db->setQuery($query);
		$synonym = $db->loadObject();

		// Возвращаем результат
		return $synonym;
	}
}