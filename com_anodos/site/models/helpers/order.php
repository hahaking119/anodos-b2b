<?php
defined('_JEXEC') or die;

class Order {

	// TODO
	public function createOrder($clientId, $clientName, $contractorId, $contractorName, $orderId, $orderName) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		// Если имя заказа не задано
		if ('' === $orderName) {
			$orderName = $db->quote('Новый заказ');
		}

		// Если новый заказчик
		if (0 === $clientId) {
			$clientId = 'NULL';
			// Если имя заказчика не задано
			if ('' === $clientName) {
				$clientName = $db->quote('Новый заказчик');
			}
		} else {
			$clientId = $db->quote($clientId);
			$clientName = 'NULL';
		}

		// Если новое юридическое лицо
		if (0 === $contractorId) {
			$contractorId = 'NULL';
			// Если имя юридического лица не задано
			if ('' === $contractorName) {
				$contractorName = $db->quote('Новое юридическое лицо');
			}
		} else {
			$contractorId = $db->quote($contractorId);
			$contractorName = 'NULL';
		}

		// Определяем автора
		$createdBy = $db->quote($user->id);

		// Определяем публичные ключи
		$viewKey = md5(microtime());
		$editKey = md5($viewKey);

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_order (
				partner_id,
				partner_name,
				contractor_id,
				contractor_name,
				created,
				created_by,
				name,
				state,
				stage_id,
				description,
				modified,
				modified_by,
				view_open_key,
				edit_open_key)
			VALUES (
				{$clientId},
				{$clientName},
				{$contractorId},
				{$contractorName},
				NOW(),
				{$createdBy},
				{$orderName},
				1,
				1,
				'',
				NOW(),
				{$createdBy},
				{$db->quote($viewKey)},
				{$db->quote($editKey)});";
		$db->setQuery($query);
		$db->query();

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_order
			WHERE {$db->quote($viewKey)} = view_open_key
			AND {$db->quote($editKey)} = edit_open_key;";
		$db->setQuery($query);
		$order = $db->loadObject();

		// Возвращаем результат
		return $order;
	}

	public function getOrderById($orderId) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$orderId = $db->quote($orderId);

		// Выполняем запрос выборки
		$query = "
			SELECT *
			FROM #__anodos_order
			WHERE {$orderId} = id;";
		$db->setQuery($query);
		$order = $db->loadObject();

		// Возвращаем результат
		return $order;
	}

	public function addOrderLine($order, $price, $quantity) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		// Переопределяем переменные
		$orderId = $db->quote($order->id);
		$productId = $db->quote($price->product_id);
		$productName = $db->quote($price->product_name);
		$quantity = $db->quote($quantity);
		$measureUnitId = $db->quote($price->measure_unit_id);
		$priceIn = $db->quote($price->price_in);
		$currencyInId = $db->quote($price->currency_in_id);
		$priceTypeInId = $db->quote($price->price_type_in_id);
		$stockId = $db->quote($price->stock_id);
		$priceOut = $db->quote($price->price_out);
		$createdBy = $db->quote($user->id);

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_order_line (
				order_id,
				product_id,
				product_name,
				quantity,
				measure_unit_id,
				price_in,
				currency_in_id,
				price_type_in_id,
				stock_id,
				price_out,
				currency_out_id,
				created,
				created_by,
				modified,
				modified_by)
			VALUES (
				{$orderId},
				{$productId},
				{$productName},
				{$quantity},
				{$measureUnitId},
				{$priceIn},
				{$currencyInId},
				{$priceTypeInId},
				{$stockId},
				{$priceOut},
				'1',
				NOW(),
				{$createdBy},
				NOW(),
				{$createdBy}
			);
		";
		$db->setQuery($query);
		$db->query();

		// Возвращаем результат
		return true;
	}

	public function getLinesFromOrder($orderId) {

		// Инициализируем переменные
		$db = JFactory::getDBO();

		// Исключаем инъекцию
		$orderId = $db->quote($orderId);

		// Выполняем запрос выборки и возвращаем результат
		$query = "SELECT * FROM #__anodos_order_line WHERE {$orderId} = order_id;";
		$db->setQuery($query);
		$lines = $db->loadObjectList();

		// Возвращаем результат
		return $lines;
	}

}
