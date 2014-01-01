<?php

defined('_JEXEC') or die;

class Price {

	// TODO test
	// Возвращает объект лучшей цены на продукт
	public function getPrice($productId) {

		// Обрабатываем результат
		$price = Price::getPriceIn($productId);
		$price = Price::getPriceOut($price);

		// Возвращаем результат
		return $price;
	}

	// TODO test
	// Возвращает объект входной цены для продукта
	public function getPriceIn($productId) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$productId = $db->quote($productId);

		// Выполняем запрос
		$query="
			SELECT
				product.id AS product_id,
				product.name AS product_name,
				product.measure_unit_id AS measure_unit_id,
				price.price AS price_in,
				MIN(price.price*rate.rate/rate.quantity) AS price_in_rub,
				price.price_type_id AS price_type_in_id,
				price_type.alias AS price_type_in_alias,
				price_type.name AS price_type_in_name,
				currency.id AS currency_in_id,
				currency.alias AS currency_alias,
				currency.name_html AS currency_name,
				quantity.quantity AS quantity,
				stock.id AS stock_id,
				stock.name AS stock_name
			FROM #__anodos_product AS product
			INNER JOIN #__anodos_price AS price ON price.product_id = product.id
			INNER JOIN #__anodos_price_type AS price_type ON price.price_type_id = price_type.id
			INNER JOIN #__anodos_currency AS currency ON currency.id = price.currency_id
			INNER JOIN #__anodos_currency_rate AS rate ON rate.currency_id = currency.id
			INNER JOIN #__anodos_product_quantity AS quantity ON quantity.product_id = product.id
			INNER JOIN #__anodos_stock AS stock ON stock.id = quantity.stock_id
			WHERE price.stock_id = quantity.stock_id
			AND quantity.quantity != 0
			AND product.id = {$productId}
			GROUP BY product_id;
		";
		$db->setQuery($query);
		$price = $db->loadObject();

		// Обрабатываем результат
		$price->price_in = round($price->price_in, 2, PHP_ROUND_HALF_UP);

		// Возвращаем результат
		return $price;
	}

	// TODO test
	// Возвращает объект розничной цены для продукта
	// На вход получает или id-продукта или объект цены с product_id, price_in, price_type_in_alias
	public function getPriceOut($input = 0) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$componentParams = JComponentHelper::getParams('com_anodos');

		// 
		if ((!isset($input->product_id)) or (!isset($input->price_in_rub)) or (!isset($input->price_type_in_alias))) {

			// Исключаем инъекцию
			$productId = $db->quote($input);

			// Выполняем запрос
			$query="
				SELECT
					product.id AS product_id,
					product.name AS product_name,
					product.measure_unit_id AS measure_unit_id,
					price.price AS price_in,
					MIN(price.price*rate.rate/rate.quantity) AS price_in_rub,
					price.price_type_id AS price_type_in_id,
					price_type.alias AS price_type_in_alias,
					price_type.name AS price_type_in_name,
					currency.id AS currency_in_id,
					currency.alias AS currency_alias,
					currency.name_html AS currency_name,
					quantity.quantity AS quantity,
					stock.id AS stock_id,
					stock.name AS stock_name
				FROM #__anodos_product AS product
				INNER JOIN #__anodos_price AS price ON price.product_id = product.id
				INNER JOIN #__anodos_price_type AS price_type ON price.price_type_id = price_type.id
				INNER JOIN #__anodos_currency AS currency ON currency.id = price.currency_id
				INNER JOIN #__anodos_currency_rate AS rate ON rate.currency_id = currency.id
				INNER JOIN #__anodos_product_quantity AS quantity ON quantity.product_id = product.id
				INNER JOIN #__anodos_stock AS stock ON stock.id = quantity.stock_id
				WHERE price.stock_id = quantity.stock_id
				AND quantity.quantity != 0
				AND product.id = {$productId}
				GROUP BY product_id;
			";
			$db->setQuery($query);
			$price = $db->loadObject();

			// Обрабатываем результат
			$price->price_in = round($price->price_in, 2, PHP_ROUND_HALF_UP);
		} else {
			$price = $input;
		}

		// Получаем стандартную наценку
		$marginStd = $componentParams->get('margin-std', 8);

		// Обрабатываем результат
		$price->price_rub_out = round($price->price_in_rub * (100 + $marginStd) / 100, 0, PHP_ROUND_HALF_UP);

		// Возвращаем результат
		return $price;
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

	// Удаляет цены указанного товара
	public function removePriceOfProduct($productId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$productId = $db->quote($productId);

		// Выполняем запрос
		$query = "DELETE FROM #__anodos_price WHERE product_id = {$productId};";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
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
