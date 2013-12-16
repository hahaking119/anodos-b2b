<?php
defined('_JEXEC') or die;

class Order {

	// TODO
	public function createOrder($orderName, $clientId, $clientName) {

		// Инициализируем переменные
		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		// Переопределяем значения переменных
		if ('' === $orderName) {
			$orderName = 'Новый заказ';
		}
		if (0 === $clientId) { // Новый заказчик
			$clientId = 'NULL';
			$orderName = $db->quote($orderName);
		} else { // Существующий заказ
			$clientId = $db->quote($clientId);
			$orderName = $db->quote($clientName.' - '.$orderName);
		}
		$createdBy = $db->quote($user->id);
		$viewKey = md5(microtime());
		$editKey = md5($viewKey);

		// Выполняем запрос вставки
		$query = "
			INSERT INTO #__anodos_order (
				partner_id,
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
}
