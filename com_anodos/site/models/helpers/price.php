<?php

defined('_JEXEC') or die;

class Price {

	// Возвращает последнюю цену на продукт указанного поставщика
	public function getPrice($stockId, $productId, $state = 1) {
		//TODO
	}

	// Заносит цену в базу
	public function addPrice($stockId, $productId, $price, $currencyId, $priceTypeId, $addDate = 3, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Заносим информацию в базу
		$query="
			INSERT INTO #__anodos_price (
				stock_id,
				product_id,
				created,
				price,
				currency_id,
				price_type_id,
				created_by,
				publish_up,
				publish_down)
			VALUES (
				'{$stockId}',
				'{$productId}',
				NOW(),
				'{$price}',
				'{$currencyId}',
				'{$priceTypeId}',
				'{$createdBy}',
				NOW(),
				NOW() + INTERVAL '{$addDate} days'
			);
		";
		$db->setQuery($query);
		$db->query();
		return true;
	}

	// Возвращает id типа цены по псевдониму
	public function getPriceTypeFromAlias($alias) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT * 
			FROM #__anodos_price_type
			WHERE alias = '{$alias}'
		;";
		$db->setQuery($query);
		$priceType = $db->loadObject();

		// Возвращаем результат
		return $priceType;
	}

	// Добавляет тип цен
	public function addPriceType($name, $alias, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			INSERT INTO #__anodos_price_type (
				name,
				alias,
				state,
				created,
				created_by
				)
			VALUES (
				'{$name}',
				'{$alias}',
				'1',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос
		$query = "
			SELECT * 
			FROM #__anodos_price_type
			WHERE alias = '{$alias}'
		;";
		$db->setQuery($query);
		$priceType = $db->loadObject();

		// Возвращаем результат
		return $priceType;
	}

	// Снятие с публикации устаревшей информации о ценах
	public function clearSQL($stockId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE #__anodos_price
			SET state = '0'
			WHERE stock_id = '{$stockId}';";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}
}
