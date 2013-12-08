<?php

defined('_JEXEC') or die;

class Price {

	// Возвращает последнюю цену на продукт указанного поставщика
	public function getPrice($stockId, $productId, $state = 1) {
		//TODO
	}

	// Заносит цену в базу
	public function addPrice($stockId, $productId, $price, $currencyId, $priceTypeId, $addDate = 3, $createdBy = 0) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$stockId = $db->quote($stockId);
		$productId = $db->quote($productId);
		$unixtime = $db->quote(microtime());
		$price = $db->quote($price);
		$currencyId = $db->quote($currencyId);
		$priceTypeId = $db->quote($priceTypeId);
		$addDate = $db->quote($addDate);
		$createdBy = $db->quote($createdBy);

		// Помечаем неактуальной цены в истории
//		$query="
//			UPDATE #__anodos_price_history
//			SET state = 0
//			WHERE stock_id = {$stockId} AND product_id = {$productId};
//		";
//		$db->setQuery($query);
//		$db->query();

		// Добавляем цену в историю
//		$query="
//			INSERT INTO #__anodos_price_history (
//				stock_id,
//				product_id,
//				unixtime,
//				created,
//				price,
//				currency_id,
//				price_type_id,
//				created_by,
//				modified_by,
//				publish_up,
//				publish_down)
//			VALUES (
//				{$stockId},
//				{$productId},
//				{$unixtime},
//				NOW(),
//				{$price},
//				{$currencyId},
//				{$priceTypeId},
//				{$createdBy},
//				{$createdBy},
//				NOW(),
//				DATE_ADD(NOW(), INTERVAL {$addDate} DAY));
//		";
//		$db->setQuery($query);
//		$db->query();

		// если цена есть - изменяем, если цены нет - добавляем
		$query="SELECT price FROM #__anodos_price WHERE stock_id = {$stockId} AND product_id = {$productId};";
		$db->setQuery($query);
		if (true == $db->loadResult()) {
			$query="
				UPDATE #__anodos_price SET
					price = {$price},
					currency_id = {$currencyId},
					price_type_id = {$priceTypeId},
					publish_up = NOW(),
					publish_down = DATE_ADD(NOW(), INTERVAL {$addDate} DAY)
				WHERE stock_id = {$stockId} AND product_id = {$productId};
			";
			$db->setQuery($query);
			$db->query();
		} else {
			$query="
				INSERT INTO #__anodos_price (
					stock_id,
					product_id,
					price,
					currency_id,
					price_type_id,
					publish_up,
					publish_down)
				VALUES (
					{$stockId},
					{$productId},
					{$price},
					{$currencyId},
					{$priceTypeId},
					NOW(),
					DATE_ADD(NOW(), INTERVAL {$addDate} DAY));
				";
			$db->setQuery($query);
			$db->query();
		}
		return true;
	}

	// Возвращает id типа цены по псевдониму
	public function getPriceTypeFromAlias($alias) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$alias = $db->quote($alias);

		// Выполняем запрос
		$query = "
			SELECT * 
			FROM #__anodos_price_type
			WHERE alias = {$alias};";
		$db->setQuery($query);
		$priceType = $db->loadObject();

		// Возвращаем результат
		return $priceType;
	}

	// Добавляет тип цен
	public function addPriceType($name, $alias, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$alias = $db->quote($alias);
		$name = $db->quote($name);
		$createdBy = $db->quote($createdBy);

		// Выполняем запрос
		$query = "
			INSERT INTO #__anodos_price_type (
				name,
				alias,
				created,
				created_by
				)
			VALUES (
				{$name},
				{$alias},
				NOW(),
				{$createdBy});";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос
		$query = "
			SELECT * 
			FROM #__anodos_price_type
			WHERE alias = {$alias};";
		$db->setQuery($query);
		$priceType = $db->loadObject();

		// Возвращаем результат
		return $priceType;
	}

	// Снятие с публикации устаревшей информации о ценах
	public function clearSQL($stockId, $upTime) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$stockId = $db->quote($stockId);
		$upTime = $db->quote($upTime);

		// Помечаем неактуальные цены в истории неактуальными
//		$query = "
//			UPDATE #__anodos_price_history
//			SET state = 0
//			WHERE stock_id = {$stockId} AND publish_up < {$deadTime} AND state = 1;";
//		$db->setQuery($query);
//		$db->query();

		// Удаляем неактуальные цены из основной таблицы
		$query = "
			DELETE FROM #__anodos_price
			WHERE stock_id = {$stockId} AND publish_up < {$upTime};";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}
}
