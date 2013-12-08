<?php

defined('_JEXEC') or die;

class Category {

	// Определяем id категории
	public function getSynonym($synonym, $partnerId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$synonym = $db->quote($synonym);
		$partnerId = $db->quote($partnerId);

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_category_synonym
			WHERE {$synonym} = name
			AND {$partnerId} = partner_id;";
		$db->setQuery($query);

		// Возвращаем результат
		return $db->loadObject();
	}

	// Определяем id категории
	public function getSynonymFromOriginalId($originalId, $partnerId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$originalId = $db->quote($originalId);
		$partnerId = $db->quote($partnerId);

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_category_synonym
			WHERE {$originalId} = original_id
			AND {$partnerId} = partner_id;";
		$db->setQuery($query);

		// Возвращаем результат
		return $db->loadObject();
	}

	// Добавляет синоним категории в базу
	public function addSynonym($synonym, $partnerId, $originalId = 'NULL', $createdBy = 0) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$synonym = $db->quote($synonym);
		$partnerId = $db->quote($partnerId);
		$createdBy = $db->quote($createdBy);
		if ('NULL' !== $originalId) {
			$originalId = $db->quote($originalId);
		}

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_category_synonym (
				name,
				partner_id,
				original_id,
				created,
				created_by)
			VALUES (
				{$synonym},
				{$partnerId},
				{$originalId},
				NOW(),
				{$createdBy});";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_category_synonym
			WHERE {$synonym} = name
			AND {$partnerId} = partner_id;";
		$db->setQuery($query);

		// Возвращаем результат
		return $db->loadObject();
	}

	// Добавляет синоним категории в базу
	public function setOriginalIdToSynonym($synonymId, $originalId = '') {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$synonymId = $db->quote($synonymId);
		if ('' !== $originalId) {
			$originalId = $db->quote($originalId);
		}

		// Выполняем запрос вставки
		$query = "
			UPDATE #__anodos_category_synonym
			SET original_id = {$originalId}
			WHERE id = {$synonymId};";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_category_synonym
			WHERE {$synonymId} = id;";
		$db->setQuery($query);

		// Возвращаем результат
		return $db->loadObject();
	}

	// Возвращает объект категории по id
	public function getCategory($id) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$id = $db->quote($id);

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__categories
			WHERE {$id} = id;";
		$db->setQuery($query);

		// Возвращаем результат
		return $db->loadObject();
	}

	// TODO
	public function addCategory($category) {
		return false;
	}

	// Привязывает загрузчика к контрагенту
	public function linkSynonymToCategory($synonymId, $categoryId) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$synonymId = $db->quote($synonymId);
		$categoryId = $db->quote($categoryId);

		// Выполняем запрос
		$query = "
			UPDATE #__anodos_category_synonym
			SET category_id = {$categoryId}
			WHERE id = {$synonymId};";
		$db->setQuery($query);
		$db->query();

		// Возвразщаем результат
		return true;
	}
}
