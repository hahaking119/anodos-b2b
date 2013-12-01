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

	// Добавляет синоним категории в базу
	public function addSynonym($synonym, $partnerId, $createdBy = 0) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$synonym = $db->quote($synonym);
		$partnerId = $db->quote($partnerId);
		$createdBy = $db->quote($createdBy);

		// TODO перед добавлением провести проверку на уникальность

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_category_synonym (
				name,
				partner_id,
				created,
				created_by)
			VALUES (
				{$synonym},
				{$partnerId},
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

		// Инициализируем переменные
//		$db = JFactory::getDBO();

		// TODO перед добавлением провести проверку на уникальность

		// Выполняем запрос вставки
//		$query = "
//			INSERT INTO #__categories (
//				asset_id,
//				parent_id,
//				lft,
//				rgt,
//				level,
//				path,
//				extension,
//				title,
//				alias,
//				note,
//				description,
//				published,
//				checked_out,
//				checked_out_time,
//				access,
//				params,
//				metadesc,
//				metakey,
//				metadata,
//				created_user_id,
//				created_time,
//				language,
//				version
//			)
//			VALUES (
//				'{$category->asset_id}',
//				'{$category->parent_id}',
//				'{$category->lft}',
//				'{$category->rgt}',
//				'{$category->level}',
//				'{$category->path}',
//				'{$category->extension}',
//				'{$category->title}',
//				'{$category->alias}',
//				'{$category->note}',
//				'{$category->description}',
//				1,
//				0,
//				'1970-01-01 00:00:00',
//				1,
//				'{$category->params}',
//				'{$category->metadesc}',
//				'{$category->metakey}',
//				'{$category->metadata}',
//				'{$category->created_user_id}',
//				NOW(),
//				'{$category->language}',
//				'{$category->version}'
//			);";
//		$db->setQuery($query);
//		$db->query();

		return true;
	}

//	public function getNextLFT($parent) {

		// Подключаемся к базе
//		$db = JFactory::getDBO();

		// Выполняем запрос выборки
//		$query = "
//			SELECT MAX(lft)
//			FROM #__categories
//			WHERE '{$parent->id}' = parent_id
//			OR '{$parent->id}' = id;";
//		$db->setQuery($query);

		// Возвращаем результат
//		return $db->loadResult() + 1;
//	}

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
