<?php

defined('_JEXEC') or die;

class Vendor {

	// TODO TEST Возвращает информацию о производителе (по alias из базы)
	public function getVendorFromAlias($alias) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM #__anodos_partner
			WHERE alias = '{$alias}';";
		$db->setQuery($query);
		$vendor = $db->loadObject();

		// Возвращаем результат
		return $vendor;
	}

	// TODO TEST Добавляет контрагента в базу, возвращает объект контрагента
	public function addVendor($vendor) {

		// Инициализируем переменные и готовим запрос
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$vendor->name = $db->quote($vendor->name);
		$vendor->alias = $db->quote($vendor->alias);

		// Выполняем запрос добавления
		$query = "
			INSERT INTO #__anodos_partner (
				name,
				alias,
				vendor,
				created,
				created_by)
			VALUES (
				{$vendor->name},
				{$vendor->alias},
				'1',
				NOW(),
				'{$vendor->created_by}');";
		$db->setQuery($query);
		$db->query();
	}

	// Определяем id производителя
	public function getSynonym($synonym, $partnerId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM #__anodos_vendor_synonym
			WHERE '{$synonym}' = name
			AND '{$partnerId}' = partner_id;";
		$db->setQuery($query);

		// Возвращаем результат
		return $db->loadObject();
	}

	// Добавляет синоним производителя
	public function addSynonym($synonym, $partnerId, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// TODO Провести проверку на уникальность перед добавлением

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_vendor_synonym (
				name,
				partner_id,
				created,
				created_by)
			VALUES (
				'{$synonym}',
				'{$partnerId}',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_vendor_synonym
			WHERE '{$synonym}' = name
			AND '{$partnerId}' = partner_id;";
		$db->setQuery($query);

		// Возвращаем результат
		return $db->loadObject();
	}

	// Привязывает загрузчика к контрагенту
	public function linkSynonymToVendor($synonymId, $vendorId) {

		// Подколючаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE #__anodos_vendor_synonym
			SET vendor_id = {$vendorId}
			WHERE id = '{$synonymId}';";
		$db->setQuery($query);
		$db->query();

		// Возвразщаем результат
		return true;
	}
}
