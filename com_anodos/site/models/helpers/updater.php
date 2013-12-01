<?php

defined('_JEXEC') or die;

class Updater {

	public function getStartTime() {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "SELECT NOW();";
		$db->setQuery($query);
		$result = $db->loadResult();

		// Возвращаем результат
		return $result;
	}

	public function getUpdater($id) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM #__anodos_updater
			WHERE id = '{$id}';";
		$db->setQuery($query);
		$updater = $db->loadObject();

		// Возвращаем результат
		return $updater;
	}

	// Привязывает загрузчика к контрагенту
	public function linkToPartner ($updaterId, $partnerId) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE #__anodos_updater
			SET partner_id = {$partnerId}
			WHERE id = '{$updaterId}';";
		$db->setQuery($query);
		$db->query();

		// Возвразщаем результат
		return true;
	}

	// Устанавливает время обновления
	public function setUpdated ($id) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE #__anodos_updater
			SET updated = NOW()
			WHERE id = '{$id}';";
		$db->setQuery($query);
		$db->query();

		// Возвразщаем результат
		return true;
	}
}
