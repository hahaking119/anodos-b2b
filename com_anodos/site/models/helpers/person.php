<?php

defined('_JEXEC') or die;

class Person {

	// Возвращает информацию о партнеру (по alias из базы)
	public function getPersonFromUser($userId) {

		// инициализируем переменные
		$db = JFactory::getDBO();
		$userId = $db->quote($userId);

		// Выполняем запрос
		$query = "
			SELECT *
			FROM #__anodos_person
			WHERE user_id = {$userId};";
		$db->setQuery($query);
		$result = $db->loadObject();

		// Возвращаем результат
		return $result;
	}
}
