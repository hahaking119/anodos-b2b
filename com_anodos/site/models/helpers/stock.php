<?php
defined('_JEXEC') or die;

class Stock {

	// Возвращает объект склада (по alias из базы)
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
			WHERE alias = {$alias};";
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

	// Заносим количество в базу
	public function addQuantity($stockId, $productId, $quantity, $addDate = 3, $createdBy = 0) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$stockId = $db->quote($stockId);
		$productId = $db->quote($productId);
		$unixtime = $db->quote(microtime());
		$quantity = $db->quote($quantity);
		$addDate = $db->quote($addDate);
		$createdBy = $db->quote($createdBy);

		// Заносим информацию в базу
//		$query="
//			UPDATE #__anodos_product_quantity_history
//			SET state = 0
//			WHERE stock_id = {$stockId} AND product_id = {$productId};
//		";
//		$db->setQuery($query);
//		$db->query();

//		$query="
//			INSERT INTO #__anodos_product_quantity_history (
//				product_id,
//				stock_id,
//				unixtime,
//				quantity,
//				created,
//				created_by,
//				modified_by,
//				publish_up,
//				publish_down)
//			VALUES (
//				{$productId},
//				{$stockId},
//				{$unixtime},
//				{$quantity},
//				NOW(),
//				{$createdBy},
//				{$createdBy},
//				NOW(),
//				DATE_ADD(NOW(), INTERVAL {$addDate} DAY));
//		";
//		$db->setQuery($query);
//		$db->query();

		// TODO есть цена есть - изменяем, если цены нет - добавляем
		$query="SELECT quantity FROM #__anodos_product_quantity WHERE stock_id = {$stockId} AND product_id = {$productId};";
		$db->setQuery($query);

		if (true == $db->loadResult()) {
			$query="
				UPDATE #__anodos_product_quantity SET
					quantity = {$quantity},
					publish_up = NOW(),
					publish_down = DATE_ADD(NOW(), INTERVAL {$addDate} DAY)
				WHERE stock_id = {$stockId} AND product_id = {$productId};
			";
			$db->setQuery($query);
			$db->query();
		} else {
			$query="
				INSERT INTO #__anodos_product_quantity (
					product_id,
					stock_id,
					quantity,
					publish_up,
					publish_down)
				VALUES (
					{$productId},
					{$stockId},
					{$quantity},
					NOW(),
					DATE_ADD(NOW(), INTERVAL {$addDate} DAY));
				";
			$db->setQuery($query);
			$db->query();
		}
		return true;
	}

	// Удаляет состояние склада указанного товара
	public function removeQuantityOfProduct($productId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$productId = $db->quote($productId);

		// Выполняем запрос
		$query = "DELETE FROM #__anodos_product_quantity WHERE product_id = {$productId};";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}

	// Снятие с публикации устаревшей информации о количествах товара на складах
	public function clearSQL($stockId, $deadTime) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$stockId = $db->quote($stockId);
		$deadTime = $db->quote($deadTime);

		// Помечаем неактуальную информацию о наличии в истории неактуальными
//		$query = "
//			UPDATE #__anodos_product_quantity_history
//			SET state = 0
//			WHERE stock_id = {$stockId} AND publish_up < NOW() AND state = 1;";
//		$db->setQuery($query);
//		$db->query();

		// Удаляем неактуальную информацию о наличии  из основной таблицы
		$query = "
			DELETE FROM #__anodos_product_quantity
			WHERE stock_id = {$stockId} AND publish_up < {$deadTime};";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}
}
