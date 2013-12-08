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

	// Удаляет категорию
	public function removeCategory($categoryId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$categoryId = $db->quote($categoryId);

		// Выполняем запрос
		$query = "DELETE FROM #__categories WHERE id = {$categoryId};";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}

	// Переименовывает категорию
	public function renameCategory($categoryId, $categoryName) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$categoryId = $db->quote($categoryId);
		$categoryName = $db->quote($categoryName);

		// Выполняем запрос
		$query = "
			UPDATE #__categories
			SET title = {$categoryName}
			WHERE id = {$categoryId};";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__categories
			WHERE {$categoryId} = id;";
		$db->setQuery($query);
		$category = $db->loadObject();

		// Возвращаем результат
		if(!isset($category->id)) {
			return false;
		} else {
			return $category;
		}
	}

	public function getTreeFromCategory($categoryId) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$categoryId = $db->quote($categoryId);

		// Получаем указанную категорию
		$query = "
			SELECT id, path
			FROM #__categories
			WHERE id = {$categoryId} AND extension = 'com_anodos'
			ORDER BY lft;";
		$db->setQuery($query);
		$category = $db->loadObject();

		if(!isset($category->id)) {
			return false;
		}

		// Получаем массив категорий (указанная и все потомки)
		$query = "
			SELECT id, title
			FROM #__categories
			WHERE LOCATE('{$category->path}', path) = 1 AND extension = 'com_anodos'
			ORDER BY lft;";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Возвращаем результат
		return $result;
	}

	// Привязывает синоним к категории
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

	// Отвязывает синоним от категории
	public function unlinkSynonymOfCategory($categoryId) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$categoryId = $db->quote($categoryId);

		// Выполняем запрос
		$query = "
			UPDATE #__anodos_category_synonym SET
			category_id = NULL
			WHERE category_id = {$categoryId};
		";
		$db->setQuery($query);
		$db->query();

		// Возвразщаем результат
		return true;
	}
}
