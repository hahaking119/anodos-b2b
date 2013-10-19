<?php

defined('_JEXEC') or die;

class Partner {

	// Возвращает информацию о партнеру (по alias из базы)
	public function getPartnerFromAlias($alias) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM #__anodos_partner
			WHERE alias = '{$alias}';";
		$db->setQuery($query);
		$partner = $db->loadObject();

		// Возвращаем результат
		return $partner;
	}

	// Добавляет партнера в базу, возвращает его объект
	public function addPartner($name, $alias, $createdBy = 0) {

		// Инициализируем переменные и готовим запрос
		$db = JFactory::getDBO();

		// Выполняем запрос добавления
		$query = "
			INSERT INTO #__anodos_partner (
				name,
				alias,
				created,
				created_by)
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
			FROM #__anodos_partner
			WHERE alias = '{$alias}';";
		$db->setQuery($query);
		$partner = $db->loadObject();

		// Возвращаем результат
		return $partner;
	}
}
