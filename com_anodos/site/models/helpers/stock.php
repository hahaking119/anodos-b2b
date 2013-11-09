<?php
defined('_JEXEC') or die;

class Stock {

	// Возвращает объект склада (по alias из базы), если его нет - добавляет
	public function getStockFromAlias($alias) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$alias = $db->quote($alias);

		// Выполняем запрос
		$query = "
			SELECT *
			FROM #__anodos_stock
			WHERE alias = {$alias};";
		$db->setQuery($query);
		$stock = $db->loadObject();

		// Возвращаем результат
		return $stock;
	}

	// Создает склад
	public function addStock($name, $alias, $partnerId, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$name = $db->quote($name);
		$alias = $db->quote($alias);
		$partnerId = $db->quote($partnerId);
		$createdBy = $db->quote($createdBy);

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_stock (
				name,
				alias,
				partner_id,
				created,
				created_by)
			VALUES (
				{$name},
				{$alias},
				{$partnerId},
				NOW(),
				{$createdBy});";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_stock
			WHERE alias = '{$alias}';";
		$db->setQuery($query);
		$stock = $db->loadObject();

		// Возвращаем результат
		return $stock;
	}

	public function linkStockToPartner($stockId, $partnerId, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$stockId = $db->quote($stockId);
		$partnerId = $db->quote($partnerId);

		// Выполняем запрос
		$query = "
			UPDATE #__anodos_stock
			SET partner_id = {$partnerId}
			WHERE id = {$stockId};";
		$db->setQuery($query);
		$db->query();

		// Возвразщаем результат
		return true;
	}

	// Снятие с публикации устаревшей информации о количествах товара на складах
	public function clearSQL($stockId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$stockId = $db->quote($stockId);

		// Выполняем запрос
		$query = "
			UPDATE #__anodos_product_quantity
			SET state = 0
			WHERE stock_id = {$stockId} AND publish_down < NOW() AND state = 1;";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}

	// Заносим количество в базу
	public function addQuantity($stockId, $productId, $quantity, $addDate = 3, $createdBy = 0) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$stockId = $db->quote($stockId);
		$productId = $db->quote($productId);
		$quantity = $db->quote($quantity);
		$addDate = $db->quote($addDate.' days');
		$createdBy = $db->quote($createdBy);

		// Заносим информацию в базу
		$query="
			BEGIN;

			UPDATE #__anodos_product_quantity
			SET state = '0'
			WHERE stock_id = {$stockId} AND product_id = {$productId};

			INSERT INTO #__anodos_product_quantity (
				product_id,
				stock_id,
				quantity,
				created,
				created_by,
				publish_up,
				publish_down)
			VALUES (
				{$productId},
				{$stockId},
				{$quantity},
				NOW(),
				{$createdBy},
				NOW(),
				NOW() + INTERVAL {$addDate});

			COMMIT;
		";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}
}
