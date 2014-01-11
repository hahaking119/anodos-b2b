<?php
defined('_JEXEC') or die;

// public function getPersonFromUser($userId)
// public function createPersonToUser($userId)
// public function getPersonInfo($personId)
// public function createPersonInfo($info)
class Person {

	// Возвращает объект контактного лица
	public function getPersonFromUser($userId) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$userId = $db->quote($userId);

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_person
			WHERE user_id = {$userId};";
		$db->setQuery($query);
		$result = $db->loadObject();

		// Возвращаем результат
		return $result;
	}

	// Создает контактное лицо для указанного пользователя
	public function createPersonToUser($userId) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$userId = $db->quote($userId);
		$user = JFactory::getUser();
		$createdBy = $db->quote($user->id);
		$name = $db->quote($user->name);
		$state = $db->quote(1);

		// Выполняем запрос
		$query = "
			INSERT INTO #__anodos_person (
				name,
				partner_id,
				position,
				state,
				created,
				created_by,
				modified,
				modified_by,
				user_id)
			VALUES (
				$name,
				NULL,
				'',
				$state,
				NOW(),
				$createdBy,
				NOW(),
				$createdBy,
				$userId
			);
		";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_person
			WHERE user_id = {$userId};";
		$db->setQuery($query);
		$result = $db->loadObject();

		// Возвращаем результат
		return $result;
	}

	// Возвращает информацию о контактном лице
	public function getPersonInfo($personId) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$personId = $db->quote($personId);

		// Выполняем запрос выборки
		$query = "
			SELECT
				info.id AS id,
				type.name AS name,
				type.alias AS alias,
				info.content AS content,
				info.can_remove AS can_remove,
				info.ordering AS info_ordering,
				type.ordering AS type_ordering,
				type.icon AS type_icon,
				type.max AS type_max
			FROM #__anodos_person_info AS info
			INNER JOIN #__anodos_person_info_type AS type ON type.id = info.type_id
			WHERE person_id = {$personId} AND info.state = 1 AND type.state = 1;";
		$db->setQuery($query);
		$result = $db->loadObjectList();

		// Возвращаем результат
		return $result;
	}

	// Добавляет информацию о контактном лице в базу
	/*
			$info->alias = name;
			$info->content = $user->name;
			$info->canRemove = 0;
			$info->ordering = 1;
			$info->state = 1;
			Person::createPersonInfo($info);
	*/
	public function createPersonInfo($info) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$createdBy = $db->quote($user->id);

		if (isset($info->personId)) {
			$info->personId = $db->quote($info->personId);
		} else {
			return false;
		}

		if (isset($info->alias)) {
			$info->alias = $db->quote($info->alias);
		} else {
			return false;
		}

		if (isset($info->content)) {
			$info->content = $db->quote($info->content);
		} else {
			$info->content = $db->quote('');
		}

		if (isset($info->canRemove)) {
			$info->canRemove = $db->quote($info->canRemove);
		} else {
			$info->canRemove = $db->quote(1);
		}

		if (isset($info->ordering)) {
			$info->ordering = $db->quote($info->ordering);
		} else {
			$query = "
				SELECT MAX(ordering)
				FROM #__anodos_person_info
				WHERE person_id = {$personId} AND info.state = 1;";
			$db->setQuery($query);
			$info->ordering = $db->loadResult();
			$info->ordering = $db->quote($info->ordering);
		}

		if (isset($info->canRemove)) {
			$info->state = $db->quote($info->state);
		} else {
			$info->state = $db->quote(1);
		}

		// Получаем type_id
		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_person_info_type
			WHERE alias = {$info->alias};";
		$db->setQuery($query);
		$type = $db->loadObject();

		if (isset($type->id)) {
			$info->typeId = $db->quote($type->id);
		} else {
			return false;
		}

		// Выполняем запрос вставки
		$query = "
			INSERT INTO `#__anodos_person_info` (
				person_id,
				type_id,
				content,
				can_remove,
				ordering,
				state,
				created,
				created_by,
				modified,
				modified_by)
			VALUES (
				{$info->personId},
				{$info->typeId},
				{$info->content},
				{$info->canRemove},
				{$info->ordering},
				{$info->state},
				NOW(),
				$createdBy,
				NOW(),
				$createdBy);
		";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}
}
