<?php

defined('_JEXEC') or die;

class Stock {

	// Возвращает объект склада (по alias из базы), если его нет - добавляет
	public function getStockFromAlias($alias) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			SELECT *
			FROM `#__anodos_stock`
			WHERE `alias` = '{$alias}';";
		$db->setQuery($query);
		$stock = $db->loadObject();

		// Возвращаем результат
		return $stock;
	}

	// Создает склад
	public function addStock($name, $alias, $partnerId, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_stock (
				`name`,
				`alias`,
				`partner_id`,
				`created`,
				`created_by`)
			VALUES (
				'{$name}',
				'{$alias}',
				'{$partnerId}',
				NOW(),
				'{$createdBy}');";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM `#__anodos_stock`
			WHERE `alias` = '{$alias}';";
		$db->setQuery($query);
		$stock = $db->loadObject();

		// Возвращаем результат
		return $stock;
	}

	public function linkStockToContractor ($stockId, $contractorId, $createdBy = 0) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE `#__anodos_stock`
			SET `contractor_id` = {$contractorId}
			WHERE `id` = '{$stockId}';";
		$db->setQuery($query);
		$db->query();

		// Возвразщаем результат
		return true;
	}

	// Снятие с публикации устаревшей информации о количествах товара на складах
	public function clearSQL($stockId) {

		// Подключаемся к базе
		$db = JFactory::getDBO();

		// Выполняем запрос
		$query = "
			UPDATE `#__anodos_product_quantity`
			SET state = '0'
			WHERE `stock_id` = '{$stockId}';";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}

	// Заносим количество в базу
	public function addQuantity($stockId, $productId, $quantity, $addDate = 3, $createdBy = 0) {

		// Определяем переменные
		$version = 0;

		// Инициализируем переменные
		$db =& JFactory::getDBO();

		// TODO проверем наличие цены c теми же ключами
		$query = "
			SELECT MAX(version)
			FROM `#__anodos_product_quantity`
			WHERE `#__anodos_product_quantity`.`stock_id` = '{$stockId}'
			AND   `#__anodos_product_quantity`.`product_id`    = '{$productId}'
			AND   `#__anodos_product_quantity`.`created`       = NOW();";
		$db->setQuery($query);
		$version =  $db->loadResult();

		//Получаем последнюю версию
		if (NULL != $version) {
			$version++;
		} else {
			$version = 0;
		}

		// Заносим информацию в базу
		$query="
			INSERT INTO `#__anodos_product_quantity` (
				`product_id`,
				`stock_id`,
				`version`,
				`quantity`,
				`created`,
				`created_by`,
				`publish_up`,
				`publish_down`)
			VALUES (
				'{$productId}',
				'{$stockId}',
				'{$version}',
				'{$quantity}',
				NOW(),
				'{$createdBy}',
				NOW(),
				ADDDATE(now(), {$addDate}));";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}
}
