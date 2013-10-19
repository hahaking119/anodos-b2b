<?php

defined('_JEXEC') or die;

class Updater {

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
			WHERE id = '{$partnerId}';";
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
